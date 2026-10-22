@extends('layouts.admin')

@section('title', 'Platform Settings & Governance')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                Store Settings & Governance
            </h1>
            <p class="text-slate-400 text-xs mt-1">Configure global store parameters, currency formatting, tax rates, shipping fee schedules, and administrator access.</p>
        </div>
    </div>

    <!-- Main Settings Form & Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                @csrf

                <!-- General Identity -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6">
                    <h2 class="text-sm font-bold text-white tracking-tight mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Brand & Storefront Identity
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Store Name</label>
                            <input type="text" name="store_name" value="{{ old('store_name', $settings['store_name']) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Store Slogan / Tagline</label>
                            <input type="text" name="store_tagline" value="{{ old('store_tagline', $settings['store_tagline']) }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Primary Support Email</label>
                            <input type="email" name="store_email" value="{{ old('store_email', $settings['store_email']) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Support Telephone Hotline</label>
                            <input type="text" name="store_phone" value="{{ old('store_phone', $settings['store_phone']) }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Headquarters Physical Address</label>
                            <input type="text" name="store_address" value="{{ old('store_address', $settings['store_address']) }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                        </div>
                    </div>
                </div>

                <!-- Currency & Taxes -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6">
                    <h2 class="text-sm font-bold text-white tracking-tight mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Financials, Currency & Tax Slabs
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Currency Code</label>
                            <input type="text" name="currency_code" value="{{ old('currency_code', $settings['currency_code']) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white uppercase font-mono focus:border-amber-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Currency Symbol</label>
                            <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol']) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Standard Tax Rate (%)</label>
                            <input type="number" step="0.01" min="0" max="50" name="tax_rate" value="{{ old('tax_rate', $settings['tax_rate']) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-500">
                        </div>
                    </div>
                </div>

                <!-- Shipping Rates -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6">
                    <h2 class="text-sm font-bold text-white tracking-tight mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        Fulfillment & Delivery Schedules
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Standard Shipping ($)</label>
                            <input type="number" step="0.01" min="0" name="shipping_standard_rate" value="{{ old('shipping_standard_rate', $settings['shipping_standard_rate']) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Express Courier ($)</label>
                            <input type="number" step="0.01" min="0" name="shipping_express_rate" value="{{ old('shipping_express_rate', $settings['shipping_express_rate']) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Free Shipping Cart Minimum ($)</label>
                            <input type="number" step="0.01" min="0" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold']) }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-500">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-lg shadow-amber-500/20 transition">
                        Save System Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info: Admin Users and Activity Log (1 col) -->
        <div class="space-y-6">
            <!-- Active Admin Roles -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider font-mono mb-3 flex items-center justify-between">
                    <span>Authorized Admins</span>
                    <span class="text-amber-400">{{ $adminUsers->count() }}</span>
                </h3>

                <div class="space-y-2.5">
                    @foreach($adminUsers as $admin)
                        <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-950/60 border border-slate-800/80">
                            <img src="{{ $admin->avatar ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=80' }}" class="w-8 h-8 rounded-full object-cover">
                            <div class="text-xs overflow-hidden">
                                <div class="font-bold text-white truncate">{{ $admin->name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono truncate">{{ $admin->email }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Activity Logs Stream -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider font-mono mb-3">
                    Recent Administrative Audit
                </h3>

                <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    @forelse($activityLogs as $log)
                        <div class="text-xs border-b border-slate-800/60 pb-2.5 last:border-0 last:pb-0">
                            <div class="font-semibold text-amber-400">{{ $log->action }}</div>
                            <div class="text-slate-400 text-[11px] mt-0.5">{{ $log->description }}</div>
                            <div class="text-[10px] text-slate-500 font-mono mt-1">
                                {{ $log->user->name ?? 'System' }} • {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-500 py-4 text-center">No logs recorded.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
