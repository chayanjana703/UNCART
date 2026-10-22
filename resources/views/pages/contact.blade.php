@extends('layouts.app')

@section('title', 'Contact Platform Support - ShopiCart')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
        
        <div class="space-y-6">
            <span class="text-xs font-bold tracking-widest text-indigo-600 dark:text-indigo-400 uppercase font-mono">We are here to assist</span>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                Get in Touch with ShopiCart
            </h1>
            <p class="text-xs text-slate-500 leading-relaxed">
                Have inquiries regarding an order, corporate partnerships, or seller onboarding? Our dedicated support staff typically responds within 2 business hours.
            </p>

            <div class="space-y-4 pt-4 text-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 flex items-center justify-center font-bold">📍</div>
                    <div>
                        <strong class="block text-slate-900 dark:text-white">Headquarters</strong>
                        <span class="text-slate-500">100 Silicon Way, Suite 500, San Francisco, CA 94107</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center font-bold">✉️</div>
                    <div>
                        <strong class="block text-slate-900 dark:text-white">Direct Email</strong>
                        <span class="text-slate-500">support@shopicart.com &bull; contact@shopicart.com</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 flex items-center justify-center font-bold">📞</div>
                    <div>
                        <strong class="block text-slate-900 dark:text-white">Toll-Free Phone</strong>
                        <span class="text-slate-500">+1 (800) 892-0199 (Mon-Fri 8 AM - 6 PM PST)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xl space-y-4">
            <h3 class="font-bold text-sm text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-100 dark:border-slate-800">
                Send a Direct Message
            </h3>

            <form action="{{ route('static.contact.submit') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold mb-1">Your Full Name</label>
                    <input type="text" name="name" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold mb-1">Subject</label>
                    <input type="text" name="subject" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold mb-1">Message</label>
                    <textarea name="message" rows="4" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white"></textarea>
                </div>
                <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all active:scale-95">
                    Dispatch Message
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
