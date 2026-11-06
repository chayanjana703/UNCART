@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' — UNCART')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ cancelModal: false, returnModal: false }">

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4" data-reveal>
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Order #{{ $order->order_number }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-100">
                    {{ str_replace('_', ' ', $order->order_status) }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('orders.track', $order->order_number) }}" class="py-2.5 px-5 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider shadow-sm transition active:scale-95">
                📍 Live Tracking
            </a>
            <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="py-2.5 px-5 rounded-full bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs uppercase tracking-wider border border-slate-200 shadow-sm transition">
                🧾 Printable Invoice
            </a>
        </div>
    </div>

    <!-- Return / Refund Status Alert if requested -->
    @if($order->return_status !== 'none' && !empty($order->return_status))
        <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-center justify-between" data-reveal>
            <div class="flex items-center gap-2">
                <span class="text-base">🔄</span>
                <span>Return Status: <strong class="uppercase font-bold">{{ $order->return_status }}</strong> — Reason: {{ $order->return_reason }}</span>
            </div>
        </div>
    @endif

    <div class="space-y-6">
        
        <!-- Items List -->
        <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm p-6 space-y-4" data-reveal="scale">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100">
                Purchased Items
            </h3>
            
            <div class="divide-y divide-slate-100">
                @foreach($order->items as $item)
                    <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4 text-xs">
                        <div class="flex items-center gap-4">
                            <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-16 h-16 rounded-2xl object-contain bg-slate-50 border border-slate-100 p-2 flex-shrink-0">
                            <div>
                                <h4 class="font-bold text-sm text-slate-900 leading-snug">
                                    {{ $item->product_name }}
                                </h4>
                                <div class="text-slate-500 text-[11px] mt-1">
                                    Qty: {{ $item->quantity }} • <span x-text="$store.currency ? $store.currency.format({{ (float)$item->price }}) : '${{ number_format($item->price, 2) }}'">${{ number_format($item->price, 2) }}</span> each
                                </div>
                            </div>
                        </div>

                        <div class="text-right font-bold text-slate-900 text-sm" x-text="$store.currency ? $store.currency.format({{ (float)$item->subtotal }}) : '${{ number_format($item->subtotal, 2) }}'">
                            ${{ number_format($item->subtotal, 2) }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Financials Breakdown -->
            <div class="pt-4 border-t border-slate-100 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal</span>
                    <span class="font-bold text-slate-900" x-text="$store.currency ? $store.currency.format({{ (float)$order->subtotal }}) : '${{ number_format($order->subtotal, 2) }}'">${{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-emerald-600 font-medium">
                        <span>Discount Voucher ({{ $order->coupon_code }})</span>
                        <span x-text="'-' + ($store.currency ? $store.currency.format({{ (float)$order->discount_amount }}) : '${{ number_format($order->discount_amount, 2) }}')">-${{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-slate-600">
                    <span>Shipping Freight</span>
                    <span class="font-bold text-slate-900" x-text="$store.currency ? $store.currency.format({{ (float)$order->shipping_amount }}) : '${{ number_format($order->shipping_amount, 2) }}'">${{ number_format($order->shipping_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Tax Surcharge</span>
                    <span class="font-bold text-slate-900" x-text="$store.currency ? $store.currency.format({{ (float)$order->tax_amount }}) : '${{ number_format($order->tax_amount, 2) }}'">${{ number_format($order->tax_amount, 2) }}</span>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-between text-base font-black text-slate-900">
                    <span>Total Settled</span>
                    <span class="text-indigo-600" x-text="$store.currency ? $store.currency.format({{ (float)$order->total_amount }}) : '${{ number_format($order->total_amount, 2) }}'">${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Address & Payment Meta Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6" data-reveal>
            <!-- Shipping Info -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-2 text-xs">
                <h4 class="font-bold uppercase tracking-wider text-slate-900">Delivery Destination</h4>
                @if(is_array($order->shipping_address))
                    <div class="text-slate-600 space-y-1">
                        <p class="font-bold text-slate-900">{{ $order->shipping_address['name'] ?? '' }}</p>
                        <p>{{ $order->shipping_address['address_line1'] ?? '' }}</p>
                        <p>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}</p>
                        <p class="text-indigo-600 font-semibold pt-1">{{ $order->shipping_address['phone'] ?? '' }}</p>
                    </div>
                @else
                    <p class="text-slate-600">{{ $order->shipping_address ?? 'Standard address on file' }}</p>
                @endif
            </div>

            <!-- Payment & Logistics Info -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-2 text-xs">
                <h4 class="font-bold uppercase tracking-wider text-slate-900">Settlement & Carrier</h4>
                <div class="text-slate-600 space-y-1">
                    <p><span class="text-slate-400">Payment Rail:</span> <strong class="uppercase text-slate-900">{{ $order->payment_method }}</strong></p>
                    <p><span class="text-slate-400">Courier Carrier:</span> {{ $order->carrier_name ?: 'Global Express' }}</p>
                    <p><span class="text-slate-400">Waybill Air Code:</span> <span class="font-bold text-indigo-600">{{ $order->tracking_number ?: 'TRK-90281-EXP' }}</span></p>
                    <p><span class="text-slate-400">Shipping Speed:</span> {{ $order->shipping_method }}</p>
                </div>
            </div>
        </div>

        <!-- Order Actions (Cancel / Return) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center justify-between text-xs" data-reveal>
            <div>
                <span class="font-bold text-slate-900">Order Management</span>
                <p class="text-slate-500 text-[11px]">Need to cancel or initiate a return consignment?</p>
            </div>

            <div class="flex items-center gap-3">
                @if($order->canBeCancelled())
                    <button type="button" @click="cancelModal = true" class="py-2 px-4 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold transition">
                        Cancel Order
                    </button>
                @endif

                @if($order->canBeReturned())
                    <button type="button" @click="returnModal = true" class="py-2 px-4 rounded-full bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold transition">
                        Request Return
                    </button>
                @endif
            </div>
        </div>

    </div>

    <!-- Cancel Modal -->
    <div x-show="cancelModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="cancelModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Cancel Order #{{ $order->order_number }}</h3>
            <p class="text-xs text-slate-500">Are you sure you want to cancel this order? Funds will be returned to your original payment method.</p>
            <form action="{{ route('orders.cancel', $order->order_number) }}" method="POST" class="space-y-4">
                @csrf
                <textarea name="cancel_reason" rows="2" required placeholder="Reason for cancellation..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-xs text-slate-900"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="cancelModal = false" class="px-4 py-2 rounded-full border border-slate-200 text-slate-600 text-xs font-semibold">Keep Order</button>
                    <button type="submit" class="px-5 py-2 rounded-full bg-rose-600 text-white text-xs font-bold shadow-sm">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Return Modal -->
    <div x-show="returnModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="returnModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Request Return for Order #{{ $order->order_number }}</h3>
            <p class="text-xs text-slate-500">Returns are covered by our 30-day money-back guarantee.</p>
            <form action="{{ route('orders.return', $order->order_number) }}" method="POST" class="space-y-4">
                @csrf
                <textarea name="return_reason" rows="3" required placeholder="Please describe the reason for return or defect..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-xs text-slate-900"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="returnModal = false" class="px-4 py-2 rounded-full border border-slate-200 text-slate-600 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-full bg-amber-600 text-white text-xs font-bold shadow-sm">Submit Return Request</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
