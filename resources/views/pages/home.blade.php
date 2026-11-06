@extends('layouts.app')

@section('title', 'UNCART — Upgrade Your Lifestyle with Modern Precision Commerce')

@section('content')
<div class="space-y-12 sm:space-y-16 pb-16 overflow-hidden" x-data="{ videoModal: false, activeCatFilter: 'all' }">

    <!-- 1. Editorial Lumora-Style Showcase Hero Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-2 sm:pt-4" data-reveal>
        <div class="relative bg-[#F0F3F7] rounded-3xl sm:rounded-[44px] border border-[#DDE4EC] shadow-sm overflow-hidden p-6 sm:p-10 lg:p-14">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-8 items-center">
                
                <!-- Left Hero Content Column (5 Cols) -->
                <div class="lg:col-span-5 space-y-6 sm:space-y-8 z-10">
                    
                    <!-- Eyebrow Tagline -->
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] sm:text-xs font-bold uppercase tracking-[0.22em] text-[#6B2A88] font-sans">Good Products &bull; Brighter Days</span>
                        <span class="h-px bg-[#DDE4EC] flex-1 max-w-[80px]"></span>
                    </div>

                    <!-- Main Editorial Headline -->
                    <div class="space-y-3">
                        <h1 class="text-4xl sm:text-6xl lg:text-[62px] font-serif font-black text-[#1B0E24] leading-[1.08] tracking-tight">
                            Find What <br>
                            Makes You <br>
                            <span class="relative inline-block text-[#F59E0B] font-serif italic font-normal tracking-normal">
                                Happier
                                <!-- Hand-drawn vector brush stroke underline -->
                                <svg class="absolute -bottom-2 sm:-bottom-3 left-0 w-full h-3 sm:h-4 text-[#F59E0B]" viewBox="0 0 250 20" fill="none" preserveAspectRatio="none">
                                    <path d="M3 14C50 4 150 2 247 8C190 12 100 16 20 18" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                                </svg>
                            </span>
                        </h1>
                        <p class="text-xs sm:text-sm lg:text-base text-slate-600 max-w-md leading-relaxed font-sans font-normal pt-1">
                            Premium products. Unbeatable deals. A better everyday &mdash; all in one place.
                        </p>
                    </div>

                    <!-- CTA Action Buttons -->
                    <div class="flex items-center gap-3 sm:gap-4 flex-wrap pt-1">
                        <a href="{{ route('products.index') }}" 
                           class="py-3.5 px-8 rounded-full bg-[#F59E0B] hover:bg-[#D97706] text-[#1B0E24] font-black text-xs sm:text-sm tracking-wide shadow-lg shadow-[#F59E0B]/25 transition active:scale-95 flex items-center gap-2 group">
                            <span>Shop Now</span>
                            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </a>
                        <button @click="videoModal = true" 
                                class="py-3.5 px-6 rounded-full bg-white hover:bg-[#FAF5FC] text-[#1B0E24] font-bold text-xs sm:text-sm border border-[#DDE4EC] shadow-xs flex items-center gap-2.5 transition active:scale-95">
                            <span class="w-6 h-6 rounded-full bg-[#1B0E24] text-white flex items-center justify-center text-[10px] pl-0.5">▶</span>
                            <span>Watch Our Story</span>
                        </button>
                    </div>

                    <!-- Trust Metrics Row -->
                    <div class="pt-6 sm:pt-8 border-t border-[#DDE4EC] grid grid-cols-3 gap-3 sm:gap-4 text-left">
                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5 text-[#1B0E24] font-bold text-xs">
                                <svg class="w-4 h-4 text-[#6B2A88] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                <span>Free Shipping</span>
                            </div>
                            <p class="text-[11px] text-slate-500 font-medium">On orders over <span x-text="$store.currency ? $store.currency.format(99) : '$99'">$99</span></p>
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5 text-[#1B0E24] font-bold text-xs">
                                <svg class="w-4 h-4 text-[#6B2A88] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Secure Payment</span>
                            </div>
                            <p class="text-[11px] text-slate-500 font-medium">100% safe &amp; secure</p>
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5 text-[#1B0E24] font-bold text-xs">
                                <svg class="w-4 h-4 text-[#6B2A88] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Easy Returns</span>
                            </div>
                            <p class="text-[11px] text-slate-500 font-medium">7-day return policy</p>
                        </div>
                    </div>

                </div>

                <!-- Right Hero Multi-Card Composition Column (7 Cols) -->
                <div class="lg:col-span-7 relative flex items-center justify-center min-h-[460px] sm:min-h-[520px]">
                    
                    <!-- 1. Central Fluid Arched Lifestyle Cutout -->
                    <div class="relative w-[280px] sm:w-[360px] lg:w-[410px] h-[380px] sm:h-[460px] overflow-hidden shadow-2xl border-4 border-white/90 bg-[#E8EDF3]" 
                         style="border-radius: 46% 54% 62% 38% / 46% 44% 56% 54%;">
                        <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=1000&auto=format&fit=crop&q=80" 
                             alt="Live Stylish Everyday" 
                             class="w-full h-full object-cover object-top scale-105 hover:scale-110 transition-transform duration-700">
                        
                        <!-- Cursive Handwriting Overlay on Model -->
                        <div class="absolute top-8 left-6 select-none -rotate-6 z-10 text-[#1B0E24]">
                            <div class="font-script text-2xl sm:text-3xl leading-none font-bold text-[#1B0E24] drop-shadow-xs">Live Stylish Everyday</div>
                            <svg class="w-5 h-5 text-[#F59E0B] ml-2 -mt-0.5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l2.4 7.2L22 12l-7.6 2.8L12 22l-2.4-7.2L2 12l7.6-2.8L12 2z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- 2. Floating Sneaker Card (Overlapping bottom-left of arch) -->
                    <div class="absolute -bottom-4 sm:bottom-0 left-0 sm:left-2 z-20 bg-white/95 backdrop-blur-md rounded-3xl p-3 sm:p-4 shadow-xl border border-[#DDE4EC] max-w-[190px] sm:max-w-[215px] transition-transform hover:-translate-y-1 duration-300">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[9px] uppercase tracking-wider font-bold text-[#6B2A88]">Streetwear</span>
                            <button type="button" 
                                    @click="window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Added to your wishlist!', type: 'success' } }))"
                                    class="text-slate-400 hover:text-rose-500 transition">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </button>
                        </div>
                        <a href="{{ route('products.show', $heroSneaker->slug ?? 'apex-carbon-ultra-lightweight-sneakers') }}" class="block group">
                            <div class="h-20 sm:h-24 flex items-center justify-center p-1 bg-[#F0F3F7] rounded-2xl mb-2 overflow-hidden">
                                <img src="{{ $heroSneaker->primary_image ?? 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=400&auto=format&fit=crop&q=80' }}" 
                                     alt="Sneakers" 
                                     class="max-h-full object-contain group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <h4 class="font-bold text-xs text-[#1B0E24] truncate group-hover:text-[#F59E0B] transition-colors">{{ $heroSneaker->name ?? 'Nike Air Force 1' }}</h4>
                            <div class="flex items-center gap-1 text-[10px] text-[#F59E0B] my-0.5">
                                <span>★★★★★</span>
                                <span class="text-slate-700 font-semibold">4.7</span>
                                <span class="text-slate-400">(1.3K)</span>
                            </div>
                        </a>
                        <div class="flex items-center justify-between pt-1">
                            <div>
                                <span class="font-black text-xs sm:text-sm text-[#1B0E24]" x-text="$store.currency ? $store.currency.format({{ (float)($heroSneaker->price ?? 149) }}) : '$149.00'">$149.00</span>
                                <span class="text-[10px] text-slate-400 line-through ml-1" x-text="$store.currency ? $store.currency.format({{ (float)($heroSneaker->compare_price ?? 180) }}) : '$180.00'">$180.00</span>
                            </div>
                            <button type="button" 
                                    @click="fetch('{{ route('cart.add') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ product_id: {{ $heroSneaker->id ?? 1 }}, quantity: 1 }) }).then(r=>r.json()).then(d=>{ window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: d.cart_count } })); window.dispatchEvent(new CustomEvent('open-cart-drawer')); window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Sneakers added to bag!', type: 'success' } })); })"
                                    class="w-7 h-7 rounded-full border border-[#DDE4EC] hover:bg-[#F59E0B] hover:border-[#F59E0B] hover:text-[#1B0E24] text-slate-700 flex items-center justify-center transition active:scale-95 shadow-xs font-bold text-sm"
                                    title="Quick Add">
                                +
                            </button>
                        </div>
                    </div>

                    <!-- Sneaker Cursive Annotation -->
                    <div class="absolute bottom-6 left-[195px] sm:left-[225px] select-none hidden sm:flex items-center gap-2 z-10">
                        <div class="font-script text-xl text-slate-600 rotate-6">Iconic Style</div>
                        <svg class="w-7 h-7 text-slate-400 -rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </div>

                    <!-- 3. Floating Headphones Card (Elevated on Right) -->
                    <div class="absolute top-2 sm:top-6 -right-2 sm:right-2 z-20 bg-white/95 backdrop-blur-md rounded-3xl p-4 sm:p-5 shadow-2xl border border-[#DDE4EC] max-w-[220px] sm:max-w-[250px] transition-transform hover:-translate-y-1 duration-300">
                        <div class="flex items-center justify-between mb-2">
                            <button type="button" 
                                    @click="window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Added to your wishlist!', type: 'success' } }))"
                                    class="text-slate-400 hover:text-rose-500 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </button>
                            <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-[#FAF5FC] text-[#6B2A88]">Spotlight</span>
                        </div>
                        
                        <a href="{{ route('products.show', $heroHeadphone->slug ?? 'aether-pro-spatial-wireless-headphones') }}" class="block group">
                            <div class="h-28 sm:h-32 flex items-center justify-center p-2 bg-[#F0F3F7] rounded-2xl mb-2.5 overflow-hidden">
                                <img src="{{ $heroHeadphone->primary_image ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&auto=format&fit=crop&q=80' }}" 
                                     alt="Headphones" 
                                     class="max-h-full object-contain group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <h4 class="font-extrabold text-xs sm:text-sm text-[#1B0E24] truncate group-hover:text-[#F59E0B] transition-colors">{{ $heroHeadphone->name ?? 'Sony WH-1000XM5' }}</h4>
                            <div class="text-[10px] text-slate-500">Wireless Headphones</div>
                            <div class="flex items-center gap-1 text-[10px] text-[#F59E0B] my-1">
                                <span>★★★★★</span>
                                <span class="text-slate-700 font-semibold">4.8</span>
                                <span class="text-slate-400">(2.1K)</span>
                            </div>
                        </a>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                            <div>
                                <div class="font-black text-xs sm:text-sm text-[#1B0E24]" x-text="$store.currency ? $store.currency.format({{ (float)($heroHeadphone->price ?? 329) }}) : '$329.00'">$329.00</div>
                                <div class="text-[10px] text-slate-400 line-through" x-text="$store.currency ? $store.currency.format({{ (float)($heroHeadphone->compare_price ?? 389) }}) : '$389.00'">$389.00</div>
                            </div>
                            <button type="button" 
                                    @click="fetch('{{ route('cart.add') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ product_id: {{ $heroHeadphone->id ?? 1 }}, quantity: 1 }) }).then(r=>r.json()).then(d=>{ window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: d.cart_count } })); window.dispatchEvent(new CustomEvent('open-cart-drawer')); window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Headphones added to bag!', type: 'success' } })); })"
                                    class="px-3 py-1.5 rounded-full bg-[#1B0E24] hover:bg-[#F59E0B] hover:text-[#1B0E24] text-white font-bold text-[11px] transition flex items-center gap-1.5 active:scale-95 shadow-xs whitespace-nowrap">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>Add to Cart</span>
                            </button>
                        </div>
                    </div>

                    <!-- Headphone Cursive Annotation -->
                    <div class="absolute -top-2 right-2 sm:right-6 select-none hidden sm:flex items-center gap-1 z-10">
                        <div class="font-script text-lg sm:text-xl text-slate-700 -rotate-3">Premium Sound Everywhere</div>
                        <svg class="w-6 h-6 text-slate-400 rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </div>

                    <!-- 4. Floating Social Proof Card (Bottom Right) -->
                    <div class="absolute -bottom-6 sm:-bottom-3 right-0 sm:right-2 z-20 bg-white/95 backdrop-blur-md rounded-2xl sm:rounded-full p-2.5 sm:px-5 sm:py-3 shadow-lg border border-[#DDE4EC] flex items-center gap-3 sm:gap-4">
                        <div class="flex -space-x-2">
                            <img class="w-7 h-7 rounded-full border-2 border-white object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80" alt="Customer">
                            <img class="w-7 h-7 rounded-full border-2 border-white object-cover" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80" alt="Customer">
                            <img class="w-7 h-7 rounded-full border-2 border-white object-cover" src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&auto=format&fit=crop&q=80" alt="Customer">
                            <div class="w-7 h-7 rounded-full bg-[#F59E0B] border-2 border-white flex items-center justify-center text-[#1B0E24] text-[10px] font-black">+</div>
                        </div>
                        <div class="flex items-center gap-3 sm:gap-4 divide-x divide-slate-200 text-[#1B0E24]">
                            <div>
                                <div class="font-black text-xs sm:text-sm leading-none">50K+</div>
                                <div class="text-[9px] text-slate-500 font-medium">Happy Customers</div>
                            </div>
                            <div class="pl-3 sm:pl-4">
                                <div class="font-black text-xs sm:text-sm leading-none">1000+</div>
                                <div class="text-[9px] text-slate-500 font-medium">Premium Products</div>
                            </div>
                            <div class="pl-3 sm:pl-4">
                                <div class="font-black text-xs sm:text-sm leading-none flex items-center gap-0.5">
                                    <span>4.8</span>
                                    <span class="text-[#F59E0B]">★</span>
                                </div>
                                <div class="text-[9px] text-slate-500 font-medium">Average Rating</div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- 2. Floating Category Carousel Dock Strip (Matching Reference Image) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-reveal>
        <div class="bg-white/95 backdrop-blur-md rounded-3xl sm:rounded-full border border-[#DDE4EC] shadow-xl p-2 sm:p-2.5 flex items-center gap-2 sm:gap-3 overflow-x-auto no-scrollbar">
            
            <!-- Category 1: Electronics (Active Marigold Highlight) -->
            <a href="{{ route('products.index', ['category' => 'electronics']) }}" 
               class="flex items-center gap-3 px-3.5 py-2 rounded-2xl sm:rounded-full bg-[#FEF8E7] border border-[#FDEEC4] flex-shrink-0 transition-transform active:scale-95 group">
                <div class="w-10 h-10 rounded-xl sm:rounded-full bg-white flex items-center justify-center p-1 shadow-2xs flex-shrink-0">
                    <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=100&auto=format&fit=crop&q=80" 
                         alt="Electronics" 
                         class="max-w-full max-h-full object-contain">
                </div>
                <div>
                    <div class="font-black text-xs text-[#1B0E24]">Electronics</div>
                    <div class="text-[10px] text-slate-500 whitespace-nowrap">Laptops, Audio, Cameras & More</div>
                </div>
                <div class="w-6 h-6 rounded-full bg-white text-slate-800 flex items-center justify-center text-xs group-hover:bg-[#F59E0B] group-hover:text-[#1B0E24] transition-colors ml-1 flex-shrink-0">
                    &rarr;
                </div>
            </a>

            <!-- Category 2: Fashion -->
            <a href="{{ route('products.index', ['category' => 'fashion']) }}" 
               class="flex items-center gap-3 px-3.5 py-2 rounded-2xl sm:rounded-full bg-[#F0F3F7]/70 hover:bg-[#FEF8E7] border border-[#DDE4EC] hover:border-[#FDEEC4] flex-shrink-0 transition-all active:scale-95 group">
                <div class="w-10 h-10 rounded-xl sm:rounded-full bg-white flex items-center justify-center p-1 shadow-2xs flex-shrink-0">
                    <img src="https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=100&auto=format&fit=crop&q=80" 
                         alt="Fashion" 
                         class="max-w-full max-h-full object-contain">
                </div>
                <div>
                    <div class="font-black text-xs text-[#1B0E24]">Fashion</div>
                    <div class="text-[10px] text-slate-500 whitespace-nowrap">Men, Women, Kids</div>
                </div>
                <div class="w-6 h-6 rounded-full bg-white text-slate-800 flex items-center justify-center text-xs group-hover:bg-[#F59E0B] group-hover:text-[#1B0E24] transition-colors ml-1 flex-shrink-0">
                    &rarr;
                </div>
            </a>

            <!-- Category 3: Home & Living -->
            <a href="{{ route('products.index', ['category' => 'home']) }}" 
               class="flex items-center gap-3 px-3.5 py-2 rounded-2xl sm:rounded-full bg-[#F0F3F7]/70 hover:bg-[#FEF8E7] border border-[#DDE4EC] hover:border-[#FDEEC4] flex-shrink-0 transition-all active:scale-95 group">
                <div class="w-10 h-10 rounded-xl sm:rounded-full bg-white flex items-center justify-center p-1 shadow-2xs flex-shrink-0">
                    <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=100&auto=format&fit=crop&q=80" 
                         alt="Home & Living" 
                         class="max-w-full max-h-full object-contain">
                </div>
                <div>
                    <div class="font-black text-xs text-[#1B0E24]">Home & Living</div>
                    <div class="text-[10px] text-slate-500 whitespace-nowrap">Decor, Furniture, Kitchen</div>
                </div>
                <div class="w-6 h-6 rounded-full bg-white text-slate-800 flex items-center justify-center text-xs group-hover:bg-[#F59E0B] group-hover:text-[#1B0E24] transition-colors ml-1 flex-shrink-0">
                    &rarr;
                </div>
            </a>

            <!-- Category 4: Beauty -->
            <a href="{{ route('products.index', ['category' => 'beauty']) }}" 
               class="flex items-center gap-3 px-3.5 py-2 rounded-2xl sm:rounded-full bg-[#F0F3F7]/70 hover:bg-[#FEF8E7] border border-[#DDE4EC] hover:border-[#FDEEC4] flex-shrink-0 transition-all active:scale-95 group">
                <div class="w-10 h-10 rounded-xl sm:rounded-full bg-white flex items-center justify-center p-1 shadow-2xs flex-shrink-0">
                    <img src="https://images.unsplash.com/photo-1541643600914-78b084683601?w=100&auto=format&fit=crop&q=80" 
                         alt="Beauty" 
                         class="max-w-full max-h-full object-contain">
                </div>
                <div>
                    <div class="font-black text-xs text-[#1B0E24]">Beauty</div>
                    <div class="text-[10px] text-slate-500 whitespace-nowrap">Skincare, Makeup & Fragrances</div>
                </div>
                <div class="w-6 h-6 rounded-full bg-white text-slate-800 flex items-center justify-center text-xs group-hover:bg-[#F59E0B] group-hover:text-[#1B0E24] transition-colors ml-1 flex-shrink-0">
                    &rarr;
                </div>
            </a>

            <!-- Category 5: Sports -->
            <a href="{{ route('products.index', ['category' => 'sports']) }}" 
               class="flex items-center gap-3 px-3.5 py-2 rounded-2xl sm:rounded-full bg-[#F0F3F7]/70 hover:bg-[#FEF8E7] border border-[#DDE4EC] hover:border-[#FDEEC4] flex-shrink-0 transition-all active:scale-95 group">
                <div class="w-10 h-10 rounded-xl sm:rounded-full bg-white flex items-center justify-center p-1 shadow-2xs flex-shrink-0">
                    <img src="https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=100&auto=format&fit=crop&q=80" 
                         alt="Sports" 
                         class="max-w-full max-h-full object-contain">
                </div>
                <div>
                    <div class="font-black text-xs text-[#1B0E24]">Sports</div>
                    <div class="text-[10px] text-slate-500 whitespace-nowrap">Fitness & Outdoors</div>
                </div>
                <div class="w-6 h-6 rounded-full bg-white text-slate-800 flex items-center justify-center text-xs group-hover:bg-[#F59E0B] group-hover:text-[#1B0E24] transition-colors ml-1 flex-shrink-0">
                    &rarr;
                </div>
            </a>

            <!-- Category 6: Toys & Games -->
            <a href="{{ route('products.index', ['category' => 'books']) }}" 
               class="flex items-center gap-3 px-3.5 py-2 rounded-2xl sm:rounded-full bg-[#F0F3F7]/70 hover:bg-[#FEF8E7] border border-[#DDE4EC] hover:border-[#FDEEC4] flex-shrink-0 transition-all active:scale-95 group">
                <div class="w-10 h-10 rounded-xl sm:rounded-full bg-white flex items-center justify-center p-1 shadow-2xs flex-shrink-0">
                    <img src="https://images.unsplash.com/photo-1600080972464-8e5f35f63d08?w=100&auto=format&fit=crop&q=80" 
                         alt="Toys & Games" 
                         class="max-w-full max-h-full object-contain">
                </div>
                <div>
                    <div class="font-black text-xs text-[#1B0E24]">Toys & Games</div>
                    <div class="text-[10px] text-slate-500 whitespace-nowrap">For All Ages</div>
                </div>
                <div class="w-6 h-6 rounded-full bg-white text-slate-800 flex items-center justify-center text-xs group-hover:bg-[#F59E0B] group-hover:text-[#1B0E24] transition-colors ml-1 flex-shrink-0">
                    &rarr;
                </div>
            </a>

        </div>
    </section>

    <!-- 2.5 Brand Partners Logo Row (Matching Reference Image) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2" data-reveal>
        <div class="flex items-center justify-between gap-6 sm:gap-10 overflow-x-auto no-scrollbar py-2 text-slate-400 font-bold opacity-80">
            <span class="text-xs sm:text-sm font-black tracking-widest font-mono text-slate-600 hover:text-[#1B0E24] transition cursor-default">SAMSUNG</span>
            <span class="text-sm font-bold text-slate-600 hover:text-[#1B0E24] transition flex items-center gap-1 cursor-default">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 170 170"><path d="M150.37 130.25c-2.45 5.66-5.35 10.87-8.71 15.66-4.58 6.53-8.33 11.05-11.22 13.56-4.48 4.12-9.28 6.23-14.42 6.35-3.69 0-8.14-1.05-13.32-3.18-5.19-2.12-9.97-3.17-14.34-3.17-4.58 0-9.49 1.05-14.75 3.17-5.26 2.13-9.5 3.24-12.74 3.35-4.35.13-9.16-1.9-14.42-6.08-3.7-3.04-7.58-7.7-11.64-13.99-5.88-9.08-10.45-19.38-13.72-30.88-3.26-11.51-4.9-22.38-4.9-32.61 0-14.92 3.82-27.42 11.45-37.51 7.63-10.09 17.29-15.24 28.98-15.46 4.7 0 9.8 1.16 15.31 3.49 5.51 2.33 9.4 3.55 11.67 3.66 1.8.11 5.79-1.16 11.96-3.82 6.17-2.66 11.39-3.88 15.67-3.66 11.75.76 21.32 5.06 28.71 12.9-10.23 6.19-15.24 14.8-15.02 25.82.22 8.7 3.65 15.93 10.28 21.69 6.64 5.76 14.31 8.97 23.01 9.63-2.18 6.64-4.8 13.06-7.85 19.26zM119.22 31.84c0-7.39 2.67-14.24 8.01-20.55 5.34-6.31 11.97-10.29 19.89-11.94.33 1.2.49 2.29.49 3.27 0 7.39-2.78 14.29-8.34 20.7-5.56 6.41-12.3 10.46-20.22 12.15-.22-1.2-.33-2.4-.33-3.63z"/></svg>
                Apple
            </span>
            <span class="text-xs sm:text-sm font-black tracking-widest font-mono text-slate-600 hover:text-[#1B0E24] transition cursor-default">SONY</span>
            <span class="text-sm font-black italic tracking-tighter text-slate-600 hover:text-[#1B0E24] transition cursor-default">NIKE</span>
            <span class="text-xs sm:text-sm font-black tracking-wider text-slate-600 hover:text-[#1B0E24] transition cursor-default">adidas</span>
            <span class="text-xs sm:text-sm font-bold tracking-tight text-slate-600 hover:text-[#1B0E24] transition cursor-default">bo<span class="text-[#F59E0B]">A</span>t</span>
            <span class="text-xs sm:text-sm font-black tracking-widest font-serif text-slate-600 hover:text-[#1B0E24] transition cursor-default">Canon</span>
            <span class="text-xs sm:text-sm font-black tracking-wider font-mono text-slate-600 hover:text-[#1B0E24] transition cursor-default">LG</span>
            <span class="text-xs sm:text-sm font-black tracking-wider text-slate-600 hover:text-[#1B0E24] transition cursor-default">PHILIPS</span>
            <span class="text-xs sm:text-sm font-bold tracking-wider text-slate-600 hover:text-[#1B0E24] transition cursor-default">ZEBRONICS</span>
        </div>
    </section>

    <!-- 3. Promotional Bento Banners (UNCART Tri-Grid) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-reveal>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left Large Card: Premium Smart Watches (7 cols) -->
            <div class="lg:col-span-7 relative bg-gradient-to-r from-[#1B0E24] via-[#271435] to-[#38144A] rounded-3xl overflow-hidden p-8 sm:p-10 text-white flex flex-col justify-between min-h-[300px] shadow-lg border border-[#38144A] group">
                <!-- Marigold warm nebula light effect -->
                <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-[#F59E0B]/20 blur-3xl pointer-events-none"></div>

                <!-- Top Meta & Limited Time Badge -->
                <div class="flex items-center justify-between z-10">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/10 backdrop-blur-md text-[#F59E0B] border border-[#F59E0B]/30">
                        Special Offer
                    </span>
                    <div class="w-14 h-14 rounded-full border border-[#F59E0B]/40 bg-[#1B0E24]/60 flex items-center justify-center text-[9px] font-bold text-center leading-tight uppercase font-mono tracking-tighter text-[#F59E0B]">
                        LIMITED<br>TIME
                    </div>
                </div>

                <!-- Content & CTA -->
                <div class="space-y-4 max-w-sm z-10 py-6">
                    <h3 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight text-white">
                        Premium Smart Watches
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300">
                        Style meets technology. Up to 50% Off
                    </p>
                    <a href="{{ route('products.index', ['category' => 'electronics']) }}" 
                       class="inline-block py-2.5 px-6 rounded-full bg-[#F59E0B] hover:bg-[#D97706] text-[#1B0E24] font-black text-xs uppercase tracking-wider transition-all active:scale-95 shadow">
                        Shop Now
                    </a>
                </div>

                <!-- Product Image Overlaid on the Right -->
                <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80" 
                     alt="Smart Watch" 
                     referrerpolicy="no-referrer"
                     class="absolute right-2 sm:right-6 bottom-0 w-44 sm:w-64 object-contain group-hover:scale-105 transition-transform duration-500 pointer-events-none">
            </div>

            <!-- Right Column: 2 Stacked Cards (5 cols) -->
            <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-6">
                
                <!-- Top Right Card: New Arrivals Trendy Sneakers -->
                <div class="relative bg-white rounded-3xl border border-[#DDE4EC] overflow-hidden p-6 sm:p-7 flex flex-col justify-between min-h-[170px] shadow-sm group">
                    <div class="space-y-1.5 max-w-[200px] z-10">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#6B2A88] block">New Arrivals</span>
                        <h4 class="text-lg sm:text-xl font-black text-[#1B0E24] tracking-tight leading-snug">Trendy Sneakers</h4>
                        <a href="{{ route('products.index', ['category' => 'fashion']) }}" 
                           class="inline-block mt-2 py-1.5 px-4 rounded-full bg-[#1B0E24] hover:bg-[#F59E0B] hover:text-[#1B0E24] text-white font-bold text-[11px] transition active:scale-95">
                            Shop Now
                        </a>
                    </div>
                    <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&auto=format&fit=crop&q=80" 
                         alt="Sneakers" 
                         referrerpolicy="no-referrer"
                         class="absolute -right-2 bottom-0 w-36 sm:w-44 object-contain group-hover:scale-105 transition-transform duration-300 pointer-events-none">
                </div>

                <!-- Bottom Right Card: Top Deals Gaming Gear -->
                <div class="relative bg-gradient-to-r from-[#FAF5FC] to-[#F3E8F9] border border-[#E5CEF1] rounded-3xl overflow-hidden p-6 sm:p-7 flex flex-col justify-between min-h-[170px] shadow-sm group">
                    <div class="space-y-1.5 max-w-[200px] z-10">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#6B2A88] block">Top Deals</span>
                        <h4 class="text-lg sm:text-xl font-black text-[#1B0E24] tracking-tight leading-snug">Gaming Gear</h4>
                        <a href="{{ route('products.index', ['category' => 'electronics']) }}" 
                           class="inline-block mt-2 py-1.5 px-4 rounded-full bg-[#1B0E24] hover:bg-[#F59E0B] hover:text-[#1B0E24] text-white font-bold text-[11px] transition active:scale-95">
                            Shop Now
                        </a>
                    </div>
                    <img src="https://images.unsplash.com/photo-1600080972464-8e5f35f63d08?w=400&auto=format&fit=crop&q=80" 
                         alt="Gaming Controller" 
                         referrerpolicy="no-referrer"
                         class="absolute right-0 bottom-0 w-36 sm:w-44 object-contain group-hover:scale-105 transition-transform duration-300 pointer-events-none">
                </div>

            </div>

        </div>
    </section>

    <!-- 4. Featured Products Section (With Filter Pills Matching Screenshot) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-reveal>
        
        <!-- Header & Category Filter Pills -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <h2 class="text-2xl sm:text-3xl font-black text-[#1B0E24] tracking-tight">
                Featured Products
            </h2>

            <!-- Pills Bar -->
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                @php
                    $filterTabs = [
                        'all' => 'All',
                        'electronics' => 'Electronics',
                        'fashion' => 'Fashion',
                        'home' => 'Home',
                        'beauty' => 'Beauty',
                        'sports' => 'Sports',
                        'bags' => 'Bags & Carry'
                    ];
                @endphp
                @foreach($filterTabs as $fSlug => $fLabel)
                    <button type="button"
                            @click="activeCatFilter = '{{ $fSlug }}'"
                            :class="activeCatFilter === '{{ $fSlug }}' ? 'bg-[#1B0E24] text-white shadow-sm' : 'bg-white hover:bg-[#FAF5FC] text-slate-700 border border-[#DDE4EC]'"
                            class="py-1.5 px-4 rounded-full text-xs font-bold transition-all duration-200 active:scale-95 whitespace-nowrap">
                        {{ $fLabel }}
                    </button>
                @endforeach

                <a href="{{ route('products.index') }}" class="ml-2 text-xs font-bold text-[#6B2A88] hover:text-[#F59E0B] transition whitespace-nowrap">
                    View All &rarr;
                </a>
            </div>
        </div>

        <!-- Products Grid (Matching 5-Col on Desktop / 2-Col on Mobile) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
            @foreach($featuredProducts as $product)
                <div x-show="activeCatFilter === 'all' || '{{ strtolower($product->category->slug ?? '') }}'.includes(activeCatFilter)"
                     class="h-full">
                    <x-product-card :product="$product" />
                </div>
            @endforeach
        </div>

    </section>

    <!-- 5. Flash Sale & Limited-Time Savings Section -->
    @if(isset($flashSaleProducts) && $flashSaleProducts->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-reveal>
        <div class="bg-gradient-to-br from-[#1B0E24] via-[#271435] to-[#1B0E24] rounded-3xl p-6 sm:p-8 border border-[#38144A] shadow-lg text-white space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-[#F59E0B] text-[#1B0E24] flex items-center justify-center text-xl shadow-md shadow-[#F59E0B]/20 animate-pulse font-bold">
                        ⚡
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight">Flash Sale & Daily Deals</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#F59E0B] text-[#1B0E24]">Up to 40% OFF</span>
                        </div>
                        <p class="text-xs text-slate-300">Limited quantity deals expiring soon. Grab them before stock runs out.</p>
                    </div>
                </div>

                <!-- Live Countdown Timer -->
                <div x-data="{
                    hours: 7, minutes: 34, seconds: 48,
                    init() {
                        setInterval(() => {
                            if (this.seconds > 0) this.seconds--;
                            else { this.seconds = 59; if (this.minutes > 0) this.minutes--; else { this.minutes = 59; if (this.hours > 0) this.hours--; } }
                        }, 1000);
                    }
                }" class="flex items-center gap-2 self-start sm:self-auto">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wider mr-1">Ends in:</span>
                    <div class="flex items-center gap-1 font-mono text-xs font-black">
                        <span class="px-2.5 py-1.5 rounded-xl bg-white/10 text-white border border-white/10" x-text="String(hours).padStart(2, '0')">07</span>
                        <span class="text-slate-400 font-bold">:</span>
                        <span class="px-2.5 py-1.5 rounded-xl bg-white/10 text-white border border-white/10" x-text="String(minutes).padStart(2, '0')">34</span>
                        <span class="text-slate-400 font-bold">:</span>
                        <span class="px-2.5 py-1.5 rounded-xl bg-[#F59E0B] text-[#1B0E24]" x-text="String(seconds).padStart(2, '0')">48</span>
                    </div>
                </div>
            </div>

            <!-- Flash Sale Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
                @foreach($flashSaleProducts as $product)
                    <div class="h-full">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 6. Value / Trust Proposition Bar (Matching White Icon Bar) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-reveal>
        <div class="bg-white rounded-3xl border border-slate-100 p-6 sm:p-8 shadow-sm grid grid-cols-2 md:grid-cols-4 gap-6 items-center">
            
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl flex-shrink-0">
                    🚚
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900">Free Shipping</h4>
                    <p class="text-[11px] text-slate-500">On orders over $99</p>
                </div>
            </div>

            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl flex-shrink-0">
                    🔄
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900">Easy Returns</h4>
                    <p class="text-[11px] text-slate-500">7 days hassle free</p>
                </div>
            </div>

            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl flex-shrink-0">
                    🛡️
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900">Secure Payment</h4>
                    <p class="text-[11px] text-slate-500">100% secure payment</p>
                </div>
            </div>

            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl flex-shrink-0">
                    🎧
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900">24/7 Support</h4>
                    <p class="text-[11px] text-slate-500">Dedicated assistance</p>
                </div>
            </div>

        </div>
    </section>

    <!-- 7. New Arrivals Section -->
    @if(isset($newArrivals) && $newArrivals->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-reveal>
        <div class="flex items-center justify-between gap-4 mb-6 sm:mb-8">
            <div>
                <div class="text-[11px] font-bold text-indigo-600 uppercase tracking-widest">Fresh From Studio</div>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-0.5">New Arrivals</h3>
            </div>
            <a href="{{ route('products.index', ['sort' => 'latest']) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                <span>Explore All</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
            @foreach($newArrivals->take(5) as $product)
                <div class="h-full">
                    <x-product-card :product="$product" />
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- 8. Bottom Lifestyle Banners (Home Essentials, Beauty, Gaming) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-reveal>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Home Essentials -->
            <div class="relative bg-[#f5f1eb] rounded-3xl overflow-hidden p-6 sm:p-7 flex items-center justify-between min-h-[160px] shadow-sm group">
                <div class="space-y-1.5 z-10 max-w-[180px]">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 block">Minimal & Stylish</span>
                    <h4 class="text-lg font-black text-slate-900 leading-snug">Home Essentials</h4>
                    <p class="text-[11px] text-slate-500">Make your space better</p>
                    <a href="{{ route('products.index', ['category' => 'home']) }}" 
                       class="inline-block mt-2 py-1.5 px-4 rounded-full bg-slate-900 hover:bg-amber-700 text-white font-bold text-[11px] transition active:scale-95">
                        Shop Now
                    </a>
                </div>
                <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&auto=format&fit=crop&q=80" 
                     alt="Armchair" 
                     referrerpolicy="no-referrer"
                     class="absolute right-0 bottom-0 w-36 sm:w-44 object-contain group-hover:scale-105 transition-transform duration-300 pointer-events-none">
            </div>

            <!-- Beauty & Self Care -->
            <div class="relative bg-[#fdf0f4] rounded-3xl overflow-hidden p-6 sm:p-7 flex items-center justify-between min-h-[160px] shadow-sm group">
                <div class="space-y-1.5 z-10 max-w-[180px]">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-pink-600 block">Beauty & Self Care</span>
                    <h4 class="text-lg font-black text-slate-900 leading-snug">Look Good, Feel Better</h4>
                    <a href="{{ route('products.index', ['category' => 'beauty']) }}" 
                       class="inline-block mt-3 py-1.5 px-4 rounded-full bg-slate-900 hover:bg-pink-600 text-white font-bold text-[11px] transition active:scale-95">
                        Shop Now
                    </a>
                </div>
                <img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400&auto=format&fit=crop&q=80" 
                     alt="Skincare" 
                     referrerpolicy="no-referrer"
                     class="absolute right-0 bottom-0 w-36 sm:w-44 object-contain group-hover:scale-105 transition-transform duration-300 pointer-events-none">
            </div>

            <!-- Gaming Accessories -->
            <div class="relative bg-gradient-to-r from-slate-900 to-indigo-950 text-white rounded-3xl overflow-hidden p-6 sm:p-7 flex items-center justify-between min-h-[160px] shadow-sm group">
                <div class="space-y-1.5 z-10 max-w-[180px]">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400 block">Built for Performance</span>
                    <h4 class="text-lg font-black text-white leading-snug">Gaming Accessories</h4>
                    <a href="{{ route('products.index', ['category' => 'electronics']) }}" 
                       class="inline-block mt-3 py-1.5 px-4 rounded-full bg-white hover:bg-cyan-400 hover:text-slate-900 text-slate-900 font-bold text-[11px] transition active:scale-95">
                        Shop Now
                    </a>
                </div>
                <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?w=400&auto=format&fit=crop&q=80" 
                     alt="Gaming Gear" 
                     referrerpolicy="no-referrer"
                     class="absolute right-0 bottom-0 w-36 sm:w-44 object-contain group-hover:scale-105 transition-transform duration-300 pointer-events-none">
            </div>

        </div>
    </section>

    <!-- 9. Certified Premier Makers Showcase -->
    @if(isset($topVendors) && $topVendors->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-reveal>
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-widest">Certified Makers</span>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight mt-0.5">Shop by Premier Brand</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach($topVendors as $vendor)
                <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition flex items-center gap-5">
                    <img src="{{ $vendor->shop_logo }}" alt="{{ $vendor->shop_name }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-100 shadow-sm flex-shrink-0" referrerpolicy="no-referrer">
                    <div class="space-y-1 flex-1">
                        <div class="flex items-center gap-2">
                            <h4 class="font-extrabold text-base text-slate-900">{{ $vendor->shop_name }}</h4>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-600 border border-indigo-100">Verified</span>
                        </div>
                        <p class="text-xs text-slate-500 line-clamp-1">{{ $vendor->shop_description }}</p>
                        <div class="flex items-center gap-4 text-xs font-semibold text-slate-400 pt-1">
                            <span>📦 {{ $vendor->products_count }} Products</span>
                            <span>★ 4.9 Rating</span>
                        </div>
                    </div>
                    <a href="{{ route('vendor.storefront', $vendor->shop_slug) }}" class="py-2 px-4 rounded-full bg-slate-100 hover:bg-slate-900 hover:text-white text-xs font-bold text-slate-700 transition flex-shrink-0">
                        Visit Shop &rarr;
                    </a>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- 10. Verified Customer Reviews Wall -->
    @if(isset($featuredReviews) && $featuredReviews->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-reveal>
        <div class="text-center max-w-xl mx-auto mb-8 sm:mb-12">
            <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-widest">Real Customer Feedback</span>
            <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">Loved by 500,000+ Customers</h3>
            <p class="text-xs sm:text-sm text-slate-500 mt-2">See why design engineers, creators, and everyday buyers choose UNCART for precision products.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredReviews->take(3) as $rev)
                <div class="bg-white rounded-3xl border border-slate-100 p-6 sm:p-7 shadow-sm flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex text-amber-400 text-sm">★★★★★</div>
                            <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Verified Buyer</span>
                        </div>
                        <h4 class="font-bold text-sm text-slate-900">{{ $rev->title }}</h4>
                        <p class="text-xs text-slate-600 leading-relaxed italic line-clamp-3">“{{ $rev->comment }}”</p>
                    </div>

                    <div class="pt-3 border-t border-slate-50 flex items-center gap-3">
                        <img src="{{ $rev->user->avatar ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100' }}" class="w-9 h-9 rounded-full object-cover border border-slate-100" alt="{{ $rev->user->name ?? 'Customer' }}" referrerpolicy="no-referrer">
                        <div>
                            <div class="font-bold text-xs text-slate-900">{{ $rev->user->name ?? 'Verified Shopper' }}</div>
                            <div class="text-[10px] text-slate-400">Purchased {{ $rev->product->name ?? 'Product' }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- 11. VIP Newsletter & Instant $10 Off Club Banner -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-reveal>
        <div class="relative bg-gradient-to-r from-[#1B0E24] via-[#271435] to-[#1B0E24] rounded-3xl p-8 sm:p-12 overflow-hidden text-white shadow-xl border border-[#38144A] flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-[#F59E0B]/15 blur-3xl pointer-events-none"></div>
            
            <div class="space-y-3 max-w-lg z-10 text-center md:text-left">
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#F59E0B]/20 text-[#F59E0B] border border-[#F59E0B]/30">
                    VIP Member Exclusive
                </span>
                <h3 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight text-white">
                    Get $10 Off Your First Order
                </h3>
                <p class="text-xs sm:text-sm text-slate-300">
                    Join our weekly digest of limited studio drops, architectural tech hardware, and private flash discounts.
                </p>
            </div>

            <div class="w-full md:w-auto z-10" x-data="{ email: '', joined: false }">
                <form @submit.prevent="joined = true; $dispatch('toast', { message: 'Use coupon WELCOME10 for $10 off!', type: 'success' })" 
                      class="flex flex-col sm:flex-row items-center gap-2 bg-white/10 p-2 rounded-2xl sm:rounded-full border border-white/10 backdrop-blur-md">
                    <input type="email" 
                           x-model="email" 
                           placeholder="Enter your email address..." 
                           required 
                           class="w-full sm:w-72 bg-transparent text-white placeholder-slate-400 text-xs px-4 py-2.5 focus:outline-none border-none ring-0">
                    <button type="submit" 
                            class="w-full sm:w-auto py-2.5 px-6 rounded-full bg-[#F59E0B] hover:bg-[#D97706] text-[#1B0E24] font-black text-xs uppercase tracking-wider transition duration-200 active:scale-95 shadow">
                        <span x-text="joined ? 'Claimed! 🎉' : 'Claim $10'"></span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Interactive Video Showcase Modal Window -->
    <div x-show="videoModal" 
         x-transition 
         class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-md flex items-center justify-center p-4" 
         style="display: none;"
         @keydown.escape.window="videoModal = false">
        <div @click.away="videoModal = false" class="relative w-full max-w-3xl bg-black rounded-3xl overflow-hidden shadow-2xl border border-white/10">
            <button @click="videoModal = false" class="absolute top-4 right-4 z-20 w-8 h-8 rounded-full bg-white/20 text-white hover:bg-white/40 flex items-center justify-center">
                ✕
            </button>
            <div class="aspect-video w-full flex items-center justify-center bg-slate-950">
                <iframe class="w-full h-full" src="https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=0" title="Product Showcase" allowfullscreen></iframe>
            </div>
        </div>
    </div>

</div>
@endsection
