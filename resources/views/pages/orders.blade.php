@extends('layouts.app')

@section('title', 'My Orders — UNCART')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8" data-reveal>
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Purchase History</span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1">My Orders</h1>
        <p class="text-xs text-slate-500 mt-1">Track active deliveries, print invoices, and review past items</p>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-100 p-16 text-center space-y-4 shadow-sm" data-reveal>
            <span class="text-4xl">📦</span>
            <h3 class="text-base font-bold text-slate-900">No orders placed yet</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Once you place an order, your tracking details and downloadable invoices will appear right here.
            </p>
            <div class="pt-2">
                <a href="{{ route('products.index') }}" class="inline-block py-3 px-8 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider shadow-md transition active:scale-95">
                    Start Shopping &rarr;
                </a>
            </div>
        </div>
    @else
        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300" data-reveal="scale">
                    
                    <!-- Card Top Header -->
                    <div class="bg-slate-50 p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4 text-xs">
                        <div class="flex items-center gap-6">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Order Placed</span>
                                <span class="font-semibold text-slate-900">{{ $order->created_at->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Total</span>
                                <span class="font-bold text-slate-900" x-text="$store.currency ? $store.currency.format({{ (float)$order->total_amount }}) : '${{ number_format($order->total_amount, 2) }}'">${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Ship To</span>
                                <span class="font-medium text-slate-700">{{ $order->shipping_address['name'] ?? 'Recipient' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-100">
                                {{ str_replace('_', ' ', $order->order_status) }}
                            </span>
                            <span class="text-slate-500 font-bold text-xs">#{{ $order->order_number }}</span>
                        </div>
                    </div>

                    <!-- Items Body -->
                    <div class="p-6 divide-y divide-slate-100">
                        @foreach($order->items as $item)
                            <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $item->product_image }}" class="w-16 h-16 rounded-2xl object-contain bg-slate-50 border border-slate-100 p-2">
                                    <div class="space-y-1">
                                        <h4 class="font-bold text-sm text-slate-900">{{ $item->product_name }}</h4>
                                        <div class="text-xs text-slate-500">Qty: {{ $item->quantity }} • <span x-text="$store.currency ? $store.currency.format({{ (float)$item->price }}) : '${{ number_format($item->price, 2) }}'">${{ number_format($item->price, 2) }}</span> each</div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                    <a href="{{ route('orders.show', $order->order_number) }}" class="py-2 px-4 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                        View Order
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Footer Action Bar -->
                    <div class="bg-slate-50/50 p-4 px-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-4">
                            <a href="{{ route('orders.track', $order->order_number) }}" class="font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1.5">
                                <span>📍 Live Tracking</span>
                            </a>
                            <span class="text-slate-300">•</span>
                            <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="font-medium text-slate-600 hover:text-slate-900">
                                🧾 Invoice PDF
                            </a>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
