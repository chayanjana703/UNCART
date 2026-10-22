@extends('layouts.app')

@section('title', 'Order Confirmed - ShopiCart')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16" x-data x-init="
    // Fire celebratory confetti on page mount
    if (typeof confetti === 'function') {
        confetti({
            particleCount: 120,
            spread: 80,
            origin: { y: 0.6 }
        });
        setTimeout(() => {
            confetti({ particleCount: 60, angle: 60, spread: 55, origin: { x: 0 } });
            confetti({ particleCount: 60, angle: 120, spread: 55, origin: { x: 1 } });
        }, 400);
    }
">

    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-12 border border-slate-200/80 dark:border-slate-800 shadow-2xl text-center space-y-6">
        
        <!-- Animated Success Badge -->
        <div class="w-20 h-20 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 mx-auto flex items-center justify-center text-4xl shadow-lg shadow-emerald-500/20 animate-bounce">
            ✓
        </div>

        <div>
            <span class="text-xs font-bold tracking-widest text-emerald-600 dark:text-emerald-400 uppercase font-mono">Payment Authorized</span>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
                Thank You For Your Order!
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 max-w-md mx-auto">
                We've verified your order and dispatched confirmation details to your account. Our warehouse team is packaging your items.
            </p>
        </div>

        <!-- Order Meta Pill Strip -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 dark:bg-slate-850 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 text-left text-xs">
            <div>
                <span class="text-[10px] text-slate-400 uppercase font-mono font-bold block">Order Number</span>
                <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $order->order_number }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 uppercase font-mono font-bold block">Status</span>
                <span class="font-bold text-emerald-600 capitalize">{{ $order->order_status }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 uppercase font-mono font-bold block">Total Paid</span>
                <span class="font-bold text-slate-900 dark:text-white">${{ number_format($order->total_amount, 2) }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 uppercase font-mono font-bold block">Carrier</span>
                <span class="font-bold text-slate-900 dark:text-white truncate block">{{ $order->carrier_name ?: 'Express Direct' }}</span>
            </div>
        </div>

        <!-- Itemized Summary Preview -->
        <div class="text-left border-t border-slate-100 dark:border-slate-800 pt-6 space-y-3">
            <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Purchased Items</h4>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($order->items as $item)
                    <div class="py-3 flex items-center justify-between gap-4 text-xs">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 dark:border-slate-700">
                            <div>
                                <h5 class="font-bold text-slate-900 dark:text-white">{{ $item->product_name }}</h5>
                                <span class="text-[11px] text-slate-500">Qty: {{ $item->quantity }} &bull; ${{ number_format($item->price, 2) }}</span>
                            </div>
                        </div>
                        <span class="font-bold text-slate-900 dark:text-white">${{ number_format($item->total, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('orders.track', $order->order_number) }}" 
               class="w-full sm:w-auto py-3 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2 active:scale-95">
                <span>📍 Track Live Shipment</span>
            </a>

            <a href="{{ route('orders.invoice', $order->order_number) }}" 
               class="w-full sm:w-auto py-3 px-6 rounded-2xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-white font-bold text-xs uppercase tracking-wider transition-colors flex items-center justify-center gap-2">
                <span>🧾 View & Print Invoice</span>
            </a>

            <a href="{{ route('home') }}" class="w-full sm:w-auto py-3 px-5 text-xs text-slate-500 hover:text-slate-900 dark:hover:text-white font-medium">
                Back to Store
            </a>
        </div>

    </div>
</div>
@endsection
