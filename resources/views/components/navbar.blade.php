@php
    $cartCount = 0;
    if (Auth::check()) {
        $cartCount = \App\Models\CartItem::where('user_id', Auth::id())->where('is_saved_for_later', false)->sum('quantity');
        $wishlistCount = \App\Models\Wishlist::where('user_id', Auth::id())->count();
        $notifCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
        $unreadNotifs = \App\Models\Notification::where('user_id', Auth::id())->latest()->take(5)->get();
    } else {
        $cartCount = \App\Models\CartItem::where('session_id', session()->getId())->whereNull('user_id')->where('is_saved_for_later', false)->sum('quantity');
        $wishlistCount = 0;
        $notifCount = 0;
        $unreadNotifs = collect();
    }
    $navCategories = \App\Models\Category::where('is_active', true)->orderBy('sort_order')->take(6)->get();
@endphp

<header x-data="{
    mobileMenu: false,
    cartCount: {{ $cartCount }},
    notifOpen: false,
    userOpen: false
}" 
@cart-updated.window="cartCount = $event.detail.count"
class="sticky top-0 z-50 bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
    
    <!-- Top Announcement Strip -->
    <div class="bg-slate-950 text-white text-[11px] font-medium py-1.5 px-4 text-center tracking-wide border-b border-slate-800 flex items-center justify-center gap-3">
        <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-400">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
            FLASH SALE IS LIVE
        </span>
        <span>Get free express shipping on orders over $99 with code <strong class="text-white underline decoration-emerald-400 decoration-2">WELCOME10</strong></span>
    </div>

    <!-- Main Navigation Bar -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">

            <!-- Logo -->
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white flex items-center justify-center font-black text-lg shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition-transform duration-200">
                        S
                    </div>
                    <span class="font-black text-xl tracking-tight text-slate-900 dark:text-white">
                        SHOPI<span class="text-indigo-600 dark:text-indigo-400 font-bold">CART</span>
                    </span>
                </a>

                <!-- Category Dropdown Trigger -->
                <div x-data="{ open: false }" class="relative hidden md:block ml-4">
                    <button @click="open = !open" @click.outside="open = false" 
                            class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                        <span>Categories</span>
                        <svg class="w-3 h-3 text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="open" 
                         x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-150"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         class="absolute left-0 mt-2 w-64 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 p-2 z-50">
                        @foreach($navCategories as $cat)
                            <a href="{{ route('products.index', ['category' => $cat->slug]) }}" 
                               class="flex items-center justify-between px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <span>{{ $cat->name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $cat->products_count ?? '' }}</span>
                            </a>
                        @endforeach
                        <div class="pt-2 mt-1 border-t border-slate-100 dark:border-slate-800">
                            <a href="{{ route('products.index') }}" class="block px-3 py-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                Browse All Categories &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Search Form -->
            <div class="hidden sm:flex flex-1 max-w-lg mx-2">
                <form action="{{ route('products.index') }}" method="GET" class="relative w-full">
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}"
                           placeholder="Search headphones, keyboards, watches, apparel..." 
                           class="w-full bg-slate-100 dark:bg-slate-800/90 text-slate-900 dark:text-white placeholder-slate-400 text-xs rounded-full pl-10 pr-4 py-2 border border-transparent focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </form>
            </div>

            <!-- Right Controls -->
            <div class="flex items-center gap-2">

                <!-- Compare shortcut -->
                <a href="{{ route('products.compare') }}" 
                   title="Compare Products"
                   class="hidden lg:flex items-center gap-1 px-2.5 py-1.5 text-xs text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Compare</span>
                </a>

                <!-- Wishlist Icon -->
                <a href="{{ route('wishlist.index') }}" 
                   title="My Wishlist"
                   class="relative p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:text-rose-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    @if($wishlistCount > 0)
                        <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center">
                            {{ $wishlistCount }}
                        </span>
                    @endif
                </a>

                <!-- Cart Button -->
                <a href="{{ route('cart.index') }}" 
                   title="Shopping Cart"
                   class="relative p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex items-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span x-show="cartCount > 0" 
                          x-text="cartCount"
                          class="absolute top-1 right-1 w-4 h-4 rounded-full bg-indigo-600 text-white text-[10px] font-bold flex items-center justify-center animate-scale">
                    </span>
                </a>

                <!-- Notification Dropdown -->
                @auth
                    <div class="relative" x-data="{ notifOpen: false }">
                        <button @click="notifOpen = !notifOpen" @click.outside="notifOpen = false" 
                                class="relative p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @if($notifCount > 0)
                                <span class="absolute top-1 right-1 w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            @endif
                        </button>

                        <div x-show="notifOpen" 
                             x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-150"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 p-3 z-50">
                            <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Notifications</span>
                                <a href="{{ route('notifications.index') }}" class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">View All</a>
                            </div>
                            <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-64 overflow-y-auto">
                                @forelse($unreadNotifs as $notif)
                                    <div class="py-2 px-1 text-xs">
                                        <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $notif->title }}</p>
                                        <p class="text-slate-500 dark:text-slate-400 text-[11px] line-clamp-2 mt-0.5">{{ $notif->message }}</p>
                                    </div>
                                @empty
                                    <div class="py-6 text-center text-xs text-slate-400">
                                        No new notifications
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endauth

                <!-- User Dropdown / Auth Links -->
                @auth
                    <div class="relative" x-data="{ userOpen: false }">
                        <button @click="userOpen = !userOpen" @click.outside="userOpen = false" 
                                class="flex items-center gap-2 pl-2 pr-1 py-1 rounded-full border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                            <img src="{{ Auth::user()->avatar ?: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100' }}" 
                                 alt="{{ Auth::user()->name }}" 
                                 class="w-7 h-7 rounded-full object-cover">
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-200 hidden md:inline truncate max-w-[90px]">
                                {{ explode(' ', Auth::user()->name)[0] }}
                            </span>
                            <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="userOpen" 
                             x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-150"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 p-2 z-50">
                            
                            <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800 mb-1">
                                <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                    {{ Auth::user()->role }}
                                </span>
                            </div>

                            <a href="{{ route('profile.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <span>👤</span> My Profile & Settings
                            </a>
                            <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <span>📦</span> Order History & Tracking
                            </a>
                            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <span>❤️</span> My Wishlist
                            </a>

                            @if(Auth::user()->isVendor() || Auth::user()->isAdmin())
                                <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                                <a href="{{ route('vendor.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-indigo-600 dark:text-indigo-400 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition-colors">
                                    <span>🏪</span> Vendor Seller Portal
                                </a>
                            @endif

                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-amber-600 dark:text-amber-400 rounded-xl hover:bg-amber-50 dark:hover:bg-amber-950/40 transition-colors">
                                    <span>🛡️</span> Admin Control Center
                                </a>
                            @endif

                            <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                                    <span>🚪</span> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-1.5 text-xs font-semibold bg-slate-900 text-white dark:bg-white dark:text-slate-900 rounded-full hover:bg-slate-800 transition-all active:scale-95 shadow-md">
                            Join
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>
