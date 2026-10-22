@props(['product'])

@php
    $inWishlist = false;
    if (Auth::check()) {
        $inWishlist = \App\Models\Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->exists();
    }
@endphp

<div x-data="{
    wishlisted: {{ $inWishlist ? 'true' : 'false' }},
    adding: false,
    async toggleWishlist(e) {
        e.preventDefault();
        e.stopPropagation();
        @if(!Auth::check())
            window.location.href = '{{ route('login') }}';
            return;
        @endif
        this.wishlisted = !this.wishlisted;
        try {
            let res = await fetch('{{ route('wishlist.toggle') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: {{ $product->id }} })
            });
            let data = await res.json();
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
        } catch (err) {
            this.wishlisted = !this.wishlisted;
        }
    },
    async quickAdd(e) {
        e.preventDefault();
        e.stopPropagation();
        this.adding = true;
        try {
            let res = await fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: {{ $product->id }}, quantity: 1 })
            });
            let data = await res.json();
            this.adding = false;
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
            window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
        } catch (err) {
            this.adding = false;
        }
    }
}" 
class="group relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 shadow-sm hover:shadow-xl hover:shadow-slate-200/50 dark:hover:shadow-slate-950/40 transition-all duration-300 flex flex-col overflow-hidden">

    <!-- Thumbnail Area -->
    <a href="{{ route('products.show', $product->slug) }}" class="relative aspect-square w-full overflow-hidden bg-slate-100 dark:bg-slate-800/60 block">
        <img src="{{ $product->featured_image }}" 
             alt="{{ $product->name }}" 
             loading="lazy"
             class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]">

        <!-- Badges -->
        <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
            @if($product->sale_price && $product->sale_price < $product->price)
                <span class="px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider bg-rose-600 text-white rounded-lg shadow-sm backdrop-blur-sm">
                    -{{ $product->discount_percentage }}% OFF
                </span>
            @endif
            @if($product->is_featured)
                <span class="px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider bg-indigo-600 text-white rounded-lg shadow-sm">
                    Featured
                </span>
            @endif
        </div>

        <!-- Wishlist Button -->
        <button @click="toggleWishlist($event)" 
                type="button"
                title="Save to Wishlist"
                class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/90 dark:bg-slate-900/90 backdrop-blur-md flex items-center justify-center text-slate-400 hover:text-rose-500 shadow-md transition-all duration-200 active:scale-90 z-10"
                :class="{ 'text-rose-500 fill-rose-500': wishlisted }">
            <svg class="w-4 h-4 transition-colors" :class="{ 'fill-rose-500 text-rose-500': wishlisted }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </button>

        <!-- Quick Add Overlay on Hover -->
        <div class="absolute inset-x-3 bottom-3 opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-200 z-10">
            <button @click="quickAdd($event)" 
                    type="button"
                    :disabled="adding || {{ !$product->in_stock ? 'true' : 'false' }}"
                    class="w-full py-2.5 px-4 bg-slate-900/95 hover:bg-slate-900 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-950 text-xs font-semibold rounded-xl shadow-lg flex items-center justify-center gap-2 backdrop-blur-md active:scale-[0.98] transition-all">
                <template x-if="!adding">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>{{ $product->in_stock ? 'Quick Add' : 'Out of Stock' }}</span>
                    </span>
                </template>
                <template x-if="adding">
                    <span class="flex items-center gap-1.5">
                        <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Adding...</span>
                    </span>
                </template>
            </button>
        </div>
    </a>

    <!-- Card Content -->
    <div class="p-4 flex-1 flex flex-col justify-between">
        <div>
            <!-- Category & Vendor -->
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 mb-1.5">
                <span class="font-medium tracking-wide">{{ $product->category->name ?? 'General' }}</span>
                @if($product->vendor)
                    <span class="flex items-center gap-1 text-[11px] truncate max-w-[120px]" title="{{ $product->vendor->shop_name }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span class="truncate">{{ $product->vendor->shop_name }}</span>
                    </span>
                @endif
            </div>

            <!-- Title -->
            <h3 class="font-semibold text-slate-900 dark:text-slate-100 text-sm group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2 leading-snug">
                <a href="{{ route('products.show', $product->slug) }}">
                    {{ $product->name }}
                </a>
            </h3>

            <!-- Rating -->
            <div class="flex items-center gap-1.5 mt-2">
                <div class="flex items-center text-amber-400 text-xs">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($product->rating_avg))
                            <span>★</span>
                        @else
                            <span class="text-slate-300 dark:text-slate-700">★</span>
                        @endif
                    @endfor
                </div>
                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                    ({{ $product->rating_count }})
                </span>
            </div>
        </div>

        <!-- Price -->
        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-baseline justify-between">
            <div class="flex items-baseline gap-2">
                <span class="text-base font-bold text-slate-900 dark:text-white">
                    ${{ number_format($product->current_price, 2) }}
                </span>
                @if($product->sale_price && $product->sale_price < $product->price)
                    <span class="text-xs text-slate-400 line-through">
                        ${{ number_format($product->price, 2) }}
                    </span>
                @endif
            </div>

            @if($product->stock_quantity <= 5 && $product->stock_quantity > 0)
                <span class="text-[11px] font-semibold text-amber-600 dark:text-amber-400">
                    Only {{ $product->stock_quantity }} left!
                </span>
            @endif
        </div>
    </div>
</div>
