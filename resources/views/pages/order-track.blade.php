@extends('layouts.app')

@section('title', 'Shipment Tracking #' . $order->order_number . ' — UNCART')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4" data-reveal>
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Real-Time Delivery Tracking</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Live updates for Order #{{ $order->order_number }}</p>
        </div>

        <a href="{{ route('orders.show', $order->order_number) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
            &larr; Order Details
        </a>
    </div>

    <!-- Carrier Header Strip -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-md mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6" data-reveal>
        <div>
            <span class="text-[10px] uppercase font-bold tracking-widest text-indigo-300 block">Assigned Delivery Courier</span>
            <h3 class="text-xl sm:text-2xl font-black mt-1">{{ $order->carrier_name ?: 'Global Express Logistics' }}</h3>
            <div class="flex items-center gap-2 text-xs text-slate-300 font-mono mt-3">
                <span class="text-slate-400">Waybill Code:</span>
                <strong class="text-white bg-white/10 px-3 py-1 rounded-full border border-white/10">{{ $order->tracking_number ?: 'TRK-902847291-EXP' }}</strong>
            </div>
        </div>

        <div class="text-left sm:text-right">
            <span class="text-[10px] uppercase font-bold tracking-widest text-slate-400 block">Estimated Delivery</span>
            <div class="text-base sm:text-lg font-bold text-white mt-1">
                @if($order->order_status === 'delivered')
                    Delivered on {{ $order->delivered_at ? $order->delivered_at->format('M d, Y') : 'Recent' }}
                @else
                    Today before 6:00 PM
                @endif
            </div>
            <span class="inline-block mt-2 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                {{ str_replace('_', ' ', $order->order_status) }}
            </span>
        </div>
    </div>

    <!-- Stepped Progress Tracker -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm mb-8 space-y-8" data-reveal>
        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100">
            Delivery Milestones
        </h3>

        <!-- Stepped Bar -->
        <div class="relative pl-6 sm:pl-0 sm:flex sm:items-center sm:justify-between">
            <div class="hidden sm:block absolute top-4 left-6 right-6 h-1 bg-slate-100 -z-0">
                @php
                    $steps = $order->tracking_timeline;
                    $completedCount = count(array_filter($steps, fn($s) => $s['completed']));
                    $pct = count($steps) > 1 ? (($completedCount - 1) / (count($steps) - 1)) * 100 : 0;
                @endphp
                <div class="bg-gradient-to-r from-indigo-600 to-emerald-500 h-1 transition-all duration-700" style="width: {{ $pct }}%"></div>
            </div>

            @foreach($steps as $idx => $step)
                <div class="relative z-10 flex sm:flex-col items-center gap-4 sm:gap-2 mb-6 sm:mb-0 last:mb-0 group">
                    <!-- Dot Indicator -->
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all duration-300 {{ $step['completed'] ? 'bg-indigo-600 text-white shadow-sm' : ($step['current'] ? 'bg-amber-400 text-slate-900 animate-pulse ring-4 ring-amber-100' : 'bg-slate-100 border border-slate-200 text-slate-400') }}">
                        @if($step['completed'])
                            ✓
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>

                    <!-- Step Label & Timestamp -->
                    <div class="text-left sm:text-center">
                        <div class="text-xs font-bold {{ $step['completed'] || $step['current'] ? 'text-slate-900' : 'text-slate-400' }}">
                            {{ $step['title'] ?? $step['label'] ?? '' }}
                        </div>
                        @if(!empty($step['time']))
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                {{ $step['time'] }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Destination Address Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm" data-reveal>
        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Delivery Destination</h4>
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 space-y-1">
            @if(is_array($order->shipping_address))
                <div class="font-bold text-slate-900">{{ $order->shipping_address['name'] ?? '' }}</div>
                <div>{{ $order->shipping_address['address_line1'] ?? '' }}</div>
                <div>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}</div>
                <div class="text-indigo-600 pt-1 font-semibold">{{ $order->shipping_address['phone'] ?? '' }}</div>
            @else
                <div>{{ $order->shipping_address ?? 'Standard address on file' }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
