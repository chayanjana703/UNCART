@extends('layouts.app')

@section('title', 'Live Tracking #' . $order->order_number . ' - ShopiCart')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Real-Time Shipment Tracker</h1>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Live telemetric tracking for Order #{{ $order->order_number }}</p>
        </div>

        <a href="{{ route('orders.show', $order->order_number) }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
            &larr; Order Details
        </a>
    </div>

    <!-- Carrier Header Strip -->
    <div class="bg-gradient-to-r from-slate-950 via-indigo-950 to-slate-950 text-white rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div>
            <span class="text-[10px] uppercase font-bold tracking-widest text-emerald-400 font-mono block">Designated Carrier</span>
            <h3 class="text-xl font-black mt-1">{{ $order->carrier_name ?: 'Global Express Logistics' }}</h3>
            <div class="flex items-center gap-2 text-xs text-slate-300 font-mono mt-2">
                <span>Waybill Tracking Number:</span>
                <strong class="text-white bg-slate-900/90 px-2.5 py-1 rounded-lg border border-slate-700/80">{{ $order->tracking_number ?: 'TRK-902847291-EXP' }}</strong>
            </div>
        </div>

        <div class="text-left sm:text-right">
            <span class="text-[10px] uppercase font-bold tracking-widest text-slate-400 font-mono block">Estimated Delivery</span>
            <div class="text-lg font-bold text-white mt-1">
                @if($order->order_status === 'delivered')
                    Delivered on {{ $order->delivered_at ? $order->delivered_at->format('M d, Y') : 'Recent' }}
                @else
                    Today before 6:00 PM
                @endif
            </div>
            <span class="inline-block mt-2 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $order->status_badge['bg'] }}">
                {{ $order->status_badge['label'] }}
            </span>
        </div>
    </div>

    <!-- Animated Stepped Progress Tracker (Emil Kowalski Physics & Hierarchy) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm mb-8 space-y-8">
        <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-100 dark:border-slate-800">
            Fulfillment Progress
        </h3>

        <!-- Stepped Bar -->
        <div class="relative pl-6 sm:pl-0 sm:flex sm:items-center sm:justify-between">
            <!-- Timeline Connectors for Desktop -->
            <div class="hidden sm:block absolute top-4 left-6 right-6 h-1 bg-slate-200 dark:bg-slate-800 -z-0">
                @php
                    $steps = $order->tracking_timeline;
                    $completedCount = count(array_filter($steps, fn($s) => $s['completed']));
                    $percent = count($steps) > 1 ? (($completedCount - 1) / (count($steps) - 1)) * 100 : 0;
                @endphp
                <div class="h-full bg-gradient-to-r from-indigo-500 to-emerald-400 rounded-full transition-all duration-700 ease-[cubic-bezier(0.16,1,0.3,1)]" style="width: {{ $percent }}%"></div>
            </div>

            <!-- Steps -->
            @foreach($order->tracking_timeline as $idx => $step)
                <div class="relative sm:text-center pb-8 sm:pb-0 z-10 flex sm:flex-col items-start sm:items-center gap-4 sm:gap-2">
                    
                    <!-- Circle Beacon -->
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all duration-300 shadow-md flex-shrink-0"
                         class="{{ $step['completed'] ? 'bg-indigo-600 text-white ring-4 ring-indigo-100 dark:ring-indigo-950' : 'bg-slate-200 dark:bg-slate-800 text-slate-400' }} {{ $step['current'] ? 'ring-4 ring-emerald-400 bg-emerald-600 text-white animate-pulse' : '' }}">
                        @if($step['completed'] && !$step['current'])
                            ✓
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>

                    <!-- Label -->
                    <div>
                        <div class="text-xs font-bold {{ $step['completed'] ? 'text-slate-900 dark:text-white' : 'text-slate-400' }}">
                            {{ $step['title'] }}
                        </div>
                        <div class="text-[10px] text-slate-500 mt-0.5 max-w-[130px] sm:mx-auto">
                            {{ $step['desc'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Detailed Carrier Checkpoint Log -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-100 dark:border-slate-800">
            Carrier Checkpoints Activity
        </h3>

        <div class="space-y-4 text-xs">
            <div class="flex items-start gap-4">
                <span class="font-mono text-slate-400 text-[11px] whitespace-nowrap">{{ now()->format('h:i A') }}</span>
                <div>
                    <strong class="text-slate-900 dark:text-white block font-bold">Courier Out for Final Delivery</strong>
                    <span class="text-slate-500">Agent assigned with electric cargo vehicle for rapid drop-off</span>
                </div>
            </div>
            <div class="flex items-start gap-4">
                <span class="font-mono text-slate-400 text-[11px] whitespace-nowrap">{{ now()->subHours(3)->format('h:i A') }}</span>
                <div>
                    <strong class="text-slate-900 dark:text-white block font-bold">Arrived at Local Distribution Facility</strong>
                    <span class="text-slate-500">Sorted and scanned at Metro Hub Sorting Terminal</span>
                </div>
            </div>
            <div class="flex items-start gap-4">
                <span class="font-mono text-slate-400 text-[11px] whitespace-nowrap">{{ now()->subHours(10)->format('h:i A') }}</span>
                <div>
                    <strong class="text-slate-900 dark:text-white block font-bold">In Transit - Air Freight Departed</strong>
                    <span class="text-slate-500">Package scanned into cargo container Flight EXP-402</span>
                </div>
            </div>
            <div class="flex items-start gap-4">
                <span class="font-mono text-slate-400 text-[11px] whitespace-nowrap">{{ $order->created_at->format('h:i A') }}</span>
                <div>
                    <strong class="text-slate-900 dark:text-white block font-bold">Merchant Dispatched Package</strong>
                    <span class="text-slate-500">Origin facility packaged, weighed and transferred custody to carrier</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
