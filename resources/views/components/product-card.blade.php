@props(['product'])

@php
    $inWishlist = false;
    if (Auth::check()) {
        $inWishlist = \App\Models\Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->exists();
    }
    $productJson = [
        'id' => $product->id,
        'name' => $product->name,
        'slug' => $product->slug,
        'price' => $product->price,
        'compare_price' => $product->compare_price,
        'image' => $product->primary_image,
        'category' => $product->category->name ?? 'Hardware',
        'vendor' => $product->vendor->shop_name ?? 'UNCART Direct',
        'badge' => $product->is_featured ? 'Spotlight' : ($product->discount_percent > 0 ? "-{$product->discount_percent}%" : null),
        'description' => $product->short_description ?? Str::limit($product->description, 160),
        'rating' => $product->rating,
        'reviews_count' => $product->reviews_count ?? 12,
    ];
@endphp

<div x-data="{
    wishlisted: {{ $inWishlist ? 'true' : 'false' }},
    adding: false,
    openQuickModal(e) {
        e.preventDefault();
        e.stopPropagation();
        window.dispatchEvent(new CustomEvent('open-quick-view', { detail: {{ json_encode($productJson) }} }));
    },
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
            window.dispatchEvent(new CustomEvent('open-cart-drawer'));
        } catch (err) {
            this.adding = false;
        }
    }
}" 
class="group relative bg-white rounded-2xl sm:rounded-3xl border border-[#DDE4EC] hover:border-[#F59E0B]/50 shadow-xs hover:shadow-xl hover:shadow-[#1B0E24]/5 transition-all duration-300 flex flex-col h-full overflow-hidden hover:-translate-y-1.5 will-change-transform">

    <!-- Card Top Actions & Image Container: Fixed uniform box preventing height jump -->
    <div class="relative w-full h-48 sm:h-52 bg-[#F0F3F7]/60 p-3 sm:p-4 flex items-center justify-center overflow-hidden flex-shrink-0">
        <a href="{{ route('products.show', $product->slug) }}" class="w-full h-full flex items-center justify-center overflow-hidden">
            <img src="{{ $product->primary_image }}" 
                 alt="{{ $product->name }}" 
                 loading="lazy"
                 referrerpolicy="no-referrer"
                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80';"
                 class="max-w-full max-h-full object-contain group-hover:scale-108 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]">
        </a>

        <!-- Discount Pill Badge (Top Left) -->
        <div class="absolute top-3 left-3 flex flex-col gap-1 z-10">
            @if($product->discount_percent > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#F59E0B] text-[#1B0E24] shadow-sm">
                    -{{ $product->discount_percent }}%
                </span>
            @elseif($product->is_featured)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#1B0E24] text-white shadow-sm">
                    Hot
                </span>
            @endif
        </div>

        <!-- Top Right Actions: Wishlist & Compare -->
        <div class="absolute top-3 right-3 flex flex-col gap-1.5 z-10">
            <!-- Wishlist Button -->
            <button @click="toggleWishlist($event)"
                    class="w-8 h-8 rounded-full bg-white/95 hover:bg-white text-slate-400 hover:text-rose-500 shadow-sm border border-slate-100 flex items-center justify-center transition-all duration-200 active:scale-90"
                    :class="{ 'text-rose-500 fill-rose-500': wishlisted }"
                    title="Save to Wishlist">
                <svg class="w-4 h-4 transition-transform" :class="{ 'scale-125 fill-rose-500 text-rose-500': wishlisted }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>

            <!-- Compare Button -->
            <button @click.prevent.stop="window.dispatchEvent(new CustomEvent('toggle-compare', { detail: { id: {{ $product->id }}, name: '{{ addslashes($product->name) }}', image: '{{ $product->primary_image }}' } }))"
                    type="button"
                    class="w-8 h-8 rounded-full bg-white/95 hover:bg-white text-slate-400 hover:text-[#6B2A88] shadow-sm border border-slate-100 flex items-center justify-center transition-all duration-200 active:scale-90 opacity-0 group-hover:opacity-100"
                    title="Compare Product">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </button>
        </div>

        <!-- Quick View Hover Floating Pill -->
        <button @click="openQuickModal($event)" 
                class="absolute bottom-3 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-200 py-1.5 px-3.5 rounded-full bg-white/95 text-[#1B0E24] text-[11px] font-bold shadow-md hover:bg-[#1B0E24] hover:text-[#F59E0B] flex items-center gap-1.5 z-10 whitespace-nowrap">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <span>Quick View</span>
        </button>
    </div>

    <!-- Product Card Details Area: Even layout alignment -->
    <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between space-y-3 bg-white">
        
        <div>
            <!-- Category / Subtitle Spec -->
            <div class="text-[11px] text-slate-400 font-medium truncate mb-1">
                {{ $product->category->name ?? 'Curated Electronics' }}
            </div>

            <!-- Product Title: 2-line max for perfect multi-line balance without misalignment -->
            <h3 class="text-xs sm:text-sm font-bold text-[#1B0E24] group-hover:text-[#F59E0B] transition-colors line-clamp-2 min-h-[2rem]">
                <a href="{{ route('products.show', $product->slug) }}">
                    {{ $product->name }}
                </a>
            </h3>

            <!-- Rating & Verified Count -->
            <div class="flex items-center gap-1.5 mt-1.5 text-xs">
                <div class="flex text-[#F59E0B] text-xs">★★★★★</div>
                <span class="text-[11px] text-slate-500 font-semibold">{{ number_format($product->rating, 1) }}</span>
                <span class="text-[10px] text-slate-400">({{ $product->reviews_count ?? 12 }})</span>
            </div>
        </div>

        <!-- Pricing & Quick Add Button -->
        <div class="pt-2 border-t border-slate-50 flex items-center justify-between gap-2 mt-auto">
            <div>
                <div class="text-sm sm:text-base font-extrabold text-[#1B0E24]">
                    <span x-text="$store.currency ? $store.currency.format({{ (float)$product->price }}) : '${{ number_format($product->price, 2) }}'">${{ number_format($product->price, 2) }}</span>
                </div>
                @if($product->compare_price && $product->compare_price > $product->price)
                    <div class="text-[11px] text-slate-400 line-through">
                        <span x-text="$store.currency ? $store.currency.format({{ (float)$product->compare_price }}) : '${{ number_format($product->compare_price, 2) }}'">${{ number_format($product->compare_price, 2) }}</span>
                    </div>
                @endif
            </div>

            <!-- Quick Add to Cart Icon Button -->
            <button @click="quickAdd($event)"
                    :disabled="adding"
                    class="w-9 h-9 rounded-xl bg-[#F0F3F7] hover:bg-[#1B0E24] text-[#1B0E24] hover:text-[#F59E0B] flex items-center justify-center transition-all duration-200 active:scale-95 shadow-xs flex-shrink-0 font-bold"
                    title="Add to Cart">
                <template x-if="!adding">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </template>
                <template x-if="adding">
                    <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </template>
            </button>
        </div>

    </div>

</div>
