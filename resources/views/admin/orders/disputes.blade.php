@extends('layouts.admin')

@section('title', 'Order Disputes & Return Resolutions')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                Disputes & Returns Resolution
                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    {{ $stats['requested'] }} Pending Resolution
                </span>
            </h1>
            <p class="text-slate-400 text-xs mt-1">Review customer return requests, inspect defect descriptions, approve merchandise returns, or issue direct credit refunds.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:border-slate-700 text-xs font-semibold flex items-center gap-2 transition">
                <span>&larr;</span> Back to All Orders
            </a>
        </div>
    </div>

    <!-- Stats Quick Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <a href="{{ route('admin.disputes.index') }}" class="p-4 rounded-2xl bg-slate-900/90 border {{ !request('status') ? 'border-amber-500/50 bg-amber-500/5' : 'border-slate-800' }} transition">
            <div class="text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold">Total Lodged</div>
            <div class="text-2xl font-black text-white mt-1">{{ $stats['total'] }}</div>
        </a>
        <a href="{{ route('admin.disputes.index', ['status' => 'requested']) }}" class="p-4 rounded-2xl bg-slate-900/90 border {{ request('status') === 'requested' ? 'border-amber-500 bg-amber-500/10' : 'border-slate-800' }} transition">
            <div class="text-[10px] font-mono uppercase tracking-wider text-amber-400 font-bold">Pending Review</div>
            <div class="text-2xl font-black text-amber-400 mt-1">{{ $stats['requested'] }}</div>
        </a>
        <a href="{{ route('admin.disputes.index', ['status' => 'approved']) }}" class="p-4 rounded-2xl bg-slate-900/90 border {{ request('status') === 'approved' ? 'border-blue-500 bg-blue-500/10' : 'border-slate-800' }} transition">
            <div class="text-[10px] font-mono uppercase tracking-wider text-blue-400 font-bold">Return Approved</div>
            <div class="text-2xl font-black text-blue-400 mt-1">{{ $stats['approved'] }}</div>
        </a>
        <a href="{{ route('admin.disputes.index', ['status' => 'refunded']) }}" class="p-4 rounded-2xl bg-slate-900/90 border {{ request('status') === 'refunded' ? 'border-emerald-500 bg-emerald-500/10' : 'border-slate-800' }} transition">
            <div class="text-[10px] font-mono uppercase tracking-wider text-emerald-400 font-bold">Refund Credited</div>
            <div class="text-2xl font-black text-emerald-400 mt-1">{{ $stats['refunded'] }}</div>
        </a>
        <a href="{{ route('admin.disputes.index', ['status' => 'rejected']) }}" class="p-4 rounded-2xl bg-slate-900/90 border {{ request('status') === 'rejected' ? 'border-rose-500 bg-rose-500/10' : 'border-slate-800' }} transition">
            <div class="text-[10px] font-mono uppercase tracking-wider text-rose-400 font-bold">Dispute Rejected</div>
            <div class="text-2xl font-black text-rose-400 mt-1">{{ $stats['rejected'] }}</div>
        </a>
    </div>

    <!-- Filters Bar -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.disputes.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-3 relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by Order #, customer name, email or return explanation..." class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-200 placeholder-slate-500 transition">
                @if(request('q'))
                    <a href="{{ route('admin.disputes.index', array_filter(request()->except('q'))) }}" class="absolute right-3 top-2.5 text-xs text-slate-500 hover:text-white">&times;</a>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-300">
                    <option value="">All Resolution Stages</option>
                    <option value="requested" {{ request('status') === 'requested' ? 'selected' : '' }}>Pending Review</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved Return</option>
                    <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>

                @if(request()->hasAny(['q', 'status']))
                    <a href="{{ route('admin.disputes.index') }}" class="px-3 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white text-xs transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Disputes Queue -->
    <div class="space-y-4">
        @forelse($disputes as $order)
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl transition-all" x-data="{ openResolve: false }">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center text-amber-400 font-mono font-bold text-xs">
                            #
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="font-mono font-bold text-sm text-white hover:text-amber-400 transition">
                                    {{ $order->order_number }}
                                </a>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider
                                    {{ $order->return_status === 'requested' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/30 animate-pulse' :
                                       ($order->return_status === 'approved' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/30' :
                                       ($order->return_status === 'refunded' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30')) }}">
                                    Status: {{ ucfirst($order->return_status) }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                Lodged on {{ $order->updated_at->format('M d, Y \a\t h:i A') }} ({{ $order->updated_at->diffForHumans() }})
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <div class="text-[10px] uppercase font-mono text-slate-400">Claim Value</div>
                            <div class="text-base font-black text-white font-mono">${{ number_format($order->total_amount, 2) }}</div>
                        </div>

                        <button type="button" @click="openResolve = !openResolve" 
                                class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-amber-500/20">
                            <span>Resolve Action</span>
                            <svg class="w-3.5 h-3.5 transform transition-transform" :class="openResolve ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Customer Statement & Items Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 py-4">
                    <!-- Customer Details & Reason (7 cols) -->
                    <div class="lg:col-span-7 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-slate-800 flex items-center justify-center font-bold text-xs text-slate-300">
                                {{ strtoupper(substr($order->user ? $order->user->name : 'G', 0, 1)) }}
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-200">{{ $order->user ? $order->user->name : 'Guest Customer' }}</span>
                                <span class="text-xs text-slate-500 ml-2 font-mono">{{ $order->user ? $order->user->email : 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-3.5">
                            <div class="text-[10px] font-mono uppercase tracking-wider text-amber-400 font-bold mb-1">Customer Reason For Return / Dispute:</div>
                            <p class="text-xs text-slate-200 leading-relaxed font-sans">
                                "{{ $order->return_reason ?: 'No customer explanation provided.' }}"
                            </p>
                        </div>

                        @if($order->notes)
                            <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-3 text-[11px] text-slate-400 font-mono">
                                <span class="text-slate-300 font-bold">Admin Resolution Notes:</span><br>
                                {!! nl2br(e($order->notes)) !!}
                            </div>
                        @endif
                    </div>

                    <!-- Items Summary (5 cols) -->
                    <div class="lg:col-span-5 bg-slate-950/50 border border-slate-800 rounded-2xl p-3.5">
                        <div class="text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold mb-2">Order Items ({{ $order->items->count() }})</div>
                        <div class="space-y-2 max-h-36 overflow-y-auto pr-1">
                            @foreach($order->items as $item)
                                <div class="flex items-center justify-between gap-2 text-xs">
                                    <div class="flex items-center gap-2 truncate">
                                        <img src="{{ $item->product ? $item->product->primary_image : 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=80' }}" 
                                             class="w-8 h-8 rounded-lg object-cover bg-slate-800 flex-shrink-0" alt="">
                                        <span class="text-slate-300 truncate">{{ $item->product_name }}</span>
                                    </div>
                                    <span class="font-mono text-slate-400 whitespace-nowrap">{{ $item->quantity }}x ${{ number_format($item->unit_price, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Interactive Resolution Tray (Collapsible) -->
                <div x-show="openResolve" x-transition class="pt-4 border-t border-slate-800 mt-2 bg-slate-950/80 -mx-5 -mb-5 p-5 rounded-b-3xl">
                    <form method="POST" action="{{ route('admin.disputes.resolve', $order->id) }}" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="cursor-pointer border border-slate-800 rounded-xl p-3 flex items-center gap-2.5 hover:border-blue-500/50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-500/10">
                                <input type="radio" name="return_status" value="approved" class="text-blue-500 focus:ring-0" {{ $order->return_status === 'approved' ? 'checked' : '' }}>
                                <div>
                                    <div class="font-bold text-xs text-white">Approve Return</div>
                                    <div class="text-[10px] text-slate-400">Instruct return shipment</div>
                                </div>
                            </label>

                            <label class="cursor-pointer border border-slate-800 rounded-xl p-3 flex items-center gap-2.5 hover:border-emerald-500/50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500/10">
                                <input type="radio" name="return_status" value="refunded" class="text-emerald-500 focus:ring-0" {{ $order->return_status === 'refunded' ? 'checked' : '' }}>
                                <div>
                                    <div class="font-bold text-xs text-white">Credit Full Refund</div>
                                    <div class="text-[10px] text-slate-400">${{ number_format($order->total_amount, 2) }} to customer</div>
                                </div>
                            </label>

                            <label class="cursor-pointer border border-slate-800 rounded-xl p-3 flex items-center gap-2.5 hover:border-rose-500/50 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-500/10">
                                <input type="radio" name="return_status" value="rejected" class="text-rose-500 focus:ring-0" {{ $order->return_status === 'rejected' ? 'checked' : '' }}>
                                <div>
                                    <div class="font-bold text-xs text-white">Reject Dispute</div>
                                    <div class="text-[10px] text-slate-400">Disallow return claim</div>
                                </div>
                            </label>
                        </div>

                        <div>
                            <input type="text" name="admin_notes" placeholder="Resolution justification notes sent to customer (e.g. Return shipping label issued / Item verified damaged)..." 
                                   class="w-full bg-slate-900 border border-slate-800 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-200">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-1">
                            <button type="button" @click="openResolve = false" class="px-3 py-1.5 rounded-xl border border-slate-800 text-xs text-slate-400 hover:text-white">
                                Dismiss
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold transition shadow-lg shadow-amber-500/20">
                                Confirm Resolution
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-12 text-center text-slate-500">
                <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-slate-800 flex items-center justify-center text-emerald-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-sm font-bold text-white">No return disputes pending resolution</h3>
                <p class="text-xs text-slate-400 mt-1">All customer claims have been reviewed, approved, or refunded.</p>
            </div>
        @endforelse
    </div>

    @if($disputes->hasPages())
        <div class="p-4 border-t border-slate-800">
            {{ $disputes->links() }}
        </div>
    @endif
</div>
@endsection
