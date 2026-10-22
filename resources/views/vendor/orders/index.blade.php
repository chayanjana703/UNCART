@extends('layouts.vendor')

@section('page-title', 'Order Fulfillment & Dispatch')

@section('content')
<div class="space-y-6">

    <!-- Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs">
        <a href="{{ route('vendor.orders.index') }}" 
           class="px-4 py-2 rounded-xl font-bold transition-all {{ !request('status') ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white' }}">
            All Orders
        </a>
        <a href="{{ route('vendor.orders.index', ['status' => 'pending']) }}" 
           class="px-4 py-2 rounded-xl font-bold transition-all {{ request('status') == 'pending' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white' }}">
            Pending
        </a>
        <a href="{{ route('vendor.orders.index', ['status' => 'processing']) }}" 
           class="px-4 py-2 rounded-xl font-bold transition-all {{ request('status') == 'processing' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white' }}">
            Processing / Packed
        </a>
        <a href="{{ route('vendor.orders.index', ['status' => 'shipped']) }}" 
           class="px-4 py-2 rounded-xl font-bold transition-all {{ request('status') == 'shipped' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white' }}">
            In Transit / Shipped
        </a>
        <a href="{{ route('vendor.orders.index', ['status' => 'delivered']) }}" 
           class="px-4 py-2 rounded-xl font-bold transition-all {{ request('status') == 'delivered' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white' }}">
            Delivered
        </a>
    </div>

    <!-- Orders Table -->
    <div class="bg-slate-950 rounded-3xl border border-slate-800 shadow-xl overflow-x-auto">
        <table class="w-full text-xs text-left min-w-[750px]">
            <thead>
                <tr class="border-b border-slate-850 text-slate-400 uppercase font-mono tracking-wider text-[10px] bg-slate-900/60">
                    <th class="p-4">Order #</th>
                    <th class="p-4">Merchandise</th>
                    <th class="p-4">Customer</th>
                    <th class="p-4">Total</th>
                    <th class="p-4">Fulfillment Status</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-850">
                @forelse($orderItems as $item)
                    <tr class="hover:bg-slate-900/40 transition-colors">
                        <td class="p-4 font-mono font-bold text-white">
                            #{{ $item->order->order_number ?? 'N/A' }}
                            <span class="text-[10px] text-slate-500 block font-normal">{{ $item->created_at->format('M d, Y') }}</span>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $item->product_image }}" alt="" class="w-10 h-10 rounded-xl object-cover border border-slate-800 flex-shrink-0">
                                <div>
                                    <div class="font-bold text-white line-clamp-1">{{ $item->product_name }}</div>
                                    <span class="text-slate-400 text-[11px]">Qty: {{ $item->quantity }} &bull; ${{ number_format($item->price, 2) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-slate-300">
                            <div class="font-bold text-white">{{ $item->order->shipping_address['name'] ?? 'Buyer' }}</div>
                            <span class="text-[11px] text-slate-500">{{ $item->order->shipping_address['city'] ?? '' }}, {{ $item->order->shipping_address['country'] ?? '' }}</span>
                        </td>
                        <td class="p-4 font-black text-white">
                            ${{ number_format($item->total, 2) }}
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider font-mono
                                  {{ $item->vendor_status === 'delivered' ? 'bg-emerald-950 text-emerald-400' : 
                                    ($item->vendor_status === 'shipped' ? 'bg-purple-950 text-purple-400' : 'bg-indigo-950 text-indigo-400') }}">
                                {{ $item->vendor_status }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('vendor.orders.shipping-label', $item->id) }}" target="_blank" 
                                   class="py-1.5 px-3 rounded-lg bg-slate-900 text-slate-300 hover:text-white hover:bg-slate-850 text-xs font-semibold" title="Print Shipping Label">
                                    🏷️ Label
                                </a>
                                <a href="{{ route('vendor.orders.show', $item->id) }}" 
                                   class="py-1.5 px-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md">
                                    Manage &rarr;
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-xs text-slate-500">
                            No orders found under this fulfillment status.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $orderItems->links() }}
    </div>

</div>
@endsection
