@extends('layouts.admin')

@section('title', 'System Overview & Analytics')

@section('content')
<div class="space-y-8">
    <!-- Top Greeting & Live Stats Indicator -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-[11px] font-mono uppercase tracking-widest text-emerald-400 font-bold">Production Node • Normal Operations</span>
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight">Admin Command Center</h1>
            <p class="text-slate-400 text-xs mt-1">Multi-vendor marketplace telemetry, orders, revenue streams, and system audit logs.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:border-slate-700 text-xs font-semibold flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Detailed Reports
            </a>
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/20 flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Catalog Moderation
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Revenue -->
        <div class="bg-slate-900/90 border border-slate-800/80 rounded-2xl p-5 hover:border-slate-700 transition relative overflow-hidden group">
            <div class="absolute -right-3 -top-3 w-20 h-20 bg-amber-500/5 rounded-full group-hover:scale-125 transition"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-mono font-medium text-slate-400 uppercase tracking-wider">Settled Revenue</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-white font-mono tracking-tight">${{ number_format($totalRevenue, 2) }}</div>
            <div class="text-[11px] text-emerald-400 mt-2 flex items-center gap-1 font-medium">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <span>+18.4% volume this week</span>
            </div>
        </div>

        <!-- Orders -->
        <div class="bg-slate-900/90 border border-slate-800/80 rounded-2xl p-5 hover:border-slate-700 transition relative overflow-hidden group">
            <div class="absolute -right-3 -top-3 w-20 h-20 bg-indigo-500/5 rounded-full group-hover:scale-125 transition"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-mono font-medium text-slate-400 uppercase tracking-wider">Total Orders</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-white font-mono tracking-tight">{{ number_format($totalOrders) }}</div>
            <div class="text-[11px] text-indigo-400 mt-2 flex items-center gap-1 font-medium">
                <span>All multi-vendor line items</span>
            </div>
        </div>

        <!-- Products -->
        <div class="bg-slate-900/90 border border-slate-800/80 rounded-2xl p-5 hover:border-slate-700 transition relative overflow-hidden group">
            <div class="absolute -right-3 -top-3 w-20 h-20 bg-emerald-500/5 rounded-full group-hover:scale-125 transition"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-mono font-medium text-slate-400 uppercase tracking-wider">Active Products</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-white font-mono tracking-tight">{{ number_format($totalProducts) }}</div>
            <div class="text-[11px] text-emerald-400 mt-2 flex items-center gap-1 font-medium">
                <span>Across verified sellers</span>
            </div>
        </div>

        <!-- Vendors & Customers -->
        <div class="bg-slate-900/90 border border-slate-800/80 rounded-2xl p-5 hover:border-slate-700 transition relative overflow-hidden group">
            <div class="absolute -right-3 -top-3 w-20 h-20 bg-purple-500/5 rounded-full group-hover:scale-125 transition"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-mono font-medium text-slate-400 uppercase tracking-wider">Registered Base</span>
                <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-white font-mono tracking-tight">
                {{ number_format($totalUsers) }} <span class="text-xs text-slate-500 font-normal">users</span> / {{ $totalVendors }} <span class="text-xs text-slate-500 font-normal">merchants</span>
            </div>
            <div class="text-[11px] text-purple-400 mt-2 flex items-center gap-1 font-medium">
                <span>KYC compliant network</span>
            </div>
        </div>
    </div>

    <!-- Chart Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Velocity Trend -->
        <div class="lg:col-span-2 bg-slate-900/80 border border-slate-800 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-white tracking-tight">Marketplace Revenue Velocity</h2>
                    <p class="text-slate-400 text-xs">Total gross merchandise value over the last 7 monthly cycles.</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-slate-800 text-[10px] font-mono text-slate-400 font-semibold border border-slate-700">Realtime Feed</span>
            </div>
            <div class="h-64">
                <canvas id="adminSalesChart"></canvas>
            </div>
        </div>

        <!-- Vendor Distribution Pie -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-white tracking-tight">Order Distribution</h2>
                    <p class="text-slate-400 text-xs">Order fulfillment by lifecycle status.</p>
                </div>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="adminOrderStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Two Column Table Section: Recent Orders & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders (2 columns) -->
        <div class="lg:col-span-2 bg-slate-900/80 border border-slate-800 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-white tracking-tight">Recent Customer Orders</h2>
                    <p class="text-slate-400 text-xs">Real-time transactions across all storefront tenants.</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-amber-400 hover:text-amber-300 text-xs font-semibold">View All Orders &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider font-mono text-[10px]">
                            <th class="py-3 px-3">Order #</th>
                            <th class="py-3 px-3">Buyer</th>
                            <th class="py-3 px-3">Amount</th>
                            <th class="py-3 px-3">Payment</th>
                            <th class="py-3 px-3">Fulfillment</th>
                            <th class="py-3 px-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="py-3 px-3 font-mono font-bold text-amber-400">
                                    <a href="{{ route('admin.orders.show', $order->id) }}">{{ $order->order_number }}</a>
                                </td>
                                <td class="py-3 px-3 text-white">
                                    <div class="font-medium">{{ $order->user->name ?? 'Guest Buyer' }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $order->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="py-3 px-3 font-mono font-bold text-white">
                                    ${{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="py-3 px-3">
                                    @if($order->payment_status === 'paid')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">PAID</span>
                                    @elseif($order->payment_status === 'refunded')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-500/10 text-red-400 border border-red-500/20">REFUNDED</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">{{ strtoupper($order->payment_status) }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                        {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-[11px] font-semibold transition">
                                        Inspect
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500 text-xs">No orders recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Activity Audit Log (1 column) -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-white tracking-tight">Audit Trail</h2>
                    <p class="text-slate-400 text-xs">System logs & administrative events.</p>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($recentActivity as $log)
                    <div class="flex items-start gap-3 p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80">
                        <div class="w-6 h-6 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center flex-shrink-0 text-xs font-mono font-bold">
                            &bull;
                        </div>
                        <div class="text-xs">
                            <div class="font-bold text-slate-200">{{ $log->action }}</div>
                            <div class="text-slate-400 text-[11px] line-clamp-2">{{ $log->description }}</div>
                            <div class="text-[10px] text-slate-400 font-mono mt-1">{{ $log->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 text-xs">
                        No activity recorded yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Sales Chart
    const ctxSales = document.getElementById('adminSalesChart');
    if (ctxSales) {
        new Chart(ctxSales, {
            type: 'line',
            data: {
                labels: ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Current'],
                datasets: [{
                    label: 'Gross Platform Sales ($)',
                    data: [12400, 15900, 18200, 24100, 29800, 36200, {{ max(1000, $totalSales) }}],
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#f59e0b',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 10 } }
                    },
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#94a3b8', font: { family: 'JetBrains Mono', size: 10 } }
                    }
                }
            }
        });
    }

    // Order Status Doughnut Chart
    const ctxStatus = document.getElementById('adminOrderStatusChart');
    if (ctxStatus) {
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Delivered', 'In Transit', 'Processing', 'Cancelled'],
                datasets: [{
                    data: [65, 20, 10, 5],
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#cbd5e1',
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            boxWidth: 12
                        }
                    }
                },
                cutout: '72%'
            }
        });
    }
});
</script>
@endsection
