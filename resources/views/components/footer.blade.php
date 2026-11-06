<footer class="bg-[#1B0E24] text-slate-300 pt-16 pb-12 border-t border-[#271435] text-xs relative z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Top Footer Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-[#271435]">
            
            <!-- Brand & Mission -->
            <div class="lg:col-span-2 space-y-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <span class="text-2xl text-[#F59E0B] group-hover:rotate-12 transition-transform select-none">✦</span>
                    <span class="font-extrabold text-2xl tracking-tight text-white font-sans">
                        UN<span class="text-[#F59E0B]">CART</span>
                    </span>
                </a>
                <p class="text-slate-400 leading-relaxed max-w-sm text-xs">
                    Discover premium lifestyle and electronics products at unbeatable prices. Shop the latest trends in tech, fashion, home essentials, and gaming gear.
                </p>
                <div class="pt-2 flex flex-wrap items-center gap-2 text-slate-300 text-[11px] font-semibold">
                    <span class="px-2.5 py-1 rounded-full bg-[#271435] text-emerald-400 border border-[#38144A]">
                        ✓ 100% Authentic Products
                    </span>
                    <span class="px-2.5 py-1 rounded-full bg-[#271435] text-[#F59E0B] border border-[#38144A]">
                        ✓ 256-Bit SSL Secure Checkout
                    </span>
                </div>
            </div>

            <!-- Categories -->
            <div>
                <h4 class="font-bold text-white uppercase tracking-wider text-[11px] mb-4">Categories</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('products.index', ['category' => 'electronics']) }}" class="hover:text-[#F59E0B] transition-colors">Electronics & Gadgets</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'fashion']) }}" class="hover:text-[#F59E0B] transition-colors">Fashion & Apparel</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'home']) }}" class="hover:text-[#F59E0B] transition-colors">Home & Living</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'beauty']) }}" class="hover:text-[#F59E0B] transition-colors">Beauty & Skincare</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'sports']) }}" class="hover:text-[#F59E0B] transition-colors">Sports & Outdoors</a></li>
                </ul>
            </div>

            <!-- Customer Service -->
            <div>
                <h4 class="font-bold text-white uppercase tracking-wider text-[11px] mb-4">Customer Care</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('orders.index') }}" class="hover:text-[#F59E0B] transition-colors">Track Active Shipment</a></li>
                    <li><a href="{{ route('orders.index') }}" class="hover:text-[#F59E0B] transition-colors">Returns & Refunds</a></li>
                    <li><a href="{{ route('static.faq') }}" class="hover:text-[#F59E0B] transition-colors">Help & FAQ</a></li>
                    <li><a href="{{ route('static.contact') }}" class="hover:text-[#F59E0B] transition-colors">Support Concierge</a></li>
                    <li><a href="{{ route('static.about') }}" class="hover:text-[#F59E0B] transition-colors">About Our Platform</a></li>
                </ul>
            </div>

            <!-- Seller & Platform -->
            <div>
                <h4 class="font-bold text-white uppercase tracking-wider text-[11px] mb-4">Ecosystem</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('vendor.become') }}" class="hover:text-[#F59E0B] transition-colors font-semibold text-[#F59E0B]">Sell on UNCART &rarr;</a></li>
                    <li><a href="{{ route('vendor.dashboard') }}" class="hover:text-[#F59E0B] transition-colors">Merchant Portal</a></li>
                    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-[#F59E0B] transition-colors">Admin Console</a></li>
                    <li><a href="{{ route('static.terms') }}" class="hover:text-[#F59E0B] transition-colors">Terms of Service</a></li>
                    <li><a href="{{ route('static.privacy') }}" class="hover:text-[#F59E0B] transition-colors">Privacy Governance</a></li>
                </ul>
            </div>

        </div>

        <!-- Bottom Copyright & Academic Attribution -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-400">
            <div>
                &copy; {{ date('Y') }} UNCART Multi-Vendor E-Commerce. All rights reserved.
            </div>

            <!-- Student Credit Card Badge -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#271435] border border-[#38144A]">
                <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                <span class="font-bold text-white">Chayan Jana</span>
                <span class="text-slate-500">&bull;</span>
                <span class="font-mono text-slate-300">Roll No: 34042724001</span>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Footer Currency Pill -->
                <div class="flex items-center gap-1.5 bg-[#271435] border border-[#38144A] rounded-full px-2.5 py-0.5 text-[11px] font-mono">
                    <span class="text-slate-400">Currency:</span>
                    <button type="button" @click="$store.currency.set('USD')" :class="$store.currency && $store.currency.current === 'USD' ? 'text-[#F59E0B] font-bold' : 'text-slate-400 hover:text-white'">USD</button>
                    <span class="text-slate-600">·</span>
                    <button type="button" @click="$store.currency.set('EUR')" :class="$store.currency && $store.currency.current === 'EUR' ? 'text-[#F59E0B] font-bold' : 'text-slate-400 hover:text-white'">EUR</button>
                    <span class="text-slate-600">·</span>
                    <button type="button" @click="$store.currency.set('GBP')" :class="$store.currency && $store.currency.current === 'GBP' ? 'text-[#F59E0B] font-bold' : 'text-slate-400 hover:text-white'">GBP</button>
                    <span class="text-slate-600">·</span>
                    <button type="button" @click="$store.currency.set('INR')" :class="$store.currency && $store.currency.current === 'INR' ? 'text-[#F59E0B] font-bold' : 'text-slate-400 hover:text-white'">INR</button>
                </div>

                <span class="text-xs text-slate-400">Payment Rails:</span>
                <span class="font-bold text-slate-300">VISA</span>
                <span class="font-bold text-slate-300">MasterCard</span>
                <span class="font-bold text-slate-300">UPI</span>
                <span class="font-bold text-slate-300">Apple Pay</span>
            </div>
        </div>

    </div>
</footer>
