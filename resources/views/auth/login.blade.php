@extends('layouts.app')

@section('title', 'Sign In - ShopiCart')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl">
        
        <div class="text-center">
            <div class="mx-auto w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-2xl shadow-lg shadow-indigo-600/30">
                S
            </div>
            <h2 class="mt-4 text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Welcome back to ShopiCart
            </h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                Enter your credentials or choose a quick test account below
            </p>
        </div>

        <!-- 1-Click Fast Demo Credentials Bar -->
        <div class="bg-slate-50 dark:bg-slate-850 p-3 rounded-2xl border border-slate-200/80 dark:border-slate-800 text-xs">
            <div class="font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider text-[10px] mb-2 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Instant 1-Click Demo Login
            </div>
            <div class="grid grid-cols-3 gap-2">
                <a href="{{ route('quick.login', 'customer') }}" class="py-1.5 px-2 text-center rounded-xl font-medium bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-[11px] transition-colors">
                    👤 Customer
                </a>
                <a href="{{ route('quick.login', 'vendor') }}" class="py-1.5 px-2 text-center rounded-xl font-medium bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-[11px] transition-colors">
                    🏪 Seller
                </a>
                <a href="{{ route('quick.login', 'admin') }}" class="py-1.5 px-2 text-center rounded-xl font-medium bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-[11px] transition-colors">
                    🛡️ Admin
                </a>
            </div>
        </div>

        <!-- Standard Form -->
        <form class="mt-6 space-y-4" action="{{ route('login.submit') }}" method="POST">
            @csrf

            @if($errors->any())
                <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 text-xs text-rose-700 dark:text-rose-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Email address
                </label>
                <input id="email" 
                       name="email" 
                       type="email" 
                       required 
                       value="{{ old('email', 'customer@shopicart.com') }}"
                       class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Password
                    </label>
                    <a href="{{ route('password.request') }}" class="text-[11px] font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                        Forgot password?
                    </a>
                </div>
                <input id="password" 
                       name="password" 
                       type="password" 
                       required 
                       value="password"
                       class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-slate-600 dark:text-slate-400">Remember this device</span>
                </label>
            </div>

            <button type="submit" 
                    class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs tracking-wider uppercase transition-all shadow-lg shadow-indigo-600/30 active:scale-[0.98]">
                Sign In to Account
            </button>
        </form>

        <!-- Social login options -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
            <span class="text-[11px] text-slate-400 uppercase tracking-widest font-mono">Or continue with</span>
            <div class="grid grid-cols-2 gap-3 mt-3">
                <a href="{{ route('quick.login', 'customer') }}" class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <span>🌐</span> Google
                </a>
                <a href="{{ route('quick.login', 'customer') }}" class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <span>📘</span> Facebook
                </a>
            </div>

            <div class="mt-6 text-xs text-slate-500 dark:text-slate-400">
                New to ShopiCart? 
                <a href="{{ route('register') }}" class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Create an account
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
