<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="strict-origin-when-cross-origin">

    <title>@yield('title', 'UNCART — Precision Modern Commerce')</title>

    <!-- Typography: Plus Jakarta Sans, Playfair Display & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;0,900;1,600;1,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&family=Caveat:wght@600;700&family=Covered+By+Your+Grace&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN with typography & forms -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                        script: ['"Covered By Your Grace"', '"Caveat"', 'cursive'],
                    },
                    colors: {
                        aubergine: {
                            50: '#FAF5FC',
                            100: '#F3E8F9',
                            200: '#E5CEF1',
                            300: '#D0A9E4',
                            400: '#B47BD2',
                            500: '#8E44AD',
                            600: '#6B2A88',
                            700: '#4F1E66',
                            800: '#38144A',
                            900: '#271435',
                            950: '#1B0E24',
                            DEFAULT: '#1B0E24',
                        },
                        marigold: {
                            50: '#FFFDF5',
                            100: '#FEF8E7',
                            200: '#FDEEC4',
                            300: '#FCDF96',
                            400: '#FAC856',
                            500: '#F59E0B',
                            600: '#D97706',
                            700: '#B45309',
                            800: '#92400E',
                            900: '#78350F',
                            DEFAULT: '#F59E0B',
                        },
                        silver: {
                            50: '#FBFDFE',
                            100: '#F5F8FA',
                            200: '#EAEFF4',
                            300: '#DDE4EC',
                            400: '#CBD5E1',
                            500: '#94A3B8',
                            600: '#64748B',
                            700: '#475569',
                            800: '#334155',
                            900: '#1E293B',
                            ice: '#F0F3F7',
                            DEFAULT: '#F0F3F7',
                        },
                        brand: {
                            50: '#FAF5FC',
                            100: '#F3E8F9',
                            200: '#E5CEF1',
                            300: '#D0A9E4',
                            400: '#B47BD2',
                            500: '#8E44AD',
                            600: '#6B2A88',
                            700: '#4F1E66',
                            800: '#38144A',
                            900: '#271435',
                        }
                    },
                    transitionTimingFunction: {
                        'spring': 'cubic-bezier(0.16, 1, 0.3, 1)',
                        'bounce-sm': 'cubic-bezier(0.34, 1.56, 0.64, 1)',
                    },
                    animation: {
                        'float-slow': 'float 6s ease-in-out infinite',
                        'float-delayed': 'float 7s ease-in-out 2s infinite',
                        'pulse-subtle': 'pulseSubtle 4s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        pulseSubtle: {
                            '0%, 100%': { opacity: '0.4', transform: 'scale(1)' },
                            '50%': { opacity: '0.7', transform: 'scale(1.05)' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js Store & Global Systems -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('currency', {
                current: localStorage.getItem('uncart_currency') || localStorage.getItem('shopicart_currency') || 'USD',
                rates: {
                    USD: { symbol: '$', rate: 1.0, name: 'USD ($)', flag: '🇺🇸' },
                    EUR: { symbol: '€', rate: 0.92, name: 'EUR (€)', flag: '🇪🇺' },
                    GBP: { symbol: '£', rate: 0.79, name: 'GBP (£)', flag: '🇬🇧' },
                    INR: { symbol: '₹', rate: 83.5, name: 'INR (₹)', flag: '🇮🇳' },
                },
                set(curr) {
                    if (this.rates[curr]) {
                        this.current = curr;
                        localStorage.setItem('uncart_currency', curr);
                        document.cookie = `uncart_currency=${curr};path=/;max-age=31536000`;
                        window.dispatchEvent(new CustomEvent('currency-changed', { detail: { currency: curr } }));
                    }
                },
                format(usdAmount) {
                    if (usdAmount === null || usdAmount === undefined || usdAmount === '') return '';
                    const cleanNum = typeof usdAmount === 'string' ? parseFloat(usdAmount.replace(/[^0-9.-]+/g, '')) : Number(usdAmount);
                    if (isNaN(cleanNum)) return '';
                    const r = (this.rates && this.rates[this.current]) ? this.rates[this.current] : (this.rates ? this.rates.USD : { symbol: '$', rate: 1.0 });
                    const val = (cleanNum * r.rate).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    return `${r.symbol}${val}`;
                }
            });
        });
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <!-- Canvas Confetti -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

    <style>
        /* Smooth Modern Font Smoothing */
        body {
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: #F0F3F7;
            color: #1B0E24;
        }

        /* Tactile Press Physics (Emil Kowalski) */
        button, a.btn, [role="button"] {
            transition: transform 180ms cubic-bezier(0.16, 1, 0.3, 1), box-shadow 180ms cubic-bezier(0.16, 1, 0.3, 1);
        }
        button:active, a.btn:active, [role="button"]:active {
            transform: scale(0.97) !important;
        }

        /* Page Content Smooth Transition */
        #page-content {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 220ms cubic-bezier(0.16, 1, 0.3, 1), transform 220ms cubic-bezier(0.16, 1, 0.3, 1);
        }
        body.page-transitioning #page-content {
            opacity: 0.2;
            transform: translateY(6px);
        }

        /* On-Scroll Reveal Animations */
        [data-reveal] {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 600ms cubic-bezier(0.16, 1, 0.3, 1), transform 600ms cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        [data-reveal="scale"] {
            transform: scale(0.94) translateY(20px);
        }
        [data-reveal].is-revealed {
            opacity: 1 !important;
            transform: translate(0) scale(1) !important;
        }

        /* Hide scrollbars for category strip on touch */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @media (prefers-reduced-motion: reduce) {
            [data-reveal] {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
            #page-progress-bar { display: none !important; }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-[#f8fafc] text-slate-900 min-h-screen flex flex-col font-sans selection:bg-indigo-600 selection:text-white relative pb-16 sm:pb-0"
      x-data="globalAppModal()">

    <!-- Ambient Floating Soft Pastel Gradient Mesh -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden" aria-hidden="true">
        <div class="absolute w-[500px] h-[500px] -top-32 -left-32 bg-indigo-100/50 rounded-full blur-3xl animate-float-slow"></div>
        <div class="absolute w-[550px] h-[550px] top-1/4 -right-32 bg-purple-100/40 rounded-full blur-3xl animate-float-delayed"></div>
        <div class="absolute w-[450px] h-[450px] bottom-10 left-1/3 bg-pink-100/30 rounded-full blur-3xl animate-pulse-subtle"></div>
    </div>

    <!-- UNCART Global Brand Preloader & Navigation Progress Bar -->
    <x-page-loader />

    <!-- Header / Navbar -->
    <x-navbar />

    <!-- Main Content Area with Smooth Page Transition Container -->
    <main id="page-content" class="flex-1 relative z-10">
        @yield('content')
    </main>

    <!-- Footer -->
    <x-footer />

    <!-- Mobile Bottom App Dock Navigation (Matching Mobile Screenshot) -->
    <nav class="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-lg border-t border-slate-200/80 px-2 py-2 flex items-center justify-around shadow-lg">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('home') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px]">Home</span>
        </a>
        <a href="{{ route('products.index') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('products.index') && !request('deals') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            <span class="text-[10px]">Categories</span>
        </a>
        <a href="{{ route('products.index', ['deals' => 1]) }}" class="flex flex-col items-center gap-0.5 {{ request('deals') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            <span class="text-[10px]">Deals</span>
        </a>
        <button type="button" 
                @click.prevent="window.dispatchEvent(new CustomEvent('open-cart-drawer'))" 
                class="flex flex-col items-center gap-0.5 text-slate-500 hover:text-indigo-600 relative">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <span class="text-[10px]">Bag</span>
        </button>
        <a href="{{ Auth::check() ? route('profile.index') : route('login') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('profile*') || request()->routeIs('login') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-[10px]">Account</span>
        </a>
    </nav>

    <!-- 1-Click Role Switcher -->
    <x-role-switcher />

    <!-- Toast Notifications -->
    <x-toast />

    <!-- Slide-over Quick Cart Drawer -->
    <x-cart-drawer />

    <!-- Floating Compare Tray -->
    <x-compare-tray />

    <!-- Global Quick View Pop-Up Modal Window (Matching Clean Aesthetic) -->
    <div x-show="quickViewOpen" 
         x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6" 
         style="display: none;"
         @keydown.escape.window="closeQuickView()">

        <div @click.away="closeQuickView()"
             x-show="quickViewOpen"
             x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-4xl bg-white border border-slate-100 rounded-3xl shadow-2xl overflow-hidden p-6 sm:p-8">

            <!-- Close Button -->
            <button @click="closeQuickView()" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <!-- Quick View Modal Content Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center" x-show="activeProduct">
                <!-- Gallery Image -->
                <div class="space-y-3">
                    <div class="relative aspect-square rounded-2xl overflow-hidden bg-slate-50 border border-slate-100 flex items-center justify-center p-4 group">
                        <img :src="activeProduct.image" 
                             :alt="activeProduct.name" 
                             referrerpolicy="no-referrer"
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600';"
                             class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105">
                        <span x-show="activeProduct.badge" x-text="activeProduct.badge" class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-500 text-white shadow-sm"></span>
                    </div>
                </div>

                <!-- Product Specifications & Buy Actions -->
                <div class="space-y-4">
                    <div>
                        <span x-text="activeProduct.category || 'Featured Collection'" class="text-[11px] font-bold text-indigo-600 uppercase tracking-widest block"></span>
                        <h2 x-text="activeProduct.name" class="text-2xl font-black text-slate-900 tracking-tight mt-1"></h2>
                        <div class="flex items-center gap-2 mt-2">
                            <div class="flex text-amber-400 text-sm">★★★★★</div>
                            <span class="text-xs text-slate-500 font-medium" x-text="'(' + (activeProduct.reviews_count || 12) + ' reviews)'"></span>
                        </div>
                    </div>

                    <div class="flex items-baseline gap-3">
                        <span class="text-3xl font-black text-slate-900" x-text="'$' + Number(activeProduct.price).toFixed(2)"></span>
                        <span x-show="activeProduct.compare_price" class="text-base text-slate-400 line-through" x-text="'$' + Number(activeProduct.compare_price).toFixed(2)"></span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">In Stock</span>
                    </div>

                    <p x-text="activeProduct.description" class="text-xs text-slate-600 leading-relaxed line-clamp-3"></p>

                    <!-- Vendor Badge -->
                    <div class="py-2.5 px-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-500">Sold by:</span>
                        <span class="font-bold text-slate-800" x-text="activeProduct.vendor || 'UNCART Direct'"></span>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-2 flex items-center gap-3">
                        <!-- Add to Cart Form -->
                        <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="product_id" :value="activeProduct.id">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full py-3.5 px-6 rounded-full bg-[#1B0E24] hover:bg-[#F59E0B] hover:text-[#1B0E24] text-white font-bold text-xs uppercase tracking-wider shadow-md flex items-center justify-center gap-2 transition active:scale-95">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>Add to Cart</span>
                            </button>
                        </form>

                        <!-- Link to Details -->
                        <a :href="'{{ url('/product') }}/' + activeProduct.slug" class="py-3.5 px-5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                            <span>Details</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Client Script -->
    <script>
        function globalAppModal() {
            return {
                quickViewOpen: false,
                activeProduct: null,
                init() {
                    window.addEventListener('open-quick-view', (e) => {
                        this.activeProduct = e.detail;
                        this.quickViewOpen = true;
                    });
                },
                closeQuickView() {
                    this.quickViewOpen = false;
                }
            };
        }

        document.addEventListener('DOMContentLoaded', () => {
            // 1. Staggered On-Scroll Reveals
            const revealElements = document.querySelectorAll('[data-reveal]');
            if ('IntersectionObserver' in window && revealElements.length > 0) {
                const observerOptions = {
                    root: null,
                    rootMargin: '0px 0px -40px 0px',
                    threshold: 0.1
                };

                const revealObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-revealed');
                            observer.unobserve(entry.target);
                        }
                    });
                }, observerOptions);

                revealElements.forEach((el) => {
                    revealObserver.observe(el);
                });
            } else {
                revealElements.forEach(el => el.classList.add('is-revealed'));
            }

            // Link clicks for smooth page transitions
            document.querySelectorAll('a[href]').forEach(link => {
                const href = link.getAttribute('href');
                if (
                    !href || 
                    href.startsWith('#') || 
                    href.startsWith('javascript:') || 
                    href.startsWith('mailto:') || 
                    href.startsWith('tel:') || 
                    link.getAttribute('target') === '_blank' ||
                    link.hasAttribute('download')
                ) {
                    return;
                }

                const url = new URL(link.href, window.location.origin);
                if (url.origin === window.location.origin && url.pathname !== window.location.pathname) {
                    link.addEventListener('click', (e) => {
                        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
                        if (window.startLoader) window.startLoader();
                        document.body.classList.add('page-transitioning');
                    });
                }
            });

            window.addEventListener('pageshow', (event) => {
                document.body.classList.remove('page-transitioning');
                if (window.stopLoader) window.stopLoader();
                if (event.persisted) {
                    window.location.reload();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
