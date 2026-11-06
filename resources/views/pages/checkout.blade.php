@extends('layouts.app')

@section('title', 'Express Checkout — UNCART')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
    sameBilling: true,
    shippingMethod: 'standard',
    shippingCost: {{ $shippingAmount }},
    subtotal: {{ $subtotal }},
    discount: {{ $discountAmount }},
    taxRate: 0.085,
    paymentMethod: 'card',
    savedAddrSelected: {{ $defaultShippingAddress ? 'true' : 'false' }},
    
    // Live recalculations
    get calculatedShipping() {
        if (this.shippingMethod === 'express') return 25.00;
        if (this.shippingMethod === 'overnight') return 45.00;
        return {{ $shippingAmount }};
    },
    get calculatedTax() {
        return Math.round((this.subtotal - this.discount) * this.taxRate * 100) / 100;
    },
    get calculatedTotal() {
        return Math.max(0, this.subtotal - this.discount + this.calculatedShipping + this.calculatedTax);
    },
    formatMoney(val) {
        if (this.$store && this.$store.currency) {
            return this.$store.currency.format(val);
        }
        return '$' + Number(val).toFixed(2);
    }
}">

    <div class="mb-8" data-reveal>
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Checkout Process</span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1">Order Checkout</h1>
        <p class="text-xs text-slate-500 mt-1">Complete your delivery address and choose your payment rail</p>
    </div>

    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <input type="hidden" name="currency" :value="$store.currency ? $store.currency.current : 'USD'">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <!-- Left 2 Cols: Form Sections -->
            <div class="lg:col-span-2 space-y-6" data-reveal>
                
                <!-- Section 1: Shipping Address -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">1</span>
                            <h3 class="text-base font-bold text-slate-900">Delivery Address</h3>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">Step 1 of 3</span>
                    </div>

                    @if($savedAddresses->isNotEmpty())
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Saved Addresses on File</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                                @foreach($savedAddresses as $addr)
                                    <div class="p-3.5 rounded-2xl border cursor-pointer transition-all text-xs bg-slate-50"
                                         :class="savedAddrSelected ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-600' : 'border-slate-200'"
                                         @click="savedAddrSelected = true">
                                        <div class="font-bold text-slate-900">{{ $addr->full_name }}</div>
                                        <div class="text-slate-500 text-[11px]">{{ $addr->address_line1 }}</div>
                                        <div class="text-slate-500 text-[11px]">{{ $addr->city }}, {{ $addr->state }} {{ $addr->postal_code }}</div>
                                        <div class="text-[10px] text-indigo-600 font-semibold mt-1">{{ $addr->phone }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Manual Address Inputs -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                                <input type="text" name="shipping_name" value="{{ old('shipping_name', $defaultShippingAddress->full_name ?? Auth::user()->name ?? '') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                                <input type="text" name="shipping_phone" value="{{ old('shipping_phone', $defaultShippingAddress->phone ?? Auth::user()->phone ?? '') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Street Address</label>
                            <input type="text" name="shipping_address_line1" value="{{ old('shipping_address_line1', $defaultShippingAddress->address_line1 ?? '') }}" placeholder="123 Innovation Drive, Suite 400" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">City</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city', $defaultShippingAddress->city ?? '') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">State / Province</label>
                                <input type="text" name="shipping_state" value="{{ old('shipping_state', $defaultShippingAddress->state ?? '') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Postal Code</label>
                                <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code', $defaultShippingAddress->postal_code ?? '') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                            </div>
                        </div>

                        <div>
                            <input type="hidden" name="shipping_country" value="United States">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Delivery Speed Method -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">2</span>
                            <h3 class="text-base font-bold text-slate-900">Shipping Method</h3>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">Step 2 of 3</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <label class="p-4 rounded-2xl border cursor-pointer transition flex items-center justify-between bg-slate-50"
                               :class="shippingMethod === 'standard' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-600' : 'border-slate-200'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="shipping_method" value="standard" x-model="shippingMethod" class="text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <div>
                                    <div class="font-bold text-slate-900 text-xs">Standard Delivery</div>
                                    <div class="text-[11px] text-slate-500">3-5 business days</div>
                                </div>
                            </div>
                            <span class="font-bold text-xs" :class="calculatedShipping === 0 ? 'text-emerald-600' : 'text-slate-900'"
                                  x-text="calculatedShipping === 0 ? 'FREE' : formatMoney(calculatedShipping)">
                                {{ $shippingAmount == 0 ? 'FREE' : '$' . number_format($shippingAmount, 2) }}
                            </span>
                        </label>

                        <label class="p-4 rounded-2xl border cursor-pointer transition flex items-center justify-between bg-slate-50"
                               :class="shippingMethod === 'express' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-600' : 'border-slate-200'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="shipping_method" value="express" x-model="shippingMethod" class="text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <div>
                                    <div class="font-bold text-slate-900 text-xs">Priority Air Express</div>
                                    <div class="text-[11px] text-slate-500">1-2 business days</div>
                                </div>
                            </div>
                            <span class="font-bold text-xs text-indigo-600" x-text="formatMoney(25)">$25.00</span>
                        </label>

                        <label class="p-4 rounded-2xl border cursor-pointer transition flex items-center justify-between bg-slate-50"
                               :class="shippingMethod === 'overnight' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-600' : 'border-slate-200'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="shipping_method" value="overnight" x-model="shippingMethod" class="text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <div>
                                    <div class="font-bold text-slate-900 text-xs">Overnight Courier</div>
                                    <div class="text-[11px] text-slate-500">Next-morning arrival</div>
                                </div>
                            </div>
                            <span class="font-bold text-xs text-indigo-600" x-text="formatMoney(45)">$45.00</span>
                        </label>
                    </div>
                </div>

                <!-- Section 3: Payment Options -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">3</span>
                            <h3 class="text-base font-bold text-slate-900">Payment Option</h3>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">Step 3 of 3</span>
                    </div>

                    <!-- Payment Rail Tabs -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button type="button" 
                                @click="paymentMethod = 'card'" 
                                class="p-3 rounded-2xl border text-xs font-bold transition flex flex-col items-center gap-1.5 active:scale-95"
                                :class="paymentMethod === 'card' ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                            <span>💳 Card</span>
                        </button>

                        <button type="button" 
                                @click="paymentMethod = 'upi'" 
                                class="p-3 rounded-2xl border text-xs font-bold transition flex flex-col items-center gap-1.5 active:scale-95"
                                :class="paymentMethod === 'upi' ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                            <span>📱 UPI / QR</span>
                        </button>

                        <button type="button" 
                                @click="paymentMethod = 'wallet'" 
                                class="p-3 rounded-2xl border text-xs font-bold transition flex flex-col items-center gap-1.5 active:scale-95"
                                :class="paymentMethod === 'wallet' ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                            <span>💼 Wallet</span>
                        </button>

                        <button type="button" 
                                @click="paymentMethod = 'cod'" 
                                class="p-3 rounded-2xl border text-xs font-bold transition flex flex-col items-center gap-1.5 active:scale-95"
                                :class="paymentMethod === 'cod' ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                            <span>💵 Cash COD</span>
                        </button>
                    </div>

                    <input type="hidden" name="payment_method" :value="paymentMethod">

                    <!-- Card Form Simulation -->
                    <div x-show="paymentMethod === 'card'" class="space-y-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Card Number</label>
                            <input type="text" placeholder="4242 •••• •••• 4242" value="4242 4242 4242 4242" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Expiry</label>
                                <input type="text" placeholder="MM/YY" value="12/28" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">CVV / CVC</label>
                                <input type="password" placeholder="•••" value="888" maxlength="4" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 font-mono">
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400">⚡ Test card auto-filled for instant verification. PCI-DSS Level 1 compliant.</p>
                    </div>

                    <div x-show="paymentMethod === 'upi'" class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                        <label class="block text-slate-700 font-semibold mb-1">Virtual Payment Address (VPA)</label>
                        <input type="text" placeholder="username@upi" value="customer@upi" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900">
                    </div>

                    <div x-show="paymentMethod === 'wallet'" class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                        <p>Available balance: <strong class="text-emerald-600 font-bold" x-text="formatMoney(1450)">$1,450.00</strong> (Instant 1-Click checkout)</p>
                    </div>

                    <div x-show="paymentMethod === 'cod'" class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                        <p>💵 Pay with cash upon delivery. Please have the exact payment ready for courier handover.</p>
                    </div>
                </div>

            </div>

            <!-- Right 1 Col: Order Summary & Placement -->
            <div class="space-y-6" data-reveal>
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-100 shadow-sm space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                            Order Breakdown
                        </h3>

                        <!-- Currency Selector Pill in Checkout -->
                        <div class="relative" x-data="{ openCheckoutCurr: false }">
                            <button type="button" @click="openCheckoutCurr = !openCheckoutCurr" @click.outside="openCheckoutCurr = false"
                                    class="flex items-center gap-1.5 font-mono font-bold text-slate-700 hover:text-indigo-600 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-full text-[11px] transition">
                                <span x-text="$store.currency && $store.currency.rates[$store.currency.current] ? $store.currency.rates[$store.currency.current].flag : '🇺🇸'">🇺🇸</span>
                                <span x-text="$store.currency ? $store.currency.current : 'USD'">USD</span>
                                <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openCheckoutCurr" x-transition.origin.top.right style="display: none;"
                                 class="absolute right-0 mt-1.5 w-36 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50 text-xs">
                                <div class="px-3 py-1 text-[9px] font-mono uppercase tracking-wider text-slate-400 font-bold border-b border-slate-100 mb-1">Currency</div>
                                <template x-for="(curr, code) in ($store.currency ? $store.currency.rates : {})" :key="code">
                                    <button type="button" 
                                            @click="$store.currency.set(code); openCheckoutCurr = false" 
                                            class="w-full text-left px-3 py-1.5 hover:bg-slate-50 flex items-center justify-between font-mono text-[11px]" 
                                            :class="$store.currency && $store.currency.current === code ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700'">
                                        <span class="flex items-center gap-1.5">
                                            <span x-text="curr.flag"></span>
                                            <span x-text="curr.name"></span>
                                        </span>
                                        <span x-show="$store.currency && $store.currency.current === code" class="text-indigo-600 text-xs font-bold">✓</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Mini Cart Item List -->
                    <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                        @foreach($cartItems as $item)
                            <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->product->primary_image }}" 
                                         alt="{{ $item->product->name }}"
                                         referrerpolicy="no-referrer"
                                         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=100';"
                                         class="w-10 h-10 rounded-xl object-contain bg-slate-50 border border-slate-100">
                                    <div class="truncate max-w-[140px]">
                                        <div class="font-bold text-slate-900 truncate">{{ $item->product->name }}</div>
                                        <div class="text-[11px] text-slate-400">
                                            Qty: {{ $item->quantity }} • 
                                            <span x-text="formatMoney({{ (float)$item->product->price }})">${{ number_format($item->product->price, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="font-bold text-slate-900" x-text="formatMoney({{ (float)($item->product->price * $item->quantity) }})">${{ number_format($item->product->price * $item->quantity, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Financials -->
                    <div class="pt-3 border-t border-slate-100 space-y-2 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-bold text-slate-900" x-text="formatMoney(subtotal)">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        @if($discountAmount > 0)
                            <div class="flex justify-between text-emerald-600 font-medium">
                                <span>Discount</span>
                                <span x-text="'-' + formatMoney(discount)">-${{ number_format($discountAmount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span>Shipping</span>
                            <span class="font-bold text-slate-900" x-text="calculatedShipping === 0 ? 'FREE' : formatMoney(calculatedShipping)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tax (8.5%)</span>
                            <span class="font-bold text-slate-900" x-text="formatMoney(calculatedTax)"></span>
                        </div>
                        <div class="pt-3 border-t border-slate-100 flex justify-between items-baseline text-base font-black text-slate-900">
                            <span>Total</span>
                            <span class="text-xl text-indigo-600 font-black" x-text="formatMoney(calculatedTotal)"></span>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 px-6 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider shadow-md transition-all active:scale-95 flex items-center justify-center gap-2">
                        <span>Place Order Now</span>
                        <span class="text-indigo-300 font-mono" x-text="'(' + formatMoney(calculatedTotal) + ')'"></span>
                        <span>&rarr;</span>
                    </button>

                    <div class="pt-2 text-center text-[10px] text-slate-400">
                        🔒 Encrypted by 256-Bit SSL Protection
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
