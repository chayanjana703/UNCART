@extends('layouts.app')

@section('title', 'Order Confirmed — UNCART')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16" x-data x-init="
    if (typeof confetti === 'function') {
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 }
        });
        setTimeout(() => {
            confetti({ particleCount: 60, angle: 60, spread: 55, origin: { x: 0 } });
            confetti({ particleCount: 60, angle: 120, spread: 55, origin: { x: 1 } });
        }, 300);
    }
">

    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-100 shadow-sm text-center space-y-6" data-reveal>
        
        <!-- Animated Success Badge -->
        <div class="w-20 h-20 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 mx-auto flex items-center justify-center text-3xl shadow-sm animate-bounce">
            ✓
        </div>

        <div>
            <span class="text-xs font-bold tracking-widest text-emerald-600 uppercase">Payment Authorized & Confirmed</span>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1">
                Thank You for Your Order!
            </h1>
            <p class="text-xs text-slate-500 mt-2 max-w-md mx-auto leading-relaxed">
                We've received your order. Our merchant fulfillment partners have been alerted and your items will be dispatched promptly.
            </p>
        </div>

        <!-- Order Meta Pill Strip -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-left text-xs">
            <div>
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Order Number</span>
                <span class="font-bold text-indigo-600">#{{ $order->order_number }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Status</span>
                <span class="font-bold text-emerald-600 capitalize">{{ $order->order_status }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Settled</span>
                <span class="font-bold text-slate-900" x-text="$store.currency ? $store.currency.format({{ (float)$order->total_amount }}) : '${{ number_format($order->total_amount, 2) }}'">${{ number_format($order->total_amount, 2) }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Payment Method</span>
                <span class="font-bold text-slate-700 uppercase">{{ $order->payment_method }}</span>
            </div>
        </div>

        <!-- Order Item Summary -->
        <div class="text-left space-y-3 pt-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Items in This Order</h3>
            <div class="divide-y divide-slate-100 rounded-2xl bg-slate-50 border border-slate-100 p-4">
                @foreach($order->items as $item)
                    <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->product_image }}" class="w-10 h-10 rounded-xl object-contain bg-white border border-slate-100">
                            <div>
                                <h4 class="font-bold text-slate-900">{{ $item->product_name }}</h4>
                                <span class="text-slate-400 text-[11px]">Qty: {{ $item->quantity }} • <span x-text="$store.currency ? $store.currency.format({{ (float)$item->price }}) : '${{ number_format($item->price, 2) }}'">${{ number_format($item->price, 2) }}</span></span>
                            </div>
                        </div>
                        <div class="font-bold text-slate-900" x-text="$store.currency ? $store.currency.format({{ (float)$item->subtotal }}) : '${{ number_format($item->subtotal, 2) }}'">${{ number_format($item->subtotal, 2) }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- CTA Buttons -->
        <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('orders.track', $order->order_number) }}" class="w-full sm:w-auto py-3 px-6 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider shadow-md transition active:scale-95 flex items-center justify-center gap-2">
                <span>📍 Live Order Tracking</span>
            </a>
            <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="w-full sm:w-auto py-3 px-6 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider transition">
                <span>🧾 View Tax Invoice</span>
            </a>
            <a href="{{ route('products.index') }}" class="w-full sm:w-auto py-3 px-6 rounded-full bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider transition">
                <span>Continue Shopping &rarr;</span>
            </a>
        </div>

    </div>
</div>
@endsection
