@extends('layouts.admin')

@section('title', 'Platform Orders & Fulfillment')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                Orders & Fulfillment
                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    {{ $orders->total() }} Orders
                </span>
            </h1>
            <p class="text-slate-400 text-xs mt-1">Cross-vendor fulfillment oversight, carrier dispatch tracking, refund approvals, and CSV exports.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.export') }}" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:border-slate-700 text-xs font-semibold flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export All CSV
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by Order # (e.g. ORD-94821) or tracking number..." class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-200 placeholder-slate-500 transition">
                @if(request('q'))
                    <a href="{{ route('admin.orders.index', array_filter(request()->except('q'))) }}" class="absolute right-3 top-2.5 text-xs text-slate-500 hover:text-white">&times;</a>
                @endif
            </div>

            <div>
                <select name="status" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-300">
                    <option value="">All Fulfillment Statuses</option>
                    <option value="placed" {{ request('status') === 'placed' ? 'selected' : '' }}>Placed</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped / En Route</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <select name="return_status" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-300">
                    <option value="">All Return Requests</option>
                    <option value="requested" {{ request('return_status') === 'requested' ? 'selected' : '' }}>Return Requested</option>
                    <option value="refunded" {{ request('return_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>

                @if(request()->hasAny(['q', 'status', 'return_status']))
                    <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white text-xs transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/50 text-slate-400 uppercase tracking-wider font-mono text-[10px]">
                        <th class="py-3 px-4">Order ID</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Items</th>
                        <th class="py-3 px-4">Total Amount</th>
                        <th class="py-3 px-4">Payment</th>
                        <th class="py-3 px-4">Lifecycle Status</th>
                        <th class="py-3 px-4">Dispatched Carrier</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-amber-400">
                                <a href="{{ route('admin.orders.show', $order->id) }}">{{ $order->order_number }}</a>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white">{{ $order->user->name ?? 'Guest Checkout' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $order->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-300">
                                {{ $order->items->count() }} items
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-white">
                                ${{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($order->payment_status === 'paid')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">PAID ({{ strtoupper($order->payment_method) }})</span>
                                @elseif($order->payment_status === 'refunded')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20">REFUNDED</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">{{ strtoupper($order->payment_status) }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-200 border border-slate-700 capitalize">
                                    {{ str_replace('_', ' ', $order->order_status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-400 text-[11px]">
                                @if($order->tracking_number)
                                    <div>{{ $order->carrier_name ?? 'Standard' }}</div>
                                    <div class="text-[10px] text-amber-400 font-semibold">{{ $order->tracking_number }}</div>
                                @else
                                    <span class="text-slate-600">Pending label</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition">
                                    Inspect &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">No orders match filter query.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
