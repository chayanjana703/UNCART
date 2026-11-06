@extends('layouts.app')

@section('title', 'Privacy Policy — UNCART')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">

    <!-- Header Section -->
    <div class="border-b border-slate-200/80 pb-8 mb-10 space-y-3" data-reveal>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-700 text-[11px] font-bold uppercase tracking-wider">
            GDPR & CCPA Compliant
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight">
            Privacy Policy & Data Sovereignty
        </h1>
        <div class="flex items-center gap-4 text-xs text-slate-500 pt-1">
            <span>Last Updated: October 2026</span>
            <span>&bull;</span>
            <span>Encryption Standard: TLS 1.3 / AES-256</span>
            <span>&bull;</span>
            <span class="text-emerald-700 font-semibold">Zero Ad-Broker Selling Policy</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        
        <!-- Sticky Left TOC Navigation (4 cols) -->
        <div class="hidden lg:block lg:col-span-4 sticky top-28 space-y-4" data-reveal>
            <div class="p-5 rounded-2xl bg-white border border-slate-100 shadow-sm space-y-3 text-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Table of Articles</span>
                <nav class="space-y-1.5 font-medium">
                    <a href="#priv-1" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition">
                        1. Data Minimization
                    </a>
                    <a href="#priv-2" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition">
                        2. Cryptographic Security
                    </a>
                    <a href="#priv-3" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition">
                        3. Cookies & Session Storage
                    </a>
                    <a href="#priv-4" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition">
                        4. User Rights & Data Deletion
                    </a>
                </nav>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-[11px] text-slate-500 space-y-1">
                <p class="font-bold text-slate-900">Academic Project Credit</p>
                <p>Created by <strong class="text-slate-800">Chayan Jana</strong> (Roll No: 34042724001) as part of an advanced e-commerce engineering study.</p>
            </div>
        </div>

        <!-- Main Content (8 cols) -->
        <div class="lg:col-span-8 space-y-8" data-reveal="scale">
            <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-100 shadow-sm space-y-8 text-xs text-slate-600 leading-relaxed">
                
                <section id="priv-1" class="space-y-3 scroll-mt-28">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-700 text-xs flex items-center justify-center font-bold">01</span>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Data Minimization Principles</h2>
                    </div>
                    <p>
                        We operate under strict data minimization guidelines. We only record information strictly necessary to process order consignments, facilitate carrier deliveries, and execute customer authentication.
                    </p>
                </section>

                <div class="border-t border-slate-100"></div>

                <section id="priv-2" class="space-y-3 scroll-mt-28">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-700 text-xs flex items-center justify-center font-bold">02</span>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Cryptographic Security Standards</h2>
                    </div>
                    <p>
                        All platform exchanges occur over TLS 1.3 encrypted pipelines. Payment card numbers are processed directly through PCI-DSS Level 1 certified gateways and never stored in plain text.
                    </p>
                </section>

                <div class="border-t border-slate-100"></div>

                <section id="priv-3" class="space-y-3 scroll-mt-28">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-700 text-xs flex items-center justify-center font-bold">03</span>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Cookies & Session Storage</h2>
                    </div>
                    <p>
                        We utilize first-party functional session cookies exclusively to preserve shopping cart allocations, role switching states, and authentication persistence.
                    </p>
                </section>

                <div class="border-t border-slate-100"></div>

                <section id="priv-4" class="space-y-3 scroll-mt-28">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-700 text-xs flex items-center justify-center font-bold">04</span>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">User Rights & Data Deletion</h2>
                    </div>
                    <p>
                        You maintain full sovereignty over your account data. You can update saved delivery addresses, toggle 2FA, or request complete account eradication from your profile settings.
                    </p>
                </section>

            </div>
        </div>

    </div>
</div>
@endsection
