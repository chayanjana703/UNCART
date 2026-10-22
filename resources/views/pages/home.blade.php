@extends('layouts.app')

@section('title', 'ShopiCart - The Modern High-Craft Marketplace')

@section('content')
<div class="space-y-20 pb-20">

    <!-- 1. Hero Showcase Carousel -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <div x-data="{
            current: 0,
            banners: {{ json_encode($banners) }},
            next() { this.current = (this.current + 1) % this.banners.length },
            prev() { this.current = (this.current - 1 + this.banners.length) % this.banners.length },
            init() { setInterval(() => this.next(), 6500) }
        }" class="relative h-[480px] sm:h-[540px] rounded-3xl overflow-hidden shadow-2xl bg-slate-950">
            
            <template x-for="(b, idx) in banners" :key="idx">
                <div x-show="current === idx"
                     x-transition:enter="transition-all ease-[cubic-bezier(0.16,1,0.3,1)] duration-700"
                     x-transition:enter-start="opacity-0 scale-105"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition-all ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0">
                    <img :src="b.image" :alt="b.title" class="w-full h-full object-cover object-center filter brightness-[0.70]">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent flex items-end p-8 sm:p-16">
                        <div class="max-w-xl space-y-4">
                            <span x-show="b.badge" x-text="b.badge" class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-emerald-500/90 text-slate-950 backdrop-blur-md inline-block"></span>
                            <h2 x-text="b.title" class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight"></h2>
                            <p x-text="b.subtitle" class="text-slate-300 text-sm sm:text-base font-normal leading-relaxed"></p>
                            <div class="pt-2">
                                <a :href="b.link || '{{ route('products.index') }}'" 
                                   class="inline-flex items-center gap-2 py-3.5 px-6 rounded-2xl bg-white text-slate-950 font-bold text-xs uppercase tracking-wider hover:bg-slate-100 active:scale-95 transition-all shadow-xl">
                                    <span x-text="b.button_text || 'Shop Now'"></span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Navigation Controls -->
            <div class="absolute bottom-6 right-8 flex items-center gap-2 z-20">
                <button @click="prev()" class="w-10 h-10 rounded-full bg-slate-900/80 backdrop-blur-md text-white flex items-center justify-center hover:bg-slate-800 transition-colors active:scale-90 border border-slate-700/60">
                    &larr;
                </button>
                <button @click="next()" class="w-10 h-10 rounded-full bg-slate-900/80 backdrop-blur-md text-white flex items-center justify-center hover:bg-slate-800 transition-colors active:scale-90 border border-slate-700/60">
                    &rarr;
                </button>
            </div>
        </div>
    </section>

    <!-- 2. Value Proposition Strip -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="flex items-center gap-3.5 p-2">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
                    ⚡
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">Fast Courier Delivery</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Free on orders above $99</p>
                </div>
            </div>
            <div class="flex items-center gap-3.5 p-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg">
                    🛡️
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">Certified KYC Sellers</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">100% authentic merchandise</p>
                </div>
            </div>
            <div class="flex items-center gap-3.5 p-2">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg">
                    🔄
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">30-Day Hassle Returns</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">No questions asked refund</p>
                </div>
            </div>
            <div class="flex items-center gap-3.5 p-2">
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg">
                    🔒
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">256-Bit Escrow Pay</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Card, UPI, Wallet & COD</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Curated Categories -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8">
            <div>
                <span class="text-xs font-bold tracking-widest text-indigo-600 dark:text-indigo-400 uppercase font-mono">Portfolios</span>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Explore by Category</h3>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1 transition-colors">
                View All Catalog &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
                   class="group relative h-48 rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-300">
                    <img src="{{ $category->image }}" alt="{{ $category->name }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent flex flex-col justify-end p-5">
                        <h4 class="font-bold text-white text-sm group-hover:text-indigo-400 transition-colors">
                            {{ $category->name }}
                        </h4>
                        <p class="text-[11px] text-slate-300 mt-0.5">
                            {{ $category->products_count }} curated products
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- 4. Flash Deals with Animated Countdown -->
    @if($flashSaleProducts->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{
                hours: 14,
                minutes: 42,
                seconds: 59,
                init() {
                    setInterval(() => {
                        if (this.seconds > 0) this.seconds--;
                        else {
                            this.seconds = 59;
                            if (this.minutes > 0) this.minutes--;
                            else { this.minutes = 59; if (this.hours > 0) this.hours--; }
                        }
                    }, 1000);
                }
            }" class="p-8 sm:p-10 rounded-3xl bg-gradient-to-br from-indigo-950 via-slate-900 to-slate-950 border border-indigo-900/50 shadow-2xl text-white">
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 border-b border-slate-800/80 pb-6">
                    <div>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-500 text-white shadow-lg shadow-rose-500/20">
                            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                            Flash Sale Deals
                        </span>
                        <h3 class="text-2xl font-black tracking-tight mt-2 text-white">Save Up to 50% Off Limited Batches</h3>
                    </div>

                    <!-- Live Clock -->
                    <div class="flex items-center gap-2">
                        <div class="text-center bg-slate-900/80 border border-slate-800 px-3 py-2 rounded-xl min-w-[55px]">
                            <span class="font-mono text-xl font-bold text-white" x-text="String(hours).padStart(2, '0')"></span>
                            <div class="text-[9px] uppercase tracking-wider text-slate-400">Hours</div>
                        </div>
                        <span class="font-bold text-slate-500">:</span>
                        <div class="text-center bg-slate-900/80 border border-slate-800 px-3 py-2 rounded-xl min-w-[55px]">
                            <span class="font-mono text-xl font-bold text-white" x-text="String(minutes).padStart(2, '0')"></span>
                            <div class="text-[9px] uppercase tracking-wider text-slate-400">Mins</div>
                        </div>
                        <span class="font-bold text-slate-500">:</span>
                        <div class="text-center bg-slate-900/80 border border-slate-800 px-3 py-2 rounded-xl min-w-[55px]">
                            <span class="font-mono text-xl font-bold text-emerald-400" x-text="String(seconds).padStart(2, '0')"></span>
                            <div class="text-[9px] uppercase tracking-wider text-slate-400">Secs</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($flashSaleProducts as $prod)
                        <x-product-card :product="$prod" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- 5. Featured Products Catalog -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8">
            <div>
                <span class="text-xs font-bold tracking-widest text-indigo-600 dark:text-indigo-400 uppercase font-mono">Curated Selection</span>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Featured Essentials</h3>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1 transition-colors">
                Browse All ({{ \App\Models\Product::count() }}) &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>

    <!-- 6. Top Certified Sellers -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8">
            <div>
                <span class="text-xs font-bold tracking-widest text-emerald-600 dark:text-emerald-400 uppercase font-mono">Makers & Artisans</span>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">Featured Merchant Studios</h3>
            </div>
            <a href="{{ route('register', ['role' => 'vendor']) }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                Apply as a Merchant &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($topVendors as $vendor)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 flex flex-col sm:flex-row gap-6 items-start shadow-sm hover:shadow-xl transition-all duration-300">
                    <img src="{{ $vendor->shop_logo ?: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=200' }}" 
                         alt="{{ $vendor->shop_name }}" 
                         class="w-20 h-20 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 flex-shrink-0 shadow-md">
                    <div class="flex-1 space-y-2">
                        <div class="flex items-center gap-2">
                            <h4 class="font-extrabold text-base text-slate-900 dark:text-white">{{ $vendor->shop_name }}</h4>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                Verified
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                            {{ $vendor->shop_description }}
                        </p>
                        <div class="pt-2 flex items-center justify-between text-xs border-t border-slate-100 dark:border-slate-800">
                            <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $vendor->products_count }} Products Online</span>
                            <a href="{{ route('products.index', ['vendor' => $vendor->shop_slug]) }}" class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                Visit Store &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- 7. Verified Reviews Ticker -->
    @if($featuredReviews->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-10">
                <span class="text-xs font-bold tracking-widest text-indigo-600 dark:text-indigo-400 uppercase font-mono">Social Proof</span>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">What Discerning Buyers Say</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($featuredReviews as $review)
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 flex flex-col justify-between shadow-sm hover:shadow-lg transition-all">
                        <div class="space-y-3">
                            <div class="flex items-center text-amber-400 text-sm">
                                @for($i = 0; $i < $review->rating; $i++) ★ @endfor
                            </div>
                            <h5 class="text-xs font-bold text-slate-900 dark:text-white">{{ $review->title }}</h5>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed italic line-clamp-4">
                                "{{ $review->comment }}"
                            </p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $review->user->name ?? 'Verified Buyer' }}</span>
                            <span class="text-indigo-600 dark:text-indigo-400 font-medium truncate max-w-[140px]">{{ $review->product->name }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

</div>
@endsection
