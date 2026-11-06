@extends('layouts.app')

@section('title', 'Compare Products — UNCART')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6" data-reveal>
        <a href="{{ route('home') }}" class="hover:text-indigo-600 transition">Home</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-indigo-600 transition">Shop</a>
        <span>/</span>
        <span class="text-slate-900 font-semibold">Compare</span>
    </nav>

    <!-- Header -->
    <div class="mb-8" data-reveal>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 mb-2">
            <span>⚖️</span> Product Comparison Matrix
        </div>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Compare Products</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Side-by-side technical specifications, aesthetics, ratings and value analysis.</p>
    </div>

    @if($compareProducts->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-100 p-16 text-center space-y-4 shadow-sm" data-reveal>
            <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-600 mx-auto flex items-center justify-center text-2xl">
                ⚖️
            </div>
            <h3 class="text-base font-bold text-slate-900">No items selected to compare</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Browse our catalog and click the comparison icon on any card to inspect their specifications side-by-side.
            </p>
            <div class="pt-2">
                <a href="{{ route('products.index') }}" class="inline-block py-3 px-8 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider shadow-md transition active:scale-95">
                    Browse All Products &rarr;
                </a>
            </div>
        </div>
    @else
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden" data-reveal="scale">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left min-w-[700px]">
                    <tbody class="divide-y divide-slate-100">
                        
                        <!-- Product Header (Image & Name) -->
                        <tr class="bg-slate-50/70">
                            <td class="p-5 font-bold text-slate-500 w-48 uppercase tracking-wider text-[11px]">Product</td>
                            @foreach($compareProducts as $p)
                                <td class="p-5 w-64 align-top">
                                    <div class="space-y-3">
                                        <div class="aspect-square w-full rounded-2xl overflow-hidden bg-white border border-slate-100 p-3 flex items-center justify-center">
                                            <img src="{{ $p->primary_image }}" 
                                                 alt="{{ $p->name }}" 
                                                 referrerpolicy="no-referrer"
                                                 class="max-w-full max-h-full object-contain">
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider">{{ $p->category->name ?? 'General' }}</span>
                                            <h4 class="font-bold text-xs text-slate-900 line-clamp-2 mt-0.5">
                                                <a href="{{ route('products.show', $p->slug) }}" class="hover:text-indigo-600 transition">
                                                    {{ $p->name }}
                                                </a>
                                            </h4>
                                        </div>
                                    </div>
                                </td>
                            @endforeach
                        </tr>

                        <!-- Price -->
                        <tr>
                            <td class="p-5 font-bold text-slate-500 uppercase tracking-wider text-[11px]">Price</td>
                            @foreach($compareProducts as $p)
                                <td class="p-5">
                                    <span class="text-base font-extrabold text-slate-900" x-text="$store.currency ? $store.currency.format({{ (float)$p->price }}) : '${{ number_format($p->price, 2) }}'">${{ number_format($p->price, 2) }}</span>
                                    @if($p->compare_price && $p->compare_price > $p->price)
                                        <span class="text-xs text-slate-400 line-through ml-1" x-text="$store.currency ? $store.currency.format({{ (float)$p->compare_price }}) : '${{ number_format($p->compare_price, 2) }}'">${{ number_format($p->compare_price, 2) }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        <!-- Rating -->
                        <tr>
                            <td class="p-5 font-bold text-slate-500 uppercase tracking-wider text-[11px]">Rating</td>
                            @foreach($compareProducts as $p)
                                <td class="p-5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-amber-400 font-bold">★ {{ number_format($p->rating, 1) }}</span>
                                        <span class="text-[10px] text-slate-400">({{ $p->reviews_count ?? 12 }} reviews)</span>
                                    </div>
                                </td>
                            @endforeach
                        </tr>

                        <!-- Merchant / Brand -->
                        <tr>
                            <td class="p-5 font-bold text-slate-500 uppercase tracking-wider text-[11px]">Seller</td>
                            @foreach($compareProducts as $p)
                                <td class="p-5 text-slate-800 font-medium">
                                    {{ $p->vendor->shop_name ?? 'UNCART Direct' }}
                                </td>
                            @endforeach
                        </tr>

                        <!-- Stock Availability -->
                        <tr>
                            <td class="p-5 font-bold text-slate-500 uppercase tracking-wider text-[11px]">Stock Status</td>
                            @foreach($compareProducts as $p)
                                <td class="p-5">
                                    @if($p->stock_quantity > 0)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                            In Stock ({{ $p->stock_quantity }})
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                            Out of Stock
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        <!-- Technical Specs -->
                        <tr>
                            <td class="p-5 font-bold text-slate-500 uppercase tracking-wider text-[11px]">Key Specs</td>
                            @foreach($compareProducts as $p)
                                <td class="p-5 text-slate-600 text-[11px] leading-relaxed">
                                    {{ $p->short_description ?? 'Precision built for performance.' }}
                                </td>
                            @endforeach
                        </tr>

                        <!-- Buy Action -->
                        <tr class="bg-slate-50/40">
                            <td class="p-5 font-bold text-slate-500 uppercase tracking-wider text-[11px]">Action</td>
                            @foreach($compareProducts as $p)
                                <td class="p-5">
                                    <div class="space-y-2">
                                        <a href="{{ route('products.show', $p->slug) }}" class="inline-block w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider text-center transition active:scale-95 shadow-sm">
                                            View Product &rarr;
                                        </a>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
