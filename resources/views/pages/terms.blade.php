@extends('layouts.app')

@section('title', 'Terms of Service — UNCART')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">

    <!-- Header Section -->
    <div class="border-b border-slate-200/80 pb-8 mb-10 space-y-3" data-reveal>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-[11px] font-bold uppercase tracking-wider">
            Legal Protocol
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight">
            Terms of Service
        </h1>
        <div class="flex items-center gap-4 text-xs text-slate-500 pt-1">
            <span>Effective Date: October 2026</span>
            <span>&bull;</span>
            <span>Version: 3.4.0</span>
            <span>&bull;</span>
            <span class="text-indigo-600">Multi-Vendor E-Commerce</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        
        <!-- Sticky Left TOC Navigation (4 cols) -->
        <div class="hidden lg:block lg:col-span-4 sticky top-28 space-y-4" data-reveal>
            <div class="p-5 rounded-2xl bg-white border border-slate-100 shadow-sm space-y-3 text-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Table of Articles</span>
                <nav class="space-y-1.5 font-medium">
                    <a href="#art-1" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition">
                        1. Acceptance of Terms
                    </a>
                    <a href="#art-2" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition">
                        2. Buyer & Merchant Conduct
                    </a>
                    <a href="#art-3" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition">
                        3. Platform Fees & Escrow
                    </a>
                    <a href="#art-4" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition">
                        4. Intellectual Property Rights
                    </a>
                    <a href="#art-5" class="block py-1.5 px-2.5 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition">
                        5. Dispute Resolution & Liability
                    </a>
                </nav>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-[11px] text-slate-500 space-y-1">
                <p class="font-bold text-slate-900">Academic Project Credit</p>
                <p>Designed and programmed by <strong class="text-slate-800">Chayan Jana</strong> (Roll No: 34042724001) for educational showcase.</p>
            </div>
        </div>

        <!-- Main Content (8 cols) -->
        <div class="lg:col-span-8 space-y-8" data-reveal="scale">
            <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-100 shadow-sm space-y-8 text-xs text-slate-600 leading-relaxed">
                
                <section id="art-1" class="space-y-3 scroll-mt-28">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-700 text-xs flex items-center justify-center font-bold">01</span>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Acceptance of Terms</h2>
                    </div>
                    <p>
                        By registering an account, browsing product catalogs, or placing orders on UNCART, you acknowledge that you have read, understood, and agreed to be governed by these Terms of Service.
                    </p>
                </section>

                <div class="border-t border-slate-100"></div>

                <section id="art-2" class="space-y-3 scroll-mt-28">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-700 text-xs flex items-center justify-center font-bold">02</span>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Buyer & Merchant Conduct</h2>
                    </div>
                    <p>
                        Buyers affirm that all billing, shipping, and payment parameters provided during checkout are authentic. Merchants warrant that all listed inventory complies with advertised technical specifications and respects customer return policies.
                    </p>
                </section>

                <div class="border-t border-slate-100"></div>

                <section id="art-3" class="space-y-3 scroll-mt-28">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-700 text-xs flex items-center justify-center font-bold">03</span>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Platform Fees & Escrow Protection</h2>
                    </div>
                    <p>
                        All consumer purchase funds are held securely until verified carrier logistics milestones confirm doorstep package delivery.
                    </p>
                </section>

                <div class="border-t border-slate-100"></div>

                <section id="art-4" class="space-y-3 scroll-mt-28">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-700 text-xs flex items-center justify-center font-bold">04</span>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Intellectual Property & Attribution</h2>
                    </div>
                    <p>
                        All user interface components, software algorithms, layout designs, and visual assets associated with the platform are the property of its creator, <strong>Chayan Jana</strong> (Roll No: 34042724001).
                    </p>
                </section>

            </div>
        </div>

    </div>
</div>
@endsection
