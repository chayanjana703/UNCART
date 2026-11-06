@extends('layouts.app')

@section('title', 'Direct Merchant Inquiries — UNCART')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8" data-reveal>
        <span class="text-xs font-mono font-bold uppercase tracking-widest text-indigo-400">Merchant Communications</span>
        <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1">Direct Inquiries & Helpdesk</h1>
        <p class="text-xs text-slate-400 mt-1 font-mono">Direct dialogue with certified hardware designers and shop owners</p>
    </div>

    @if($inquiries->isEmpty())
        <div class="bg-slate-900/60 backdrop-blur-2xl rounded-3xl border border-white/10 p-16 text-center space-y-3 shadow-2xl" data-reveal>
            <span class="text-4xl">💬</span>
            <h3 class="text-base font-bold text-white">No active inquiries</h3>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                Have a question about custom dimensions or lead times? Click "Ask Question" on any product detail page.
            </p>
            <div class="pt-2">
                <a href="{{ route('products.index') }}" class="inline-block py-3 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition active:scale-95">
                    Visit Catalog Drops &rarr;
                </a>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($inquiries as $inq)
                <div class="bg-slate-900/60 backdrop-blur-2xl rounded-3xl p-6 border border-white/10 shadow-xl space-y-3" data-reveal="scale">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-sm text-white">{{ $inq->subject }}</h4>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider font-mono {{ $inq->status === 'replied' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                {{ ucfirst($inq->status) }}
                            </span>
                        </div>
                        <span class="text-slate-500 text-[11px] font-mono">{{ $inq->created_at->format('M d, Y') }}</span>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed">
                        {{ $inq->message }}
                    </p>

                    @if($inq->reply)
                        <div class="p-4 rounded-2xl bg-indigo-950/20 border-l-2 border-indigo-500 space-y-1">
                            <span class="font-bold text-[11px] text-indigo-400 block font-mono">
                                Response from {{ $inq->vendor->shop_name }}:
                            </span>
                            <p class="text-xs text-slate-200">{{ $inq->reply }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="pt-6" data-reveal>
            {{ $inquiries->links() }}
        </div>
    @endif
</div>
@endsection
