@extends('layouts.admin')

@section('title', 'Review Moderation & Trust')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                Review Moderation
                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    {{ $reviews->total() }} Reviews
                </span>
            </h1>
            <p class="text-slate-400 text-xs mt-1">Audit customer feedback, star ratings, photographic evidence, and combat review abuse.</p>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <select name="status" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-300">
                    <option value="">All Review Statuses</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved Reviews</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Moderation</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Flagged / Rejected</option>
                </select>
            </div>

            @if(request('status'))
                <div>
                    <a href="{{ route('admin.reviews.index') }}" class="inline-block px-4 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white text-xs transition">
                        Reset Filter
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Reviews Moderation List -->
    <div class="space-y-4">
        @forelse($reviews as $review)
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-3 border-b border-slate-800/80">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-800 text-amber-400 font-bold flex items-center justify-center text-xs">
                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-white text-xs">{{ $review->user->name ?? 'Anonymous User' }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">({{ $review->user->email ?? 'N/A' }})</span>
                                @if($review->is_verified_purchase)
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Verified</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <div class="text-amber-400 text-xs">
                                    @for($i = 1; $i <= 5; $i++)
                                        {{ $i <= $review->rating ? '★' : '☆' }}
                                    @endfor
                                </div>
                                <span class="text-xs font-bold text-slate-200">{{ $review->title }}</span>
                                <span class="text-[10px] text-slate-500 font-mono">• {{ $review->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($review->status === 'approved')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase">Approved</span>
                        @elseif($review->status === 'rejected')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20 uppercase">Rejected</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 uppercase">Pending</span>
                        @endif

                        <!-- Actions -->
                        @if($review->status !== 'approved')
                            <form action="{{ route('admin.reviews.status', $review->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold transition">
                                    Approve
                                </button>
                            </form>
                        @endif

                        @if($review->status !== 'rejected')
                            <form action="{{ route('admin.reviews.status', $review->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/20 text-[11px] font-medium transition">
                                    Flag & Hide
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Permanently eradicate this review?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1 rounded-lg bg-slate-800 hover:bg-red-950/60 text-slate-400 hover:text-red-400 transition" title="Delete Review">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Product Subject -->
                @if($review->product)
                    <div class="mt-3 flex items-center gap-3 py-1.5 px-3 bg-slate-950/60 rounded-xl border border-slate-800/60 text-xs">
                        <img src="{{ $review->product->primary_image }}" alt="" class="w-7 h-7 rounded-lg object-cover bg-slate-800">
                        <span class="text-slate-400">On Product:</span>
                        <a href="{{ route('products.show', $review->product->slug) }}" target="_blank" class="text-amber-400 hover:underline font-semibold">
                            {{ $review->product->name }}
                        </a>
                        <span class="text-slate-500 font-mono ml-auto">SKU: {{ $review->product->sku }}</span>
                    </div>
                @endif

                <!-- Review Content -->
                <div class="mt-3 text-xs text-slate-300 leading-relaxed bg-slate-950/40 p-3 rounded-xl border border-slate-800/40">
                    {{ $review->comment }}
                </div>

                <!-- Photos attached -->
                @if(!empty($review->photos) && is_array($review->photos))
                    <div class="mt-3 flex items-center gap-2">
                        @foreach($review->photos as $photo)
                            <a href="{{ $photo }}" target="_blank">
                                <img src="{{ $photo }}" alt="Evidence" class="w-12 h-12 rounded-lg object-cover border border-slate-700 hover:opacity-80 transition">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-12 text-center text-slate-500 text-xs">
                No customer reviews match this query.
            </div>
        @endforelse
    </div>

    <div class="pt-2">
        {{ $reviews->links() }}
    </div>
</div>
@endsection
