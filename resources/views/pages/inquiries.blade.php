@extends('layouts.app')

@section('title', 'Merchant Inquiries & Messages - ShopiCart')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Direct Merchant Inquiries</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Direct communication channel with product creators and certified sellers</p>
    </div>

    @if($inquiries->isEmpty())
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-16 text-center space-y-3">
            <span class="text-4xl">💬</span>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">No active inquiries</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Have a question about a product or custom fabrication? Click "Contact Seller" on any product detail page.
            </p>
            <a href="{{ route('products.index') }}" class="inline-block py-2 px-4 rounded-xl bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider">
                Visit Catalog
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($inquiries as $inq)
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $inq->subject }}</h4>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $inq->status === 'replied' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ ucfirst($inq->status) }}
                            </span>
                        </div>
                        <span class="text-slate-400 text-[11px] font-mono">{{ $inq->created_at->format('M d, Y') }}</span>
                    </div>

                    <p class="text-xs text-slate-600 dark:text-slate-300">
                        {{ $inq->message }}
                    </p>

                    @if($inq->reply)
                        <div class="p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 space-y-1">
                            <span class="font-bold text-[11px] text-indigo-700 dark:text-indigo-400 block">
                                Seller Reply ({{ $inq->vendor->shop_name }}):
                            </span>
                            <p class="text-xs text-slate-700 dark:text-slate-200">{{ $inq->reply }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
