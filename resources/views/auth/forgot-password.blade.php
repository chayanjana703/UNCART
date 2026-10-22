@extends('layouts.app')

@section('title', 'Reset Password - ShopiCart')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6 bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl">
        <div class="text-center">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Forgot Password?</h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                Enter your registered email address and we'll dispatch password recovery instructions.
            </p>
        </div>

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email address</label>
                <input id="email" name="email" type="email" required placeholder="name@domain.com"
                       class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs tracking-wider uppercase transition-all shadow-lg shadow-indigo-600/30 active:scale-[0.98]">
                Send Recovery Link
            </button>
        </form>

        <div class="text-center pt-2">
            <a href="{{ route('login') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                &larr; Return to Sign In
            </a>
        </div>
    </div>
</div>
@endsection
