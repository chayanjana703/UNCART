<footer class="bg-slate-950 text-slate-400 pt-16 pb-12 border-t border-slate-900 text-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-slate-900">
            
            <!-- Brand & Mission -->
            <div class="lg:col-span-2 space-y-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-base shadow-lg shadow-indigo-600/30">
                        S
                    </div>
                    <span class="font-black text-lg tracking-tight text-white">
                        SHOPI<span class="text-indigo-400">CART</span>
                    </span>
                </a>
                <p class="text-slate-400 leading-relaxed max-w-sm">
                    Engineered commerce for the discerning buyer. Discover bespoke acoustic hardware, ergonomic workspace furniture, minimalist timepieces, and artisan apparel from certified independent makers.
                </p>
                <div class="pt-2 flex items-center gap-3 text-slate-500">
                    <span class="px-2.5 py-1 rounded-md bg-slate-900 border border-slate-800 text-[10px] uppercase font-mono font-bold text-emerald-400">
                        ✓ 100% Verified Vendors
                    </span>
                    <span class="px-2.5 py-1 rounded-md bg-slate-900 border border-slate-800 text-[10px] uppercase font-mono font-bold text-sky-400">
                        ✓ 256-Bit SSL Encrypted
                    </span>
                </div>
            </div>

            <!-- Categories -->
            <div>
                <h4 class="font-bold text-white uppercase tracking-wider text-[11px] mb-4">Shop Portfolios</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('products.index', ['category' => 'audio-gadgets']) }}" class="hover:text-white transition-colors">Acoustic Audio Gear</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'computing-keyboards']) }}" class="hover:text-white transition-colors">Mechanical Keyboards</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'wearable-tech']) }}" class="hover:text-white transition-colors">Minimalist Timepieces</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'home-living']) }}" class="hover:text-white transition-colors">Ergonomic Studio Desks</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'bags-edc']) }}" class="hover:text-white transition-colors">Weatherproof EDC Bags</a></li>
                </ul>
            </div>

            <!-- Customer Service -->
            <div>
                <h4 class="font-bold text-white uppercase tracking-wider text-[11px] mb-4">Customer Care</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('orders.index') }}" class="hover:text-white transition-colors">Track Active Shipment</a></li>
                    <li><a href="{{ route('orders.index') }}" class="hover:text-white transition-colors">Returns & Exchanges</a></li>
                    <li><a href="{{ route('static.faq') }}" class="hover:text-white transition-colors">Delivery FAQs</a></li>
                    <li><a href="{{ route('static.contact') }}" class="hover:text-white transition-colors">Contact Support</a></li>
                    <li><a href="{{ route('static.about') }}" class="hover:text-white transition-colors">Our Philosophy</a></li>
                </ul>
            </div>

            <!-- Seller & Platform -->
            <div>
                <h4 class="font-bold text-white uppercase tracking-wider text-[11px] mb-4">Merchant Ecosystem</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('register', ['role' => 'vendor']) }}" class="hover:text-white transition-colors">Sell on ShopiCart</a></li>
                    <li><a href="{{ route('vendor.dashboard') }}" class="hover:text-white transition-colors">Vendor Portal Login</a></li>
                    <li><a href="{{ route('static.terms') }}" class="hover:text-white transition-colors">Seller Policies & Fee Schedule</a></li>
                    <li><a href="{{ route('static.privacy') }}" class="hover:text-white transition-colors">Privacy & Data Governance</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-[11px] text-slate-500">
            <div>
                &copy; {{ date('Y') }} <strong>ShopiCart</strong>. Developed by <strong>Chayan Jana</strong> (Roll No: 34042724001). Powered by Laravel & Modern Design Engineering.
            </div>
            <div class="flex items-center gap-4">
                <span>Secure Payments via:</span>
                <span class="font-mono text-slate-400 font-bold tracking-wider">STRIPE &bull; UPI &bull; VISA &bull; MASTERCARD &bull; COD</span>
            </div>
        </div>
    </div>
</footer>
