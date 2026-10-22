@extends('layouts.app')

@section('title', 'Browse Catalog - ShopiCart')

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

    <!-- Page Header & Active Search -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                All Products
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results
                @if(request('q')) for "<strong class="text-slate-800 dark:text-slate-200">{{ request('q') }}</strong>" @endif
            </p>
        </div>

        <!-- Sort Select -->
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-500 font-medium whitespace-nowrap">Sort By:</span>
            <form id="sortForm" method="GET" action="{{ route('products.index') }}" class="inline-block">
                @foreach(request()->except(['sort', 'page']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <select name="sort" onchange="document.getElementById('sortForm').submit()" 
                        class="text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 py-2 pl-3 pr-8 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                </select>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Filters Sidebar -->
        <aside class="space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-5">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Filters</h3>
                    <a href="{{ route('products.index') }}" class="text-[11px] font-semibold text-rose-500 hover:underline">Reset</a>
                </div>

                <form action="{{ route('products.index') }}" method="GET" class="space-y-6">
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    <!-- Keyword -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Search Keyword</label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Filter by name, model..."
                               class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Categories</label>
                        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                            <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400 cursor-pointer hover:text-slate-900 dark:hover:text-white">
                                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()" class="text-indigo-600 focus:ring-indigo-500">
                                <span>All Categories</span>
                            </label>
                            @foreach($categories as $cat)
                                <label class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 cursor-pointer hover:text-slate-900 dark:hover:text-white">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'checked' : '' }} onchange="this.form.submit()" class="text-indigo-600 focus:ring-indigo-500">
                                        <span>{{ $cat->name }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-mono">({{ $cat->products_count }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Price Range ($)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" min="0"
                                   class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" min="0"
                                   class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Rating Filter -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Customer Rating</label>
                        <div class="space-y-1 text-xs">
                            @foreach([4 => '4 Stars & Up', 3 => '3 Stars & Up', 2 => '2 Stars & Up'] as $stars => $label)
                                <label class="flex items-center gap-2 cursor-pointer text-slate-600 dark:text-slate-400">
                                    <input type="radio" name="rating" value="{{ $stars }}" {{ request('rating') == $stars ? 'checked' : '' }} onchange="this.form.submit()" class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-amber-400">@for($i=0; $i<$stars; $i++)★@endfor</span>
                                    <span class="text-[11px]">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- In Stock Checkbox -->
                    <div class="pt-2">
                        <label class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span>In-Stock Items Only</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-950 font-bold text-xs uppercase tracking-wider hover:bg-slate-800 transition-colors">
                        Apply Filters
                    </button>
                </form>
            </div>
        </aside>

        <!-- Product Grid Area -->
        <div class="lg:col-span-3 space-y-6">
            @if($products->isEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center space-y-3">
                    <span class="text-4xl">🔍</span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">No products found</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                        Try clearing some filters or searching with a different term.
                    </p>
                    <a href="{{ route('products.index') }}" class="inline-block py-2 px-4 rounded-xl bg-indigo-600 text-white text-xs font-semibold">
                        View All Catalog
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <div class="relative">
                            <x-product-card :product="$product" />
                            
                            <!-- Compare Checkbox Pin -->
                            <div class="mt-2 flex items-center justify-end px-1">
                                <label class="inline-flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 cursor-pointer hover:text-indigo-600">
                                    <input type="checkbox" 
                                           :checked="compareList.includes({{ $product->id }})" 
                                           @change="toggleCompare({{ $product->id }})"
                                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5">
                                    <span>Compare Specs</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pt-6">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Floating Compare Drawer (when products are checked) -->
    <div x-show="compareList.length > 0" 
         x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-200"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed bottom-6 right-6 sm:right-12 z-40 bg-slate-950 text-white p-4 rounded-2xl shadow-2xl border border-slate-800 flex items-center gap-4">
        <div class="text-xs font-medium">
            <span class="font-bold text-indigo-400" x-text="compareList.length"></span>/4 items selected for comparison
        </div>
        <a :href="'{{ route('products.compare') }}?ids=' + compareList.join(',')" 
           class="py-2 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition-all shadow-lg active:scale-95">
            Compare Now &rarr;
        </a>
        <button @click="compareList = []" class="text-slate-400 hover:text-white text-xs">
            Clear
        </button>
    </div>

</div>
@endsection
