<div x-data="compareTrayHandler()" 
     x-cloak
     @toggle-compare.window="toggleItem($event.detail)"
     class="fixed bottom-16 sm:bottom-6 left-1/2 -translate-x-1/2 z-40">

    <div x-show="items.length > 0"
         x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 scale-95"
         class="bg-slate-900/95 backdrop-blur-md text-white rounded-full p-2 pl-4 pr-3 shadow-2xl border border-white/10 flex items-center gap-3 sm:gap-4 max-w-lg"
         style="display: none;">

        <!-- Indicator & Count -->
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-xs font-bold text-slate-200">
                Compare <span class="text-indigo-400" x-text="'(' + items.length + '/4)'"></span>
            </span>
        </div>

        <!-- Thumbnails of selected items -->
        <div class="flex items-center -space-x-2 overflow-hidden py-0.5">
            <template x-for="item in items" :key="item.id">
                <div class="relative group/thumb">
                    <img :src="item.image" 
                         :alt="item.name" 
                         referrerpolicy="no-referrer"
                         class="w-8 h-8 rounded-full object-cover bg-white p-0.5 ring-2 ring-slate-900 border border-slate-200/40">
                    <button type="button" 
                            @click="removeItem(item.id)" 
                            class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-600 text-white text-[9px] font-bold flex items-center justify-center opacity-0 group-hover/thumb:opacity-100 transition shadow">
                        &times;
                    </button>
                </div>
            </template>
        </div>

        <!-- Compare Action Button -->
        <a :href="'{{ route('products.compare') }}?ids=' + items.map(i => i.id).join(',')" 
           class="py-1.5 px-4 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs uppercase tracking-wider transition active:scale-95 shadow flex items-center gap-1.5 whitespace-nowrap">
            <span>Compare</span>
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </a>

        <!-- Clear Button -->
        <button type="button" 
                @click="clearAll()" 
                class="text-[11px] text-slate-400 hover:text-slate-200 transition font-medium"
                title="Clear tray">
            &times;
        </button>
    </div>

</div>

<script>
function compareTrayHandler() {
    return {
        items: [],
        init() {
            try {
                let saved = localStorage.getItem('uncart_compare') || localStorage.getItem('shopicart_compare');
                if (saved) {
                    this.items = JSON.parse(saved);
                }
            } catch(e) {
                this.items = [];
            }
        },
        toggleItem(product) {
            let idx = this.items.findIndex(i => i.id === product.id);
            if (idx > -1) {
                this.items.splice(idx, 1);
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Removed from comparison', type: 'info' } }));
            } else {
                if (this.items.length >= 4) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'You can compare up to 4 items at once', type: 'warning' } }));
                    return;
                }
                this.items.push(product);
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Added to comparison tray', type: 'success' } }));
            }
            this.save();
        },
        removeItem(id) {
            this.items = this.items.filter(i => i.id !== id);
            this.save();
        },
        clearAll() {
            this.items = [];
            this.save();
        },
        save() {
            localStorage.setItem('uncart_compare', JSON.stringify(this.items));
        }
    };
}
</script>
