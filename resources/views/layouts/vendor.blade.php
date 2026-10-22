<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Vendor Portal') - ShopiCart Seller</title>

    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <!-- Chart.js for Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        body { font-feature-settings: "cv02", "cv03", "cv04", "cv11"; }
        button:active, a.btn:active { transform: scale(0.98); }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex antialiased font-sans">

    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-slate-950 border-r border-slate-800 flex flex-col justify-between hidden md:flex flex-shrink-0">
        <div>
            <!-- Header Brand -->
            <div class="h-16 px-6 flex items-center justify-between border-b border-slate-800">
                <a href="{{ route('vendor.dashboard') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-sm shadow-md">
                        S
                    </div>
                    <div>
                        <div class="font-extrabold text-sm text-white tracking-tight">SHOPI<span class="text-indigo-400">CART</span></div>
                        <div class="text-[10px] text-slate-400 uppercase font-mono tracking-widest font-bold">Seller Portal</div>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="p-3 space-y-1 text-xs">
                <a href="{{ route('vendor.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('vendor.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>

                <div class="pt-3 pb-1 px-3 text-[10px] uppercase tracking-wider font-bold text-slate-400 font-mono">Catalog</div>

                <a href="{{ route('vendor.products.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('vendor.products.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>My Products</span>
                </a>

                <a href="{{ route('vendor.products.create') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-900 transition-colors ml-4 text-[11px]">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add New Product</span>
                </a>

                <div class="pt-3 pb-1 px-3 text-[10px] uppercase tracking-wider font-bold text-slate-400 font-mono">Sales & Dispatch</div>

                <a href="{{ route('vendor.orders.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('vendor.orders.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Vendor Orders</span>
                </a>

                <a href="{{ route('vendor.analytics.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('vendor.analytics.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Analytics & Reports</span>
                </a>

                <div class="pt-3 pb-1 px-3 text-[10px] uppercase tracking-wider font-bold text-slate-400 font-mono">Engagement</div>

                <a href="{{ route('vendor.inquiries') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('vendor.inquiries*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span>Customer Inquiries</span>
                </a>

                <a href="{{ route('vendor.reviews') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('vendor.reviews*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    <span>Product Reviews</span>
                </a>

                <div class="pt-3 pb-1 px-3 text-[10px] uppercase tracking-wider font-bold text-slate-400 font-mono">Store Profile</div>

                <a href="{{ route('vendor.shop.edit') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-colors {{ request()->routeIs('vendor.shop.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Shop Settings & KYC</span>
                </a>
            </nav>
        </div>

        <!-- Footer / Storefront link -->
        <div class="p-4 border-t border-slate-800">
            <a href="{{ route('home') }}" class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-900 text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-850 transition-colors">
                <span>View Storefront</span>
                <span>&rarr;</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top bar -->
        <header class="h-16 bg-slate-950 border-b border-slate-800 px-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h1 class="text-base font-bold text-white">@yield('page-title', 'Vendor Control Dashboard')</h1>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="text-xs text-slate-400 hover:text-white transition-colors">
                    Back to Store
                </a>
                <div class="flex items-center gap-2 pl-3 border-l border-slate-800">
                    <img src="{{ Auth::user()->avatar ?: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100' }}" 
                         alt="{{ Auth::user()->name }}" 
                         class="w-7 h-7 rounded-full object-cover">
                    <span class="text-xs font-semibold text-slate-200">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </header>

        <!-- Body -->
        <main class="p-6 flex-1 bg-slate-900">
            @yield('content')
        </main>
    </div>

    <!-- Live Demo Switcher & Toasts -->
    <x-role-switcher />
    <x-toast />

    @stack('scripts')
</body>
</html>
