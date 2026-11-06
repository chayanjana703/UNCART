@extends('layouts.app')

@section('title', 'Create Account — UNCART')

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
                Join UNCART
            </h2>
            <p class="mt-1 text-xs text-slate-500">
                Create a customer account or open a merchant storefront
            </p>
        </div>

        <form class="space-y-4" action="{{ route('register.submit', [], false) }}" method="POST">
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

            <!-- Role Selector -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase text-[10px]">
                    Account Type
                </label>
                <div class="grid grid-cols-2 gap-3" x-data="{ role: '{{ request('role', 'customer') }}' }">
                    <label class="border rounded-2xl p-3 flex flex-col items-center cursor-pointer transition-all text-center bg-slate-50"
                           :class="role === 'customer' ? 'border-indigo-600 bg-indigo-50/50 text-indigo-900 ring-1 ring-indigo-600' : 'border-slate-200 text-slate-600'">
                        <input type="radio" name="role" value="customer" x-model="role" class="sr-only">
                        <span class="text-xl">🛍️</span>
                        <span class="text-xs font-bold mt-1">Customer</span>
                        <span class="text-[10px] text-slate-400">Browse & Shop</span>
                    </label>

                    <label class="border rounded-2xl p-3 flex flex-col items-center cursor-pointer transition-all text-center bg-slate-50"
                           :class="role === 'vendor' ? 'border-indigo-600 bg-indigo-50/50 text-indigo-900 ring-1 ring-indigo-600' : 'border-slate-200 text-slate-600'">
                        <input type="radio" name="role" value="vendor" x-model="role" class="sr-only">
                        <span class="text-xl">🏪</span>
                        <span class="text-xs font-bold mt-1">Merchant</span>
                        <span class="text-[10px] text-slate-400">Sell Products</span>
                    </label>
                </div>
            </div>

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                    Full Name
                </label>
                <input id="name" 
                       name="name" 
                       type="text" 
                       required 
                       value="{{ old('name') }}"
                       placeholder="e.g. Alex Morgan"
                       class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                    Email address
                </label>
                <input id="email" 
                       name="email" 
                       type="email" 
                       required 
                       value="{{ old('email') }}"
                       placeholder="alex@domain.com"
                       class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                        Password
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           minlength="8"
                           placeholder="Min 8 characters"
                           class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">
                        Confirm Password
                    </label>
                    <input id="password_confirmation" 
                           name="password_confirmation" 
                           type="password" 
                           required 
                           minlength="8"
                           placeholder="Repeat password"
                           class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <p class="text-[11px] text-slate-500 leading-normal">
                By registering, you agree to UNCART's 
                <a href="{{ route('static.terms') }}" class="text-indigo-600 underline">Terms</a> and 
                <a href="{{ route('static.privacy') }}" class="text-indigo-600 underline">Privacy Policy</a>.
            </p>

            <button type="submit" class="w-full py-3.5 px-4 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md active:scale-[0.98]">
                Create Free Account &rarr;
            </button>
        </form>

        <div class="text-center pt-2">
            <span class="text-xs text-slate-500">Already registered?</span>
            <a href="{{ route('login') }}" class="text-xs font-bold text-indigo-600 hover:underline ml-1">
                Sign In Instead
            </a>
        </div>

    </div>
</div>
@endsection
