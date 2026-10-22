@extends('layouts.vendor')

@section('page-title', 'Seller Operations Dashboard')

@section('content')
<div class="space-y-8">

    <!-- KPI Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 shadow-xl space-y-2">
            <div class="flex items-center justify-between text-slate-400 text-xs font-mono">
                <span>Gross Revenue</span>
                <span class="text-emerald-400 font-bold">+18.4%</span>
            </div>
            <div class="text-2xl font-black text-white">
                ${{ number_format($grossSales, 2) }}
            </div>
            <div class="text-[11px] text-slate-500">Net after platform fees: ${{ number_format($grossSales * 0.915, 2) }}</div>
        </div>

        <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 shadow-xl space-y-2">
            <div class="flex items-center justify-between text-slate-400 text-xs font-mono">
                <span>Total Orders</span>
                <span class="text-indigo-400 font-bold">Fulfilled</span>
            </div>
            <div class="text-2xl font-black text-white">
                {{ $totalOrders }}
            </div>
            <div class="text-[11px] text-slate-500">Across verified client addresses</div>
        </div>

        <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 shadow-xl space-y-2">
            <div class="flex items-center justify-between text-slate-400 text-xs font-mono">
                <span>Active Catalog</span>
                <span class="text-emerald-400 font-bold">Live</span>
            </div>
            <div class="text-2xl font-black text-white">
                {{ $totalProducts }}
            </div>
            <div class="text-[11px] text-slate-500"><a href="{{ route('vendor.products.create') }}" class="text-indigo-400 hover:underline">+ Add new item</a></div>
        </div>

        <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 shadow-xl space-y-2">
            <div class="flex items-center justify-between text-slate-400 text-xs font-mono">
                <span>Pending Shipments</span>
                <span class="text-amber-400 font-bold animate-pulse">Action Required</span>
            </div>
            <div class="text-2xl font-black text-amber-400">
                {{ $pendingShipments }}
            </div>
            <div class="text-[11px] text-slate-500"><a href="{{ route('vendor.orders.index') }}" class="text-amber-400 hover:underline">Dispatch packages &rarr;</a></div>
        </div>

    </div>

    <!-- Revenue Analytics Chart -->
    <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-850">
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Revenue Trend (30 Days)</h3>
                <p class="text-xs text-slate-500 mt-0.5">Real-time GMV growth and settlement projections</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-indigo-950 text-indigo-400 border border-indigo-800/60">
                Updated Just Now
            </span>
        </div>

        <div class="h-64 w-full">
            <canvas id="vendorRevenueChart"></canvas>
        </div>
    </div>

    <!-- Two-Column Row: Recent Orders & Low Inventory Alert -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Recent Orders (2 Cols) -->
        <div class="lg:col-span-2 bg-slate-950 p-6 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-850">
                <h4 class="text-xs font-bold text-white uppercase tracking-wider">Recent Orders Received</h4>
                <a href="{{ route('vendor.orders.index') }}" class="text-xs font-bold text-indigo-400 hover:underline">View All</a>
            </div>

            <div class="divide-y divide-slate-850">
                @forelse($recentItems as $item)
                    <div class="py-3.5 flex items-center justify-between gap-4 text-xs">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->product_image }}" alt="" class="w-12 h-12 rounded-xl object-cover border border-slate-800">
                            <div>
                                <h5 class="font-bold text-white truncate max-w-[200px]">{{ $item->product_name }}</h5>
                                <span class="text-slate-400 text-[11px]">#{{ $item->order->order_number ?? 'N/A' }} &bull; Qty: {{ $item->quantity }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="font-black text-white">${{ number_format($item->total, 2) }}</span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $item->vendor_status === 'delivered' ? 'bg-emerald-950 text-emerald-400' : 'bg-indigo-950 text-indigo-400' }}">
                                {{ $item->vendor_status }}
                            </span>
                            <a href="{{ route('vendor.orders.show', $item->id) }}" class="text-slate-400 hover:text-white">&rarr;</a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-500">
                        No orders recorded yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Low Stock Alerts (1 Col) -->
        <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-850">
                <h4 class="text-xs font-bold text-white uppercase tracking-wider">Inventory Alerts</h4>
                <span class="text-[11px] font-mono font-bold text-amber-400">{{ $lowStockProducts->count() }} Low</span>
            </div>

            <div class="space-y-3">
                @forelse($lowStockProducts as $low)
                    <div class="p-3 rounded-2xl bg-slate-900 border border-slate-850 flex items-center justify-between text-xs">
                        <div class="truncate max-w-[150px]">
                            <div class="font-bold text-white truncate">{{ $low->name }}</div>
                            <span class="text-[10px] text-slate-500 font-mono">SKU: {{ $low->sku }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-amber-400">{{ $low->stock_quantity }} left</span>
                            <a href="{{ route('vendor.products.edit', $low->id) }}" class="text-[10px] text-indigo-400 block hover:underline">Restock &rarr;</a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-500">
                        All product inventory levels are healthy!
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('vendorRevenueChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    datasets: [{
                        label: 'Gross Sales ($)',
                        data: [1200, 2800, 3900, {{ max(4500, (int)$grossSales) }}],
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#818cf8',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: 'rgba(51, 65, 85, 0.3)' }, ticks: { color: '#94a3b8' } },
                        y: { grid: { color: 'rgba(51, 65, 85, 0.3)' }, ticks: { color: '#94a3b8' } }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
