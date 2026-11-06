@extends('layouts.app')

@section('title', 'Help Center & FAQ — UNCART')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 space-y-10" 
     x-data="{ 
         active: 1, 
         searchQuery: '', 
         activeCategory: 'all',
         faqs: [
             {
                 id: 1,
                 category: 'security',
                 catLabel: 'Authenticity & Guarantees',
                 q: 'How does UNCART guarantee product authenticity?',
                 a: 'All sellers undergo strict identity verification and business checks. Products must meet strict quality standards, serial number verification, and warranty commitments before being listed.'
             },
             {
                 id: 2,
                 category: 'logistics',
                 catLabel: 'Shipping & Delivery',
                 q: 'What is the standard delivery timeline and carrier coverage?',
                 a: 'Orders placed before 2:00 PM are processed same-day. Standard shipping takes 3 to 5 business days, while Express Air Courier delivers within 24 to 48 hours across North America.'
             },
             {
                 id: 3,
                 category: 'returns',
                 catLabel: 'Returns & Refunds',
                 q: 'How do I return an item or request a refund?',
                 a: 'You can request a return directly from your Order History within 7 to 30 days of delivery. Once the merchant receives the returned package, funds are automatically refunded to your original payment method.'
             },
             {
                 id: 4,
                 category: 'merchant',
                 catLabel: 'Seller Onboarding',
                 q: 'How can I become a verified merchant on the platform?',
                 a: 'Select Seller / Vendor during registration. Fill in your shop profile, shipping policies, and submit official business identification documents for review.'
             },
             {
                 id: 5,
                 category: 'payments',
                 catLabel: 'Payment Methods',
                 q: 'What payment options and rails are accepted?',
                 a: 'We accept Credit/Debit Cards (Visa, MasterCard, American Express), Instant UPI, Digital Wallets, and Cash on Delivery for eligible domestic postal codes.'
             }
         ],
         get filteredFaqs() {
             return this.faqs.filter(item => {
                 const matchesCat = this.activeCategory === 'all' || item.category === this.activeCategory;
                 const matchesSearch = !this.searchQuery || 
                     item.q.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                     item.a.toLowerCase().includes(this.searchQuery.toLowerCase());
                 return matchesCat && matchesSearch;
             });
         }
     }">

    <!-- Header Section -->
    <div class="text-center space-y-4 max-w-2xl mx-auto" data-reveal>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-[11px] font-bold uppercase tracking-wider">
            Help Center & Knowledge Base
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight">
            Frequently Asked Questions
        </h1>
        <p class="text-sm text-slate-600 leading-relaxed">
            Everything you need to know about deliveries, returns, authenticity, and seller onboarding.
        </p>

        <!-- Live Instant Search Bar -->
        <div class="pt-4 max-w-lg mx-auto">
            <div class="relative">
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Search questions (e.g. shipping, returns, seller, payment)..."
                       class="w-full pl-11 pr-4 py-3 rounded-full bg-white border border-slate-200 text-slate-900 placeholder-slate-400 text-xs focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 shadow-sm transition-all">
                <span class="absolute left-4 top-3.5 text-slate-400 text-sm">🔍</span>
                <button x-show="searchQuery" 
                        @click="searchQuery = ''" 
                        class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-800 text-xs font-bold">
                    ESC
                </button>
            </div>
        </div>
    </div>

    <!-- Category Filter Tabs -->
    <div class="flex items-center justify-center gap-2 flex-wrap" data-reveal>
        @php
            $cats = [
                'all' => 'All Topics',
                'security' => 'Authenticity',
                'logistics' => 'Shipping',
                'returns' => 'Returns',
                'payments' => 'Payments',
                'merchant' => 'Seller Info'
            ];
        @endphp
        @foreach($cats as $slug => $label)
            <button type="button"
                    @click="activeCategory = '{{ $slug }}'"
                    :class="activeCategory === '{{ $slug }}' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                    class="py-1.5 px-4 rounded-full text-[11px] font-semibold transition active:scale-95">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <!-- Accordion Items List -->
    <div class="space-y-3" data-reveal="fade-up">
        <template x-for="f in filteredFaqs" :key="f.id">
            <div class="rounded-2xl bg-white border border-slate-100 overflow-hidden shadow-sm transition-all hover:border-indigo-100">
                <button @click="active = (active === f.id ? null : f.id)"
                        class="w-full p-5 text-left flex items-start justify-between gap-4 transition-colors group">
                    <div class="space-y-1">
                        <span class="text-[10px] uppercase tracking-wider text-indigo-600 font-bold block" x-text="f.catLabel"></span>
                        <h3 class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors" x-text="f.q"></h3>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-sm font-bold text-slate-600 flex-shrink-0 group-hover:bg-slate-900 group-hover:text-white transition-all">
                        <span x-text="active === f.id ? '−' : '+'"></span>
                    </div>
                </button>

                <div x-show="active === f.id" 
                     x-transition 
                     class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-4">
                    <p x-text="f.a"></p>
                </div>
            </div>
        </template>
    </div>

    <!-- Bottom Contact CTA Banner -->
    <div class="p-8 rounded-3xl bg-slate-900 text-white shadow-md flex flex-col sm:flex-row items-center justify-between gap-6" data-reveal>
        <div class="space-y-1 text-center sm:text-left">
            <h3 class="text-lg font-bold text-white">Still Have Unanswered Inquiries?</h3>
            <p class="text-xs text-slate-300">Our customer support team is available 24/7 to assist you.</p>
        </div>
        <a href="{{ route('static.contact') }}" 
           class="py-3 px-6 rounded-full bg-white hover:bg-slate-100 text-slate-900 font-bold text-xs uppercase tracking-wider shadow-sm transition active:scale-95 whitespace-nowrap">
            Contact Support &rarr;
        </a>
    </div>

</div>
@endsection
