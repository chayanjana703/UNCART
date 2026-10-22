@extends('layouts.vendor')

@section('page-title', 'Merchant Analytics & Reports')

@section('content')
<div class="space-y-8">

    <!-- Top Action Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-white tracking-tight">Sales Performance & Feedback</h2>
            <p class="text-xs text-slate-500 mt-0.5">Comprehensive store telemetry and financial reports</p>
        </div>

        <a href="{{ route('vendor.analytics.export') }}" class="py-2.5 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/30 flex items-center gap-2 transition-all active:scale-95">
            <span>📥 Export Sales Report (CSV)</span>
        </a>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 shadow-xl space-y-1">
            <span class="text-xs font-mono text-slate-400">Total Lifetime Gross Volume</span>
            <div class="text-3xl font-black text-white">${{ number_format($grossSales, 2) }}</div>
            <p class="text-[11px] text-slate-500">From {{ $totalOrders }} confirmed client orders</p>
        </div>

        <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 shadow-xl space-y-1">
            <span class="text-xs font-mono text-slate-400">Average Customer Rating</span>
            <div class="text-3xl font-black text-amber-400">★ {{ number_format($avgRating, 1) }}</div>
            <p class="text-[11px] text-slate-500">Based on verified purchaser product reviews</p>
        </div>

        <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 shadow-xl space-y-1">
            <span class="text-xs font-mono text-slate-400">Estimated Store Traffic</span>
            <div class="text-3xl font-black text-indigo-400">14,890</div>
            <p class="text-[11px] text-slate-500">Page visits across active listings</p>
        </div>
    </div>

    <!-- Two-Column: Top Products & Recent Review Scores -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Top Products -->
        <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-850">
                Top Performing Products by Volume
            </h3>

            <div class="space-y-3">
                @forelse($topProducts as $p)
                    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-850 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3">
                            <img src="{{ $p->featured_image }}" alt="" class="w-10 h-10 rounded-xl object-cover border border-slate-800">
                            <div>
                                <h5 class="font-bold text-white truncate max-w-[200px]">{{ $p->name }}</h5>
                                <span class="text-[11px] text-slate-400 font-mono">${{ number_format($p->current_price, 2) }}</span>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="text-xs font-bold text-emerald-400 font-mono">{{ $p->sales_count }} sold</span>
                            <span class="text-[10px] text-slate-500 block">★ {{ number_format($p->rating_avg, 1) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-500">
                        No product sales recorded yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Customer Feedback -->
        <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-850">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">
                    Recent Customer Ratings
                </h3>
                <a href="{{ route('vendor.reviews') }}" class="text-xs text-indigo-400 font-bold hover:underline">
                    Moderate & Reply &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentReviews as $rev)
                    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-850 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-amber-400 font-mono">@for($i=0; $i<$rev->rating; $i++)★@endfor</span>
                            <span class="text-[10px] text-slate-500">{{ $rev->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="font-bold text-white line-clamp-1">{{ $rev->title }}</div>
                        <p class="text-slate-400 line-clamp-2">{{ $rev->comment }}</p>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-500">
                        No product reviews received yet.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
