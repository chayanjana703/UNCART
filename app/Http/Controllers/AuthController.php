<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use App\Models\ActivityLog;
use App\Notifications\ResetPasswordOtpNotification;
use App\Notifications\RegistrationOtpNotification;
use App\Notifications\AccountCreatedNotification;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($user->is_blocked) {
                Auth::logout();
                return back()->with('error', 'Your account has been suspended by administration.');
            }

            // Check 2FA
            if ($user->two_factor_enabled) {
                session(['2fa_user_id' => $user->id]);
                Auth::logout();
                return redirect()->route('2fa.show');
            }

            $request->session()->regenerate();

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'User Login',
                'description' => "Logged in via email: {$user->email}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('success', "Welcome back, Admin {$user->name}!");
            } elseif ($user->isVendor()) {
                return redirect()->route('vendor.dashboard')->with('success', "Welcome back to your Seller Portal, {$user->name}!");
            }

            return redirect()->intended(route('home'))->with('success', "Welcome back, {$user->name}!");
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|in:customer,vendor',
        ]);

        $role = $validated['role'] ?? 'customer';
        $email = strtolower(trim($validated['email']));

        // Generate 6-digit numeric OTP
        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        session([
            'pending_registration' => [
                'name' => $validated['name'],
                'email' => $email,
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role' => $role,
                'otp_hash' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(15)->timestamp,
            ],
        ]);

        // Send OTP notification
        try {
            Notification::route('mail', $email)->notify(
                new RegistrationOtpNotification($otp, $validated['name'], $role)
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Could not dispatch registration OTP email to {$email}: " . $e->getMessage());
        }

        return redirect()->route('register.otp.show')->with('success', "A 6-digit verification code has been dispatched to {$email}. Please verify below to complete your registration.");
    }

    public function showVerifyOtp(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $pending = session('pending_registration');
        if (!$pending) {
            return redirect()->route('register')->with('error', 'No pending registration found. Please register first.');
        }

        $secondsLeft = max(0, ($pending['otp_expires_at'] ?? now()->timestamp) - now()->timestamp);

        return view('auth.verify-registration-otp', [
            'email' => $pending['email'],
            'role' => $pending['role'] ?? 'customer',
            'secondsLeft' => $secondsLeft,
            'verifyRoute' => route('register.otp.verify'),
            'resendRoute' => route('register.otp.resend'),
        ]);
    }

    public function verifyRegistrationOtp(Request $request)
    {
        $pending = session('pending_registration');
        if (!$pending) {
            return redirect()->route('register')->with('error', 'Session expired. Please fill out registration again.');
        }

        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if (now()->timestamp > ($pending['otp_expires_at'] ?? 0)) {
            return back()->with('error', 'The verification code has expired. Please request a new code.');
        }

        if (!Hash::check($request->otp, $pending['otp_hash'])) {
            return back()->with('error', 'Invalid verification code. Please check your email and try again.')->withInput();
        }

        // Ensure email wasn't taken in the interim
        if (User::where('email', $pending['email'])->exists()) {
            session()->forget('pending_registration');
            return redirect()->route('login')->with('info', 'An account with this email address already exists. Please sign in.');
        }

        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'phone' => $pending['phone'] ?? null,
            'password' => $pending['password'], // already hashed
            'role' => $pending['role'],
            'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400&auto=format&fit=crop&q=80',
            'email_verified_at' => now(),
        ]);

        $vendor = null;
        if ($pending['role'] === 'vendor') {
            $vendor = \App\Models\Vendor::create([
                'user_id' => $user->id,
                'shop_name' => $user->name . "'s Shop",
                'shop_slug' => Str::slug($user->name . '-' . rand(100, 999)),
                'status' => 'pending',
                'kyc_status' => 'pending',
                'commission_rate' => 10.00,
            ]);
        }

        // Dispatch account created confirmation email with account details
        try {
            $user->notify(new AccountCreatedNotification($user, $vendor));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Could not dispatch account created welcome email to {$user->email}: " . $e->getMessage());
        }

        Auth::login($user);
        $request->session()->regenerate();
        session()->forget('pending_registration');

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'User Registration (Verified)',
            'description' => "Verified email with OTP and activated account with role: {$user->role}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($user->isVendor()) {
            return redirect()->route('vendor.dashboard')->with('success', 'Email verified! Your seller account is active. Complete your shop profile to start selling.');
        }

        return redirect()->route('home')->with('success', "Welcome to UNCART, {$user->name}! Your email is verified.");
    }

    public function resendRegistrationOtp(Request $request)
    {
        $pending = session('pending_registration');
        if (!$pending) {
            return redirect()->route('register')->with('error', 'Session expired. Please fill out registration again.');
        }

        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $pending['otp_hash'] = Hash::make($otp);
        $pending['otp_expires_at'] = now()->addMinutes(15)->timestamp;
        session(['pending_registration' => $pending]);

        try {
            Notification::route('mail', $pending['email'])->notify(
                new RegistrationOtpNotification($otp, $pending['name'], $pending['role'])
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Could not resend registration OTP email: " . $e->getMessage());
        }

        return back()->with('success', "A fresh 6-digit verification code has been dispatched to {$pending['email']}.");
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'User Logout',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'You have been signed out successfully.');
    }

    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.forgot-password');
    }

    public function sendResetOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email'
        ]);

        $email = strtolower(trim($validated['email']));
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->with('error', 'We could not find an account registered with that email address.')->withInput();
        }

        try {
            // Generate 6-digit numeric OTP
            $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

            // Store hashed OTP in password_reset_tokens
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => Hash::make($otp),
                    'created_at' => now(),
                ]
            );

            // Send branded OTP notification via Gmail SMTP
            $user->notify(new ResetPasswordOtpNotification($otp));

            session(['password_reset_email' => $email]);

            return redirect()->route('password.reset')->with('success', "A 6-digit verification code has been dispatched to {$email}. Please enter it below to set your new password.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Unable to send OTP email: ' . $e->getMessage())->withInput();
        }
    }

    public function resendResetOtp(Request $request)
    {
        $email = strtolower(trim($request->input('email', session('password_reset_email', ''))));

        if (!$email) {
            return redirect()->route('password.request')->with('error', 'Please enter your email to request a verification code.');
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('password.request')->with('error', 'Account not found.');
        }

        try {
            $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => Hash::make($otp),
                    'created_at' => now(),
                ]
            );

            $user->notify(new ResetPasswordOtpNotification($otp));
            session(['password_reset_email' => $email]);

            return back()->with('success', "A fresh 6-digit verification code has been sent to {$email}.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Unable to resend OTP email: ' . $e->getMessage());
        }
    }

    public function showResetPassword(Request $request, ?string $token = null)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $email = $request->query('email', session('password_reset_email', ''));

        return view('auth.reset-password', [
            'email' => $email,
            'presetOtp' => $token,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = strtolower(trim($validated['email']));
        $otp = trim($validated['otp']);

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record) {
            return back()->with('error', 'No active verification code found for this email. Please request a new code.')->withInput($request->except('password', 'password_confirmation'));
        }

        // Check 15-minute expiration
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return back()->with('error', 'This verification code has expired (valid for 15 minutes). Please click resend to get a fresh code.')->withInput($request->except('password', 'password_confirmation'));
        }

        // Check OTP match (supports both bcrypt hash and plain string fallback)
        if (!Hash::check($otp, $record->token) && $otp !== $record->token) {
            return back()->with('error', 'The 6-digit verification code you entered is invalid. Please check your inbox and try again.')->withInput($request->except('password', 'password_confirmation', 'otp'));
        }

        // Update user password
        $user = User::where('email', $email)->firstOrFail();
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->setRememberToken(Str::random(60));
        $user->save();

        // Consume OTP token
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        session()->forget('password_reset_email');

        event(new PasswordReset($user));

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Password Reset',
            'description' => "Password successfully reset via 6-digit OTP verification.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('login')->with('success', 'Your password has been reset successfully! Please sign in with your new password.');
    }

    public function show2FA()
    {
        if (!session('2fa_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.two-factor');
    }

    public function verify2FA(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $userId = session('2fa_user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        // For simulation, accept code '123456' or any 6-digit code
        session()->forget('2fa_user_id');
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Two-Factor Authentication verified successfully.');
    }

    /**
     * Instant 1-Click Role Switcher for seamless demonstration & evaluation
     */
    public function quickLogin($role)
    {
        $targetEmail = match ($role) {
            'admin' => User::where('email', 'admin@uncart.com')->exists() ? 'admin@uncart.com' : 'admin@shopicart.com',
            'vendor' => User::where('email', 'vendor@uncart.com')->exists() ? 'vendor@uncart.com' : 'vendor@shopicart.com',
            'customer' => User::where('email', 'customer@uncart.com')->exists() ? 'customer@uncart.com' : 'customer@shopicart.com',
            default => null,
        };

        if (!$targetEmail) {
            Auth::logout();
            return redirect()->route('home')->with('info', 'Switched to Guest mode.');
        }

        $user = User::where('email', $targetEmail)->first();
        if ($user) {
            Auth::login($user);
            request()->session()->regenerate();

            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard')->with('success', "Quick switched to Administrator ({$user->name})"),
                'vendor' => redirect()->route('vendor.dashboard')->with('success', "Quick switched to Vendor ({$user->name})"),
                default => redirect()->route('home')->with('success', "Quick switched to Customer ({$user->name})"),
            };
        }

        return redirect()->route('home')->with('error', 'User not found.');
    }
}
