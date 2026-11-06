@extends('layouts.app')

@section('title', 'Shop Catalog — UNCART')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
    compareList: [],
    toggleCompare(id) {
        if (this.compareList.includes(id)) {
            this.compareList = this.compareList.filter(x => x !== id);
        } else {
            if (this.compareList.length >= 4) {
                alert('You can compare a maximum of 4 products simultaneously.');
                return;
            }
            this.compareList.push(id);
        }
    }
}">

    <!-- Page Header & Filter Status Bar -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-[#DDE4EC] pb-6" data-reveal>
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                <span class="text-xs font-bold uppercase tracking-widest text-[#6B2A88]">Explore Collection</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black text-[#1B0E24] tracking-tight mt-1">
                All Products & Categories
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} items
                @if(request('q')) for "<strong class="text-[#1B0E24]">{{ request('q') }}</strong>" @endif
            </p>
        </div>

        <!-- Sort Control -->
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-500 font-medium">Sort By:</span>
            <form id="sortForm" method="GET" action="{{ route('products.index') }}" class="inline-block">
                @foreach(request()->except(['sort', 'page']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <select name="sort" onchange="document.getElementById('sortForm').submit()" 
                        class="text-xs font-semibold rounded-full border border-[#DDE4EC] bg-white text-[#1B0E24] py-2 pl-4 pr-10 focus:border-[#F59E0B] focus:outline-none focus:ring-2 focus:ring-[#F59E0B]/20 transition cursor-pointer shadow-xs">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Active Filter Tags if Any -->
    @if(request()->hasAny(['category', 'min_price', 'max_price', 'q']))
        <div class="mb-6 flex flex-wrap items-center gap-2" data-reveal>
            <span class="text-xs text-slate-500 font-medium">Active Filters:</span>
            @if(request('q'))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#FEF8E7] text-[#1B0E24] border border-[#FDEEC4]">
                    Query: {{ request('q') }}
                    <a href="{{ route('products.index', array_filter(request()->except('q'))) }}" class="hover:text-[#F59E0B]">&times;</a>
                </span>
            @endif
            @if(request('category'))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#FEF8E7] text-[#1B0E24] border border-[#FDEEC4]">
                    Category: {{ request('category') }}
                    <a href="{{ route('products.index', array_filter(request()->except('category'))) }}" class="hover:text-[#F59E0B]">&times;</a>
                </span>
            @endif
            <a href="{{ route('products.index') }}" class="text-xs font-semibold text-rose-500 hover:underline ml-2">Clear All</a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Filters Sidebar -->
        <aside class="space-y-6" data-reveal>
            <div class="bg-white rounded-3xl border border-[#DDE4EC] p-6 shadow-sm space-y-6">
                
                <div class="flex items-center justify-between pb-4 border-b border-[#DDE4EC]">
                    <h3 class="text-xs font-bold text-[#1B0E24] uppercase tracking-wider">Refine Filter</h3>
                    <a href="{{ route('products.index') }}" class="text-[11px] font-semibold text-rose-500 hover:underline">Reset</a>
                </div>

                <form method="GET" action="{{ route('products.index') }}" class="space-y-6">
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif

                    <!-- Categories Filter -->
                    <div>
                        <h4 class="text-xs font-bold text-[#1B0E24] mb-3 uppercase tracking-wider text-[11px]">Categories</h4>
                        <div class="space-y-1.5">
                            <label class="flex items-center justify-between text-xs text-slate-600 hover:text-[#F59E0B] cursor-pointer py-1">
                                <span class="flex items-center gap-2">
                                    <input type="radio" name="category" value="" onchange="this.form.submit()" {{ !request('category') ? 'checked' : '' }} class="text-[#6B2A88] focus:ring-[#F59E0B] border-slate-300">
                                    <span>All Categories</span>
                                </span>
                            </label>
                            @foreach($categories as $cat)
                                <label class="flex items-center justify-between text-xs text-slate-600 hover:text-[#F59E0B] cursor-pointer py-1">
                                    <span class="flex items-center gap-2">
                                        <input type="radio" name="category" value="{{ $cat->slug }}" onchange="this.form.submit()" {{ request('category') == $cat->slug ? 'checked' : '' }} class="text-[#6B2A88] focus:ring-[#F59E0B] border-slate-300">
                                        <span>{{ $cat->name }}</span>
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-semibold">{{ $cat->products_count }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="pt-4 border-t border-[#DDE4EC]">
                        <h4 class="text-xs font-bold text-[#1B0E24] mb-3 uppercase tracking-wider text-[11px]">Price Range (<span x-text="$store.currency ? $store.currency.rates[$store.currency.current].symbol : '$'">$</span>)</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-[10px] text-slate-400 block mb-1">Min Price</label>
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0" class="w-full bg-[#F0F3F7] border border-[#DDE4EC] rounded-xl px-3 py-1.5 text-xs text-[#1B0E24] focus:border-[#F59E0B]">
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-400 block mb-1">Max Price</label>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="2000" class="w-full bg-[#F0F3F7] border border-[#DDE4EC] rounded-xl px-3 py-1.5 text-xs text-[#1B0E24] focus:border-[#F59E0B]">
                            </div>
                        </div>
                        <button type="submit" class="mt-3 w-full py-2.5 rounded-full bg-[#1B0E24] hover:bg-[#F59E0B] hover:text-[#1B0E24] text-white text-xs font-bold transition shadow-sm active:scale-95">
                            Apply Filter
                        </button>
                    </div>

                    <!-- In Stock Checkbox -->
                    <div class="pt-4 border-t border-[#DDE4EC]">
                        <label class="flex items-center gap-2 text-xs text-slate-600 hover:text-[#F59E0B] cursor-pointer">
                            <input type="checkbox" name="in_stock" value="1" onchange="this.form.submit()" {{ request('in_stock') ? 'checked' : '' }} class="rounded text-[#6B2A88] focus:ring-[#F59E0B] border-slate-300">
                            <span>In Stock Only</span>
                        </label>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Main Product Grid -->
        <main class="lg:col-span-3">
            @if($products->isEmpty())
                <div class="bg-white rounded-3xl border border-slate-100 p-16 text-center shadow-sm" data-reveal>
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-2xl">
                        🔍
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-1">No Matching Products</h3>
                    <p class="text-slate-500 text-xs max-w-sm mx-auto mb-6">No products matched your search or filter settings. Try adjusting your search query or reset filters.</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 py-2.5 px-6 rounded-full bg-slate-900 text-white text-xs font-bold hover:bg-indigo-600 transition">
                        <span>Clear All Filters</span>
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
                    @foreach($products as $product)
                        <div data-reveal="scale">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 pt-6 border-t border-slate-200/80" data-reveal>
                    {{ $products->links() }}
                </div>
            @endif
        </main>
    </div>
</div>
@endsection
