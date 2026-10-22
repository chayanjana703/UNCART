@extends('layouts.app')

@section('title', 'Shopping Cart - ShopiCart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
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

    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Shopping Bag</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Review your selections, apply promotions, and proceed to checkout</p>
    </div>

    @if($cartItems->isEmpty() && $savedItems->isEmpty())
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-16 text-center space-y-4 shadow-sm">
            <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center text-3xl">
                🛍️
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Your shopping cart is currently empty</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Explore our curated catalog of precision audio gear, minimalist timepieces, and ergonomic workspace items.
            </p>
            <div class="pt-2">
                <a href="{{ route('products.index') }}" class="py-3 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all inline-block active:scale-95">
                    Start Browsing Catalog
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            
            <!-- Left 2 Cols: Cart Items List -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Free Shipping Progress Meter -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    @php
                        $progress = min(100, ($subtotal / $freeShippingThreshold) * 100);
                        $remaining = max(0, $freeShippingThreshold - $subtotal);
                    @endphp
                    <div class="flex items-center justify-between text-xs mb-2">
                        @if($remaining > 0)
                            <span class="font-medium text-slate-600 dark:text-slate-300">
                                Add <strong class="text-indigo-600 dark:text-indigo-400 font-bold">${{ number_format($remaining, 2) }}</strong> more to unlock <span class="font-bold text-emerald-600">Free Express Shipping</span>!
                            </span>
                        @else
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                <span>🎉</span> You've unlocked Free Express Shipping!
                            </span>
                        @endif
                        <span class="font-mono text-slate-400 font-bold">{{ round($progress) }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-indigo-500 to-emerald-400 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                <!-- Active Items Box -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 divide-y divide-slate-100 dark:divide-slate-800 overflow-hidden shadow-sm">
                    @foreach($cartItems as $item)
                        <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                            
                            <!-- Product Info -->
                            <div class="flex items-center gap-4 flex-1">
                                <img src="{{ $item->product->featured_image }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="w-20 h-20 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 flex-shrink-0">
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 font-mono">
                                        {{ $item->product->category->name }}
                                    </span>
                                    <h4 class="font-bold text-sm text-slate-900 dark:text-white leading-snug">
                                        <a href="{{ route('products.show', $item->product->slug) }}" class="hover:underline">
                                            {{ $item->product->name }}
                                        </a>
                                    </h4>
                                    @if($item->options)
                                        <div class="flex items-center gap-2 text-[11px] text-slate-500">
                                            @foreach($item->options as $optKey => $optVal)
                                                <span>{{ $optKey }}: <strong class="text-slate-700 dark:text-slate-300">{{ $optVal }}</strong></span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="text-xs font-bold text-slate-900 dark:text-white pt-1">
                                        ${{ number_format($item->product->current_price, 2) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Stepper & Subtotal -->
                            <div class="flex items-center justify-between w-full sm:w-auto gap-6">
                                <!-- Stepper -->
                                <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 p-1">
                                    <button @click="updateQty({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors font-bold text-xs">
                                        -
                                    </button>
                                    <span class="w-8 text-center font-bold text-xs text-slate-900 dark:text-white">
                                        {{ $item->quantity }}
                                    </span>
                                    <button @click="updateQty({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-colors font-bold text-xs">
                                        +
                                    </button>
                                </div>

                                <!-- Item Total -->
                                <div class="text-right min-w-[80px]">
                                    <span class="text-sm font-black text-slate-900 dark:text-white">
                                        ${{ number_format($item->subtotal, 2) }}
                                    </span>
                                </div>

                                <!-- Actions: Remove & Save for Later -->
                                <div class="flex items-center gap-1">
                                    <form action="{{ route('cart.save-for-later', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="Save for Later" class="p-2 text-slate-400 hover:text-indigo-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="Remove Item" class="p-2 text-slate-400 hover:text-rose-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Saved For Later Section -->
                @if($savedItems->isNotEmpty())
                    <div class="space-y-4 pt-4">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Saved for Later ({{ $savedItems->count() }})</h3>
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 divide-y divide-slate-100 dark:divide-slate-800 overflow-hidden shadow-sm">
                            @foreach($savedItems as $saved)
                                <div class="p-5 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $saved->product->featured_image }}" alt="{{ $saved->product->name }}" class="w-14 h-14 rounded-xl object-cover">
                                        <div>
                                            <h5 class="text-xs font-bold text-slate-900 dark:text-white">{{ $saved->product->name }}</h5>
                                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">${{ number_format($saved->product->current_price, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <form action="{{ route('cart.move-to-cart', $saved->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="py-1.5 px-3 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-bold text-xs hover:bg-indigo-100 transition-colors">
                                                Move to Cart
                                            </button>
                                        </form>
                                        <form action="{{ route('cart.remove', $saved->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs text-rose-500 hover:underline">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            <!-- Right Col: Summary & Coupon Box -->
            <div class="space-y-6">
                
                <!-- Coupon Code Entry -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Promotional Voucher</h4>
                    
                    @if($coupon)
                        <div class="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900 flex items-center justify-between">
                            <div>
                                <span class="font-mono font-bold text-xs text-emerald-800 dark:text-emerald-300">CODE: {{ $coupon->code }}</span>
                                <p class="text-[11px] text-emerald-600 dark:text-emerald-400">Coupon applied (-${{ number_format($discountAmount, 2) }})</p>
                            </div>
                            <form action="{{ route('cart.coupon.remove') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-rose-600 font-bold hover:underline">Remove</button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('cart.coupon.apply') }}" method="POST" class="flex gap-2">
                            @csrf
                            <input type="text" name="code" placeholder="e.g. WELCOME10" required
                                   class="flex-1 uppercase font-mono text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <button type="submit" class="py-2 px-4 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-950 font-bold text-xs uppercase tracking-wider hover:bg-slate-800 transition-colors">
                                Apply
                            </button>
                        </form>
                        <div class="text-[11px] text-slate-400">
                            Try test coupons: <strong class="text-indigo-600 dark:text-indigo-400 font-mono">WELCOME10</strong> (10% off) or <strong class="text-indigo-600 dark:text-indigo-400 font-mono">SUPER25</strong> ($25 off)
                        </div>
                    @endif
                </div>

                <!-- Order Calculations -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-100 dark:border-slate-800">
                        Order Summary
                    </h4>

                    <div class="space-y-2.5 text-xs">
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
                            <span>Estimated Shipping</span>
                            <span class="font-bold text-slate-900 dark:text-white">
                                {{ $shippingAmount == 0 ? 'FREE' : '$' . number_format($shippingAmount, 2) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                            <span>Estimated Sales Tax (8.5%)</span>
                            <span class="font-bold text-slate-900 dark:text-white">${{ number_format($taxAmount, 2) }}</span>
                        </div>

                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-baseline justify-between text-base">
                            <span class="font-bold text-slate-900 dark:text-white">Total</span>
                            <span class="text-2xl font-black text-slate-900 dark:text-white">
                                ${{ number_format($totalAmount, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-4">
                        @if($cartItems->isNotEmpty())
                            <a href="{{ route('checkout.index') }}" 
                               class="w-full py-4 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-xl shadow-indigo-600/30 flex items-center justify-center gap-2 active:scale-[0.98]">
                                <span>Proceed to Checkout</span>
                                <span>&rarr;</span>
                            </a>
                        @else
                            <button disabled class="w-full py-4 px-6 rounded-2xl bg-slate-300 dark:bg-slate-800 text-slate-500 font-bold text-xs uppercase tracking-wider cursor-not-allowed">
                                Cart is Empty
                            </button>
                        @endif
                    </div>

                    <div class="pt-2 text-center text-[11px] text-slate-400 flex items-center justify-center gap-2">
                        <span>🔒 Guaranteed Safe & Secure Checkout</span>
                    </div>
                </div>

            </div>

        </div>
    @endif

</div>
@endsection
