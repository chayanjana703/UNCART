@extends('layouts.app')

@section('title', 'Checkout - ShopiCart')

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
        return Math.max(0, this.subtotal - this.discount + this.calculatedShipping + this.calculatedTax).toFixed(2);
    }
}">

    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Express Checkout</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Complete your delivery details and choose your preferred payment option</p>
    </div>

    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            
            <!-- Left 2 Cols: Form Sections -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Section 1: Shipping Address -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">1</span>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Shipping Destination</h3>
                        </div>
                        <span class="text-xs text-slate-400 font-mono">Step 1 of 3</span>
                    </div>

                    @if($savedAddresses->isNotEmpty())
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Saved Addresses</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                                @foreach($savedAddresses as $addr)
                                    <div class="p-3 rounded-2xl border cursor-pointer transition-all text-xs"
                                         :class="savedAddrSelected ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/30' : 'border-slate-200 dark:border-slate-700'"
                                         @click="
                                            savedAddrSelected = true;
                                            $refs.s_name.value = '{{ addslashes($addr->name) }}';
                                            $refs.s_phone.value = '{{ addslashes($addr->phone) }}';
                                            $refs.s_line1.value = '{{ addslashes($addr->address_line1) }}';
                                            $refs.s_city.value = '{{ addslashes($addr->city) }}';
                                            $refs.s_state.value = '{{ addslashes($addr->state) }}';
                                            $refs.s_zip.value = '{{ addslashes($addr->postal_code) }}';
                                            $refs.s_country.value = '{{ addslashes($addr->country) }}';
                                         ">
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $addr->name }}</div>
                                        <div class="text-slate-500 mt-0.5 line-clamp-1">{{ $addr->address_line1 }}, {{ $addr->city }}</div>
                                        <div class="text-[10px] text-slate-400 mt-1 font-mono">{{ $addr->phone }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Full Recipient Name</label>
                            <input type="text" name="shipping_name" x-ref="s_name" required value="{{ old('shipping_name', $defaultShippingAddress->name ?? (Auth::user()->name ?? 'Chayan Jana')) }}"
                                   class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Phone Number</label>
                            <input type="tel" name="shipping_phone" x-ref="s_phone" required value="{{ old('shipping_phone', $defaultShippingAddress->phone ?? (Auth::user()->phone ?? '+91 98765 43210')) }}"
                                   class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Street Address</label>
                            <input type="text" name="shipping_address_line1" x-ref="s_line1" required value="{{ old('shipping_address_line1', $defaultShippingAddress->address_line1 ?? 'Suite 404, Tech Park Boulevard') }}"
                                   class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Apartment, Suite, Unit (Optional)</label>
                            <input type="text" name="shipping_address_line2" value="{{ old('shipping_address_line2', $defaultShippingAddress->address_line2 ?? 'Sector V') }}"
                                   class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">City</label>
                            <input type="text" name="shipping_city" x-ref="s_city" required value="{{ old('shipping_city', $defaultShippingAddress->city ?? 'Kolkata') }}"
                                   class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">State / Province</label>
                            <input type="text" name="shipping_state" x-ref="s_state" required value="{{ old('shipping_state', $defaultShippingAddress->state ?? 'West Bengal') }}"
                                   class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Postal / Zip Code</label>
                            <input type="text" name="shipping_postal_code" x-ref="s_zip" required value="{{ old('shipping_postal_code', $defaultShippingAddress->postal_code ?? '700091') }}"
                                   class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Country</label>
                            <input type="text" name="shipping_country" x-ref="s_country" required value="{{ old('shipping_country', $defaultShippingAddress->country ?? 'India') }}"
                                   class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Billing Address Toggle -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-800 dark:text-slate-200">
                            <input type="checkbox" name="billing_same_as_shipping" value="1" x-model="sameBilling" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span>Billing address is identical to shipping address</span>
                        </label>
                    </div>
                </div>

                <!-- Section 2: Delivery Speed / Shipping Method -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">2</span>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Shipping Method</h3>
                        </div>
                        <span class="text-xs text-slate-400 font-mono">Step 2 of 3</span>
                    </div>

                    <div class="space-y-3">
                        <label class="p-4 rounded-2xl border flex items-center justify-between cursor-pointer transition-all"
                               :class="shippingMethod === 'standard' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/30' : 'border-slate-200 dark:border-slate-800'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="shipping_method" value="standard" x-model="shippingMethod" class="text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">Standard Tracked Delivery (3-5 Business Days)</h4>
                                    <p class="text-[11px] text-slate-500">Reliable ground shipping with carrier checkpoint updates</p>
                                </div>
                            </div>
                            <span class="text-xs font-black text-slate-900 dark:text-white">
                                {{ $shippingAmount == 0 ? 'FREE' : '$' . number_format($shippingAmount, 2) }}
                            </span>
                        </label>

                        <label class="p-4 rounded-2xl border flex items-center justify-between cursor-pointer transition-all"
                               :class="shippingMethod === 'express' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/30' : 'border-slate-200 dark:border-slate-800'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="shipping_method" value="express" x-model="shippingMethod" class="text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">Express Air Courier (1-2 Business Days)</h4>
                                    <p class="text-[11px] text-slate-500">Priority expedited dispatch with signature on delivery</p>
                                </div>
                            </div>
                            <span class="text-xs font-black text-slate-900 dark:text-white">$25.00</span>
                        </label>

                        <label class="p-4 rounded-2xl border flex items-center justify-between cursor-pointer transition-all"
                               :class="shippingMethod === 'overnight' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/30' : 'border-slate-200 dark:border-slate-800'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="shipping_method" value="overnight" x-model="shippingMethod" class="text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">Priority Overnight (Next Morning Delivery)</h4>
                                    <p class="text-[11px] text-slate-500">Guaranteed next morning delivery before 10:30 AM</p>
                                </div>
                            </div>
                            <span class="text-xs font-black text-slate-900 dark:text-white">$45.00</span>
                        </label>
                    </div>
                </div>

                <!-- Section 3: Payment Method -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">3</span>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Payment Method</h3>
                        </div>
                        <span class="text-xs text-slate-400 font-mono">Step 3 of 3</span>
                    </div>

                    <!-- Payment Option Tabs -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <label class="p-3 rounded-2xl border flex flex-col items-center cursor-pointer transition-all text-center"
                               :class="paymentMethod === 'card' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 font-bold' : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400'">
                            <input type="radio" name="payment_method" value="card" x-model="paymentMethod" class="sr-only">
                            <span class="text-xl">💳</span>
                            <span class="text-xs mt-1">Credit / Debit</span>
                        </label>

                        <label class="p-3 rounded-2xl border flex flex-col items-center cursor-pointer transition-all text-center"
                               :class="paymentMethod === 'upi' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 font-bold' : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400'">
                            <input type="radio" name="payment_method" value="upi" x-model="paymentMethod" class="sr-only">
                            <span class="text-xl">⚡</span>
                            <span class="text-xs mt-1">Instant UPI</span>
                        </label>

                        <label class="p-3 rounded-2xl border flex flex-col items-center cursor-pointer transition-all text-center"
                               :class="paymentMethod === 'wallet' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 font-bold' : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400'">
                            <input type="radio" name="payment_method" value="wallet" x-model="paymentMethod" class="sr-only">
                            <span class="text-xl">👛</span>
                            <span class="text-xs mt-1">Digital Wallet</span>
                        </label>

                        <label class="p-3 rounded-2xl border flex flex-col items-center cursor-pointer transition-all text-center"
                               :class="paymentMethod === 'cod' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 font-bold' : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400'">
                            <input type="radio" name="payment_method" value="cod" x-model="paymentMethod" class="sr-only">
                            <span class="text-xl">💵</span>
                            <span class="text-xs mt-1">Cash on Delivery</span>
                        </label>
                    </div>

                    <!-- Payment Sub-Form (Card / UPI Mock) -->
                    <div x-show="paymentMethod === 'card'" class="space-y-4 pt-2">
                        <div class="p-4 rounded-2xl bg-gradient-to-r from-slate-900 to-indigo-950 text-white shadow-xl space-y-4 max-w-sm">
                            <div class="flex items-center justify-between text-xs font-mono">
                                <span>ShopiCart Secure Pay</span>
                                <span>VISA / MC</span>
                            </div>
                            <div class="font-mono text-lg tracking-widest font-bold">•••• •••• •••• 4242</div>
                            <div class="flex items-center justify-between text-[11px] font-mono text-slate-300">
                                <span>CHAYAN JANA</span>
                                <span>12/28</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="col-span-2">
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Card Number</label>
                                <input type="text" placeholder="4242 4242 4242 4242" value="4242 •••• •••• 4242" readonly
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white font-mono">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Expiry Date</label>
                                <input type="text" value="12/28" readonly
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white font-mono">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">CVC Code</label>
                                <input type="text" value="882" readonly
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white font-mono">
                            </div>
                        </div>
                    </div>

                    <div x-show="paymentMethod === 'upi'" class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 text-xs space-y-2">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Virtual Payment Address (VPA / UPI ID)</label>
                        <input type="text" placeholder="chayan@okaxis" value="chayan@okaxis"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3.5 py-2.5 text-slate-900 dark:text-white">
                        <p class="text-[11px] text-slate-400">A payment prompt will be dispatched to your BHIM / GPay / PhonePe app.</p>
                    </div>

                    <!-- Delivery Instructions -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Special Delivery Instructions (Optional)</label>
                        <input type="text" name="notes" placeholder="e.g. Leave with security guard at front gate..."
                               class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3.5 py-2.5 text-slate-900 dark:text-white">
                    </div>
                </div>

            </div>

            <!-- Right Col: Review & Submit -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-100 dark:border-slate-800">
                        Order Review ({{ $cartItems->sum('quantity') }} Items)
                    </h4>

                    <!-- Mini Items List -->
                    <div class="space-y-3 max-h-48 overflow-y-auto pr-1 text-xs">
                        @foreach($cartItems as $it)
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-md bg-slate-100 dark:bg-slate-800 font-mono text-[10px] font-bold flex items-center justify-center">
                                        {{ $it->quantity }}x
                                    </span>
                                    <span class="font-medium text-slate-800 dark:text-slate-200 truncate max-w-[140px]">{{ $it->product->name }}</span>
                                </div>
                                <span class="font-bold text-slate-900 dark:text-white">${{ number_format($it->subtotal, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Price Calculations -->
                    <div class="space-y-2.5 text-xs pt-3 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                            <span>Subtotal</span>
                            <span class="font-bold text-slate-900 dark:text-white">${{ number_format($subtotal, 2) }}</span>
                        </div>

                        @if($discountAmount > 0)
                            <div class="flex items-center justify-between text-emerald-600 font-medium">
                                <span>Discount</span>
                                <span>-${{ number_format($discountAmount, 2) }}</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                            <span>Shipping</span>
                            <span class="font-bold text-slate-900 dark:text-white" x-text="calculatedShipping == 0 ? 'FREE' : '$' + calculatedShipping.toFixed(2)"></span>
                        </div>

                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                            <span>Estimated Tax</span>
                            <span class="font-bold text-slate-900 dark:text-white" x-text="'$' + calculatedTax.toFixed(2)"></span>
                        </div>

                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-baseline justify-between text-base">
                            <span class="font-bold text-slate-900 dark:text-white">Total Amount</span>
                            <span class="text-2xl font-black text-slate-900 dark:text-white" x-text="'$' + calculatedTotal">
                                ${{ number_format($totalAmount, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <button type="submit" 
                            class="w-full py-4 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-xl shadow-indigo-600/30 flex items-center justify-center gap-2 active:scale-[0.98]">
                        <span>Authorize & Place Order</span>
                        <span>&rarr;</span>
                    </button>

                    <div class="text-[11px] text-center text-slate-400 pt-1">
                        By placing this order you agree to our 30-day money-back guarantee terms and shipping policies.
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
