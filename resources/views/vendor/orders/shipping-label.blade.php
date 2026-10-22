<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipping Label - Order #{{ $orderItem->order->order_number ?? 'N/A' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-8 flex flex-col items-center justify-center font-sans">

    <!-- Top Action -->
    <div class="mb-6 flex gap-3 no-print">
        <button onclick="window.print()" class="py-2.5 px-6 rounded-xl bg-slate-900 text-white font-bold text-xs uppercase tracking-wider hover:bg-slate-800 shadow-xl">
            🖨️ Print Shipping Label
        </button>
        <button onclick="window.close()" class="py-2.5 px-4 rounded-xl bg-white border border-slate-300 text-slate-700 font-bold text-xs">
            Close
        </button>
    </div>

    <!-- Official Thermal Dispatch Label (4x6 Aspect) -->
    <div class="w-[450px] bg-white border-4 border-slate-900 p-6 space-y-6 text-slate-900 shadow-2xl">
        
        <!-- Header Strip -->
        <div class="flex items-center justify-between border-b-4 border-slate-900 pb-4">
            <div class="flex items-center gap-2">
                <span class="font-black text-2xl tracking-tighter">SHOPI<span class="text-indigo-600">CART</span></span>
                <span class="text-[10px] font-mono font-bold uppercase bg-slate-900 text-white px-2 py-0.5 rounded">EXPRESS</span>
            </div>
            <div class="text-right">
                <span class="text-xs font-mono font-black uppercase tracking-widest block">{{ $orderItem->order->carrier_name ?: 'DHL EXPRESS' }}</span>
                <span class="text-[10px] text-slate-500 font-mono">PRIORITY AIR</span>
            </div>
        </div>

        <!-- Sender / Return Address -->
        <div class="text-[11px] leading-tight pb-3 border-b-2 border-slate-900">
            <span class="font-bold uppercase tracking-wider text-[9px] text-slate-400 block font-mono">FROM (MERCHANT):</span>
            <div class="font-bold text-xs">{{ $vendor->shop_name }}</div>
            <div>{{ $vendor->address ?: 'Cupertino Maker Studio, CA 95014' }}</div>
            <div class="text-slate-500 font-mono">Tel: {{ $vendor->phone ?: '+1 (555) 302-8492' }}</div>
        </div>

        <!-- Recipient Address (Large) -->
        <div class="text-xs leading-snug py-2 border-b-4 border-slate-900 space-y-1">
            <span class="font-bold uppercase tracking-wider text-[10px] text-slate-400 block font-mono">SHIP TO:</span>
            <div class="font-black text-base uppercase">{{ $orderItem->order->shipping_address['name'] ?? '' }}</div>
            <div class="font-bold text-sm">{{ $orderItem->order->shipping_address['address_line1'] ?? '' }}</div>
            @if(!empty($orderItem->order->shipping_address['address_line2']))
                <div class="font-bold text-sm">{{ $orderItem->order->shipping_address['address_line2'] }}</div>
            @endif
            <div class="font-black text-base uppercase tracking-tight">
                {{ $orderItem->order->shipping_address['city'] ?? '' }}, {{ $orderItem->order->shipping_address['state'] ?? '' }} {{ $orderItem->order->shipping_address['postal_code'] ?? '' }}
            </div>
            <div class="font-black uppercase tracking-wider">{{ $orderItem->order->shipping_address['country'] ?? '' }}</div>
            <div class="font-mono text-slate-600 pt-1">Tel: {{ $orderItem->order->shipping_address['phone'] ?? '' }}</div>
        </div>

        <!-- Waybill Tracking Barcode Simulator -->
        <div class="text-center py-2 space-y-2 border-b-2 border-slate-900">
            <div class="font-mono font-black text-4xl tracking-widest leading-none select-none">
                ||| | ||||| || |||||| | ||| || |||| ||||
            </div>
            <div class="font-mono font-bold text-xs tracking-wider">
                TRACKING: {{ $orderItem->order->tracking_number ?: 'EXP-849204128US' }}
            </div>
        </div>

        <!-- Footer Manifest Block -->
        <div class="flex items-center justify-between text-[10px] font-mono">
            <div>
                <span class="block text-slate-400">Order:</span>
                <strong class="font-bold">#{{ $orderItem->order->order_number }}</strong>
            </div>
            <div>
                <span class="block text-slate-400">Weight:</span>
                <strong class="font-bold">0.85 KG</strong>
            </div>
            <div>
                <span class="block text-slate-400">Item:</span>
                <strong class="font-bold truncate max-w-[120px] block">{{ $orderItem->product_name }}</strong>
            </div>
        </div>

    </div>
</body>
</html>
