@extends('layouts.app')

@section('title', 'Forgot Password — UNCART')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6 bg-white p-8 sm:p-10 rounded-3xl border border-slate-100 shadow-xl" data-reveal="scale">
        
        <div class="text-center">
            <div class="mx-auto w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-pink-500 text-white flex items-center justify-center font-black text-2xl shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h2 class="mt-4 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Forgot Password?
            </h2>
            <p class="mt-1 text-xs text-slate-500">
                Enter your registered email and we'll send you a 6-digit verification code (OTP) to reset your password.
            </p>
        </div>

        @if(session('success') || session('status'))
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start gap-2.5">
                <span class="text-emerald-600 text-sm">✓</span>
                <span class="leading-relaxed">{{ session('success') ?? session('status') }}</span>
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

        <form class="space-y-4" action="{{ route('password.email', [], false) }}" method="POST">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                    Registered Email address
                </label>
                <input id="email" 
                       name="email" 
                       type="email" 
                       required 
                       autofocus
                       value="{{ old('email', 'chayanjana703@gmail.com') }}"
                       placeholder="your-email@domain.com"
                       class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md active:scale-[0.98]">
                Send Verification Code (OTP) &rarr;
            </button>
        </form>

        <div class="text-center pt-2">
            <a href="{{ route('login') }}" class="text-xs font-bold text-indigo-600 hover:underline inline-flex items-center gap-1">
                <span>&larr;</span>
                <span>Back to Sign In</span>
            </a>
        </div>

    </div>
</div>
@endsection
