@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' - ShopiCart')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ cancelModal: false, returnModal: false }">

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Order #{{ $order->order_number }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $order->status_badge['bg'] }}">
                    {{ $order->status_badge['label'] }}
                </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('orders.track', $order->order_number) }}" class="py-2 px-4 rounded-xl bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider hover:bg-indigo-700 shadow-md">
                📍 Live Tracking
            </a>
            <a href="{{ route('orders.invoice', $order->order_number) }}" class="py-2 px-4 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold text-xs uppercase tracking-wider hover:bg-slate-300">
                🧾 Invoice
            </a>
        </div>
    </div>

    <!-- Return / Refund Status Alert if requested -->
    @if($order->return_status !== 'none' && !empty($order->return_status))
        <div class="mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 text-xs text-amber-800 dark:text-amber-300 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-base">🔄</span>
                <span>Return Status: <strong class="uppercase font-bold">{{ $order->return_status }}</strong> - Reason: {{ $order->return_reason }}</span>
            </div>
        </div>
    @endif

    <div class="space-y-6">
        
        <!-- Items List -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-sm p-6 space-y-4">
            <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-100 dark:border-slate-800">
                Purchased Merchandise
            </h3>
            
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($order->items as $item)
                    <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4 text-xs">
                        <div class="flex items-center gap-4">
                            <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 dark:border-slate-700">
                            <div>
                                <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $item->product_name }}</h4>
                                <p class="text-slate-500 mt-0.5">Sold by: {{ $item->vendor->shop_name ?? 'ShopiCart Maker' }}</p>
                                <span class="text-slate-400 text-[11px]">Qty: {{ $item->quantity }} &bull; ${{ number_format($item->price, 2) }}</span>
                            </div>
                        </div>
                        <span class="font-black text-sm text-slate-900 dark:text-white">${{ number_format($item->total, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Addresses & Financial Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Shipping Info -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3 text-xs">
                <h4 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] pb-2 border-b border-slate-100 dark:border-slate-800">
                    Delivery Details
                </h4>
                <div class="space-y-1 text-slate-600 dark:text-slate-300">
                    <p class="font-bold text-slate-900 dark:text-white">{{ $order->shipping_address['name'] ?? '' }}</p>
                    <p>{{ $order->shipping_address['address_line1'] ?? '' }}</p>
                    @if(!empty($order->shipping_address['address_line2']))
                        <p>{{ $order->shipping_address['address_line2'] }}</p>
                    @endif
                    <p>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}</p>
                    <p>{{ $order->shipping_address['country'] ?? '' }}</p>
                    <p class="text-[11px] text-slate-400 font-mono pt-1">Phone: {{ $order->shipping_address['phone'] ?? '' }}</p>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3 text-xs">
                <h4 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] pb-2 border-b border-slate-100 dark:border-slate-800">
                    Financial Summary
                </h4>
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                        <span>Items Subtotal</span>
                        <span class="font-bold text-slate-900 dark:text-white">${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex items-center justify-between text-emerald-600 font-medium">
                            <span>Promotional Discount ({{ $order->coupon_code }})</span>
                            <span>-${{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                        <span>Shipping & Handling</span>
                        <span class="font-bold text-slate-900 dark:text-white">${{ number_format($order->shipping_amount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                        <span>Sales Tax</span>
                        <span class="font-bold text-slate-900 dark:text-white">${{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-baseline justify-between text-sm">
                        <span class="font-bold text-slate-900 dark:text-white">Total Amount</span>
                        <span class="text-xl font-black text-slate-900 dark:text-white">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="text-[11px] text-slate-400 pt-1">
                        Paid via <strong class="uppercase font-mono text-slate-700 dark:text-slate-300">{{ $order->payment_method }}</strong> (Txn: {{ $order->transaction_id }})
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancellation & Return Actions -->
        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('orders.index') }}" class="text-xs text-slate-500 hover:text-slate-900 dark:hover:text-white">
                &larr; Back to Order History
            </a>

            <div class="flex items-center gap-3">
                @if($order->canBeCancelled())
                    <button @click="cancelModal = true" class="py-2 px-4 rounded-xl border border-rose-300 dark:border-rose-800 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-bold transition-colors">
                        Cancel Order
                    </button>
                @endif

                @if($order->canBeReturned())
                    <button @click="returnModal = true" class="py-2 px-4 rounded-xl border border-amber-300 dark:border-amber-800 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/40 text-xs font-bold transition-colors">
                        Request Return / Refund
                    </button>
                @endif
            </div>
        </div>

    </div>

    <!-- Cancel Modal -->
    <div x-show="cancelModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
        <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4" @click.outside="cancelModal = false">
            <h4 class="font-bold text-sm text-slate-900 dark:text-white">Cancel Order #{{ $order->order_number }}</h4>
            <p class="text-xs text-slate-500">Are you sure you want to cancel this order? Stock will be restored and any payment refunded.</p>
            
            <form action="{{ route('orders.cancel', $order->order_number) }}" method="POST" class="space-y-3">
                @csrf
                <textarea name="cancel_reason" placeholder="Reason for cancellation..." required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-3"></textarea>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" @click="cancelModal = false" class="px-4 py-2 text-xs text-slate-500">Back</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 text-white font-bold text-xs">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Return Modal -->
    <div x-show="returnModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
        <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4" @click.outside="returnModal = false">
            <h4 class="font-bold text-sm text-slate-900 dark:text-white">Return Request for Order #{{ $order->order_number }}</h4>
            <p class="text-xs text-slate-500">Please provide the reason for return according to our 30-day return policy.</p>
            
            <form action="{{ route('orders.return', $order->order_number) }}" method="POST" class="space-y-3">
                @csrf
                <textarea name="return_reason" placeholder="Reason for return request (e.g. Defective, Wrong Size, Changed mind)..." required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-3"></textarea>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" @click="returnModal = false" class="px-4 py-2 text-xs text-slate-500">Back</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 text-white font-bold text-xs">Submit Return Request</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
