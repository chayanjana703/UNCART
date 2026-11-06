@extends('layouts.app')

@section('title', 'Official Tax Invoice #' . $order->order_number . ' — UNCART')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
    formatMoney(val) {
        if (this.$store && this.$store.currency) {
            return this.$store.currency.format(val);
        }
        return '$' + Number(val).toFixed(2);
    }
}">

    <!-- Top Action Toolbar (Hidden during print) -->
    <div class="mb-6 flex items-center justify-between no-print" data-reveal>
        <a href="{{ route('orders.show', $order->order_number) }}" class="inline-flex items-center gap-2 py-2 px-3.5 rounded-xl bg-white/5 hover:bg-white/10 text-xs text-slate-300 hover:text-white font-medium border border-white/10 transition active:scale-95">
            <span>&larr;</span>
            <span>Back to Order Dossier</span>
        </a>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="py-2.5 px-5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs uppercase tracking-wider transition-all duration-200 shadow-lg shadow-indigo-600/30 active:scale-95 flex items-center gap-2">
                <span>🖨️</span>
                <span>Print Tax Invoice / PDF</span>
            </button>
        </div>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="bg-white text-slate-900 rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-2xl space-y-8 print:p-0 print:border-none print:shadow-none print:rounded-none" id="printable-invoice">
        
        <!-- Header -->
        <div class="flex items-start justify-between pb-8 border-b-2 border-slate-900">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-base shadow">
                        S
                    </div>
                    <span class="font-black text-2xl tracking-tight text-slate-900">UN<span class="text-[#F05A28]">CART</span></span>
                </div>
                <p class="text-xs font-semibold text-slate-600">The Precision Commerce Platform Inc.</p>
                <p class="text-xs text-slate-500">100 Silicon Way, Suite 500, San Francisco, CA 94107</p>
                <p class="text-xs text-slate-500 font-mono mt-1">Tax ID / VAT Registration: <strong class="text-slate-800">US-94028109-TAX</strong></p>
            </div>

            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-[11px] font-mono font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300">
                    PAID TAX INVOICE
                </span>
                <h2 class="text-2xl font-black mt-2 font-mono text-slate-900">#{{ $order->order_number }}</h2>
                <p class="text-xs text-slate-500 mt-1">Date Issued: {{ $order->created_at->format('M d, Y') }}</p>
                <p class="text-[11px] text-slate-400 font-mono">Txn Ref: {{ $order->transaction_id }}</p>
            </div>
        </div>

        <!-- Billed & Shipped To Grid -->
        <div class="grid grid-cols-2 gap-8 text-xs pb-6 border-b border-slate-200">
            <div>
                <h4 class="font-bold text-slate-400 uppercase tracking-wider text-[10px] mb-2 font-mono">Customer Consignment Address:</h4>
                <div class="space-y-1 text-slate-800 font-medium">
                    <p class="text-sm font-bold text-slate-950">{{ $order->shipping_address['name'] ?? '' }}</p>
                    <p>{{ $order->shipping_address['address_line1'] ?? '' }}</p>
                    @if(!empty($order->shipping_address['address_line2']))
                        <p>{{ $order->shipping_address['address_line2'] }}</p>
                    @endif
                    <p>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}</p>
                    <p>{{ $order->shipping_address['country'] ?? '' }}</p>
                    <p class="text-slate-500 pt-1 font-mono text-[11px]">Phone: {{ $order->shipping_address['phone'] ?? '' }}</p>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-slate-400 uppercase tracking-wider text-[10px] mb-2 font-mono">Logistics & Clearing Rail:</h4>
                <div class="space-y-1 text-slate-800 font-medium">
                    <p><span class="text-slate-500">Payment Gateway:</span> <strong class="uppercase font-mono text-slate-900">{{ $order->payment_method }}</strong></p>
                    <p><span class="text-slate-500">Fulfillment Carrier:</span> {{ $order->carrier_name ?: 'Express Air Logistics' }}</p>
                    <p><span class="text-slate-500">Waybill Consignment:</span> <span class="font-mono font-bold text-slate-900">{{ $order->tracking_number ?: 'TRK-90281-EXP' }}</span></p>
                    <p><span class="text-slate-500">Transit Tier:</span> {{ $order->shipping_method }}</p>
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <div>
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b-2 border-slate-900 text-slate-600 uppercase font-mono tracking-wider text-[10px]">
                        <th class="py-3">Merchandise Description</th>
                        <th class="py-3 text-center">Qty</th>
                        <th class="py-3 text-right">Unit Price</th>
                        <th class="py-3 text-right">Line Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="py-4">
                                <span class="font-bold text-slate-900 block text-xs">{{ $item->product_name }}</span>
                                <span class="text-[11px] text-slate-500">Merchant: {{ $item->vendor->shop_name ?? 'UNCART Direct' }}</span>
                            </td>
                            <td class="py-4 text-center font-mono font-bold text-slate-900">{{ $item->quantity }}</td>
                            <td class="py-4 text-right font-mono text-slate-700" x-text="formatMoney({{ (float)$item->price }})">${{ number_format($item->price, 2) }}</td>
                            <td class="py-4 text-right font-mono font-bold text-slate-900" x-text="formatMoney({{ (float)$item->total }})">${{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Financial Breakdown -->
        <div class="flex justify-end pt-4 border-t-2 border-slate-200">
            <div class="w-72 space-y-2 text-xs">
                <div class="flex items-center justify-between text-slate-600 font-mono">
                    <span>Subtotal</span>
                    <span class="font-bold text-slate-900" x-text="formatMoney({{ (float)$order->subtotal }})">${{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex items-center justify-between text-emerald-700 font-mono">
                        <span>Discount Voucher ({{ $order->coupon_code }})</span>
                        <span x-text="'-' + formatMoney({{ (float)$order->discount_amount }})">-${{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between text-slate-600 font-mono">
                    <span>Shipping Freight</span>
                    <span class="font-bold text-slate-900" x-text="formatMoney({{ (float)$order->shipping_amount }})">${{ number_format($order->shipping_amount, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-slate-600 font-mono">
                    <span>Tax Surcharge (8.5%)</span>
                    <span class="font-bold text-slate-900" x-text="formatMoney({{ (float)$order->tax_amount }})">${{ number_format($order->tax_amount, 2) }}</span>
                </div>
                <div class="pt-3 border-t-2 border-slate-900 flex items-baseline justify-between">
                    <span class="font-black text-slate-950 uppercase tracking-wider text-xs">Total Settled</span>
                    <span class="text-xl font-black text-slate-950 font-mono" x-text="formatMoney({{ (float)$order->total_amount }})">${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Barcode & Attribution Footer -->
        <div class="pt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left text-[11px] text-slate-500">
            <div>
                <p class="font-bold text-slate-800 tracking-tight">UNCART Global Commerce Network</p>
                <p class="text-slate-600 text-[11px]">Authorized digital tax clearance &amp; fulfillment invoice</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Automated settlement certificate &bull; Digitally verified &bull; ISO-27001 Certified</p>
            </div>
            <div class="flex flex-col items-center sm:items-end gap-1">
                <div class="flex items-center px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200/80">
                    <svg class="h-9 w-48 text-slate-900" viewBox="0 0 160 36" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <rect x="0" y="0" width="2" height="36"/>
                        <rect x="4" y="0" width="1" height="36"/>
                        <rect x="7" y="0" width="3" height="36"/>
                        <rect x="12" y="0" width="1" height="36"/>
                        <rect x="15" y="0" width="4" height="36"/>
                        <rect x="21" y="0" width="2" height="36"/>
                        <rect x="25" y="0" width="1" height="36"/>
                        <rect x="28" y="0" width="3" height="36"/>
                        <rect x="33" y="0" width="2" height="36"/>
                        <rect x="37" y="0" width="4" height="36"/>
                        <rect x="43" y="0" width="1" height="36"/>
                        <rect x="46" y="0" width="2" height="36"/>
                        <rect x="50" y="0" width="3" height="36"/>
                        <rect x="55" y="0" width="1" height="36"/>
                        <rect x="58" y="0" width="4" height="36"/>
                        <rect x="64" y="0" width="2" height="36"/>
                        <rect x="68" y="0" width="1" height="36"/>
                        <rect x="71" y="0" width="3" height="36"/>
                        <rect x="76" y="0" width="2" height="36"/>
                        <rect x="80" y="0" width="4" height="36"/>
                        <rect x="86" y="0" width="1" height="36"/>
                        <rect x="89" y="0" width="2" height="36"/>
                        <rect x="93" y="0" width="3" height="36"/>
                        <rect x="98" y="0" width="1" height="36"/>
                        <rect x="101" y="0" width="4" height="36"/>
                        <rect x="107" y="0" width="2" height="36"/>
                        <rect x="111" y="0" width="1" height="36"/>
                        <rect x="114" y="0" width="3" height="36"/>
                        <rect x="119" y="0" width="2" height="36"/>
                        <rect x="123" y="0" width="4" height="36"/>
                        <rect x="129" y="0" width="1" height="36"/>
                        <rect x="132" y="0" width="3" height="36"/>
                        <rect x="137" y="0" width="2" height="36"/>
                        <rect x="141" y="0" width="4" height="36"/>
                        <rect x="147" y="0" width="1" height="36"/>
                        <rect x="150" y="0" width="3" height="36"/>
                        <rect x="155" y="0" width="2" height="36"/>
                        <rect x="158" y="0" width="2" height="36"/>
                    </svg>
                </div>
                <div class="font-mono text-[9px] text-slate-500 tracking-wider">WAYBILL REF: <span class="font-bold text-slate-800">{{ $order->tracking_number ?: $order->order_number }}</span></div>
            </div>
        </div>

    </div>
</div>

<style>
@media print {
    .no-print, header, footer, #role-switcher-banner, #page-progress-bar, .pointer-events-none {
        display: none !important;
    }
    body {
        background: #ffffff !important;
        color: #000000 !important;
    }
    #printable-invoice {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
}
</style>
@endsection
