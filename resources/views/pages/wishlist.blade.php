@extends('layouts.app')

@section('title', 'My Wishlist - ShopiCart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ shareModal: false, copied: false }">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Saved Wishlist</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Keep track of desired products and share collections with colleagues or friends</p>
        </div>

        @if($wishlistItems->isNotEmpty())
            <button @click="shareModal = true" class="py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs flex items-center gap-2 transition-colors">
                <span>🔗 Share Wishlist</span>
            </button>
        @endif
    </div>

    @if($wishlistItems->isEmpty())
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-16 text-center space-y-4 shadow-sm">
            <span class="text-5xl">❤️</span>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Your wishlist is currently empty</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Click the heart icon on any product in our catalog to pin it to your personal wishlist.
            </p>
            <a href="{{ route('products.index') }}" class="inline-block py-2.5 px-6 rounded-xl bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider hover:bg-indigo-700 shadow-md">
                Browse Products
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($wishlistItems as $item)
                @if($item->product)
                    <div class="relative">
                        <x-product-card :product="$item->product" />
                    </div>
                @endif
            @endforeach
        </div>

        <div class="pt-6">
            {{ $wishlistItems->links() }}
        </div>
    @endif

    <!-- Share Wishlist Modal -->
    <div x-show="shareModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
        <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4" @click.outside="shareModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h4 class="font-bold text-sm text-slate-900 dark:text-white">Share Your Wishlist</h4>
                <button @click="shareModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <p class="text-xs text-slate-500">Anyone with this link can view your curated wishlist picks:</p>
            
            <div class="flex items-center gap-2">
                <input type="text" readonly value="{{ url('/shop') }}?wishlist={{ Auth::id() }}" class="flex-1 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 font-mono text-slate-700 dark:text-slate-200">
                <button @click="
                    navigator.clipboard.writeText('{{ url('/shop') }}?wishlist={{ Auth::id() }}');
                    copied = true;
                    setTimeout(() => copied = false, 2000);
                " class="py-2.5 px-4 rounded-xl bg-indigo-600 text-white text-xs font-bold whitespace-nowrap">
                    <span x-text="copied ? 'Copied!' : 'Copy Link'"></span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
