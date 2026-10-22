@extends('layouts.admin')

@section('title', 'Order Management: #' . $order->order_number)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-400 font-mono">
            <a href="{{ route('admin.orders.index') }}" class="hover:text-amber-400 transition">&larr; Back to Orders</a>
            <span>/</span>
            <span class="text-slate-200">#{{ $order->order_number }}</span>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white text-xs font-semibold flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Customer Invoice
            </a>
        </div>
    </div>

    <!-- Order Header Info -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-black text-white font-mono tracking-tight">Order #{{ $order->order_number }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-800 text-amber-400 border border-amber-500/20">
                        {{ str_replace('_', ' ', $order->order_status) }}
                    </span>
                    @if($order->payment_status === 'paid')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            Paid ({{ strtoupper($order->payment_method) }})
                        </span>
                    @elseif($order->payment_status === 'refunded')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-red-500/10 text-red-400 border border-red-500/20">
                            Refunded
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mt-1">
                    Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }} • Transaction ID: <span class="font-mono text-slate-200">{{ $order->transaction_id ?? 'SIM-TXN-OFFLINE' }}</span>
                </p>
            </div>

            <!-- Refund Trigger if Paid and Not Refunded -->
            @if($order->payment_status === 'paid')
                <div>
                    <form action="{{ route('admin.orders.refund', $order->id) }}" method="POST" onsubmit="return confirm('Authorize an immediate full refund of ${{ number_format($order->total_amount, 2) }} to this buyer?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-red-950/60 hover:bg-red-900 border border-red-800 text-red-300 text-xs font-bold transition">
                            Process Immediate Refund
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Content: Two Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Status Override & Shipping Details Form -->
        <div class="space-y-6">
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6">
                <h2 class="text-sm font-bold text-white tracking-tight mb-1">Administrative Override</h2>
                <p class="text-xs text-slate-400 mb-5">Manually transition fulfillment state or assign carrier waybill.</p>

                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Fulfillment Status</label>
                        <select name="order_status" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-white">
                            <option value="placed" {{ $order->order_status === 'placed' ? 'selected' : '' }}>Placed</option>
                            <option value="confirmed" {{ $order->order_status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="packed" {{ $order->order_status === 'packed' ? 'selected' : '' }}>Packed</option>
                            <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>Shipped / In Transit</option>
                            <option value="out_for_delivery" {{ $order->order_status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                            <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="returned" {{ $order->order_status === 'returned' ? 'selected' : '' }}>Returned</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Carrier Provider</label>
                        <input type="text" name="carrier_name" value="{{ old('carrier_name', $order->carrier_name) }}" placeholder="e.g. FedEx Ground, DHL Express..." class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-white">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Tracking Waybill #</label>
                        <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="e.g. 794912984129" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>

                    <div class="pt-3 border-t border-slate-800">
                        <button type="submit" class="w-full px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold transition shadow-lg shadow-amber-500/20">
                            Save Status Override
                        </button>
                    </div>
                </form>
            </div>

            <!-- Customer Dossier -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6">
                <h2 class="text-sm font-bold text-white tracking-tight mb-3">Customer Information</h2>
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 text-[11px] block">Customer Name</span>
                        <span class="font-bold text-white">{{ $order->user->name ?? 'Guest Buyer' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Contact Email</span>
                        <span class="font-mono text-white">{{ $order->user->email ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Shipping Destination</span>
                        <div class="mt-1 p-3 bg-slate-950 rounded-xl text-slate-300 border border-slate-800">
                            @if(is_array($order->shipping_address))
                                <div class="font-bold">{{ $order->shipping_address['name'] ?? '' }}</div>
                                <div>{{ $order->shipping_address['address_line1'] ?? '' }}</div>
                                @if(!empty($order->shipping_address['address_line2']))
                                    <div>{{ $order->shipping_address['address_line2'] }}</div>
                                @endif
                                <div>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}</div>
                                <div class="font-mono text-slate-400 mt-1">{{ $order->shipping_address['phone'] ?? '' }}</div>
                            @else
                                {{ $order->shipping_address ?? 'Standard address on file' }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Order Line Items and Totals Breakdown (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6">
                <h2 class="text-sm font-bold text-white tracking-tight mb-4">Purchased Items ({{ $order->items->count() }})</h2>

                <div class="divide-y divide-slate-800">
                    @foreach($order->items as $item)
                        <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $item->product->primary_image ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=100' }}" alt="{{ $item->product_name }}" class="w-12 h-12 rounded-xl object-cover bg-slate-800 flex-shrink-0">
                                <div>
                                    <h4 class="font-bold text-white text-xs">{{ $item->product_name }}</h4>
                                    <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                        <span>Merchant: <strong class="text-slate-300">{{ $item->vendor->shop_name ?? 'Marketplace' }}</strong></span>
                                        <span>•</span>
                                        <span class="font-mono">Qty: {{ $item->quantity }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="font-mono font-bold text-white text-xs">${{ number_format($item->subtotal, 2) }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">${{ number_format($item->price, 2) }} each</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Financial Ledger Summary -->
                <div class="mt-6 pt-6 border-t border-slate-800 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-400">
                        <span>Items Subtotal</span>
                        <span class="font-mono text-white">${{ number_format($order->subtotal, 2) }}</span>
                    </div>

                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-emerald-400">
                            <span>Coupon Discount ({{ $order->coupon_code }})</span>
                            <span class="font-mono">-${{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-slate-400">
                        <span>Estimated Shipping</span>
                        <span class="font-mono text-white">${{ number_format($order->shipping_amount, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-slate-400">
                        <span>Applicable Tax</span>
                        <span class="font-mono text-white">${{ number_format($order->tax_amount, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-base font-black text-white pt-3 border-t border-slate-800">
                        <span>Grand Total Settled</span>
                        <span class="font-mono text-amber-400">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
