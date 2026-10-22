@extends('layouts.app')

@section('title', 'Tax Invoice #' . $order->order_number . ' - ShopiCart')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Top Action Toolbar -->
    <div class="mb-6 flex items-center justify-between no-print">
        <a href="{{ route('orders.show', $order->order_number) }}" class="text-xs text-slate-500 hover:text-slate-900 dark:hover:text-white font-medium">
            &larr; Back to Order
        </a>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="py-2.5 px-5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-lg active:scale-95 flex items-center gap-2">
                <span>🖨️ Print / Save as PDF</span>
            </button>
        </div>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="bg-white text-slate-900 rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-2xl space-y-8" id="printable-invoice">
        
        <!-- Header -->
        <div class="flex items-start justify-between pb-8 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-sm">
                        S
                    </div>
                    <span class="font-black text-xl tracking-tight">SHOPI<span class="text-indigo-600">CART</span></span>
                </div>
                <p class="text-xs text-slate-500">The Precision Commerce Platform Inc.</p>
                <p class="text-xs text-slate-500">100 Silicon Way, Suite 500, San Francisco, CA 94107</p>
                <p class="text-xs text-slate-500">Tax Registration / VAT ID: US-94028109-TAX</p>
            </div>

            <div class="text-right">
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                    PAID INVOICE
                </span>
                <h2 class="text-2xl font-black mt-2">#{{ $order->order_number }}</h2>
                <p class="text-xs text-slate-500 mt-1">Invoice Date: {{ $order->created_at->format('M d, Y') }}</p>
                <p class="text-xs text-slate-500 font-mono">Txn: {{ $order->transaction_id }}</p>
            </div>
        </div>

        <!-- Billed & Shipped To -->
        <div class="grid grid-cols-2 gap-8 text-xs pb-6 border-b border-slate-200">
            <div>
                <h4 class="font-bold text-slate-400 uppercase tracking-wider text-[10px] mb-2 font-mono">Billed & Shipped To:</h4>
                <div class="space-y-1 font-medium">
                    <p class="text-sm font-bold text-slate-900">{{ $order->shipping_address['name'] ?? '' }}</p>
                    <p>{{ $order->shipping_address['address_line1'] ?? '' }}</p>
                    @if(!empty($order->shipping_address['address_line2']))
                        <p>{{ $order->shipping_address['address_line2'] }}</p>
                    @endif
                    <p>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}</p>
                    <p>{{ $order->shipping_address['country'] ?? '' }}</p>
                    <p class="text-slate-500 pt-1">Phone: {{ $order->shipping_address['phone'] ?? '' }}</p>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-slate-400 uppercase tracking-wider text-[10px] mb-2 font-mono">Payment & Logistics:</h4>
                <div class="space-y-1 font-medium">
                    <p><span class="text-slate-500">Payment Gateway:</span> <strong class="uppercase font-mono">{{ $order->payment_method }}</strong></p>
                    <p><span class="text-slate-500">Shipping Carrier:</span> {{ $order->carrier_name ?: 'Express Courier' }}</p>
                    <p><span class="text-slate-500">Tracking Code:</span> <span class="font-mono font-bold">{{ $order->tracking_number ?: 'TRK-90281-EXP' }}</span></p>
                    <p><span class="text-slate-500">Delivery Speed:</span> {{ $order->shipping_method }}</p>
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <div>
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b-2 border-slate-900 text-slate-500 uppercase font-mono tracking-wider text-[10px]">
                        <th class="py-3">Item Description</th>
                        <th class="py-3 text-center">Qty</th>
                        <th class="py-3 text-right">Unit Price</th>
                        <th class="py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="py-4">
                                <span class="font-bold text-slate-900 block text-xs">{{ $item->product_name }}</span>
                                <span class="text-[11px] text-slate-500">Merchant: {{ $item->vendor->shop_name ?? 'ShopiCart Direct' }}</span>
                            </td>
                            <td class="py-4 text-center font-mono font-bold">{{ $item->quantity }}</td>
                            <td class="py-4 text-right font-mono">${{ number_format($item->price, 2) }}</td>
                            <td class="py-4 text-right font-mono font-bold text-slate-900">${{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Calculations -->
        <div class="flex justify-end pt-4 border-t border-slate-200">
            <div class="w-64 space-y-2 text-xs">
                <div class="flex items-center justify-between text-slate-500">
                    <span>Subtotal</span>
                    <span class="font-mono font-bold text-slate-900">${{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex items-center justify-between text-emerald-600">
                        <span>Coupon ({{ $order->coupon_code }})</span>
                        <span class="font-mono">-${{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between text-slate-500">
                    <span>Shipping Fee</span>
                    <span class="font-mono font-bold text-slate-900">${{ number_format($order->shipping_amount, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-slate-500">
                    <span>Tax (8.5%)</span>
                    <span class="font-mono font-bold text-slate-900">${{ number_format($order->tax_amount, 2) }}</span>
                </div>
                <div class="pt-2 border-t-2 border-slate-900 flex items-baseline justify-between text-sm">
                    <span class="font-black text-slate-900 uppercase tracking-wider">Total Paid</span>
                    <span class="text-xl font-black text-slate-900 font-mono">${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Barcode Simulator & Footnote -->
        <div class="pt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left text-[11px] text-slate-400">
            <div>
                <p>Thank you for choosing ShopiCart platform.</p>
                <p>Support inquiries: support@shopicart.com &bull; 1-800-892-0199</p>
            </div>
            <div class="font-mono font-bold tracking-widest text-lg text-slate-800">
                ||| | ||||| || |||||| | ||| || ||||
                <div class="text-[9px] text-slate-400 tracking-normal text-center">ORDER AUTH CODE: {{ $order->order_number }}</div>
            </div>
        </div>

    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; color: black !important; }
    header, footer { display: none !important; }
}
</style>
@endsection
