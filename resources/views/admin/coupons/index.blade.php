@extends('layouts.admin')

@section('title', 'Marketing Promotions & Banners')

@section('content')
<div class="space-y-8" x-data="{ couponModal: false, bannerModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                Coupons & Marketing Banners
            </h1>
            <p class="text-slate-400 text-xs mt-1">Configure discount vouchers, threshold rules, and storefront promotional carousel hero banners.</p>
        </div>

        <div class="flex items-center gap-3">
            <button @click="couponModal = true" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/20 flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Coupon
            </button>
            <button @click="bannerModal = true" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                New Promo Banner
            </button>
        </div>
    </div>

    <!-- Active Coupons Section -->
    <div class="space-y-4">
        <h2 class="text-base font-bold text-white tracking-tight flex items-center gap-2">
            Discount Coupons
            <span class="text-xs font-mono text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-full border border-amber-500/20">{{ $coupons->count() }} active</span>
        </h2>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 bg-slate-950/50 text-slate-400 uppercase tracking-wider font-mono text-[10px]">
                            <th class="py-3 px-4">Coupon Code</th>
                            <th class="py-3 px-4">Discount Value</th>
                            <th class="py-3 px-4">Min Spend</th>
                            <th class="py-3 px-4">Usage Limit</th>
                            <th class="py-3 px-4">Expiration</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($coupons as $coupon)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3 px-4 font-mono font-black text-amber-400 text-sm">
                                    {{ $coupon->code }}
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-white">
                                    @if($coupon->discount_type === 'percent')
                                        {{ number_format($coupon->discount_value, 0) }}% Off
                                    @else
                                        ${{ number_format($coupon->discount_value, 2) }} Off
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-300">
                                    ${{ number_format($coupon->min_spend, 2) }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-300">
                                    {{ $coupon->times_used }} / {{ $coupon->usage_limit ?? 'Unlimited' }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-400 text-[11px]">
                                    {{ $coupon->expires_at ? $coupon->expires_at->format('M d, Y') : 'Never' }}
                                </td>
                                <td class="py-3 px-4">
                                    <form action="{{ route('admin.coupons.toggle', $coupon->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase transition {{ $coupon->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-500' }}">
                                            {{ $coupon->is_active ? 'Active' : 'Disabled' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('Eradicate coupon code {{ $coupon->code }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded-lg bg-slate-800 hover:bg-red-950/60 text-slate-400 hover:text-red-400 transition" title="Delete Coupon">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-500">No active coupons in circulation.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Promotional Banners Section -->
    <div class="space-y-4">
        <h2 class="text-base font-bold text-white tracking-tight flex items-center gap-2">
            Storefront Banners
            <span class="text-xs font-mono text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-full border border-amber-500/20">{{ $banners->count() }} live</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($banners as $banner)
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden group">
                    <div class="h-32 w-full relative overflow-hidden bg-slate-950">
                        <img src="{{ $banner->image }}" alt="{{ $banner->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>
                        @if($banner->badge)
                            <span class="absolute top-3 left-3 px-2 py-0.5 rounded-md bg-amber-500 text-slate-950 text-[10px] font-black uppercase tracking-wider">
                                {{ $banner->badge }}
                            </span>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-white text-sm line-clamp-1">{{ $banner->title }}</h3>
                        <p class="text-slate-400 text-xs line-clamp-2 mt-1">{{ $banner->subtitle ?? 'Featured marketplace banner spotlight' }}</p>

                        <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-mono text-[11px]">Link: {{ $banner->link }}</span>
                            <form action="{{ route('admin.coupons.banner.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Remove banner?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 font-semibold text-[11px] transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-slate-900/90 border border-slate-800 rounded-2xl p-8 text-center text-slate-500 text-xs">
                    No promotional hero banners currently configured.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Create Coupon Modal -->
    <div x-show="couponModal" x-transition.opacity class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="couponModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl relative">
            <h2 class="text-lg font-bold text-white mb-1">Create Promotional Coupon</h2>
            <p class="text-xs text-slate-400 mb-4">Mint a new voucher code for storefront checkout redemptions.</p>

            <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Coupon Voucher Code</label>
                        <input type="text" name="code" required placeholder="e.g. FLASH25, VIP50" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white uppercase font-mono font-bold focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Discount Type</label>
                        <select name="discount_type" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                            <option value="percent">Percentage (%)</option>
                            <option value="fixed">Fixed Currency ($)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Discount Value</label>
                        <input type="number" step="0.01" min="1" name="discount_value" required placeholder="25" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Minimum Cart Spend ($)</label>
                        <input type="number" step="0.01" min="0" name="min_spend" value="0.00" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Maximum Usage Cap</label>
                        <input type="number" min="1" name="usage_limit" value="100" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Expiration Date</label>
                        <input type="date" name="expires_at" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="couponModal = false" class="px-4 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white text-xs font-medium">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold">Publish Coupon</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Banner Modal -->
    <div x-show="bannerModal" x-transition.opacity class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="bannerModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl relative">
            <h2 class="text-lg font-bold text-white mb-1">Add Storefront Banner</h2>
            <p class="text-xs text-slate-400 mb-4">Publish a new promotional carousel banner on the homepage hero.</p>

            <form action="{{ route('admin.coupons.banner.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Banner Headline</label>
                    <input type="text" name="title" required placeholder="e.g. Autumn Precision Hardware Expo" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Subtitle / Descriptor</label>
                    <textarea name="subtitle" rows="2" placeholder="Up to 40% off flagship studio monitors and mechanical gear..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Badge Tag</label>
                        <input type="text" name="badge" placeholder="e.g. LIMITED EDITION, 40% OFF" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Image URL</label>
                        <input type="url" name="image" required placeholder="https://images.unsplash.com/..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Target Route / Link</label>
                        <input type="text" name="link" value="/shop" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Button Call to Action</label>
                        <input type="text" name="button_text" value="Shop Now" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="bannerModal = false" class="px-4 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white text-xs font-medium">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold">Publish Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
