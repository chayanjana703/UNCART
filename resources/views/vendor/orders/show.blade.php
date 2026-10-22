@extends('layouts.vendor')

@section('page-title', 'Manage Order Item #' . $orderItem->id)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-black text-white tracking-tight">Fulfillment Console</h2>
            <p class="text-xs text-slate-500 mt-0.5">Order #{{ $orderItem->order->order_number ?? 'N/A' }} &bull; Placed {{ $orderItem->created_at->format('M d, Y h:i A') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('vendor.orders.shipping-label', $orderItem->id) }}" target="_blank" class="py-2 px-4 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-700 shadow-md">
                🏷️ Print Dispatch Label
            </a>
            <a href="{{ route('vendor.orders.index') }}" class="text-xs text-slate-400 hover:text-white">
                &larr; Back to Orders
            </a>
        </div>
    </div>

    <!-- Product Summary Card -->
    <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 shadow-xl flex items-center justify-between gap-6 text-xs">
        <div class="flex items-center gap-4">
            <img src="{{ $orderItem->product_image }}" alt="" class="w-16 h-16 rounded-2xl object-cover border border-slate-800 flex-shrink-0">
            <div>
                <h4 class="font-bold text-base text-white">{{ $orderItem->product_name }}</h4>
                <p class="text-slate-400 mt-0.5">Quantity: <strong class="text-white">{{ $orderItem->quantity }} units</strong></p>
                <p class="text-slate-400">Unit Price: ${{ number_format($orderItem->price, 2) }}</p>
            </div>
        </div>

        <div class="text-right">
            <span class="text-slate-400 text-[10px] uppercase font-mono block">Line Total</span>
            <span class="text-2xl font-black text-white">${{ number_format($orderItem->total, 2) }}</span>
        </div>
    </div>

    <!-- Update Status Card -->
    <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-4">
        <h3 class="font-bold text-white uppercase tracking-wider text-xs pb-3 border-b border-slate-850">
            Update Dispatch & Shipping Status
        </h3>

        <form action="{{ route('vendor.orders.status', $orderItem->id) }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-300 mb-1">Fulfillment State</label>
                    <select name="status" required class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                        <option value="pending" {{ $orderItem->vendor_status == 'pending' ? 'selected' : '' }}>Pending Acceptance</option>
                        <option value="processing" {{ $orderItem->vendor_status == 'processing' ? 'selected' : '' }}>Processing / Quality Inspection</option>
                        <option value="packed" {{ $orderItem->vendor_status == 'packed' ? 'selected' : '' }}>Packed in Warehouse</option>
                        <option value="shipped" {{ $orderItem->vendor_status == 'shipped' ? 'selected' : '' }}>Shipped with Carrier</option>
                        <option value="delivered" {{ $orderItem->vendor_status == 'delivered' ? 'selected' : '' }}>Delivered to Buyer</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Carrier Name</label>
                    <input type="text" name="carrier_name" value="{{ $orderItem->order->carrier_name ?: 'DHL Global Express' }}"
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Tracking Number</label>
                    <input type="text" name="tracking_number" value="{{ $orderItem->order->tracking_number ?: 'EXP-' . strtoupper(\Illuminate\Support\Str::random(10)) }}"
                           class="w-full font-mono rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="py-2.5 px-6 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all active:scale-95">
                    Save Dispatch Status
                </button>
            </div>
        </form>
    </div>

    <!-- Customer Shipping Address Card -->
    <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 shadow-xl space-y-3 text-xs">
        <h4 class="font-bold text-white uppercase tracking-wider text-[11px] pb-2 border-b border-slate-850">
            Delivery Destination
        </h4>
        <div class="space-y-1 text-slate-300 font-medium">
            <p class="font-bold text-white">{{ $orderItem->order->shipping_address['name'] ?? '' }}</p>
            <p>{{ $orderItem->order->shipping_address['address_line1'] ?? '' }}</p>
            <p>{{ $orderItem->order->shipping_address['city'] ?? '' }}, {{ $orderItem->order->shipping_address['state'] ?? '' }} {{ $orderItem->order->shipping_address['postal_code'] ?? '' }}</p>
            <p>{{ $orderItem->order->shipping_address['country'] ?? '' }}</p>
            <p class="font-mono text-slate-400 pt-1">Phone: {{ $orderItem->order->shipping_address['phone'] ?? '' }}</p>
        </div>
    </div>

</div>
@endsection
