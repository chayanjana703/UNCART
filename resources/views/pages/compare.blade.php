@extends('layouts.app')

@section('title', 'Product Comparison - ShopiCart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Compare Products</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Side-by-side technical specification and value analysis</p>
    </div>

    @if($compareProducts->isEmpty())
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-16 text-center space-y-4 shadow-sm">
            <span class="text-5xl">⚖️</span>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">No items in your comparison tray</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Visit the catalog and check "Compare Specs" on up to 4 items to inspect them side-by-side.
            </p>
            <a href="{{ route('products.index') }}" class="inline-block py-2.5 px-6 rounded-xl bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider hover:bg-indigo-700 shadow-md">
                Browse Catalog
            </a>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-x-auto">
            <table class="w-full text-xs text-left min-w-[700px]">
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    
                    <!-- Product Header (Image & Name) -->
                    <tr class="bg-slate-50/50 dark:bg-slate-850/50">
                        <td class="p-4 font-bold text-slate-400 w-48 uppercase tracking-wider text-[10px]">Product</td>
                        @foreach($compareProducts as $p)
                            <td class="p-4 w-64 align-top">
                                <div class="space-y-3">
                                    <div class="aspect-square w-full rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                        <img src="{{ $p->featured_image }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <h4 class="font-bold text-xs text-slate-900 dark:text-white line-clamp-2">
                                        <a href="{{ route('products.show', $p->slug) }}" class="hover:text-indigo-600">
                                            {{ $p->name }}
                                        </a>
                                    </h4>
                                </div>
                            </td>
                        @endforeach
                    </tr>

                    <!-- Price -->
                    <tr>
                        <td class="p-4 font-bold text-slate-400 uppercase tracking-wider text-[10px]">Price</td>
                        @foreach($compareProducts as $p)
                            <td class="p-4 font-extrabold text-base text-slate-900 dark:text-white">
                                ${{ number_format($p->current_price, 2) }}
                                @if($p->sale_price && $p->sale_price < $p->price)
                                    <span class="text-xs text-slate-400 font-normal line-through ml-1">${{ number_format($p->price, 2) }}</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    <!-- Rating -->
                    <tr>
                        <td class="p-4 font-bold text-slate-400 uppercase tracking-wider text-[10px]">Customer Rating</td>
                        @foreach($compareProducts as $p)
                            <td class="p-4">
                                <div class="flex items-center gap-1.5 text-amber-400">
                                    <span>★ {{ number_format($p->rating_avg, 1) }}</span>
                                    <span class="text-slate-400 text-[11px]">({{ $p->rating_count }} reviews)</span>
                                </div>
                            </td>
                        @endforeach
                    </tr>

                    <!-- Availability -->
                    <tr>
                        <td class="p-4 font-bold text-slate-400 uppercase tracking-wider text-[10px]">Stock Status</td>
                        @foreach($compareProducts as $p)
                            <td class="p-4">
                                @if($p->in_stock)
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        In Stock ({{ $p->stock_quantity }})
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                        Out of Stock
                                    </span>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    <!-- Category & Seller -->
                    <tr>
                        <td class="p-4 font-bold text-slate-400 uppercase tracking-wider text-[10px]">Merchant & Category</td>
                        @foreach($compareProducts as $p)
                            <td class="p-4 text-slate-600 dark:text-slate-300">
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $p->vendor->shop_name ?? 'ShopiCart Direct' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $p->category->name }}</p>
                            </td>
                        @endforeach
                    </tr>

                    <!-- Action -->
                    <tr>
                        <td class="p-4 font-bold text-slate-400 uppercase tracking-wider text-[10px]">Action</td>
                        @foreach($compareProducts as $p)
                            <td class="p-4">
                                <a href="{{ route('products.show', $p->slug) }}" class="inline-block w-full text-center py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition-all active:scale-95">
                                    View Product &rarr;
                                </a>
                            </td>
                        @endforeach
                    </tr>

                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
