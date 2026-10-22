@extends('layouts.app')

@section('title', $product->name . ' - ShopiCart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
    activeImage: '{{ $product->featured_image }}',
    qty: 1,
    selectedColor: 'Obsidian Black',
    selectedEdition: 'Standard Edition',
    adding: false,
    reviewModal: false,
    async addToCart() {
        this.adding = true;
        try {
            let res = await fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: {{ $product->id }},
                    quantity: this.qty,
                    options: {
                        Color: this.selectedColor,
                        Edition: this.selectedEdition
                    }
                })
            });
            let data = await res.json();
            this.adding = false;
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
            window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
        } catch(e) {
            this.adding = false;
        }
    }
}">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-6 font-medium">
        <a href="{{ route('home') }}" class="hover:text-slate-900 dark:hover:text-white">Home</a>
        <span>/</span>
        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-slate-900 dark:hover:text-white">{{ $product->category->name }}</a>
        <span>/</span>
        <span class="text-slate-900 dark:text-white truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <!-- Main Product Section (2-Column) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 pb-16 border-b border-slate-200 dark:border-slate-800">
        
        <!-- Left: Image Gallery -->
        <div class="space-y-4">
            <!-- Active Large Image -->
            <div class="aspect-square w-full rounded-3xl overflow-hidden bg-slate-100 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xl relative group">
                <img :src="activeImage" 
                     alt="{{ $product->name }}" 
                     class="w-full h-full object-cover object-center transition-all duration-300">

                @if($product->sale_price && $product->sale_price < $product->price)
                    <span class="absolute top-4 left-4 px-3 py-1.5 text-xs font-bold uppercase tracking-wider bg-rose-600 text-white rounded-xl shadow-lg">
                        -{{ $product->discount_percentage }}% OFF
                    </span>
                @endif
            </div>

            <!-- Thumbnail Selector Strip -->
            @if(count($product->gallery_images) > 1)
                <div class="flex items-center gap-3 overflow-x-auto pb-2">
                    @foreach($product->gallery_images as $img)
                        <button type="button" 
                                @click="activeImage = '{{ $img }}'" 
                                class="w-20 h-20 rounded-2xl overflow-hidden border-2 flex-shrink-0 transition-all duration-150"
                                :class="activeImage === '{{ $img }}' ? 'border-indigo-600 ring-2 ring-indigo-500/30' : 'border-slate-200 dark:border-slate-800 opacity-70 hover:opacity-100'">
                            <img src="{{ $img }}" alt="Thumbnail" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right: Purchase Details & Information -->
        <div class="flex flex-col justify-between space-y-6">
            <div class="space-y-4">
                
                <!-- Category & SKU -->
                <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span class="font-bold tracking-wider uppercase text-indigo-600 dark:text-indigo-400 font-mono">{{ $product->category->name }}</span>
                    <span class="font-mono">SKU: {{ $product->sku }}</span>
                </div>

                <!-- Product Name -->
                <h1 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                    {{ $product->name }}
                </h1>

                <!-- Rating & Reviews Bar -->
                <div class="flex items-center gap-3 pt-1">
                    <div class="flex items-center text-amber-400 text-sm">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($product->rating_avg))
                                <span>★</span>
                            @else
                                <span class="text-slate-300 dark:text-slate-700">★</span>
                            @endif
                        @endfor
                    </div>
                    <span class="text-xs font-bold text-slate-900 dark:text-slate-100">{{ number_format($product->rating_avg, 2) }}</span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <a href="#reviews-section" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                        {{ $totalReviews }} Verified Reviews
                    </a>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $product->sales_count }} Units Sold</span>
                </div>

                <!-- Pricing Block -->
                <div class="py-4 border-y border-slate-200 dark:border-slate-800 flex items-baseline gap-4">
                    <span class="text-3xl font-black text-slate-900 dark:text-white">
                        ${{ number_format($product->current_price, 2) }}
                    </span>
                    @if($product->sale_price && $product->sale_price < $product->price)
                        <span class="text-lg text-slate-400 line-through">
                            ${{ number_format($product->price, 2) }}
                        </span>
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950 px-2 py-0.5 rounded-md">
                            You save ${{ number_format($product->price - $product->sale_price, 2) }}
                        </span>
                    @endif
                </div>

                <!-- Short Overview -->
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                    {{ $product->short_description }}
                </p>

                <!-- Options Simulation (Colors & Edition) -->
                <div class="space-y-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-2">Color Variant</label>
                        <div class="flex items-center gap-2">
                            @foreach(['Obsidian Black', 'Titanium Silver', 'Midnight Navy'] as $color)
                                <button type="button" 
                                        @click="selectedColor = '{{ $color }}'"
                                        class="px-3.5 py-1.5 rounded-xl text-xs font-medium border transition-all"
                                        :class="selectedColor === '{{ $color }}' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-300 font-bold' : 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300'">
                                    {{ $color }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Quantity & Add to Cart -->
                <div class="pt-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <!-- Stepper -->
                        <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-2xl bg-white dark:bg-slate-850 p-1">
                            <button @click="if(qty > 1) qty--" type="button" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors font-bold text-sm">
                                -
                            </button>
                            <span class="w-10 text-center font-bold text-sm text-slate-900 dark:text-white" x-text="qty"></span>
                            <button @click="qty++" type="button" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors font-bold text-sm">
                                +
                            </button>
                        </div>

                        <!-- Add to Cart Primary Button -->
                        <button @click="addToCart()" 
                                type="button"
                                :disabled="adding || {{ !$product->in_stock ? 'true' : 'false' }}"
                                class="flex-1 py-3.5 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all duration-150 shadow-xl shadow-indigo-600/30 flex items-center justify-center gap-2 active:scale-[0.98]">
                            <template x-if="!adding">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    <span>{{ $product->in_stock ? 'Add to Shopping Cart' : 'Out of Stock' }}</span>
                                </span>
                            </template>
                            <template x-if="adding">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    <span>Adding to Cart...</span>
                                </span>
                            </template>
                        </button>
                    </div>

                    <!-- Direct Compare Link -->
                    <div class="flex items-center justify-between text-xs text-slate-500 pt-2">
                        <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            {{ $product->stock_quantity }} units in stock (Ready for immediate courier dispatch)
                        </span>
                        <a href="{{ route('products.compare', ['ids' => $product->id]) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold flex items-center gap-1">
                            <span>Compare with others &rarr;</span>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Seller Information Card -->
            @if($product->vendor)
                <div class="mt-8 p-5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-3.5">
                        <img src="{{ $product->vendor->shop_logo ?: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=100' }}" 
                             alt="{{ $product->vendor->shop_name }}" 
                             class="w-12 h-12 rounded-xl object-cover border border-slate-200 dark:border-slate-700">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $product->vendor->shop_name }}</h4>
                                <span class="text-emerald-500 text-xs" title="Verified Merchant">✓</span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                Certified Seller &bull; Dispatch within 12h
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('products.index', ['vendor' => $product->vendor->shop_slug]) }}" 
                       class="py-1.5 px-3 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 transition-colors">
                        Visit Shop
                    </a>
                </div>
            @endif

        </div>
    </div>

    <!-- Specifications Table & Long Description -->
    <div class="py-12 border-b border-slate-200 dark:border-slate-800 space-y-10">
        <div>
            <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight mb-4">Detailed Description</h3>
            <div class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm max-w-4xl space-y-3">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>

        @if(!empty($product->specifications))
            <div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight mb-4">Technical Specifications</h3>
                <div class="max-w-3xl rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <table class="w-full text-xs text-left">
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($product->specifications as $k => $v)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/60 transition-colors">
                                    <td class="py-3 px-4 font-bold text-slate-500 dark:text-slate-400 w-1/3 bg-slate-50/50 dark:bg-slate-900/40">{{ $k }}</td>
                                    <td class="py-3 px-4 text-slate-900 dark:text-slate-100 font-medium">{{ $v }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Verified Reviews & Ratings Breakdown -->
    <div id="reviews-section" class="py-12 space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Customer Reviews & Ratings</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Authentic feedback from verified purchasers</p>
            </div>
            
            @auth
                <button @click="reviewModal = true" 
                        class="py-2.5 px-5 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-950 font-bold text-xs uppercase tracking-wider hover:bg-slate-800 transition-colors shadow-md active:scale-95">
                    Write a Review
                </button>
            @else
                <a href="{{ route('login') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Sign in to leave a review &rarr;
                </a>
            @endauth
        </div>

        <!-- Rating Overview Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 bg-slate-50 dark:bg-slate-900 p-8 rounded-3xl border border-slate-200 dark:border-slate-800">
            <!-- Overall Score -->
            <div class="text-center sm:text-left flex flex-col justify-center border-b md:border-b-0 md:border-r border-slate-200 dark:border-slate-800 pb-6 md:pb-0 md:pr-6">
                <span class="text-5xl font-black text-slate-900 dark:text-white">{{ number_format($product->rating_avg, 1) }}</span>
                <div class="flex items-center text-amber-400 text-lg my-2">
                    @for($i=1; $i<=5; $i++)
                        <span>{{ $i <= round($product->rating_avg) ? '★' : '☆' }}</span>
                    @endfor
                </div>
                <span class="text-xs text-slate-500 dark:text-slate-400">Based on {{ $totalReviews }} ratings</span>
            </div>

            <!-- Star Bars -->
            <div class="md:col-span-2 space-y-2 flex flex-col justify-center">
                @foreach([5, 4, 3, 2, 1] as $star)
                    @php
                        $count = $ratingDistribution[$star] ?? 0;
                        $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                    @endphp
                    <div class="flex items-center gap-3 text-xs">
                        <span class="w-12 font-medium text-slate-600 dark:text-slate-400">{{ $star }} Stars</span>
                        <div class="flex-1 h-2 rounded-full bg-slate-200 dark:bg-slate-800 overflow-hidden">
                            <div class="h-full bg-amber-400 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                        <span class="w-8 text-right font-mono text-slate-400 text-[11px]">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Individual Review Cards -->
        <div class="space-y-4">
            @forelse($product->reviews as $rev)
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img src="{{ $rev->user->avatar ?: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100' }}" 
                                 alt="{{ $rev->user->name ?? 'User' }}" 
                                 class="w-8 h-8 rounded-full object-cover">
                            <div>
                                <h5 class="text-xs font-bold text-slate-900 dark:text-white">{{ $rev->user->name ?? 'Customer' }}</h5>
                                <div class="flex items-center text-amber-400 text-xs mt-0.5">
                                    @for($i=0; $i<$rev->rating; $i++) ★ @endfor
                                    <span class="text-[10px] text-slate-400 ml-2">{{ $rev->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Helpful Vote Trigger -->
                        <div x-data="{
                            helpful: {{ $rev->helpful_votes }},
                            voted: false,
                            async vote(type) {
                                @if(!Auth::check())
                                    window.location.href = '{{ route('login') }}';
                                    return;
                                @endif
                                try {
                                    let res = await fetch('{{ route('reviews.vote', $rev->id) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({ vote_type: type })
                                    });
                                    let d = await res.json();
                                    this.helpful = d.helpful;
                                    this.voted = true;
                                } catch(e) {}
                            }
                        }">
                            <button @click="vote('helpful')" 
                                    class="text-xs font-medium text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800">
                                <span>👍 Helpful</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200" x-text="helpful"></span>
                            </button>
                        </div>
                    </div>

                    @if($rev->title)
                        <h4 class="font-bold text-xs text-slate-800 dark:text-slate-200">{{ $rev->title }}</h4>
                    @endif
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        {{ $rev->comment }}
                    </p>

                    <!-- Official Vendor Response (If exists) -->
                    @if($rev->vendor_reply)
                        <div class="mt-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-850 border border-slate-200/80 dark:border-slate-800/80 space-y-1">
                            <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400">Response from Seller:</span>
                            <p class="text-xs text-slate-600 dark:text-slate-300">{{ $rev->vendor_reply }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="py-8 text-center text-xs text-slate-400 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                    No customer reviews yet. Be the first to share your thoughts!
                </div>
            @endforelse
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="pt-12">
            <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight mb-6">Similar Products in this Category</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $rel)
                    <x-product-card :product="$rel" />
                @endforeach
            </div>
        </div>
    @endif

    <!-- Review Modal -->
    <div x-show="reviewModal" 
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
        <div class="max-w-lg w-full bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4" @click.outside="reviewModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h4 class="font-black text-base text-slate-900 dark:text-white">Write a Review for {{ $product->name }}</h4>
                <button @click="reviewModal = false" class="text-slate-400 hover:text-white text-lg">&times;</button>
            </div>

            <form action="{{ route('reviews.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Your Rating</label>
                    <select name="rating" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                        <option value="5">★★★★★ - Outstanding (5/5)</option>
                        <option value="4">★★★★☆ - Very Good (4/5)</option>
                        <option value="3">★★★☆☆ - Average (3/5)</option>
                        <option value="2">★★☆☆☆ - Poor (2/5)</option>
                        <option value="1">★☆☆☆☆ - Terrible (1/5)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Review Headline</label>
                    <input type="text" name="title" placeholder="Summarize your experience..." required
                           class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Detailed Review</label>
                    <textarea name="comment" rows="4" placeholder="What did you like or dislike about this product?" required
                              class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2 text-slate-900 dark:text-white"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="reviewModal = false" class="px-4 py-2 text-xs font-medium text-slate-500 hover:text-slate-900">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider hover:bg-indigo-700 shadow-md">
                        Publish Review
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
