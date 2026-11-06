<div x-data="cartDrawerHandler()" 
     x-cloak
     @open-cart-drawer.window="openDrawer()"
     @cart-updated.window="fetchDrawerData()"
     @keydown.escape.window="closeDrawer()"
     class="relative z-50">

    <!-- Backdrop Overlay -->
    <div x-show="isOpen"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeDrawer()"
         class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"
         style="display: none;"></div>

    <!-- Slide-over Panel -->
    <div x-show="isOpen"
         x-transition:enter="transform transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in duration-250"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 max-w-full flex pl-10"
         style="display: none;">

        <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between overflow-hidden border-l border-slate-100">
            
            <!-- Drawer Header -->
            <div class="p-5 sm:p-6 border-b border-[#DDE4EC] flex items-center justify-between bg-[#F0F3F7]/90">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-[#FAF5FC] text-[#6B2A88] flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-[#1B0E24] tracking-tight">Shopping Bag</h3>
                        <span class="text-xs text-slate-500 font-medium" x-text="data.count + (data.count === 1 ? ' item' : ' items')"></span>
                    </div>
                </div>

                <button @click="closeDrawer()" 
                        class="w-8 h-8 rounded-full bg-white hover:bg-[#EAEFF4] text-slate-400 hover:text-slate-700 flex items-center justify-center transition border border-[#DDE4EC]"
                        title="Close">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Free Shipping Progress Meter -->
            <div class="bg-[#FEF8E7] px-5 py-3 border-b border-[#FDEEC4]" x-show="data.count > 0">
                <div class="flex items-center justify-between text-xs mb-1.5 font-medium">
                    <template x-if="!data.unlocked_free_shipping">
                        <span class="text-slate-700">
                            Add <strong class="text-[#1B0E24] font-bold" x-text="$store.currency ? $store.currency.format(data.remaining_for_free_shipping_raw || 0) : data.remaining_for_free_shipping"></strong> for <span class="font-bold text-[#D97706]">Free Shipping</span>!
                        </span>
                    </template>
                    <template x-if="data.unlocked_free_shipping">
                        <span class="font-bold text-[#D97706] flex items-center gap-1">
                            <span>🎉</span> You've unlocked Free Shipping!
                        </span>
                    </template>
                    <span class="text-[11px] font-bold text-slate-600" x-text="data.free_shipping_progress + '%'"></span>
                </div>
                <div class="w-full bg-[#FDEEC4]/80 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-[#F59E0B] to-[#D97706] h-1.5 rounded-full transition-all duration-500"
                         :style="'width: ' + data.free_shipping_progress + '%'"></div>
                </div>
            </div>

            <!-- Items List Container (Scrollable) -->
            <div class="flex-1 overflow-y-auto overflow-x-hidden divide-y divide-slate-100 p-5 sm:p-6 space-y-4">
                
                <!-- Loading Skeleton -->
                <div x-show="loading && data.items.length === 0" class="space-y-4">
                    <div class="animate-pulse flex items-center gap-4">
                        <div class="w-20 h-20 bg-slate-100 rounded-2xl flex-shrink-0"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-3 bg-slate-100 rounded w-3/4"></div>
                            <div class="h-3 bg-slate-100 rounded w-1/2"></div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div x-show="!loading && data.count === 0" class="py-16 text-center space-y-4">
                    <div class="w-16 h-16 rounded-full bg-slate-100 mx-auto flex items-center justify-center text-2xl text-slate-400">
                        🛍️
                    </div>
                    <div>
                        <h4 class="font-bold text-base text-slate-900">Your bag is empty</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">Explore our curated collections of precision audio, mechanical peripherals, and lifestyle essentials.</p>
                    </div>
                    <a href="{{ route('products.index') }}" 
                       @click="closeDrawer()"
                       class="inline-block py-2.5 px-6 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider transition active:scale-95 shadow">
                        Explore Catalog &rarr;
                    </a>
                </div>

                <!-- Items Loop -->
                <template x-for="item in data.items" :key="item.id">
                    <div class="pt-4 first:pt-0 flex items-center gap-3.5 group">
                        <!-- Product Visual -->
                        <a :href="'{{ url('/product') }}/' + item.slug" 
                           class="w-20 h-20 min-w-[5rem] rounded-2xl bg-slate-50 border border-slate-100 p-1.5 flex items-center justify-center flex-shrink-0 overflow-hidden group-hover:border-indigo-100 transition shadow-xs">
                            <img :src="item.image" 
                                 :alt="item.name" 
                                 referrerpolicy="no-referrer"
                                 class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-300">
                        </a>

                        <!-- Details & Inline Controls -->
                        <div class="flex-1 min-w-0 space-y-1">
                            <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block truncate" x-text="item.category"></span>
                            <h4 class="font-bold text-xs text-slate-900 hover:text-indigo-600 transition-colors truncate">
                                <a :href="'{{ url('/product') }}/' + item.slug" x-text="item.name"></a>
                            </h4>
                            
                            <div class="text-xs font-black text-slate-900 pt-0.5" x-text="$store.currency ? $store.currency.format(item.price) : item.price_formatted"></div>

                            <!-- Stepper & Remove Action -->
                            <div class="flex items-center justify-between pt-1.5">
                                <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-slate-50/50">
                                    <button type="button" 
                                            @click="updateQty(item.id, item.quantity - 1)" 
                                            class="w-7 h-7 flex items-center justify-center text-slate-500 hover:bg-slate-200 text-xs font-bold transition">
                                        -
                                    </button>
                                    <span class="w-8 text-center text-xs font-bold text-slate-900 font-mono" x-text="item.quantity"></span>
                                    <button type="button" 
                                            @click="updateQty(item.id, item.quantity + 1)" 
                                            class="w-7 h-7 flex items-center justify-center text-slate-500 hover:bg-slate-200 text-xs font-bold transition">
                                        +
                                    </button>
                                </div>

                                <button type="button" 
                                        @click="removeItem(item.id)" 
                                        class="text-[11px] text-slate-400 hover:text-rose-600 font-medium transition flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span>Remove</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

            </div>

            <!-- Drawer Footer (Subtotal & Checkout Actions) -->
            <div class="p-5 sm:p-6 border-t border-[#DDE4EC] bg-[#F0F3F7]/90 space-y-3" x-show="data.count > 0">
                
                <!-- Applied Promo Badge -->
                <template x-if="data.coupon_code">
                    <div class="flex items-center justify-between text-xs text-[#1B0E24] font-semibold bg-[#FEF8E7] px-3 py-2 rounded-xl border border-[#FDEEC4]">
                        <div class="flex items-center gap-1.5">
                            <span>🎟️</span>
                            <span>Promo: <strong class="font-mono text-[#6B2A88]" x-text="data.coupon_code"></strong></span>
                            <button type="button" @click="removeCoupon()" class="text-rose-500 hover:text-rose-700 text-[10px] ml-1 font-bold underline">Remove</button>
                        </div>
                        <span class="font-mono font-bold text-[#F59E0B]" x-text="data.discount_formatted"></span>
                    </div>
                </template>

                <!-- Optional Voucher Code Input -->
                <template x-if="!data.coupon_code">
                    <div x-data="{ showInput: false }">
                        <button type="button" @click="showInput = !showInput" class="text-xs text-[#6B2A88] hover:text-[#F59E0B] font-semibold flex items-center gap-1 transition">
                            <span x-show="!showInput">+ Have a promo code?</span>
                            <span x-show="showInput">&minus; Close coupon input</span>
                        </button>
                        <div x-show="showInput" x-transition class="pt-2">
                            <div class="flex gap-1.5">
                                <input type="text" 
                                       x-model="couponInput" 
                                       @keydown.enter.prevent="applyCoupon()" 
                                       placeholder="e.g. WELCOME10" 
                                       class="flex-1 uppercase font-mono bg-white border border-[#DDE4EC] rounded-xl px-3 py-1.5 text-xs text-[#1B0E24] placeholder-slate-400 focus:outline-none focus:border-[#F59E0B]">
                                <button type="button" 
                                        @click="applyCoupon()" 
                                        :disabled="couponLoading" 
                                        class="px-3.5 py-1.5 rounded-xl bg-[#1B0E24] hover:bg-[#F59E0B] hover:text-[#1B0E24] text-white font-bold text-xs transition disabled:opacity-50">
                                    <span x-show="!couponLoading">Apply</span>
                                    <span x-show="couponLoading">...</span>
                                </button>
                            </div>
                            <p x-show="couponError" x-text="couponError" class="text-[11px] text-rose-500 mt-1 font-medium"></p>
                        </div>
                    </div>
                </template>

                <div class="flex items-center justify-between text-sm pt-1">
                    <span class="text-slate-600 font-medium">Estimated Total</span>
                    <span class="text-lg font-black text-[#1B0E24]" x-text="$store.currency ? $store.currency.format(data.final_total_raw !== undefined ? data.final_total_raw : data.subtotal_raw) : (data.final_total || data.subtotal)"></span>
                </div>
                <p class="text-[11px] text-slate-400">Shipping and tax calculated during final checkout.</p>

                <div class="space-y-2 pt-2">
                    <a href="{{ route('checkout.index') }}" 
                       class="w-full py-3.5 px-6 rounded-full bg-[#1B0E24] hover:bg-[#F59E0B] hover:text-[#1B0E24] text-white font-black text-xs uppercase tracking-wider shadow-md hover:shadow-[#F59E0B]/20 flex items-center justify-center gap-2 transition active:scale-95">
                        <span>Proceed to Checkout</span>
                        <span>&rarr;</span>
                    </a>

                    <a href="{{ route('cart.index') }}" 
                       @click="closeDrawer()"
                       class="w-full py-2.5 px-4 rounded-full bg-white hover:bg-[#EAEFF4] border border-[#DDE4EC] text-[#1B0E24] font-bold text-xs text-center block transition">
                        View Full Bag & Apply Coupon
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
function cartDrawerHandler() {
    return {
        isOpen: false,
        loading: false,
        couponInput: '',
        couponLoading: false,
        couponError: '',
        data: {
            items: [],
            count: 0,
            subtotal: '$0.00',
            subtotal_raw: 0,
            free_shipping_threshold: 99,
            free_shipping_progress: 0,
            remaining_for_free_shipping: '$99.00',
            remaining_for_free_shipping_raw: 99,
            unlocked_free_shipping: false,
            coupon_code: null,
            discount_amount: 0,
            discount_formatted: '-$0.00',
            final_total: '$0.00',
            final_total_raw: 0
        },
        init() {
            this.fetchDrawerData();
        },
        openDrawer() {
            this.isOpen = true;
            this.fetchDrawerData();
            document.body.classList.add('overflow-hidden');
        },
        closeDrawer() {
            this.isOpen = false;
            document.body.classList.remove('overflow-hidden');
        },
        async fetchDrawerData() {
            try {
                this.loading = true;
                let res = await fetch('{{ route('cart.drawer-data') }}', {
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                this.data = await res.json();
                this.loading = false;
            } catch (err) {
                this.loading = false;
            }
        },
        async updateQty(itemId, newQty) {
            if (newQty <= 0) {
                return this.removeItem(itemId);
            }
            try {
                let res = await fetch('{{ route('cart.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ id: itemId, quantity: newQty })
                });
                this.data = await res.json();
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: this.data.count } }));
            } catch (err) {}
        },
        async removeItem(itemId) {
            try {
                let res = await fetch('{{ url('/cart/remove') }}/' + itemId, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                this.data = await res.json();
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: this.data.count } }));
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Item removed from bag', type: 'info' } }));
            } catch (err) {}
        },
        async applyCoupon() {
            if (!this.couponInput.trim()) return;
            this.couponLoading = true;
            this.couponError = '';
            try {
                let res = await fetch('{{ route('cart.coupon.apply') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ code: this.couponInput.trim() })
                });
                let data = await res.json();
                this.couponLoading = false;
                if (data.success) {
                    this.couponInput = '';
                    this.data = data.drawer;
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                } else {
                    this.couponError = data.message || 'Invalid coupon code';
                }
            } catch (err) {
                this.couponLoading = false;
                this.couponError = 'Failed to apply coupon';
            }
        },
        async removeCoupon() {
            try {
                let res = await fetch('{{ route('cart.coupon.remove') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                let data = await res.json();
                if (data.drawer) {
                    this.data = data.drawer;
                } else {
                    this.fetchDrawerData();
                }
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Coupon removed', type: 'info' } }));
            } catch(e) {}
        }
    };
}
</script>
