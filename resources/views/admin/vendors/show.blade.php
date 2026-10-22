@extends('layouts.admin')

@section('title', 'Merchant Audit: ' . $vendor->shop_name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex items-center gap-2 text-xs text-slate-400 font-mono">
        <a href="{{ route('admin.vendors.index') }}" class="hover:text-amber-400 transition">&larr; Back to Vendors</a>
        <span>/</span>
        <span class="text-slate-200">{{ $vendor->shop_name }}</span>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <img src="{{ $vendor->logo ?? 'https://images.unsplash.com/photo-1572021335469-31706a17aaef?w=200' }}" alt="{{ $vendor->shop_name }}" class="w-16 h-16 rounded-2xl object-cover bg-slate-800 border-2 border-slate-700 shadow-md">
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                    {{ $vendor->shop_name }}
                    @if($vendor->status === 'active')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase">Active</span>
                    @elseif($vendor->status === 'suspended')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20 uppercase">Suspended</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 uppercase">Pending</span>
                    @endif
                </h1>
                <p class="text-slate-400 text-xs mt-1">
                    Managed by <strong class="text-white">{{ $vendor->user->name ?? 'Merchant User' }}</strong> ({{ $vendor->user->email ?? 'N/A' }})
                    • Member since {{ $vendor->created_at->format('M Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Quick Telemetry Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
            <div class="text-[11px] font-mono text-slate-400 uppercase">Commission Cut</div>
            <div class="text-xl font-bold font-mono text-amber-400 mt-1">{{ number_format($vendor->commission_rate, 1) }}%</div>
        </div>
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
            <div class="text-[11px] font-mono text-slate-400 uppercase">Live Products</div>
            <div class="text-xl font-bold font-mono text-white mt-1">{{ $vendor->products->count() }} items</div>
        </div>
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
            <div class="text-[11px] font-mono text-slate-400 uppercase">Rating Score</div>
            <div class="text-xl font-bold font-mono text-emerald-400 mt-1">{{ number_format($vendor->rating, 1) }} / 5.0</div>
        </div>
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
            <div class="text-[11px] font-mono text-slate-400 uppercase">KYC State</div>
            <div class="text-xl font-bold font-mono {{ $vendor->kyc_status === 'verified' ? 'text-emerald-400' : ($vendor->kyc_status === 'rejected' ? 'text-red-400' : 'text-amber-400') }} mt-1 capitalize">
                {{ $vendor->kyc_status }}
            </div>
        </div>
    </div>

    <!-- Two Columns: Governance Controls & Store Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Governance Form -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6">
            <h2 class="text-sm font-bold text-white tracking-tight mb-1">Administrative Controls</h2>
            <p class="text-xs text-slate-400 mb-5">Override merchant operating privileges and commission fee schedule.</p>

            <form action="{{ route('admin.vendors.status', $vendor->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Operating Status</label>
                    <select name="status" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-white">
                        <option value="active" {{ $vendor->status === 'active' ? 'selected' : '' }}>Active (Allow Sales)</option>
                        <option value="pending" {{ $vendor->status === 'pending' ? 'selected' : '' }}>Pending Onboarding</option>
                        <option value="suspended" {{ $vendor->status === 'suspended' ? 'selected' : '' }}>Suspended (Freeze Products)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">KYC Compliance State</label>
                    <select name="kyc_status" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-white">
                        <option value="verified" {{ $vendor->kyc_status === 'verified' ? 'selected' : '' }}>Verified (Passed Documentation)</option>
                        <option value="pending" {{ $vendor->kyc_status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                        <option value="rejected" {{ $vendor->kyc_status === 'rejected' ? 'selected' : '' }}>Rejected (Inadequate Documentation)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Platform Commission Rate (%)</label>
                    <input type="number" step="0.1" min="0" max="100" name="commission_rate" value="{{ old('commission_rate', $vendor->commission_rate) }}" required class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-white font-mono">
                    <p class="text-[10px] text-slate-500 mt-1">Platform deduction percentage on every settled order line item.</p>
                </div>

                <div class="pt-3 border-t border-slate-800">
                    <button type="submit" class="w-full px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold transition shadow-lg shadow-amber-500/20">
                        Update Vendor Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Merchant Profile Details & Banking Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Merchant Profile Card -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6">
                <h2 class="text-sm font-bold text-white tracking-tight mb-4">Merchant KYC & Banking Dossier</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 text-[11px] block">Business Name / Trade</span>
                        <span class="font-bold text-white">{{ $vendor->shop_name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Support Email</span>
                        <span class="font-mono text-white">{{ $vendor->support_email ?? 'Not provided' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Support Telephone</span>
                        <span class="font-mono text-white">{{ $vendor->support_phone ?? 'Not provided' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Tax / VAT / GST Number</span>
                        <span class="font-mono text-amber-400 font-bold">{{ $vendor->tax_number ?? 'UNREGISTERED' }}</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-slate-400 text-[11px] block">Disbursement Banking Information</span>
                        <div class="mt-1 p-3 bg-slate-950 rounded-xl font-mono text-[11px] text-slate-300 border border-slate-800">
                            @if(!empty($vendor->bank_details))
                                @if(is_array($vendor->bank_details))
                                    @foreach($vendor->bank_details as $k => $v)
                                        <div><span class="text-slate-500">{{ ucfirst(str_replace('_', ' ', $k)) }}:</span> {{ $v }}</div>
                                    @endforeach
                                @else
                                    {{ $vendor->bank_details }}
                                @endif
                            @else
                                <span class="text-slate-500">No banking details entered by vendor yet.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payout History -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6">
                <h2 class="text-sm font-bold text-white tracking-tight mb-4">Disbursement History</h2>
                @if($vendor->payouts->isEmpty())
                    <p class="text-xs text-slate-500">No payouts have been requested by this merchant.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider font-mono text-[10px]">
                                    <th class="py-2.5 px-3">Payout ID</th>
                                    <th class="py-2.5 px-3">Requested</th>
                                    <th class="py-2.5 px-3">Amount</th>
                                    <th class="py-2.5 px-3">Method</th>
                                    <th class="py-2.5 px-3">Status</th>
                                    <th class="py-2.5 px-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60">
                                @foreach($vendor->payouts as $payout)
                                    <tr>
                                        <td class="py-2.5 px-3 font-mono font-bold text-amber-400">#{{ $payout->id }}</td>
                                        <td class="py-2.5 px-3 text-slate-400 text-[11px]">{{ $payout->created_at->format('M d, Y') }}</td>
                                        <td class="py-2.5 px-3 font-mono font-bold text-white">${{ number_format($payout->amount, 2) }}</td>
                                        <td class="py-2.5 px-3 uppercase text-[10px] text-slate-300 font-mono">{{ $payout->payment_method }}</td>
                                        <td class="py-2.5 px-3">
                                            @if($payout->status === 'approved')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">PAID</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">PENDING</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 text-right">
                                            @if($payout->status === 'pending')
                                                <form action="{{ route('admin.vendors.payout.approve', $payout->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-semibold transition">
                                                        Approve
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-slate-500 text-[11px]">Processed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
