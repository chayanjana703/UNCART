@extends('layouts.app')

@section('title', 'Verify OTP & Reset Password — UNCART')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6 bg-white p-8 sm:p-10 rounded-3xl border border-slate-100 shadow-xl" data-reveal="scale">
        
        <div class="text-center">
            <div class="mx-auto w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-pink-500 text-white flex items-center justify-center font-black text-2xl shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="mt-4 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Verify OTP & Reset Password
            </h2>
            <p class="mt-1 text-xs text-slate-500">
                Enter the 6-digit code sent to your email to authorize your password update.
            </p>
        </div>

        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start gap-2.5">
                <span class="text-emerald-600 text-sm">✓</span>
                <span class="leading-relaxed">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-start gap-2.5">
                <span class="text-amber-600 text-sm">⚠️</span>
                <span class="leading-relaxed">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Reset Form with OTP -->
        <form class="space-y-4" action="{{ route('password.update', [], false) }}" method="POST">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                    Account Email
                </label>
                <input id="email" 
                       name="email" 
                       type="email" 
                       required 
                       value="{{ old('email', $email) }}"
                       class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <!-- OTP Input -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="otp" class="block text-xs font-semibold text-slate-700">
                        6-Digit Security Code (OTP)
                    </label>
                    <span class="text-[11px] text-indigo-600 font-semibold font-mono">15 Min Expiry</span>
                </div>
                <input id="otp" 
                       name="otp" 
                       type="text" 
                       required 
                       maxlength="6"
                       inputmode="numeric"
                       pattern="[0-9]{6}"
                       autofocus
                       value="{{ old('otp', $presetOtp ?? '') }}"
                       placeholder="••••••"
                       class="w-full text-center font-mono text-2xl font-black tracking-[0.4em] rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                    New Password
                </label>
                <input id="password" 
                       name="password" 
                       type="password" 
                       required 
                       placeholder="At least 8 characters"
                       class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">
                    Confirm New Password
                </label>
                <input id="password_confirmation" 
                       name="password_confirmation" 
                       type="password" 
                       required 
                       placeholder="Repeat new password"
                       class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md active:scale-[0.98]">
                Reset Password &rarr;
            </button>
        </form>

        <!-- Resend OTP Trigger -->
        <div class="pt-2 text-center border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Didn't receive the code?</span>
            <form action="{{ route('password.resend', [], false) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="email" value="{{ old('email', $email) }}">
                <button type="submit" class="font-bold text-indigo-600 hover:underline">
                    Resend Code
                </button>
            </form>
        </div>

        <div class="text-center pt-1">
            <a href="{{ route('login') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline inline-flex items-center gap-1">
                <span>&larr;</span>
                <span>Back to Sign In</span>
            </a>
        </div>

    </div>
</div>
@endsection
