@extends('layouts.app')

@section('title', 'My Orders - ShopiCart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Order History</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Track active deliveries, download invoices, and manage returns</p>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-16 text-center space-y-4 shadow-sm">
            <span class="text-5xl">📦</span>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">No orders placed yet</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Once you place an order, your real-time tracking timeline and invoices will appear here.
            </p>
            <a href="{{ route('products.index') }}" class="inline-block py-2.5 px-6 rounded-xl bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider hover:bg-indigo-700 shadow-md">
                Start Shopping
            </a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-200">
                    
                    <!-- Card Top Header -->
                    <div class="bg-slate-50 dark:bg-slate-850 p-6 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4 text-xs">
                        <div class="flex items-center gap-6">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 font-mono block">Order Placed</span>
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $order->created_at->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 font-mono block">Total Paid</span>
                                <span class="font-bold text-slate-900 dark:text-white">${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 font-mono block">Ship To</span>
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $order->shipping_address['name'] ?? 'Recipient' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $order->status_badge['bg'] }}">
                                {{ $order->status_badge['label'] }}
                            </span>
                            <span class="font-mono text-slate-400 font-bold text-[11px]">#{{ $order->order_number }}</span>
                        </div>
                    </div>

                    <!-- Items Body -->
                    <div class="p-6 divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($order->items as $item)
                            <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 flex-shrink-0">
                                    <div>
                                        <h4 class="font-bold text-sm text-slate-900 dark:text-white leading-snug">
                                            {{ $item->product_name }}
                                        </h4>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            Qty: {{ $item->quantity }} &bull; ${{ number_format($item->price, 2) }} each
                                        </p>
                                    </div>
                                </div>
                                <span class="text-sm font-black text-slate-900 dark:text-white">
                                    ${{ number_format($item->total, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Bottom Actions -->
                    <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-850/50 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('orders.track', $order->order_number) }}" 
                               class="py-2 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-colors shadow-sm">
                                📍 Track Shipment
                            </a>
                            <a href="{{ route('orders.invoice', $order->order_number) }}" 
                               class="py-2 px-4 rounded-xl bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs uppercase tracking-wider transition-colors">
                                🧾 Receipt / Invoice
                            </a>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('orders.show', $order->order_number) }}" class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                View Order Details &rarr;
                            </a>
                        </div>
                    </div>

                </div>
            @endforeach

            <div class="pt-4">
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
