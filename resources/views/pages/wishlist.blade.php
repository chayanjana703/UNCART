@extends('layouts.app')

@section('title', 'My Wishlist — UNCART')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ shareModal: false, copied: false }">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4" data-reveal>
        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Saved Items</span>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1">My Wishlist</h1>
            <p class="text-xs text-slate-500 mt-1">Products saved for later consideration or price alerts</p>
        </div>

        @if($wishlistItems->isNotEmpty())
            <button @click="shareModal = true" class="py-2.5 px-4 rounded-full bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs flex items-center gap-2 transition border border-slate-200 shadow-sm active:scale-95">
                <span>🔗 Share Wishlist</span>
            </button>
        @endif
    </div>

    @if($wishlistItems->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-100 p-16 text-center space-y-4 shadow-sm" data-reveal>
            <span class="text-4xl">❤️</span>
            <h3 class="text-base font-bold text-slate-900">Your wishlist is currently empty</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Click the heart icon on any product card in our store to save items to your personal wishlist.
            </p>
            <div class="pt-2">
                <a href="{{ route('products.index') }}" class="inline-block py-3 px-8 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider shadow-md transition active:scale-95">
                    Explore Products &rarr;
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($wishlistItems as $item)
                @if($item->product)
                    <div data-reveal="scale">
                        <x-product-card :product="$item->product" />
                    </div>
                @endif
            @endforeach
        </div>

        <div class="pt-6" data-reveal>
            {{ $wishlistItems->links() }}
        </div>
    @endif

    <!-- Share Wishlist Pop-Up Modal Window -->
    <div x-show="shareModal" 
         x-transition 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" 
         style="display: none;"
         @keydown.escape.window="shareModal = false">
        <div class="max-w-md w-full bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-4" @click.away="shareModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 uppercase">Share Your Wishlist</h3>
                <button @click="shareModal = false" class="text-slate-400 hover:text-slate-700">&times;</button>
            </div>
            <p class="text-xs text-slate-500">Anyone with this link can view your curated collection:</p>
            
            <div class="flex items-center gap-2">
                <input type="text" readonly value="{{ url()->current() }}" class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 font-mono">
                <button type="button" 
                        @click="navigator.clipboard.writeText('{{ url()->current() }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                        class="py-2 px-4 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold transition">
                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
