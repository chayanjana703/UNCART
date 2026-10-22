@extends('layouts.vendor')

@section('title', 'Customer Inquiries')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                Customer Inquiries
                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                    {{ $inquiries->total() }} Total
                </span>
            </h1>
            <p class="text-slate-400 text-xs mt-1">Direct inquiries from buyers about your catalog items. Fast replies elevate conversion.</p>
        </div>
    </div>

    <!-- Inquiry List -->
    @if($inquiries->isEmpty())
        <div class="bg-slate-950 border border-slate-800 rounded-2xl p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <h3 class="text-base font-bold text-white mb-1">No Inquiries Found</h3>
            <p class="text-slate-400 text-xs max-w-sm mx-auto">When prospective buyers ask questions on your product pages, they will appear here for you to answer directly.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($inquiries as $inquiry)
                <div class="bg-slate-950 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition" x-data="{ replying: false }">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-4 border-b border-slate-800/80">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-sm font-bold text-indigo-400">
                                {{ strtoupper(substr($inquiry->user->name ?? 'Buyer', 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-sm text-white">{{ $inquiry->user->name ?? 'Anonymous Buyer' }}</h3>
                                    <span class="text-[10px] font-mono text-slate-400">{{ $inquiry->user->email ?? '' }}</span>
                                </div>
                                <p class="text-[11px] text-slate-400">
                                    Subject: <span class="text-slate-200 font-medium">{{ $inquiry->subject }}</span>
                                    <span class="mx-1.5">•</span>
                                    {{ $inquiry->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if($inquiry->status === 'replied')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    Replied
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                    Pending Reply
                                </span>
                            @endif

                            <button @click="replying = !replying" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition">
                                <span x-text="replying ? 'Close Reply' : '{{ $inquiry->reply ? 'Edit Reply' : 'Reply Now' }}'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Referenced Product -->
                    @if($inquiry->product)
                        <div class="mt-3 py-2 px-3 rounded-xl bg-slate-900/60 border border-slate-800/60 flex items-center gap-3">
                            <img src="{{ $inquiry->product->primary_image }}" alt="{{ $inquiry->product->name }}" class="w-9 h-9 rounded-lg object-cover bg-slate-800 flex-shrink-0">
                            <div class="text-xs">
                                <span class="text-slate-400">Regarding item:</span>
                                <a href="{{ route('products.show', $inquiry->product->slug) }}" target="_blank" class="text-indigo-400 hover:underline font-medium ml-1">
                                    {{ $inquiry->product->name }}
                                </a>
                                <span class="text-slate-400 ml-2 font-mono">${{ number_format($inquiry->product->price, 2) }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Question Body -->
                    <div class="mt-4 bg-slate-900/40 rounded-xl p-4 border border-slate-800/40">
                        <p class="text-xs text-slate-300 leading-relaxed">{{ $inquiry->message }}</p>
                    </div>

                    <!-- Existing Reply -->
                    @if($inquiry->reply)
                        <div class="mt-3 pl-4 border-l-2 border-indigo-500 bg-indigo-950/20 rounded-r-xl p-3">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[11px] font-bold text-indigo-400">{{ $vendor->shop_name }} (Your Reply)</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $inquiry->updated_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-slate-300">{{ $inquiry->reply }}</p>
                        </div>
                    @endif

                    <!-- Reply Form Drawer -->
                    <div x-show="replying" x-transition class="mt-4 pt-4 border-t border-slate-800">
                        <form action="{{ route('vendor.inquiries.reply', $inquiry->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1">Your Response to {{ $inquiry->user->name ?? 'Customer' }}:</label>
                                <textarea name="reply" rows="3" required placeholder="Type your informative and professional reply here..." class="w-full bg-slate-900 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-3 py-2 text-xs text-slate-200 placeholder-slate-500 transition">{{ old('reply', $inquiry->reply) }}</textarea>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="replying = false" class="px-3 py-1.5 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-900 text-xs font-medium transition">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md shadow-indigo-600/30 transition">
                                    Dispatch Reply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $inquiries->links() }}
        </div>
    @endif
</div>
@endsection
