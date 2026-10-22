@extends('layouts.app')

@section('title', 'Create Account - ShopiCart')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl">
        
        <div class="text-center">
            <div class="mx-auto w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-2xl shadow-lg shadow-indigo-600/30">
                S
            </div>
            <h2 class="mt-4 text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Join ShopiCart
            </h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                Create a buyer profile or launch your verified seller shop
            </p>
        </div>

        <form class="mt-6 space-y-4" action="{{ route('register.submit') }}" method="POST">
            @csrf

            @if($errors->any())
                <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 text-xs text-rose-700 dark:text-rose-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Role Selector -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                    Account Purpose
                </label>
                <div class="grid grid-cols-2 gap-3" x-data="{ role: '{{ request('role', 'customer') }}' }">
                    <label class="border rounded-2xl p-3 flex flex-col items-center cursor-pointer transition-all text-center"
                           :class="role === 'customer' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-200 shadow-sm' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400'">
                        <input type="radio" name="role" value="customer" x-model="role" class="sr-only">
                        <span class="text-lg">🛍️</span>
                        <span class="text-xs font-bold mt-1">Customer</span>
                        <span class="text-[10px] text-slate-400">Browse & Buy</span>
                    </label>

                    <label class="border rounded-2xl p-3 flex flex-col items-center cursor-pointer transition-all text-center"
                           :class="role === 'vendor' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-200 shadow-sm' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400'">
                        <input type="radio" name="role" value="vendor" x-model="role" class="sr-only">
                        <span class="text-lg">🏪</span>
                        <span class="text-xs font-bold mt-1">Seller / Vendor</span>
                        <span class="text-[10px] text-slate-400">Open a Shop</span>
                    </label>
                </div>
            </div>

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Full Name
                </label>
                <input id="name" 
                       name="name" 
                       type="text" 
                       required 
                       value="{{ old('name') }}"
                       placeholder="e.g. Alex Morgan"
                       class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Email address
                </label>
                <input id="email" 
                       name="email" 
                       type="email" 
                       required 
                       value="{{ old('email') }}"
                       placeholder="alex@domain.com"
                       class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Phone Number (Optional)
                </label>
                <input id="phone" 
                       name="phone" 
                       type="tel" 
                       value="{{ old('phone') }}"
                       placeholder="+1 (555) 000-0000"
                       class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Password
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Confirm Password
                    </label>
                    <input id="password_confirmation" 
                           name="password_confirmation" 
                           type="password" 
                           required 
                           class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <button type="submit" 
                    class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs tracking-wider uppercase transition-all shadow-lg shadow-indigo-600/30 active:scale-[0.98]">
                Create My Account
            </button>
        </form>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                Sign in
            </a>
        </div>

    </div>
</div>
@endsection
