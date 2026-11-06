@php
    $cartCount = 0;
    if (Auth::check()) {
        $cartCount = \App\Models\CartItem::where('user_id', Auth::id())->where('is_saved_for_later', false)->sum('quantity');
        $wishlistCount = \App\Models\Wishlist::where('user_id', Auth::id())->count();
        $notifCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
    } else {
        $cartCount = \App\Models\CartItem::where('session_id', session()->getId())->whereNull('user_id')->where('is_saved_for_later', false)->sum('quantity');
        $wishlistCount = 0;
        $notifCount = 0;
    }
    $navCategories = \App\Models\Category::where('is_active', true)->orderBy('sort_order')->take(8)->get();
@endphp

<header x-data="{
    mobileMenu: false,
    cartCount: {{ $cartCount }},
    wishlistCount: {{ $wishlistCount }},
    userOpen: false,
    catOpen: false,
    searchQuery: '',
    scrolled: false
}"
@scroll.window="scrolled = (window.pageYOffset > 15)"
@cart-updated.window="cartCount = $event.detail.count"
class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-100 transition-shadow duration-300"
:class="{ 'shadow-sm': scrolled }">

    <!-- Top Announcement Bar (Subtle & Clean) -->
    <div class="hidden md:block bg-[#1B0E24] border-b border-[#2B163B] py-1.5 px-6 text-xs text-slate-300">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-[#F59E0B] text-[#1B0E24]">PROMO</span>
                <span>Get <strong class="text-white">10% off</strong> your first order with coupon <code class="font-bold text-[#F59E0B] bg-[#271435] px-1.5 py-0.5 rounded border border-[#3B1E50]">WELCOME10</code></span>
            </div>
            <div class="flex items-center gap-4 text-xs font-medium">
                <a href="{{ route('orders.index') }}" class="hover:text-[#F59E0B] text-slate-300 transition">Track Order</a>
                <span class="text-slate-600">|</span>
                <a href="{{ route('static.faq') }}" class="hover:text-[#F59E0B] text-slate-300 transition">Help Center</a>
                <span class="text-slate-600">|</span>

                <!-- Multi-Currency Switcher Dropdown -->
                <div class="relative" x-data="{ openCurr: false }">
                    <button type="button" @click="openCurr = !openCurr" @click.outside="openCurr = false"
                            class="flex items-center gap-1.5 font-mono font-bold text-slate-200 hover:text-[#F59E0B] bg-[#271435] hover:bg-[#341B46] px-2.5 py-0.5 rounded-full border border-[#3B1E50] text-[11px] transition shadow-xs">
                        <span x-text="$store.currency && $store.currency.rates[$store.currency.current] ? $store.currency.rates[$store.currency.current].flag : '🇺🇸'">🇺🇸</span>
                        <span x-text="$store.currency ? $store.currency.current : 'USD'">USD</span>
                        <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openCurr" x-transition.origin.top.right style="display: none;"
                         class="absolute right-0 mt-1.5 w-36 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50 text-xs">
                        <div class="px-3 py-1 text-[9px] font-mono uppercase tracking-wider text-slate-400 font-bold border-b border-slate-100 mb-1">Select Currency</div>
                        <template x-for="(curr, code) in ($store.currency ? $store.currency.rates : {})" :key="code">
                            <button type="button" 
                                    @click="$store.currency.set(code); openCurr = false" 
                                    class="w-full text-left px-3 py-1.5 hover:bg-slate-50 flex items-center justify-between font-mono text-[11px]" 
                                    :class="$store.currency && $store.currency.current === code ? 'text-[#1B0E24] font-bold bg-[#FAF5FC]' : 'text-slate-700'">
                                <span class="flex items-center gap-1.5">
                                    <span x-text="curr.flag"></span>
                                    <span x-text="curr.name"></span>
                                </span>
                                <span x-show="$store.currency && $store.currency.current === code" class="text-[#F59E0B] text-xs font-bold">✓</span>
                            </button>
                        </template>
                    </div>
                </div>

                <span class="text-slate-600">|</span>
                <span class="text-slate-400">24/7 Support: +1 (800) 892-0199</span>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 py-3 flex items-center justify-between gap-4 sm:gap-8">
        
        <!-- Left: Mobile Menu Trigger + Brand Logo -->
        <div class="flex items-center gap-3 sm:gap-6">
            <!-- Mobile Menu Button -->
            <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display: none;"/>
                </svg>
            </button>

            <!-- Brand Logo (Modern Sparkle Icon + UNCART typography) -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <span class="text-2xl text-[#F59E0B] group-hover:rotate-12 transition-transform select-none">✦</span>
                <span class="font-extrabold text-xl sm:text-2xl tracking-tight text-[#1B0E24] font-sans">
                    UN<span class="text-[#F59E0B]">CART</span>
                </span>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden lg:flex items-center gap-7 ml-4 text-sm font-semibold text-slate-700">
                <a href="{{ route('home') }}" class="relative py-1 transition hover:text-[#F59E0B] {{ request()->routeIs('home') ? 'text-[#1B0E24] font-bold' : '' }}">
                    <span>Home</span>
                    @if(request()->routeIs('home'))
                        <span class="absolute -bottom-1.5 left-0 w-full h-[2.5px] bg-[#F59E0B] rounded-full"></span>
                    @endif
                </a>
                <a href="{{ route('products.index') }}" class="relative py-1 transition hover:text-[#F59E0B] {{ request()->routeIs('products.index') && !request('deals') ? 'text-[#1B0E24] font-bold' : '' }}">
                    <span>Shop</span>
                    @if(request()->routeIs('products.index') && !request('deals'))
                        <span class="absolute -bottom-1.5 left-0 w-full h-[2.5px] bg-[#F59E0B] rounded-full"></span>
                    @endif
                </a>

                <!-- Categories Popover -->
                <div class="relative" @click.outside="catOpen = false">
                    <button @click="catOpen = !catOpen" class="flex items-center gap-1 py-1 transition hover:text-[#F59E0B]">
                        <span>Categories</span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform" :class="{ 'rotate-180': catOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="catOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute top-full left-0 mt-3 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-3 z-50">
                        <div class="px-4 py-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">Popular Categories</div>
                        @foreach($navCategories as $cat)
                            <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="flex items-center justify-between px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-[#FAF5FC] hover:text-[#6B2A88] transition">
                                <span>{{ $cat->name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $cat->products_count ?? '' }}</span>
                            </a>
                        @endforeach
                        <div class="border-t border-slate-100 mt-2 pt-2 px-4">
                            <a href="{{ route('products.index') }}" class="text-xs font-bold text-[#6B2A88] hover:text-[#F59E0B] transition flex items-center justify-between">
                                <span>Browse All Categories</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('products.index', ['deals' => 1]) }}" class="transition hover:text-[#F59E0B] {{ request('deals') ? 'text-[#F59E0B] font-bold' : '' }}">
                    Deals
                </a>
                <a href="{{ route('static.about') }}" class="transition hover:text-[#F59E0B] {{ request()->routeIs('static.about') ? 'text-[#F59E0B] font-bold' : '' }}">
                    About
                </a>
            </nav>
        </div>

        <!-- Center: Search Bar with Live Autocomplete -->
        <div class="flex-1 max-w-md hidden sm:block relative" 
             x-data="{
                 searchQuery: '{{ request('q') }}',
                 isOpen: false,
                 loading: false,
                 results: [],
                 totalCount: 0,
                 async performLiveSearch() {
                     if (this.searchQuery.trim().length < 2) {
                         this.results = [];
                         this.isOpen = false;
                         return;
                     }
                     this.loading = true;
                     try {
                         let res = await fetch('{{ route('products.live-search') }}?q=' + encodeURIComponent(this.searchQuery.trim()), {
                             headers: { 
                                 'Accept': 'application/json',
                                 'X-Requested-With': 'XMLHttpRequest'
                             }
                         });
                         let data = await res.json();
                         this.results = data.results || [];
                         this.totalCount = data.total || 0;
                         this.isOpen = true;
                     } catch(e) {
                         this.results = [];
                     } finally {
                         this.loading = false;
                     }
                 }
             }"
             @click.outside="isOpen = false"
             @keydown.escape.window="isOpen = false">
            
            <form action="{{ route('products.index') }}" method="GET" class="relative">
                <input type="text" 
                       name="q" 
                       x-model="searchQuery"
                       @input.debounce.250ms="performLiveSearch()"
                       @focus="if(searchQuery.trim().length >= 2) isOpen = true"
                       placeholder="Search headphones, keyboards, sneakers..." 
                       autocomplete="off"
                       class="w-full pl-10 pr-9 py-2.5 rounded-full bg-slate-100/90 hover:bg-slate-100 focus:bg-white border border-slate-200/80 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
                
                <span class="absolute left-3.5 top-3 text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>

                <!-- Clear / Loading Indicator -->
                <div class="absolute right-3.5 top-3 flex items-center">
                    <template x-if="loading">
                        <svg class="w-4 h-4 animate-spin text-indigo-600" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </template>
                    <template x-if="!loading && searchQuery.length > 0">
                        <button type="button" @click="searchQuery = ''; isOpen = false; results = []" class="text-slate-400 hover:text-slate-600">
                            &times;
                        </button>
                    </template>
                </div>
            </form>

            <!-- Floating Live Autocomplete Dropdown -->
            <div x-show="isOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-98"
                 class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-50 divide-y divide-slate-100 max-h-96 overflow-y-auto"
                 style="display: none;">
                
                <!-- Results Header -->
                <div class="px-4 py-2 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                    <span>Matching Products</span>
                    <span x-text="totalCount + ' found'"></span>
                </div>

                <!-- No Results Found -->
                <template x-if="!loading && results.length === 0">
                    <div class="p-6 text-center text-xs text-slate-500">
                        No products found matching "<strong x-text="searchQuery" class="text-slate-800"></strong>". Try another keyword.
                    </div>
                </template>

                <!-- Product Results List -->
                <template x-for="item in results" :key="item.id">
                    <a :href="'{{ url('/product') }}/' + item.slug" 
                       class="p-3 hover:bg-indigo-50/50 flex items-center gap-3 transition group">
                        <img :src="item.image" 
                             :alt="item.name" 
                             referrerpolicy="no-referrer"
                             class="w-12 h-12 rounded-xl object-contain bg-slate-50 border border-slate-100 p-1 flex-shrink-0 group-hover:scale-105 transition-transform">
                        <div class="flex-1 min-w-0">
                            <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block" x-text="item.category"></span>
                            <h5 class="font-bold text-xs text-slate-900 group-hover:text-indigo-600 truncate transition-colors" x-text="item.name"></h5>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs font-black text-slate-900" x-text="item.price"></span>
                                <span x-show="item.compare_price" class="text-[10px] text-slate-400 line-through" x-text="item.compare_price"></span>
                                <span class="text-[10px] text-amber-500 font-semibold" x-text="'★ ' + item.rating"></span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-600 group-hover:translate-x-0.5 transition-all flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </template>

                <!-- View All Results Footer Link -->
                <div class="p-2.5 bg-slate-50 text-center" x-show="totalCount > 0">
                    <a :href="'{{ route('products.index') }}?q=' + encodeURIComponent(searchQuery)" 
                       class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition inline-flex items-center gap-1">
                        <span>View all <strong x-text="totalCount"></strong> results for "<span x-text="searchQuery"></span>"</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right: Actions (Wishlist, Cart, Profile) -->
        <div class="flex items-center gap-3 sm:gap-4">
            
            <!-- Wishlist Heart Button with Badge -->
            <a href="{{ route('wishlist.index') }}" class="relative p-2.5 rounded-full hover:bg-slate-100 text-slate-700 hover:text-[#F59E0B] transition active:scale-95" title="Wishlist">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span x-show="wishlistCount > 0" 
                      x-text="wishlistCount" 
                      class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full bg-[#F59E0B] text-[#1B0E24] text-[10px] font-black flex items-center justify-center shadow-sm">
                    {{ $wishlistCount }}
                </span>
            </a>

            <!-- Cart Button with Badge (Triggers Quick Cart Drawer) -->
            <button @click.prevent="window.dispatchEvent(new CustomEvent('open-cart-drawer'))" 
                    type="button"
                    class="relative p-2.5 rounded-full hover:bg-slate-100 text-slate-700 hover:text-[#F59E0B] transition active:scale-95" 
                    title="Shopping Cart">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span x-show="cartCount > 0" 
                      x-text="cartCount" 
                      class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full bg-[#F59E0B] text-[#1B0E24] text-[10px] font-black flex items-center justify-center shadow-sm">
                    {{ $cartCount }}
                </span>
            </button>

            <!-- Profile Dropdown Avatar -->
            <div class="relative" @click.outside="userOpen = false">
                @auth
                    <button @click="userOpen = !userOpen" class="flex items-center gap-2 p-1 rounded-full hover:ring-2 hover:ring-indigo-100 transition">
                        <img src="{{ Auth::user()->avatar_url }}" 
                             alt="{{ Auth::user()->name }}" 
                             class="w-9 h-9 rounded-full object-cover border border-slate-200 global-user-avatar">
                    </button>

                    <!-- User Popover Menu -->
                    <div x-show="userOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-600">
                                {{ Auth::user()->role }}
                            </span>
                        </div>

                        <div class="py-1 text-xs font-medium text-slate-700">
                            <a href="{{ route('profile.index') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 hover:text-indigo-600">
                                <span>👤</span> Account Profile
                            </a>
                            <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 hover:text-indigo-600">
                                <span>📦</span> My Orders
                            </a>
                            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 hover:text-indigo-600">
                                <span>❤️</span> Wishlist
                            </a>

                            @if(Auth::user()->isVendor())
                                <a href="{{ route('vendor.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-indigo-600 font-bold hover:bg-indigo-50">
                                    <span>🏪</span> Merchant Portal
                                </a>
                            @endif

                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-amber-600 font-bold hover:bg-amber-50">
                                    <span>🛡️</span> Admin Console
                                </a>
                            @endif
                        </div>

                        <div class="border-t border-slate-100 pt-1">
                            <form action="{{ route('logout', [], false) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 flex items-center gap-2">
                                    <span>🚪</span> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="py-2 px-4 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs transition active:scale-95 shadow-sm">
                        Sign In
                    </a>
                @endauth
            </div>

        </div>

    </div>

    <!-- Mobile Search Bar (Only visible on small phones) -->
    <div class="sm:hidden px-4 pb-3">
        <form action="{{ route('products.index') }}" method="GET" class="relative">
            <input type="text" 
                   name="q" 
                   value="{{ request('q') }}"
                   placeholder="Search products..." 
                   class="w-full pl-9 pr-3 py-2 rounded-full bg-slate-100 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
            <span class="absolute left-3 top-2.5 text-slate-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
        </form>
    </div>

    <!-- Mobile Slide-Down Menu Drawer -->
    <div x-show="mobileMenu" 
         x-transition 
         class="md:hidden border-t border-slate-100 bg-white px-4 py-4 space-y-3">
        <nav class="flex flex-col space-y-2 text-sm font-semibold text-slate-700">
            <a href="{{ route('home') }}" class="py-1.5 px-3 rounded-lg hover:bg-slate-50 hover:text-indigo-600">Home</a>
            <a href="{{ route('products.index') }}" class="py-1.5 px-3 rounded-lg hover:bg-slate-50 hover:text-indigo-600">Shop All</a>
            <a href="{{ route('products.index', ['deals' => 1]) }}" class="py-1.5 px-3 rounded-lg hover:bg-slate-50 hover:text-indigo-600">Exclusive Deals</a>
            <a href="{{ route('static.about') }}" class="py-1.5 px-3 rounded-lg hover:bg-slate-50 hover:text-indigo-600">About UNCART</a>
            <a href="{{ route('orders.index') }}" class="py-1.5 px-3 rounded-lg hover:bg-slate-50 hover:text-indigo-600">Track Order</a>
            <a href="{{ route('static.faq') }}" class="py-1.5 px-3 rounded-lg hover:bg-slate-50 hover:text-indigo-600">FAQ & Support</a>

            <!-- Mobile Currency Switcher -->
            <div class="pt-3 border-t border-slate-100">
                <div class="text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold px-3 mb-2">Display Currency</div>
                <div class="grid grid-cols-4 gap-2 px-1">
                    <button type="button" @click="$store.currency.set('USD')" 
                            :class="$store.currency && $store.currency.current === 'USD' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-700'"
                            class="py-1.5 rounded-xl text-xs font-mono transition text-center">
                        🇺🇸 USD
                    </button>
                    <button type="button" @click="$store.currency.set('EUR')" 
                            :class="$store.currency && $store.currency.current === 'EUR' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-700'"
                            class="py-1.5 rounded-xl text-xs font-mono transition text-center">
                        🇪🇺 EUR
                    </button>
                    <button type="button" @click="$store.currency.set('GBP')" 
                            :class="$store.currency && $store.currency.current === 'GBP' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-700'"
                            class="py-1.5 rounded-xl text-xs font-mono transition text-center">
                        🇬🇧 GBP
                    </button>
                    <button type="button" @click="$store.currency.set('INR')" 
                            :class="$store.currency && $store.currency.current === 'INR' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-700'"
                            class="py-1.5 rounded-xl text-xs font-mono transition text-center">
                        🇮🇳 INR
                    </button>
                </div>
            </div>
        </nav>
    </div>

</header>
