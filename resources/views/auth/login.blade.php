@extends('layouts.app')

@section('title', 'Sign In — UNCART')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6 bg-white p-8 sm:p-10 rounded-3xl border border-slate-100 shadow-xl" data-reveal="scale">
        
        <div class="text-center">
            <div class="mx-auto w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-pink-500 text-white flex items-center justify-center font-black text-2xl shadow-md">
                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <h2 class="mt-4 text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Welcome Back
            </h2>
            <p class="mt-1 text-xs text-slate-500">
                Enter your credentials or test instantly with a 1-click role profile
            </p>
        </div>

        <!-- 1-Click Fast Demo Credentials Bar -->
        <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80 text-xs">
            <div class="font-bold text-slate-600 uppercase tracking-wider text-[10px] mb-2 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                Instant 1-Click Demo Profiles
            </div>
            <div class="grid grid-cols-3 gap-2">
                <a href="{{ route('quick.login', 'customer') }}" class="py-2 px-2 text-center rounded-xl font-bold bg-white hover:bg-slate-900 text-slate-700 hover:text-white border border-slate-200 text-[11px] transition active:scale-95 shadow-sm">
                    👤 Customer
                </a>
                <a href="{{ route('quick.login', 'vendor') }}" class="py-2 px-2 text-center rounded-xl font-bold bg-white hover:bg-indigo-600 text-slate-700 hover:text-white border border-slate-200 text-[11px] transition active:scale-95 shadow-sm">
                    🏪 Merchant
                </a>
                <a href="{{ route('quick.login', 'admin') }}" class="py-2 px-2 text-center rounded-xl font-bold bg-white hover:bg-amber-600 text-slate-700 hover:text-white border border-slate-200 text-[11px] transition active:scale-95 shadow-sm">
                    🛡️ Admin
                </a>
            </div>
        </div>

        <!-- Standard Form -->
        <form class="space-y-4" action="{{ route('login.submit', [], false) }}" method="POST">
            @csrf

            @if(session('error'))
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-center gap-2">
                    <span class="text-base">⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="p-3 rounded-xl bg-sky-50 border border-sky-200 text-xs text-sky-800 flex items-center gap-2">
                    <span class="text-base">ℹ️</span>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                    Email address
                </label>
                <input id="email" 
                       name="email" 
                       type="email" 
                       required 
                       value="{{ old('email', 'customer@uncart.com') }}"
                       class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-semibold text-slate-700">
                        Password
                    </label>
                    <a href="{{ route('password.request') }}" class="text-[11px] font-semibold text-indigo-600 hover:underline">
                        Forgot Password?
                    </a>
                </div>
                <input id="password" 
                       name="password" 
                       type="password" 
                       required 
                       value="password"
                       class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <span>Remember my session</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md active:scale-[0.98]">
                Sign In &rarr;
            </button>
        </form>

        <div class="text-center pt-2">
            <span class="text-xs text-slate-500">Don't have an account yet?</span>
            <a href="{{ route('register') }}" class="text-xs font-bold text-indigo-600 hover:underline ml-1">
                Create Account
            </a>
        </div>

    </div>
</div>
@endsection
