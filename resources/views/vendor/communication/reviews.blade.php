@extends('layouts.vendor')

@section('title', 'Customer Reviews & Feedback')

@section('content')
<div class="space-y-6" x-data="{ photoModal: false, activeModalPhoto: '', modalBuyer: '' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                Reviews & Ratings
                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                    {{ $reviews->total() }} Reviews
                </span>
            </h1>
            <p class="text-slate-400 text-xs mt-1">Direct feedback from buyers on your products. Publish official store replies to enhance trust and reputation.</p>
        </div>
    </div>

    <!-- Review Feed -->
    @if($reviews->isEmpty())
        <div class="bg-slate-950 border border-slate-800 rounded-2xl p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            </div>
            <h3 class="text-base font-bold text-white mb-1">No Reviews Yet</h3>
            <p class="text-slate-400 text-xs max-w-sm mx-auto">When verified customers purchase and review your products, their ratings and photos will show up here.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($reviews as $review)
                <div class="bg-slate-950 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition" x-data="{ replying: false }">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-4 border-b border-slate-800/80">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-sm font-bold text-amber-400">
                                {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-sm text-white">{{ $review->user->name ?? 'Verified Buyer' }}</h3>
                                    @if($review->is_verified_purchase)
                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Verified Purchase</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <!-- Stars -->
                                    <div class="flex items-center text-amber-400 text-xs">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                ★
                                            @else
                                                <span class="text-slate-700">★</span>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-200">{{ $review->title }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">• {{ $review->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button @click="replying = !replying" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition">
                                <span x-text="replying ? 'Close Reply' : '{{ $review->vendor_reply ? 'Edit Official Reply' : 'Official Seller Reply' }}'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Referenced Product -->
                    @if($review->product)
                        <div class="mt-3 py-2 px-3 rounded-xl bg-slate-900/60 border border-slate-800/60 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="{{ $review->product->primary_image }}" alt="{{ $review->product->name }}" class="w-9 h-9 rounded-lg object-cover bg-slate-800 flex-shrink-0">
                                <div class="text-xs">
                                    <a href="{{ route('products.show', $review->product->slug) }}" target="_blank" class="text-indigo-400 hover:underline font-semibold">
                                        {{ $review->product->name }}
                                    </a>
                                    <div class="text-[11px] text-slate-400 font-mono">SKU: {{ $review->product->sku }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-[11px] font-mono">
                                <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-semibold" title="Helpful votes from buyers">
                                    👍 {{ $review->helpful_votes ?? 0 }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 border border-slate-700 font-semibold" title="Unhelpful votes">
                                    👎 {{ $review->unhelpful_votes ?? 0 }}
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Review Comment -->
                    <div class="mt-4 text-xs text-slate-300 leading-relaxed bg-slate-900/40 p-4 rounded-xl border border-slate-800/40">
                        {{ $review->comment }}
                    </div>

                    <!-- Review Photos if any -->
                    @if(!empty($review->photos) && count($review->photos) > 0)
                        <div class="mt-3.5 pt-3 border-t border-slate-800/60">
                            <div class="text-[11px] font-semibold text-slate-400 mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Buyer Photos ({{ count($review->photos) }})
                            </div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                @foreach($review->photos as $photo)
                                    <button type="button" 
                                            @click="activeModalPhoto = '{{ $photo }}'; modalBuyer = '{{ addslashes($review->user->name ?? 'Verified Buyer') }}'; photoModal = true"
                                            class="group relative w-16 h-16 rounded-xl overflow-hidden border border-slate-700 bg-slate-900 hover:border-indigo-500 hover:shadow-lg transition cursor-zoom-in"
                                            title="Click to view full photo">
                                        <img src="{{ $photo }}" alt="Review photo" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white opacity-0 group-hover:opacity-100 transition drop-shadow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                            </svg>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Seller Official Reply -->
                    @if($review->vendor_reply)
                        <div class="mt-4 pl-4 border-l-2 border-indigo-500 bg-indigo-950/20 rounded-r-xl p-3.5">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[11px] font-bold text-indigo-400">Response from {{ $vendor->shop_name }} (Seller)</span>
                                @if($review->vendor_replied_at)
                                    <span class="text-[10px] text-slate-400 font-mono">• {{ \Carbon\Carbon::parse($review->vendor_replied_at)->diffForHumans() }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-300">{{ $review->vendor_reply }}</p>
                        </div>
                    @endif

                    <!-- Reply Form Drawer -->
                    <div x-show="replying" x-transition class="mt-4 pt-4 border-t border-slate-800">
                        <form action="{{ route('vendor.reviews.reply', $review->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1">Official Response (Visible to all shoppers on product page):</label>
                                <textarea name="vendor_reply" rows="3" required placeholder="Thank the customer or address their product feedback gracefully..." class="w-full bg-slate-900 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-3 py-2 text-xs text-slate-200 placeholder-slate-500 transition">{{ old('vendor_reply', $review->vendor_reply) }}</textarea>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="replying = false" class="px-3 py-1.5 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-900 text-xs font-medium transition">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md shadow-indigo-600/30 transition">
                                    Publish Store Reply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $reviews->links() }}
        </div>
    @endif

    <!-- Seller Photo Inspection Lightbox Modal -->
    <div x-show="photoModal" 
         x-transition.opacity 
         class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-md flex flex-col items-center justify-center p-4 sm:p-6" 
         style="display: none;"
         @keydown.escape.window="photoModal = false">
        <div @click.away="photoModal = false" class="relative max-w-4xl max-h-[90vh] flex flex-col items-center">
            <div class="w-full flex items-center justify-between pb-3 text-white">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-400">Buyer Attached Photo:</span>
                    <span class="text-xs font-bold text-amber-400" x-text="modalBuyer"></span>
                </div>
                <div class="flex items-center gap-2">
                    <a :href="activeModalPhoto" target="_blank" class="px-3 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-medium transition">
                        Open Original ↗
                    </a>
                    <button type="button" @click="photoModal = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                        &times;
                    </button>
                </div>
            </div>
            <img :src="activeModalPhoto" alt="Customer review photo" class="max-h-[80vh] max-w-full rounded-2xl object-contain shadow-2xl border border-slate-800">
        </div>
    </div>
</div>
@endsection
