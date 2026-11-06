@extends('layouts.app')

@section('title', 'Shopping Bag — UNCART')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
    formatMoney(val) {
        if (this.$store && this.$store.currency) {
            return this.$store.currency.format(val);
        }
        return '$' + Number(val).toFixed(2);
    },
    async updateQty(id, qty) {
        if (qty < 1) return;
        try {
            let res = await fetch('{{ route('cart.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id: id, quantity: qty })
            });
            window.location.reload();
        } catch(e) {}
    }
}">

    <div class="mb-8" data-reveal>
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Review Allocation</span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1">Shopping Bag</h1>
        <p class="text-xs text-slate-500 mt-1">Review your selections, apply promotions, and proceed to checkout</p>
    </div>

    @if($cartItems->isEmpty() && $savedItems->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-100 p-16 text-center space-y-4 shadow-sm" data-reveal>
            <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-600 mx-auto flex items-center justify-center text-3xl">
                🛍️
            </div>
            <h3 class="text-lg font-bold text-slate-900">Your shopping cart is currently empty</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Discover our curated collection of lifestyle essentials, electronics, fashion, and gaming gear.
            </p>
            <div class="pt-2">
                <a href="{{ route('products.index') }}" class="py-3 px-8 rounded-full bg-[#1B0E24] hover:bg-[#F59E0B] hover:text-[#1B0E24] text-white font-black text-xs uppercase tracking-wider shadow-md transition inline-block active:scale-95">
                    Start Shopping &rarr;
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <!-- Left 2 Cols: Cart Items List -->
            <div class="lg:col-span-2 space-y-6" data-reveal>
                
                <!-- Free Shipping Progress Meter -->
                <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm">
                    @php
                        $progress = min(100, ($subtotal / $freeShippingThreshold) * 100);
                        $remaining = max(0, $freeShippingThreshold - $subtotal);
                    @endphp
                    <div class="flex items-center justify-between text-xs mb-2">
                        @if($remaining > 0)
                            <span class="text-slate-600">
                                Add <strong class="text-indigo-600 font-bold" x-text="formatMoney({{ $remaining }})">${{ number_format($remaining, 2) }}</strong> more to unlock <span class="font-bold text-emerald-600">Free Shipping</span>!
                            </span>
                        @else
                            <span class="font-bold text-emerald-600 flex items-center gap-1.5">
                                <span>🎉</span> You've unlocked Free Shipping!
                            </span>
                        @endif
                        <span class="text-slate-500 font-bold">{{ round($progress) }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-[#F59E0B] to-[#D97706] h-2 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                <!-- Cart Items Card -->
                <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm divide-y divide-slate-100">
                    @foreach($cartItems as $item)
                        <div class="p-5 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 group">
                            
                            <!-- Item Visual & Info -->
                            <div class="flex items-center gap-4">
                                <a href="{{ route('products.show', $item->product->slug) }}" class="w-20 h-20 rounded-2xl overflow-hidden bg-slate-50 flex-shrink-0 border border-slate-100 p-2 flex items-center justify-center">
                                    <img src="{{ $item->product->primary_image }}" 
                                         alt="{{ $item->product->name }}" 
                                         referrerpolicy="no-referrer"
                                         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=200';"
                                         class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300">
                                </a>

                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider">{{ $item->product->vendor->shop_name ?? 'UNCART Direct' }}</span>
                                    <h3 class="font-bold text-sm text-slate-900 hover:text-indigo-600 transition-colors">
                                        <a href="{{ route('products.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                                    </h3>
                                    
                                    <div class="text-xs font-black text-slate-900" x-text="formatMoney({{ (float)$item->product->price }})">
                                        ${{ number_format($item->product->price, 2) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Quantity Controls & Subtotal -->
                            <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-3 sm:pt-0 border-t border-slate-100 sm:border-0">
                                
                                <!-- Quantity Counter -->
                                <div class="flex items-center rounded-full bg-slate-100 border border-slate-200 p-1">
                                    <button type="button" @click="updateQty({{ $item->id }}, {{ $item->quantity - 1 }})" class="w-7 h-7 rounded-full text-slate-600 hover:text-slate-900 hover:bg-white flex items-center justify-center font-bold text-xs transition active:scale-90">-</button>
                                    <span class="w-8 text-center font-bold text-xs text-slate-900">{{ $item->quantity }}</span>
                                    <button type="button" @click="updateQty({{ $item->id }}, {{ $item->quantity + 1 }})" class="w-7 h-7 rounded-full text-slate-600 hover:text-slate-900 hover:bg-white flex items-center justify-center font-bold text-xs transition active:scale-90">+</button>
                                </div>

                                <!-- Subtotal -->
                                <div class="text-right">
                                    <div class="text-sm font-black text-slate-900" x-text="formatMoney({{ (float)($item->product->price * $item->quantity) }})">
                                        ${{ number_format($item->product->price * $item->quantity, 2) }}
                                    </div>
                                </div>

                                <!-- Actions Menu -->
                                <div class="flex items-center gap-1">
                                    <!-- Save for Later -->
                                    <form action="{{ route('cart.save-for-later', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="Save for Later" class="p-2 rounded-xl text-slate-400 hover:text-indigo-600 hover:bg-slate-100 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                        </button>
                                    </form>

                                    <!-- Remove -->
                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="Remove item" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-slate-100 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Saved For Later Items -->
                @if($savedItems->isNotEmpty())
                    <div class="mt-8 space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Saved For Later ({{ $savedItems->count() }})</h3>
                        <div class="bg-white rounded-3xl border border-slate-100 p-5 divide-y divide-slate-100 shadow-sm">
                            @foreach($savedItems as $saved)
                                <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $saved->product->primary_image }}" class="w-12 h-12 rounded-xl object-cover bg-slate-50 border border-slate-100">
                                        <div>
                                            <h4 class="font-bold text-xs text-slate-900">{{ $saved->product->name }}</h4>
                                            <div class="text-xs text-slate-700 font-bold" x-text="formatMoney({{ (float)$saved->product->price }})">${{ number_format($saved->product->price, 2) }}</div>
                                        </div>
                                    </div>
                                    <form action="{{ route('cart.move-to-cart', $saved->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-full bg-slate-100 hover:bg-indigo-600 text-slate-700 hover:text-white text-xs font-semibold transition">
                                            Move to Cart
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right 1 Col: Summary & Coupon Box -->
            <div class="space-y-6" data-reveal>
                
                <!-- Order Summary Card -->
                <div class="bg-white rounded-3xl border border-slate-100 p-6 sm:p-7 shadow-sm space-y-5">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100">
                        Order Summary
                    </h3>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal</span>
                            <span class="font-bold text-slate-900" x-text="formatMoney({{ (float)$subtotal }})">${{ number_format($subtotal, 2) }}</span>
                        </div>

                        @if($coupon)
                            <div class="flex justify-between text-emerald-600 font-medium">
                                <span class="flex items-center gap-1">
                                    <span>Discount ({{ $coupon->code }})</span>
                                    <form action="{{ route('cart.coupon.remove') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="redirect_to" value="{{ route('cart.index') }}">
                                        <button type="submit" class="text-rose-500 hover:underline text-[10px] ml-1">&times; Remove</button>
                                    </form>
                                </span>
                                <span x-text="'-' + formatMoney({{ (float)$discountAmount }})">-${{ number_format($discountAmount, 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between text-slate-600">
                            <span>Estimated Shipping</span>
                            @if($shippingAmount == 0)
                                <span class="text-emerald-600 font-bold uppercase text-[10px]">Free</span>
                            @else
                                <span class="font-bold text-slate-900" x-text="formatMoney({{ (float)$shippingAmount }})">${{ number_format($shippingAmount, 2) }}</span>
                            @endif
                        </div>

                        <div class="flex justify-between text-slate-600">
                            <span>Estimated Tax</span>
                            <span class="font-bold text-slate-900" x-text="formatMoney({{ (float)$taxAmount }})">${{ number_format($taxAmount, 2) }}</span>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-between items-baseline text-base font-black text-slate-900">
                            <span>Total</span>
                            <span class="text-xl text-[#1B0E24] font-black" x-text="formatMoney({{ (float)$totalAmount }})">${{ number_format($totalAmount, 2) }}</span>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <a href="{{ route('checkout.index') }}" class="w-full py-3.5 px-6 rounded-full bg-[#1B0E24] hover:bg-[#F59E0B] hover:text-[#1B0E24] text-white font-black text-xs uppercase tracking-wider shadow-md text-center block transition-all active:scale-95">
                        Proceed to Checkout &rarr;
                    </a>

                    <!-- Guarantee Badges -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-around text-[10px] text-slate-400">
                        <span>🔒 256-Bit SSL</span>
                        <span>•</span>
                        <span>🔄 7-Day Returns</span>
                        <span>•</span>
                        <span>⚡ Fast Dispatch</span>
                    </div>
                </div>

                <!-- Coupon Box -->
                <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm space-y-3">
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Promotional Voucher</h4>
                    
                    <form action="{{ route('cart.coupon.apply') }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ route('cart.index') }}">
                        <input type="text" 
                               name="code" 
                               value="{{ $coupon->code ?? '' }}" 
                               placeholder="e.g. WELCOME10" 
                               class="flex-1 uppercase bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#F59E0B]">
                        <button type="submit" class="py-2 px-4 rounded-xl bg-[#1B0E24] hover:bg-[#F59E0B] hover:text-[#1B0E24] text-white font-bold text-xs transition active:scale-95">
                            Apply
                        </button>
                    </form>
                </div>

            </div>

        </div>
    @endif
</div>
@endsection
