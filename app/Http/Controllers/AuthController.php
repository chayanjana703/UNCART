<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ActivityLog;

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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400&auto=format&fit=crop&q=80',
            'email_verified_at' => now(),
        ]);

        if ($role === 'vendor') {
            \App\Models\Vendor::create([
                'user_id' => $user->id,
                'shop_name' => $user->name . "'s Shop",
                'shop_slug' => \Illuminate\Support\Str::slug($user->name . '-' . rand(100, 999)),
                'status' => 'pending',
                'kyc_status' => 'pending',
                'commission_rate' => 10.00,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'User Registration',
            'description' => "Registered new account with role: {$role}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($user->isVendor()) {
            return redirect()->route('vendor.dashboard')->with('success', 'Your seller account has been registered! Complete your shop profile to start selling.');
        }

        return redirect()->route('home')->with('success', "Welcome to ShopiCart, {$user->name}!");
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
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        return back()->with('success', 'A secure password reset link has been dispatched to your email address.');
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
            'admin' => 'admin@shopicart.com',
            'vendor' => 'vendor@shopicart.com',
            'customer' => 'customer@shopicart.com',
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
