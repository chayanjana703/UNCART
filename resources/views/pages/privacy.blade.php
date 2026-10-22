@extends('layouts.app')

@section('title', 'Privacy & Data Protection - ShopiCart')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-8 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
    <div class="border-b border-slate-200 dark:border-slate-800 pb-6">
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Privacy Policy</h1>
        <p class="text-xs text-slate-400 mt-1">Last Updated: October 2026 &bull; Strict GDPR & CCPA Compliance</p>
    </div>

    <div class="space-y-6 bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <h3 class="font-bold text-sm text-slate-900 dark:text-white mb-2">1. Data Minimization & Privacy First</h3>
            <p>We only collect information strictly required to deliver your orders, maintain secure sessions, and fulfill legal compliance. We do not sell your personal telemetry or purchase histories to third-party ad brokers.</p>
        </div>

        <div>
            <h3 class="font-bold text-sm text-slate-900 dark:text-white mb-2">2. Encryption Standards</h3>
            <p>All sensitive authentication credentials, two-factor tokens, and payment requests are transmitted via TLS 1.3 / 256-bit encryption. Payment card numbers are processed directly through PCI-DSS Level 1 certified gateways and never stored in plain text.</p>
        </div>

        <div>
            <h3 class="font-bold text-sm text-slate-900 dark:text-white mb-2">3. User Rights & Data Deletion</h3>
            <p>You maintain full sovereignty over your account data. You can inspect your activity logs, update preferences, toggle 2FA, or request complete account eradication from your profile settings.</p>
        </div>
    </div>
</div>
@endsection
