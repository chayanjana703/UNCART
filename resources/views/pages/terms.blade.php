@extends('layouts.app')

@section('title', 'Terms of Service - ShopiCart')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-8 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
    <div class="border-b border-slate-200 dark:border-slate-800 pb-6">
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Terms of Service</h1>
        <p class="text-xs text-slate-400 mt-1">Effective Date: October 2026 &bull; Governing ShopiCart Platform Usage</p>
    </div>

    <div class="space-y-6 bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <h3 class="font-bold text-sm text-slate-900 dark:text-white mb-2">1. Acceptance of Terms</h3>
            <p>By registering, browsing, or placing orders on ShopiCart ("Platform"), you acknowledge and agree to be bound by these Terms of Service. If you do not consent to these terms, please discontinue use of the platform.</p>
        </div>

        <div>
            <h3 class="font-bold text-sm text-slate-900 dark:text-white mb-2">2. Buyer & Merchant Conduct</h3>
            <p>Buyers agree to furnish accurate billing, shipping, and payment information. Merchants agree to uphold advertised product specifications, respect 30-day return windows, and process shipments within certified fulfillment timeframes.</p>
        </div>

        <div>
            <h3 class="font-bold text-sm text-slate-900 dark:text-white mb-2">3. Platform Fees & Escrow Protection</h3>
            <p>ShopiCart retains a platform transaction commission from vendor settlements. Funds remain protected in escrow until confirmed courier delivery checkpoints are validated by the logistics gateway.</p>
        </div>

        <div>
            <h3 class="font-bold text-sm text-slate-900 dark:text-white mb-2">4. Intellectual Property & Brand Rights</h3>
            <p>All brand names, design elements, interface assets, and software algorithms are the intellectual property of ShopiCart and its creator, Chayan Jana.</p>
        </div>
    </div>
</div>
@endsection
