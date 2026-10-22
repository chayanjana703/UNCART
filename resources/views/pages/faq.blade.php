@extends('layouts.app')

@section('title', 'Frequently Asked Questions - ShopiCart')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-10" x-data="{ active: 1 }">
    <div class="text-center space-y-2">
        <span class="text-xs font-bold tracking-widest text-indigo-600 dark:text-indigo-400 uppercase font-mono">Answers & Support</span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">Frequently Asked Questions</h1>
        <p class="text-xs text-slate-500 max-w-md mx-auto">Everything you need to know about deliveries, returns, authenticity, and vendor registration</p>
    </div>

    <div class="space-y-4">
        @php
            $faqs = [
                1 => ['q' => 'How does ShopiCart guarantee product authenticity?', 'a' => 'All sellers undergo stringent identity and bank verification (KYC). Products must meet strict quality standards, serial number verification, and warranty commitments before being listed.'],
                2 => ['q' => 'What is the standard delivery timeline?', 'a' => 'Orders placed before 2:00 PM are processed the same day. Standard delivery takes 3 to 5 business days, while Express Air Courier delivers within 24 to 48 hours.'],
                3 => ['q' => 'How do I return an item or request a refund?', 'a' => 'You can request a return directly from your Order History within 30 days of delivery. Once the merchant receives the returned package, funds are automatically returned to your original payment method within 3 business days.'],
                4 => ['q' => 'How can I become a verified seller on the platform?', 'a' => 'Select "Seller / Vendor" during registration or apply via the Merchant Ecosystem footer link. Fill in your shop details, policies, and submit KYC identification documents for review.'],
                5 => ['q' => 'What payment gateways are supported?', 'a' => 'We accept Credit/Debit Cards (Visa, MasterCard, Amex via 256-bit encrypted gateway), Instant UPI, Digital Wallets, and Cash on Delivery for eligible domestic addresses.']
            ];
        @endphp

        @foreach($faqs as $id => $f)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-sm">
                <button @click="active = (active === {{ $id }} ? null : {{ $id }})" 
                        class="w-full p-5 text-left flex items-center justify-between text-xs font-bold text-slate-900 dark:text-white hover:text-indigo-600 transition-colors">
                    <span>{{ $f['q'] }}</span>
                    <span class="text-lg font-light text-slate-400" x-text="active === {{ $id }} ? '−' : '+'"></span>
                </button>
                <div x-show="active === {{ $id }}" x-transition class="p-5 pt-0 text-xs text-slate-600 dark:text-slate-300 leading-relaxed border-t border-slate-100 dark:border-slate-800">
                    {{ $f['a'] }}
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
