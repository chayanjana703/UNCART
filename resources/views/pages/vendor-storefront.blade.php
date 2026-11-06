@extends('layouts.app')

@section('title', $vendor->shop_name . ' — Verified Merchant Storefront')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8" x-data="{ inquiryModal: false, activeTab: 'products' }">
    
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500" data-reveal>
        <a href="{{ route('home') }}" class="hover:text-indigo-600 transition">Home</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-indigo-600 transition">Brands</a>
        <span>/</span>
        <span class="text-slate-900 font-semibold">{{ $vendor->shop_name }}</span>
    </nav>

    <!-- Merchant Header Card (Branded Banner + Floating Profile) -->
    <div class="relative bg-white rounded-3xl sm:rounded-[36px] border border-slate-100 overflow-hidden shadow-sm" data-reveal>
        <!-- Store Banner -->
        <div class="h-44 sm:h-64 w-full relative overflow-hidden bg-slate-900">
            <img src="{{ $vendor->shop_banner }}" 
                 alt="{{ $vendor->shop_name }} Banner" 
                 referrerpolicy="no-referrer"
                 class="w-full h-full object-cover opacity-85">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
        </div>

        <!-- Store Meta & Profile Information -->
        <div class="p-6 sm:p-8 pt-0 relative flex flex-col sm:flex-row items-start sm:items-end justify-between gap-6 -mt-12 sm:-mt-16">
            
            <!-- Left: Avatar & Identity -->
            <div class="flex items-end gap-4 sm:gap-6 z-10">
                <img src="{{ $vendor->shop_logo }}" 
                     alt="{{ $vendor->shop_name }}" 
                     referrerpolicy="no-referrer"
                     class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl object-cover bg-white p-1 shadow-xl ring-4 ring-white flex-shrink-0">

                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $vendor->shop_name }}</h1>
                        @if($vendor->kyc_status === 'verified')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-200">
                                Verified Maker
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 max-w-xl leading-relaxed">{{ $vendor->shop_description }}</p>
                    <div class="flex items-center gap-4 text-xs font-semibold text-slate-500 pt-1">
                        <span>📦 {{ $products->total() }} Products</span>
                        <span>★ 4.9 Rating</span>
                        <span>🚚 Ships from {{ explode(',', $vendor->address)[0] ?? 'Studio' }}</span>
                    </div>
                </div>
            </div>

            <!-- Right: Action Buttons -->
            <div class="flex items-center gap-3 w-full sm:w-auto z-10">
                <button type="button" 
                        @click="inquiryModal = true" 
                        class="flex-1 sm:flex-initial py-2.5 px-6 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider transition active:scale-95 shadow">
                    Ask Question
                </button>
            </div>

        </div>

        <!-- Navigation Tabs Bar -->
        <div class="px-6 sm:px-8 border-t border-slate-100 flex items-center gap-6 text-xs font-bold text-slate-500">
            <button type="button" 
                    @click="activeTab = 'products'" 
                    class="py-3.5 border-b-2 transition" 
                    :class="activeTab === 'products' ? 'border-indigo-600 text-indigo-600' : 'border-transparent hover:text-slate-900'">
                All Products ({{ $products->total() }})
            </button>
            <button type="button" 
                    @click="activeTab = 'policies'" 
                    class="py-3.5 border-b-2 transition" 
                    :class="activeTab === 'policies' ? 'border-indigo-600 text-indigo-600' : 'border-transparent hover:text-slate-900'">
                Store Policies
            </button>
        </div>
    </div>

    <!-- Tab 1: Products Catalog Grid -->
    <div x-show="activeTab === 'products'" class="space-y-6">
        @if($products->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-100 p-16 text-center space-y-4 shadow-sm" data-reveal>
                <div class="w-16 h-16 rounded-full bg-slate-100 mx-auto flex items-center justify-center text-2xl text-slate-400">
                    📦
                </div>
                <h3 class="font-bold text-base text-slate-900">No active products</h3>
                <p class="text-xs text-slate-500">This maker hasn't listed any items in their public showcase yet.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($products as $prod)
                    <div class="h-full">
                        <x-product-card :product="$prod" />
                    </div>
                @endforeach
            </div>

            <div class="pt-6">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- Tab 2: Store Policies -->
    <div x-show="activeTab === 'policies'" class="grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
        <div class="bg-white rounded-3xl border border-slate-100 p-6 sm:p-8 space-y-3 shadow-sm">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                🚚
            </div>
            <h4 class="font-extrabold text-base text-slate-900">Shipping & Dispatch Policy</h4>
            <p class="text-xs text-slate-600 leading-relaxed">
                {{ $vendor->shipping_policy ?? 'All orders are carefully inspected and dispatched via verified express carriers within 24-48 business hours with end-to-end tracking.' }}
            </p>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 sm:p-8 space-y-3 shadow-sm">
            <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                🔄
            </div>
            <h4 class="font-extrabold text-base text-slate-900">Returns & Warranty Policy</h4>
            <p class="text-xs text-slate-600 leading-relaxed">
                {{ $vendor->return_policy ?? '30 days unconditional money-back guarantee. If your merchandise arrives damaged or does not meet performance criteria, reach out for an instant return label.' }}
            </p>
        </div>
    </div>

    <!-- Inquiry Modal -->
    <div x-show="inquiryModal" 
         x-transition.opacity 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;"
         @keydown.escape.window="inquiryModal = false">
        <div @click.away="inquiryModal = false" class="bg-white border border-slate-100 rounded-3xl w-full max-w-lg p-6 sm:p-8 shadow-2xl relative">
            <h3 class="text-lg font-bold text-slate-900 mb-1">Message {{ $vendor->shop_name }}</h3>
            <p class="text-xs text-slate-500 mb-5">Have a question regarding custom orders, wholesale or product specs?</p>

            <form action="{{ route('inquiries.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Subject</label>
                    <input type="text" name="subject" required placeholder="e.g. Shipping inquiry or product availability" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Message</label>
                    <textarea name="message" rows="4" required placeholder="Type your message to the merchant here..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="inquiryModal = false" class="px-4 py-2 rounded-full border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-full bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold shadow-sm">Send Message</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
