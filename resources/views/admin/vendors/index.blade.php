@extends('layouts.admin')

@section('title', 'Vendor Oversight & KYC')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                Vendor Management
                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    {{ $vendors->total() }} Merchants
                </span>
            </h1>
            <p class="text-slate-400 text-xs mt-1">Audit merchant KYC credentials, commission splits, storefront status, and payout authorizations.</p>
        </div>
    </div>

    <!-- Pending Payout Authorizations Alert if any -->
    @if($pendingPayouts->isNotEmpty())
        <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                <h3 class="text-xs font-bold uppercase tracking-wider text-amber-300 font-mono">Pending Merchant Payout Requests ({{ $pendingPayouts->count() }})</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($pendingPayouts as $payout)
                    <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-3.5 flex items-center justify-between gap-3">
                        <div>
                            <div class="font-bold text-white text-xs">{{ $payout->vendor->shop_name ?? 'Vendor' }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">Amount: <span class="text-emerald-400 font-bold">${{ number_format($payout->amount, 2) }}</span></div>
                            <div class="text-[10px] text-slate-500">{{ $payout->payment_method }} • {{ $payout->created_at->diffForHumans() }}</div>
                        </div>
                        <form action="{{ route('admin.vendors.payout.approve', $payout->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition shadow-sm">
                                Approve
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Filters Bar -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.vendors.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <select name="status" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-3 py-2 text-xs text-slate-300 transition">
                    <option value="">All Operating States</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Stores</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Onboarding</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended Stores</option>
                </select>
            </div>

            <div>
                <select name="kyc" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-3 py-2 text-xs text-slate-300 transition">
                    <option value="">All KYC Statuses</option>
                    <option value="verified" {{ request('kyc') === 'verified' ? 'selected' : '' }}>KYC Verified</option>
                    <option value="pending" {{ request('kyc') === 'pending' ? 'selected' : '' }}>KYC Pending Verification</option>
                    <option value="rejected" {{ request('kyc') === 'rejected' ? 'selected' : '' }}>KYC Rejected</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition">
                    Apply Filter
                </button>
                @if(request()->hasAny(['status', 'kyc']))
                    <a href="{{ route('admin.vendors.index') }}" class="px-3 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white text-xs transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Vendors Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/50 text-slate-400 uppercase tracking-wider font-mono text-[10px]">
                        <th class="py-3 px-4">Merchant & Shop</th>
                        <th class="py-3 px-4">Owner Account</th>
                        <th class="py-3 px-4">Products</th>
                        <th class="py-3 px-4">Commission</th>
                        <th class="py-3 px-4">KYC State</th>
                        <th class="py-3 px-4">Store Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($vendors as $vendor)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $vendor->logo ?? 'https://images.unsplash.com/photo-1572021335469-31706a17aaef?w=120' }}" alt="{{ $vendor->shop_name }}" class="w-9 h-9 rounded-xl object-cover bg-slate-800 border border-slate-700">
                                    <div>
                                        <div class="font-bold text-white">{{ $vendor->shop_name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $vendor->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-white font-medium">{{ $vendor->user->name ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $vendor->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-300">
                                {{ $vendor->products_count }} items
                            </td>
                            <td class="py-3.5 px-4 font-mono text-amber-400 font-bold">
                                {{ number_format($vendor->commission_rate, 1) }}%
                            </td>
                            <td class="py-3.5 px-4">
                                @if($vendor->kyc_status === 'verified')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">VERIFIED</span>
                                @elseif($vendor->kyc_status === 'rejected')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20">REJECTED</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">PENDING KYC</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($vendor->status === 'active')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">ACTIVE</span>
                                @elseif($vendor->status === 'suspended')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20">SUSPENDED</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">PENDING</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.vendors.show', $vendor->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition">
                                    Manage Vendor &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">No vendors found matching filter criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $vendors->links() }}
        </div>
    </div>
</div>
@endsection
