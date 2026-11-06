<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\ActivityLog;
use App\Notifications\RegistrationOtpNotification;
use App\Notifications\AccountCreatedNotification;
use Illuminate\Support\Facades\Notification;

class VendorRegistrationController extends Controller
{
    public function showLanding()
    {
        // If already a vendor, redirect to vendor dashboard
        if (Auth::check() && Auth::user()->isVendor()) {
            return redirect()->route('vendor.dashboard');
        }

        $topVendors = Vendor::where('status', 'active')
            ->where('kyc_status', 'verified')
            ->withCount('products')
            ->take(3)
            ->get();

        return view('pages.become-a-seller', compact('topVendors'));
    }

    public function register(Request $request)
    {
        $rules = [
            'shop_name' => 'required|string|max:100|unique:vendors,shop_name',
            'shop_description' => 'required|string|max:500',
            'business_email' => 'required|email|max:150',
            'business_phone' => 'required|string|max:30',
            'address' => 'required|string|max:255',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:30',
            'tax_id' => 'required|string|max:50',
            'shop_logo_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'kyc_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp|max:10240',
        ];

        if (!Auth::check()) {
            $rules['name'] = 'required|string|max:100';
            $rules['email'] = 'required|email|max:150|unique:users,email';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // Handle shop logo upload from device
        $logoPath = null;
        if ($request->hasFile('shop_logo_file')) {
            $file = $request->file('shop_logo_file');
            $filename = 'vendor_logo_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/shops', $filename);
            $logoPath = '/storage/shops/' . $filename;
        }

        // Handle KYC document upload from device
        $kycDocument = 'Tax/EIN ID: ' . $validated['tax_id'];
        if ($request->hasFile('kyc_file')) {
            $kycFile = $request->file('kyc_file');
            $kycFilename = 'kyc_' . time() . '_' . Str::random(8) . '.' . $kycFile->getClientOriginalExtension();
            $kycFile->storeAs('public/kyc', $kycFilename);
            $kycDocument = '/storage/kyc/' . $kycFilename . ' (Tax/EIN: ' . $validated['tax_id'] . ')';
        }

        $userEmail = Auth::check() ? Auth::user()->email : $validated['email'];
        $userName = Auth::check() ? Auth::user()->name : $validated['name'];
        $userId = Auth::check() ? Auth::id() : null;

        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        session([
            'pending_vendor_registration' => [
                'user_id' => $userId,
                'name' => $userName,
                'email' => strtolower(trim($userEmail)),
                'password' => !empty($validated['password']) ? Hash::make($validated['password']) : null,
                'phone' => $validated['business_phone'],
                'shop_name' => $validated['shop_name'],
                'shop_description' => $validated['shop_description'],
                'business_email' => $validated['business_email'],
                'business_phone' => $validated['business_phone'],
                'address' => $validated['address'],
                'tax_id' => $validated['tax_id'],
                'bank_name' => $validated['bank_name'],
                'account_number' => $validated['account_number'],
                'ifsc_code' => $validated['ifsc_code'],
                'shop_logo' => $logoPath ?? 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=400&auto=format&fit=crop&q=80',
                'kyc_document' => $kycDocument,
                'otp_hash' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(15)->timestamp,
            ],
        ]);

        try {
            Notification::route('mail', $userEmail)->notify(
                new RegistrationOtpNotification($otp, $userName, 'vendor')
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Could not dispatch vendor registration OTP to {$userEmail}: " . $e->getMessage());
        }

        return redirect()->route('vendor.become.otp.show')->with('success', "A 6-digit verification code has been dispatched to {$userEmail}. Please verify below to activate your merchant account.");
    }

    public function showVerifyOtp(Request $request)
    {
        $pending = session('pending_vendor_registration');
        if (!$pending) {
            return redirect()->route('vendor.become')->with('error', 'No pending seller registration found. Please submit your application.');
        }

        $secondsLeft = max(0, ($pending['otp_expires_at'] ?? now()->timestamp) - now()->timestamp);

        return view('auth.verify-registration-otp', [
            'email' => $pending['email'],
            'role' => 'vendor',
            'secondsLeft' => $secondsLeft,
            'verifyRoute' => route('vendor.become.otp.verify'),
            'resendRoute' => route('vendor.become.otp.resend'),
        ]);
    }

    public function verifyRegistrationOtp(Request $request)
    {
        $pending = session('pending_vendor_registration');
        if (!$pending) {
            return redirect()->route('vendor.become')->with('error', 'Session expired. Please fill out the seller registration again.');
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

        // 1. Resolve or create User
        if (!empty($pending['user_id'])) {
            $user = User::find($pending['user_id']);
            if (!$user) {
                return redirect()->route('vendor.become')->with('error', 'User account not found.');
            }
            $user->role = 'vendor';
            $user->save();
        } else {
            $user = User::where('email', $pending['email'])->first();
            if ($user) {
                $user->role = 'vendor';
                $user->save();
            } else {
                $user = User::create([
                    'name' => $pending['name'],
                    'email' => $pending['email'],
                    'phone' => $pending['phone'],
                    'password' => $pending['password'],
                    'role' => 'vendor',
                    'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80',
                    'email_verified_at' => now(),
                ]);
            }
            Auth::login($user);
            $request->session()->regenerate();
        }

        // 2. Generate unique shop slug
        $baseSlug = Str::slug($pending['shop_name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Vendor::where('shop_slug', $slug)->exists()) {
            $slug = "{$baseSlug}-" . $counter++;
        }

        // 3. Create Vendor Profile
        $vendor = Vendor::create([
            'user_id' => $user->id,
            'shop_name' => $pending['shop_name'],
            'shop_slug' => $slug,
            'shop_description' => $pending['shop_description'],
            'shop_logo' => $pending['shop_logo'],
            'shop_banner' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=1400&auto=format&fit=crop&q=80',
            'phone' => $pending['business_phone'],
            'email' => $pending['business_email'],
            'address' => $pending['address'],
            'kyc_status' => 'pending',
            'kyc_document' => $pending['kyc_document'],
            'bank_name' => $pending['bank_name'],
            'account_number' => $pending['account_number'],
            'ifsc_code' => $pending['ifsc_code'],
            'commission_rate' => 8.50,
            'status' => 'active',
            'return_policy' => '30 days standard customer satisfaction guarantee.',
            'shipping_policy' => 'Orders dispatched within 24-48 business hours with express tracking.',
            'total_payouts' => 0.00,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Vendor Registered (OTP Verified)',
            'description' => "New seller '{$vendor->shop_name}' verified email OTP and activated merchant store. KYC pending.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Dispatch account created welcome email with store details
        try {
            $user->notify(new AccountCreatedNotification($user, $vendor));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Could not dispatch vendor account created welcome email to {$user->email}: " . $e->getMessage());
        }

        session()->forget('pending_vendor_registration');

        return redirect()->route('vendor.dashboard')->with('success', "Congratulations! '{$vendor->shop_name}' is verified and activated. Welcome to your Seller Dashboard!");
    }

    public function resendRegistrationOtp(Request $request)
    {
        $pending = session('pending_vendor_registration');
        if (!$pending) {
            return redirect()->route('vendor.become')->with('error', 'Session expired. Please submit your application again.');
        }

        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $pending['otp_hash'] = Hash::make($otp);
        $pending['otp_expires_at'] = now()->addMinutes(15)->timestamp;
        session(['pending_vendor_registration' => $pending]);

        try {
            Notification::route('mail', $pending['email'])->notify(
                new RegistrationOtpNotification($otp, $pending['name'], 'vendor')
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Could not resend vendor registration OTP: " . $e->getMessage());
        }

        return back()->with('success', "A fresh 6-digit verification code has been dispatched to {$pending['email']}.");
    }

    public function storefront($slug)
    {
        $vendor = Vendor::where('shop_slug', $slug)
            ->where('status', 'active')
            ->with(['user'])
            ->firstOrFail();

        $products = Product::where('vendor_id', $vendor->id)
            ->where('is_active', true)
            ->where('status', 'approved')
            ->with(['category'])
            ->paginate(12);

        $totalSales = $vendor->orderItems()->sum('quantity');

        return view('pages.vendor-storefront', compact('vendor', 'products', 'totalSales'));
    }
}
