@extends('layouts.admin')

@section('title', 'Payment Gateways & Ledger')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                Payment Gateways & Transactions
            </h1>
            <p class="text-slate-400 text-xs mt-1">Configure checkout payment rails, live switch processors, and review reconciled settlement transactions.</p>
        </div>
    </div>

    <!-- Payment Rails Configuration Cards -->
    <div class="space-y-4">
        <h2 class="text-sm font-bold text-white tracking-tight">Active Payment Gateways</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Credit / Debit Card -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $paymentMethods['card'] ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-500' }}">
                            {{ $paymentMethods['card'] ? 'ENABLED' : 'DISABLED' }}
                        </span>
                    </div>
                    <h3 class="font-bold text-white text-sm">Credit / Debit Cards</h3>
                    <p class="text-slate-400 text-xs mt-1">Stripe & Visa/Mastercard 3D Secure direct settlement processing.</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800">
                    <form action="{{ route('admin.payments.toggle', 'card') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-1.5 rounded-xl text-xs font-semibold transition {{ $paymentMethods['card'] ? 'bg-red-950/60 hover:bg-red-900 text-red-300 border border-red-800' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }}">
                            {{ $paymentMethods['card'] ? 'Disable Card Gateway' : 'Enable Card Gateway' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- UPI / QR Pay -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $paymentMethods['upi'] ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-500' }}">
                            {{ $paymentMethods['upi'] ? 'ENABLED' : 'DISABLED' }}
                        </span>
                    </div>
                    <h3 class="font-bold text-white text-sm">Instant UPI / QR</h3>
                    <p class="text-slate-400 text-xs mt-1">Virtual payment address, QR scan, and instant banking rails.</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800">
                    <form action="{{ route('admin.payments.toggle', 'upi') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-1.5 rounded-xl text-xs font-semibold transition {{ $paymentMethods['upi'] ? 'bg-red-950/60 hover:bg-red-900 text-red-300 border border-red-800' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }}">
                            {{ $paymentMethods['upi'] ? 'Disable UPI Gateway' : 'Enable UPI Gateway' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Digital Wallet -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $paymentMethods['wallet'] ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-500' }}">
                            {{ $paymentMethods['wallet'] ? 'ENABLED' : 'DISABLED' }}
                        </span>
                    </div>
                    <h3 class="font-bold text-white text-sm">Digital Wallets</h3>
                    <p class="text-slate-400 text-xs mt-1">Apple Pay, Google Pay, and pre-funded user wallet balances.</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800">
                    <form action="{{ route('admin.payments.toggle', 'wallet') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-1.5 rounded-xl text-xs font-semibold transition {{ $paymentMethods['wallet'] ? 'bg-red-950/60 hover:bg-red-900 text-red-300 border border-red-800' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }}">
                            {{ $paymentMethods['wallet'] ? 'Disable Wallet' : 'Enable Wallet' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Cash on Delivery (COD) -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $paymentMethods['cod'] ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-500' }}">
                            {{ $paymentMethods['cod'] ? 'ENABLED' : 'DISABLED' }}
                        </span>
                    </div>
                    <h3 class="font-bold text-white text-sm">Cash on Delivery</h3>
                    <p class="text-slate-400 text-xs mt-1">Collect cash upon courier doorstep delivery verification.</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800">
                    <form action="{{ route('admin.payments.toggle', 'cod') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-1.5 rounded-xl text-xs font-semibold transition {{ $paymentMethods['cod'] ? 'bg-red-950/60 hover:bg-red-900 text-red-300 border border-red-800' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }}">
                            {{ $paymentMethods['cod'] ? 'Disable COD' : 'Enable COD' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Settlement Transactions Table -->
    <div class="space-y-4">
        <h2 class="text-sm font-bold text-white tracking-tight flex items-center gap-2">
            Reconciled Transactions Ledger
            <span class="text-xs font-mono text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-full border border-amber-500/20">{{ $transactions->total() }} records</span>
        </h2>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 bg-slate-950/50 text-slate-400 uppercase tracking-wider font-mono text-[10px]">
                            <th class="py-3 px-4">Transaction Reference</th>
                            <th class="py-3 px-4">Order #</th>
                            <th class="py-3 px-4">Customer</th>
                            <th class="py-3 px-4">Gateway</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Settled Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-amber-400">
                                    {{ $tx->transaction_id }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-white">
                                    <a href="{{ route('admin.orders.show', $tx->id) }}" class="hover:text-amber-400 underline">{{ $tx->order_number }}</a>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-white">{{ $tx->user->name ?? 'Guest Buyer' }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $tx->user->email ?? 'N/A' }}</div>
                                </td>
                                <td class="py-3.5 px-4 uppercase text-[11px] font-mono text-slate-300">
                                    {{ $tx->payment_method }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-white">
                                    ${{ number_format($tx->total_amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($tx->payment_status === 'paid')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">SETTLED</span>
                                    @elseif($tx->payment_status === 'refunded')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20">REFUNDED</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">{{ strtoupper($tx->payment_status) }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-slate-400 text-[11px]">
                                    {{ $tx->created_at->format('Y-m-d H:i:s') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500">No payment transactions recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-800">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
