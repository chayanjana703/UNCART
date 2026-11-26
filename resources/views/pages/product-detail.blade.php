@extends('layouts.app')

@section('title', $product->name . ' — UNCART')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12" x-data="{
    activeImage: '{{ $product->primary_image }}',
    qty: 1,
    selectedColor: 'Default Finish',
    adding: false,
    reviewModal: false,
    inquiryModal: false,
    lightboxOpen: false,
    shareModal: false,
    copied: false,
    reviewLightboxOpen: false,
    reviewLightboxImage: '',
    reviewLightboxUser: '',
    openReviewImage(url, userName) {
        this.reviewLightboxImage = url;
        this.reviewLightboxUser = userName;
        this.reviewLightboxOpen = true;
    },
    gallery: {{ json_encode($product->gallery_images) }},
    nextImage() {
        let idx = this.gallery.indexOf(this.activeImage);
        let nextIdx = (idx + 1) % this.gallery.length;
        this.activeImage = this.gallery[nextIdx];
    },
    prevImage() {
        let idx = this.gallery.indexOf(this.activeImage);
        let prevIdx = (idx - 1 + this.gallery.length) % this.gallery.length;
        this.activeImage = this.gallery[prevIdx];
    },
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
                        Color: this.selectedColor
                    }
                })
            });
            let data = await res.json();
            this.adding = false;
            confetti({
                particleCount: 60,
                spread: 60,
                origin: { y: 0.8 }
            });
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
            window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
            window.dispatchEvent(new CustomEvent('open-cart-drawer'));
        } catch(e) {
            this.adding = false;
        }
    }
}">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-8" data-reveal>
        <a href="{{ route('home') }}" class="hover:text-indigo-600 transition">Home</a>
        <span>/</span>
        <a href="{{ route('products.index', ['category' => $product->category->slug ?? 'all']) }}" class="hover:text-indigo-600 transition">{{ $product->category->name ?? 'Collection' }}</a>
        <span>/</span>
        <span class="text-slate-800 font-medium truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <!-- Main Product Section (2-Column Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 sm:gap-12 pb-16 border-b border-slate-200/80">
        
        <!-- Left: Image Gallery (Sticky on Desktop) -->
        <div class="space-y-4 lg:sticky lg:top-28 h-fit" data-reveal>
            <!-- Active Large Image Showcase -->
            <div @click="lightboxOpen = true" 
                 class="aspect-square w-full rounded-3xl overflow-hidden bg-white border border-slate-100 shadow-sm relative group p-6 flex items-center justify-center cursor-zoom-in">
                <img :src="activeImage" 
                     alt="{{ $product->name }}" 
                     referrerpolicy="no-referrer"
                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800';"
                     class="max-w-full max-h-full object-contain transition-all duration-500 group-hover:scale-108">

                @if($product->discount_percent > 0)
                    <span class="absolute top-4 left-4 px-3 py-1.5 text-xs font-black uppercase tracking-wider bg-rose-500 text-white rounded-full shadow-sm pointer-events-none">
                        -{{ $product->discount_percent }}% OFF
                    </span>
                @endif

                @if($product->is_featured)
                    <span class="absolute top-4 right-4 px-3 py-1.5 text-xs font-bold uppercase tracking-wider bg-[#1B0E24] text-[#F59E0B] rounded-full shadow-sm pointer-events-none">
                        Hot
                    </span>
                @endif

                <!-- Hover Zoom Indicator Pill -->
                <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-200 py-1 px-3 rounded-full bg-slate-900/80 backdrop-blur-md text-white text-[10px] font-bold flex items-center gap-1 shadow pointer-events-none">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                    <span>Click to Zoom</span>
                </div>
            </div>

            <!-- Thumbnail Selector Strip -->
            @if(count($product->gallery_images) > 1)
                <div class="flex items-center gap-3 overflow-x-auto pb-2">
                    @foreach($product->gallery_images as $img)
                        <button type="button" 
                                @click="activeImage = '{{ $img }}'" 
                                class="w-20 h-20 rounded-2xl overflow-hidden border-2 flex-shrink-0 transition-all duration-200 active:scale-95 bg-white p-1 flex items-center justify-center"
                                :class="activeImage === '{{ $img }}' ? 'border-indigo-600 ring-2 ring-indigo-100' : 'border-slate-200 opacity-70 hover:opacity-100'">
                            <img src="{{ $img }}" alt="Thumbnail" referrerpolicy="no-referrer" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=200';" class="max-w-full max-h-full object-contain">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right: Purchase Details & Information -->
        <div class="flex flex-col justify-between space-y-6" data-reveal>
            <div class="space-y-5">
                
                <!-- Category, SKU & Stock -->
                <div class="flex items-center justify-between text-xs">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#FEF8E7] text-[#1B0E24] border border-[#FDEEC4]">
                        {{ $product->category->name ?? 'Lifestyle' }}
                    </span>
                    <span class="text-slate-400 font-mono text-[11px]">SKU: {{ $product->sku }}</span>
                </div>

                <!-- Product Name -->
                <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight">
                    {{ $product->name }}
                </h1>

                <!-- Rating & Reviews Bar -->
                <div class="flex items-center gap-3 pt-1 text-xs">
                    <div class="flex items-center text-amber-400 text-sm">
                        @for($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= round($product->rating) ? '★' : '☆' }}</span>
                        @endfor
                    </div>
                    <span class="font-bold text-slate-900">{{ number_format($product->rating, 1) }}</span>
                    <span class="text-slate-300">&bull;</span>
                    <a href="#reviews-section" class="text-indigo-600 hover:underline font-semibold">
                        {{ $product->reviews->count() }} Verified Reviews
                    </a>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-emerald-600 font-semibold">{{ $product->sales_count }} Sold</span>
                </div>

                <!-- Pricing Block -->
                <div class="py-4 border-y border-slate-100 flex items-baseline gap-4">
                    <span class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                        <span x-text="$store.currency ? $store.currency.format({{ (float)$product->price }}) : '${{ number_format($product->price, 2) }}'">${{ number_format($product->price, 2) }}</span>
                    </span>
                    @if($product->compare_price && $product->compare_price > $product->price)
                        <span class="text-lg text-slate-400 line-through">
                            <span x-text="$store.currency ? $store.currency.format({{ (float)$product->compare_price }}) : '${{ number_format($product->compare_price, 2) }}'">${{ number_format($product->compare_price, 2) }}</span>
                        </span>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                            Save <span x-text="$store.currency ? $store.currency.format({{ (float)($product->compare_price - $product->price) }}) : '${{ number_format($product->compare_price - $product->price, 2) }}'">${{ number_format($product->compare_price - $product->price, 2) }}</span>
                        </span>
                    @endif
                </div>

                <!-- Short Overview -->
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                    {{ $product->short_description ?? $product->description }}
                </p>

                <!-- Color / Variant Chips -->
                <div class="space-y-2 pt-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Color Selection: <span class="text-indigo-600 font-normal" x-text="selectedColor"></span>
                    </label>
                    <div class="flex items-center gap-2">
                        <template x-for="c in ['Default Finish', 'Midnight Blue', 'Silver Frost']" :key="c">
                            <button type="button" 
                                    @click="selectedColor = c" 
                                    class="px-3.5 py-2 rounded-full text-xs font-semibold border transition active:scale-95"
                                    :class="selectedColor === c ? 'bg-[#1B0E24] text-white border-[#1B0E24] shadow-sm' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                                <span x-text="c"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Verified Merchant Tile -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center font-bold text-indigo-600 shadow-sm border border-slate-200">
                            {{ substr($product->vendor->shop_name ?? 'N', 0, 1) }}
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-900">{{ $product->vendor->shop_name ?? 'UNCART Direct' }}</div>
                            <div class="text-[11px] text-emerald-600 flex items-center gap-1 font-semibold">
                                <span>Verified Merchant</span>
                                <span>•</span>
                                <span>★ {{ number_format($product->vendor->rating ?? 4.9, 1) }}</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" @click="inquiryModal = true" class="px-3 py-1.5 rounded-full bg-white hover:bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700 transition">
                        Ask Question
                    </button>
                </div>

                <!-- Add to Cart & Buy Workflow -->
                <div class="pt-4 space-y-3">
                    <div class="flex items-center gap-4">
                        <!-- Quantity Counter -->
                        <div class="flex items-center rounded-full bg-slate-100 border border-slate-200 p-1">
                            <button type="button" @click="if(qty > 1) qty--" class="w-9 h-9 rounded-full text-slate-600 hover:text-slate-900 hover:bg-white flex items-center justify-center font-bold text-sm transition active:scale-90">-</button>
                            <span class="w-12 text-center font-bold text-sm text-slate-900" x-text="qty"></span>
                            <button type="button" @click="qty++" class="w-9 h-9 rounded-full text-slate-600 hover:text-slate-900 hover:bg-white flex items-center justify-center font-bold text-sm transition active:scale-90">+</button>
                        </div>

                        <!-- Add to Cart Button -->
                        <button type="button" 
                                @click="addToCart()" 
                                :disabled="adding"
                                class="flex-1 py-4 px-8 rounded-full bg-[#1B0E24] hover:bg-[#F59E0B] hover:text-[#1B0E24] text-white font-black text-xs uppercase tracking-wider shadow-md flex items-center justify-center gap-2 transition active:scale-95">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span x-text="adding ? 'Adding...' : 'Add to Cart — ' + ($store.currency ? $store.currency.format({{ (float)$product->price }} * qty) : '$' + ({{ $product->price }} * qty).toFixed(2))"></span>
                        </button>
                    </div>

                    <!-- Secondary Action Controls (Compare & Share) -->
                    <div class="flex items-center gap-3 pt-1">
                        <button type="button" 
                                @click="window.dispatchEvent(new CustomEvent('toggle-compare', { detail: { id: {{ $product->id }}, name: '{{ addslashes($product->name) }}', image: '{{ $product->primary_image }}' } }))"
                                class="flex-1 py-2.5 px-4 rounded-full bg-slate-100 hover:bg-slate-200/80 text-slate-700 text-xs font-bold transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span>Add to Compare</span>
                        </button>

                        <button type="button" 
                                @click="shareModal = true"
                                class="py-2.5 px-4 rounded-full bg-slate-100 hover:bg-slate-200/80 text-slate-700 text-xs font-bold transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            <span>Share</span>
                        </button>
                    </div>

                    <!-- Trust Strip -->
                    <div class="grid grid-cols-3 gap-2 pt-2 text-[10px] text-slate-500 text-center">
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">🚚 Free Shipping $99+</div>
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">🛡️ 2-Year Warranty</div>
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">🔄 7-Day Returns</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Technical Specifications Matrix Section -->
    <section class="py-12 border-b border-slate-200/80" data-reveal>
        <div class="max-w-3xl">
            <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Product Specifications</span>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight mt-1 mb-6">Specifications & Features</h2>
            
            <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden divide-y divide-slate-100 shadow-sm">
                @if(is_array($product->specs) && count($product->specs) > 0)
                    @foreach($product->specs as $key => $val)
                        <div class="grid grid-cols-3 p-4 text-xs">
                            <span class="text-slate-400 uppercase tracking-wider font-semibold text-[11px]">{{ $key }}</span>
                            <span class="col-span-2 text-slate-900 font-medium">{{ $val }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="grid grid-cols-3 p-4 text-xs">
                        <span class="text-slate-400 font-semibold">Dimensions</span>
                        <span class="col-span-2 text-slate-900">Standard retail dimensions (184 x 165 x 82 mm)</span>
                    </div>
                    <div class="grid grid-cols-3 p-4 text-xs">
                        <span class="text-slate-400 font-semibold">Connectivity</span>
                        <span class="col-span-2 text-slate-900">Universal compatibility, wireless & wired options</span>
                    </div>
                    <div class="grid grid-cols-3 p-4 text-xs">
                        <span class="text-slate-400 font-semibold">Warranty</span>
                        <span class="col-span-2 text-slate-900">Full 2-Year replacement & manufacturer warranty</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Customer Reviews Section -->
    <section id="reviews-section" class="py-12" data-reveal>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Customer Feedback</span>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Verified Buyer Reviews</h2>
            </div>
            
            @auth
                <button type="button" 
                        @click="reviewModal = true" 
                        class="py-2.5 px-5 rounded-full bg-[#1B0E24] hover:bg-[#F59E0B] hover:text-[#1B0E24] text-white font-bold text-xs uppercase tracking-wider shadow-sm transition active:scale-95">
                    Write a Review
                </button>
            @else
                <a href="{{ route('login') }}" 
                   class="py-2.5 px-5 rounded-full bg-[#1B0E24] hover:bg-[#F59E0B] hover:text-[#1B0E24] text-white font-bold text-xs uppercase tracking-wider shadow-sm transition active:scale-95 inline-block text-center">
                    Sign In to Review
                </a>
            @endauth
        </div>

        <!-- Review Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($product->reviews as $rev)
                <div class="p-6 rounded-3xl bg-white border border-slate-100 shadow-sm space-y-3 flex flex-col justify-between"
                     x-data="{
                         helpful: {{ (int) $rev->helpful_votes }},
                         unhelpful: {{ (int) $rev->unhelpful_votes }},
                         userVote: '{{ $rev->user_vote ?? '' }}',
                         isVoting: false,
                         async vote(type) {
                             @guest
                                 window.location.href = '{{ route('login') }}';
                                 return;
                             @endguest

                             @auth
                                 if ({{ auth()->id() }} === {{ (int) $rev->user_id }}) {
                                     alert('You cannot vote on your own review.');
                                     return;
                                 }
                             @endauth

                             if (this.isVoting) return;
                             this.isVoting = true;

                             const prevHelpful = this.helpful;
                             const prevUnhelpful = this.unhelpful;
                             const prevVote = this.userVote;

                             if (this.userVote === type) {
                                 if (type === 'helpful') this.helpful = Math.max(0, this.helpful - 1);
                                 else this.unhelpful = Math.max(0, this.unhelpful - 1);
                                 this.userVote = '';
                             } else {
                                 if (this.userVote === 'helpful') this.helpful = Math.max(0, this.helpful - 1);
                                 if (this.userVote === 'unhelpful') this.unhelpful = Math.max(0, this.unhelpful - 1);
                                 if (type === 'helpful') this.helpful++;
                                 else this.unhelpful++;
                                 this.userVote = type;
                             }

                             try {
                                 const res = await fetch('{{ route('reviews.vote', $rev->id) }}', {
                                     method: 'POST',
                                     headers: {
                                         'Content-Type': 'application/json',
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                         'Accept': 'application/json'
                                     },
                                     body: JSON.stringify({ vote_type: type })
                                 });
                                 const data = await res.json();
                                 if (data.success) {
                                     this.helpful = data.helpful;
                                     this.unhelpful = data.unhelpful;
                                     this.userVote = data.user_vote || '';
                                 } else {
                                     this.helpful = prevHelpful;
                                     this.unhelpful = prevUnhelpful;
                                     this.userVote = prevVote;
                                     alert(data.message || 'Unable to register vote.');
                                 }
                             } catch (e) {
                                 this.helpful = prevHelpful;
                                 this.unhelpful = prevUnhelpful;
                                 this.userVote = prevVote;
                             } finally {
                                 this.isVoting = false;
                             }
                         }
                     }">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-xs">
                                    {{ strtoupper(substr($rev->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-slate-900 text-xs">{{ $rev->user->name ?? 'Verified Buyer' }}</span>
                                        @if($rev->is_verified_purchase)
                                            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Verified Purchase</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-slate-400">{{ $rev->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div class="flex text-amber-400 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <span>{{ $i <= $rev->rating ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                        </div>

                        <h4 class="font-bold text-slate-900 text-sm">{{ $rev->title }}</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $rev->comment }}</p>

                        <!-- Customer Review Photos -->
                        @if(!empty($rev->photos) && count($rev->photos) > 0)
                            <div class="mt-3 pt-3 border-t border-slate-100">
                                <div class="text-[11px] font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Customer Photos ({{ count($rev->photos) }})
                                </div>
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    @foreach($rev->photos as $photo)
                                        <button type="button" 
                                                @click="openReviewImage('{{ $photo }}', '{{ addslashes($rev->user->name ?? 'Verified Buyer') }}')" 
                                                class="group relative w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden border border-slate-200/90 bg-slate-50 hover:border-indigo-500 hover:shadow-md transition-all cursor-zoom-in"
                                                title="Click to enlarge photo">
                                            <img src="{{ $photo }}" alt="Customer review photo" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 transition drop-shadow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                                </svg>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($rev->vendor_reply)
                            <div class="mt-3 pl-3.5 border-l-2 border-indigo-600 bg-indigo-50/50 p-3 rounded-r-2xl">
                                <div class="text-[11px] font-bold text-indigo-700">Merchant Response</div>
                                <p class="text-xs text-slate-600 mt-1">{{ $rev->vendor_reply }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Review Helpful Voting Bar -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3 text-xs mt-2">
                        <span class="text-slate-400 text-[11px] font-medium">Was this review helpful?</span>

                        <div class="flex items-center gap-1.5">
                            <!-- Helpful Button -->
                            <button type="button" 
                                    @click="vote('helpful')" 
                                    :disabled="isVoting"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 active:scale-95 disabled:opacity-60 cursor-pointer"
                                    :class="userVote === 'helpful' 
                                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-300 shadow-sm' 
                                        : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200/80'">
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" 
                                     :class="userVote === 'helpful' ? 'scale-110 text-emerald-600' : 'text-slate-400'" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                                </svg>
                                <span>Helpful</span>
                                <span class="font-mono text-[11px] px-1.5 py-0.2 rounded-full" 
                                      :class="userVote === 'helpful' ? 'bg-emerald-200/60 text-emerald-800' : 'bg-slate-200/60 text-slate-600'" 
                                      x-text="helpful"></span>
                            </button>

                            <!-- Unhelpful Button -->
                            <button type="button" 
                                    @click="vote('unhelpful')" 
                                    :disabled="isVoting"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 active:scale-95 disabled:opacity-60 cursor-pointer"
                                    :class="userVote === 'unhelpful' 
                                        ? 'bg-rose-50 text-rose-700 border border-rose-300 shadow-sm' 
                                        : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200/80'"
                                    title="Mark as not helpful">
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" 
                                     :class="userVote === 'unhelpful' ? 'scale-110 text-rose-600' : 'text-slate-400'" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v6zM14 9.667v-5.43a2 2 0 00-1.106-1.79l-.05-.025A4 4 0 0011.057 2H5.64a2 2 0 00-1.962 1.608l-1.2 6A2 2 0 004.44 12H8v4a2 2 0 002 2 1 1 0 001-1v-.667a4 4 0 01.8-2.4l1.4-1.866a4 4 0 00.8-2.4z"/>
                                </svg>
                                <span class="font-mono text-[11px] px-1.5 py-0.2 rounded-full"
                                      :class="userVote === 'unhelpful' ? 'bg-rose-200/60 text-rose-800' : 'bg-slate-200/60 text-slate-600'"
                                      x-text="unhelpful"></span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 p-12 text-center rounded-3xl bg-white border border-slate-100 shadow-sm">
                    <p class="text-xs text-slate-500">No customer reviews yet. Be the first to share your experience!</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Write Review Pop-Up Modal Window -->
    <div x-show="reviewModal" 
         x-transition.opacity 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;"
         @keydown.escape.window="reviewModal = false">
        <div @click.away="reviewModal = false" class="bg-white border border-slate-100 rounded-3xl w-full max-w-lg p-6 sm:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-lg font-bold text-slate-900">Write Verified Review</h3>
                <button type="button" @click="reviewModal = false" class="text-slate-400 hover:text-slate-600 p-1">&times;</button>
            </div>
            <p class="text-xs text-slate-500 mb-5">Share your experience and photos with {{ $product->name }}.</p>

            <form action="{{ route('reviews.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="space-y-4"
                  x-data="{
                      selectedFiles: [],
                      previews: [],
                      dragOver: false,
                      addFiles(fileList) {
                          const incoming = Array.from(fileList);
                          if (this.selectedFiles.length + incoming.length > 5) {
                              alert('You can upload a maximum of 5 photos per review.');
                              return;
                          }
                          for (let file of incoming) {
                              if (file.size > 5 * 1024 * 1024) {
                                  alert(file.name + ' exceeds the 5MB size limit.');
                                  continue;
                              }
                              if (!['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/gif'].includes(file.type)) {
                                  alert(file.name + ' is not a supported image format.');
                                  continue;
                              }
                              this.selectedFiles.push(file);
                              const reader = new FileReader();
                              reader.onload = (e) => {
                                  this.previews.push({ name: file.name, url: e.target.result });
                              };
                              reader.readAsDataURL(file);
                          }
                          this.syncFiles();
                      },
                      removeFile(idx) {
                          this.selectedFiles.splice(idx, 1);
                          this.previews.splice(idx, 1);
                          this.syncFiles();
                      },
                      syncFiles() {
                          const dt = new DataTransfer();
                          this.selectedFiles.forEach(f => dt.items.add(f));
                          this.$refs.reviewFileInput.files = dt.files;
                      }
                  }">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Star Rating</label>
                    <select name="rating" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="5">★★★★★ (5/5) Exceptional Quality</option>
                        <option value="4">★★★★☆ (4/5) Very Good</option>
                        <option value="3">★★★☆☆ (3/5) Average Experience</option>
                        <option value="2">★★☆☆☆ (2/5) Disappointed</option>
                        <option value="1">★☆☆☆☆ (1/5) Poor Quality</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Headline / Summary</label>
                    <input type="text" name="title" required placeholder="e.g. Exceptional sound and durable build" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Detailed Review</label>
                    <textarea name="comment" rows="3" required placeholder="Write your honest review and real-world impressions..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <!-- Review Photos Upload Dropzone -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Attach Photos <span class="text-slate-400 font-normal">(Optional)</span>
                        </span>
                        <span class="text-[10px] text-slate-500 font-mono" x-text="previews.length + ' / 5 attached'"></span>
                    </label>

                    <input type="file" 
                           x-ref="reviewFileInput" 
                           name="images[]" 
                           multiple 
                           accept="image/jpeg,image/png,image/webp,image/gif" 
                           class="hidden" 
                           @change="addFiles($event.target.files)">

                    <div @click="$refs.reviewFileInput.click()"
                         @dragover.prevent="dragOver = true"
                         @dragleave.prevent="dragOver = false"
                         @drop.prevent="dragOver = false; addFiles($event.dataTransfer.files)"
                         class="cursor-pointer border-2 border-dashed rounded-2xl p-4 text-center transition-all duration-200"
                         :class="dragOver ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-200 hover:border-indigo-400 bg-slate-50/60'">
                        <div class="flex flex-col items-center justify-center gap-1">
                            <div class="w-8 h-8 rounded-full bg-white shadow-sm border border-slate-200/80 flex items-center justify-center text-indigo-600">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <div class="text-xs text-slate-700 font-medium">
                                <span class="text-indigo-600 font-bold hover:underline">Click to upload</span> or drag photos here
                            </div>
                            <p class="text-[10px] text-slate-400">JPG, PNG, WEBP up to 5MB each (Max 5 photos)</p>
                        </div>
                    </div>

                    <!-- Thumbnails Preview Strip -->
                    <div x-show="previews.length > 0" class="mt-3 flex items-center gap-2.5 flex-wrap">
                        <template x-for="(prev, idx) in previews" :key="idx">
                            <div class="relative group w-16 h-16 rounded-xl overflow-hidden border border-slate-200 shadow-sm bg-slate-100">
                                <img :src="prev.url" :alt="prev.name" class="w-full h-full object-cover">
                                <button type="button" 
                                        @click.stop="removeFile(idx)" 
                                        class="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center text-xs font-bold shadow-md transition"
                                        title="Remove photo">
                                    &times;
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="reviewModal = false" class="px-4 py-2 rounded-full border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-full bg-[#1B0E24] hover:bg-[#F59E0B] hover:text-[#1B0E24] text-white text-xs font-bold shadow-sm transition active:scale-95">Submit Review</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ask Seller Question Modal Pop-Up -->
    <div x-show="inquiryModal" 
         x-transition.opacity 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;"
         @keydown.escape.window="inquiryModal = false">
        <div @click.away="inquiryModal = false" class="bg-white border border-slate-100 rounded-3xl w-full max-w-lg p-6 sm:p-8 shadow-2xl relative">
            <h3 class="text-lg font-bold text-slate-900 mb-1">Ask Merchant a Question</h3>
            <p class="text-xs text-slate-500 mb-5">Message the seller directly regarding {{ $product->name }}.</p>

            <form action="{{ route('inquiries.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="vendor_id" value="{{ $product->vendor_id }}">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Subject</label>
                    <input type="text" name="subject" required placeholder="e.g. Question about size or warranty" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Your Question</label>
                    <textarea name="message" rows="3" required placeholder="Type your inquiry here..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="inquiryModal = false" class="px-4 py-2 rounded-full border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-full bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold shadow-sm">Send Inquiry</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Fullscreen Image Lightbox Modal -->
    <div x-show="lightboxOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="lightboxOpen = false"
         @keydown.right.window="nextImage()"
         @keydown.left.window="prevImage()"
         class="fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-xl flex flex-col justify-between p-4 sm:p-8"
         style="display: none;">

        <!-- Top Header & Controls -->
        <div class="flex items-center justify-between text-white z-10">
            <div>
                <h4 class="font-bold text-sm text-white">{{ $product->name }}</h4>
                <span class="text-xs text-slate-400" x-text="(gallery.indexOf(activeImage) + 1) + ' of ' + gallery.length"></span>
            </div>

            <button type="button" 
                    @click="lightboxOpen = false" 
                    class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Center Image Showcase with Prev / Next Navigation -->
        <div class="relative flex-1 flex items-center justify-center p-4">
            <!-- Prev Arrow -->
            <button type="button" 
                    @click="prevImage()" 
                    x-show="gallery.length > 1"
                    class="absolute left-2 sm:left-6 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition backdrop-blur-md z-10 active:scale-95">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <!-- Large Display Image -->
            <img :src="activeImage" 
                 alt="{{ $product->name }}" 
                 referrerpolicy="no-referrer"
                 class="max-h-[75vh] max-w-full object-contain rounded-2xl drop-shadow-2xl transition-transform duration-300">

            <!-- Next Arrow -->
            <button type="button" 
                    @click="nextImage()" 
                    x-show="gallery.length > 1"
                    class="absolute right-2 sm:right-6 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition backdrop-blur-md z-10 active:scale-95">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <!-- Bottom Thumbnail Strip -->
        <div class="flex items-center justify-center gap-3 overflow-x-auto py-2 z-10" x-show="gallery.length > 1">
            <template x-for="img in gallery" :key="img">
                <button type="button" 
                        @click="activeImage = img"
                        class="w-14 h-14 rounded-xl overflow-hidden border-2 transition-all p-1 bg-white/5"
                        :class="activeImage === img ? 'border-indigo-500 scale-105' : 'border-white/10 opacity-60 hover:opacity-100'">
                    <img :src="img" class="w-full h-full object-contain" referrerpolicy="no-referrer">
                </button>
            </template>
        </div>
    </div>

    <!-- Share Product Modal -->
    <div x-show="shareModal" 
         x-transition.opacity 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;"
         @keydown.escape.window="shareModal = false">
        <div @click.away="shareModal = false" class="bg-white border border-slate-100 rounded-3xl w-full max-w-md p-6 sm:p-7 shadow-2xl relative space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-sm text-slate-900">Share This Product</h3>
                <button @click="shareModal = false" class="text-slate-400 hover:text-slate-700">&times;</button>
            </div>
            
            <p class="text-xs text-slate-500">Share this direct link with friends or colleagues:</p>
            
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

    <!-- Review Photo Lightbox Modal -->
    <div x-show="reviewLightboxOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-xl flex flex-col justify-between p-4 sm:p-8" 
         style="display: none;"
         @keydown.escape.window="reviewLightboxOpen = false">
        
        <!-- Header -->
        <div class="flex items-center justify-between text-white z-10">
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">Customer Photo by</span>
                <span class="text-xs font-bold text-amber-400" x-text="reviewLightboxUser"></span>
            </div>
            <button type="button" 
                    @click="reviewLightboxOpen = false" 
                    class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Center Image View -->
        <div class="relative flex-1 flex items-center justify-center p-4">
            <img :src="reviewLightboxImage" 
                 alt="Customer review photo" 
                 class="max-h-[80vh] max-w-full object-contain rounded-2xl drop-shadow-2xl border border-white/10">
        </div>

        <div class="text-center text-xs text-slate-500 py-1">
            Press ESC or click the close button to exit
        </div>
    </div>
</div>
@endsection
