<div x-data="aiAssistantHandler()" 
     x-cloak
     class="relative">

    <!-- 1. Floating Trigger Button (Bottom-Right) -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-2.5">
        
        <!-- Helpful Greeting Bubble Tooltip (appears once, auto-hides or closes) -->
        <div x-show="!isOpen && showGreetingBubble" 
             x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
             class="bg-white/95 backdrop-blur-md border border-slate-200/90 shadow-xl rounded-2xl p-3 pr-3.5 flex items-center gap-3 text-xs max-w-[280px] pointer-events-auto"
             style="display: none;">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-amber-400 to-amber-600 flex items-center justify-center text-white text-sm font-black shadow-sm flex-shrink-0">
                ✦
            </div>
            <div class="flex-1 cursor-pointer" @click="toggleChat()">
                <div class="font-bold text-slate-900 leading-tight">UNCART Concierge</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Need help choosing or comparing? Ask me!</div>
            </div>
            <button type="button" @click.stop="showGreetingBubble = false" class="text-slate-400 hover:text-slate-600 p-1 text-xs">
                &times;
            </button>
        </div>

        <!-- Main FAB Trigger Button -->
        <button type="button"
                @click="toggleChat()"
                class="group relative flex items-center gap-2.5 py-3 px-4 sm:px-5 rounded-full bg-[#1B0E24] hover:bg-slate-900 text-white font-bold text-xs shadow-2xl shadow-indigo-950/30 border border-white/15 transition-all duration-300 active:scale-95 will-change-transform"
                :class="isOpen ? 'ring-2 ring-indigo-400/50' : 'hover:shadow-amber-500/10'">
            
            <!-- Animated Glow Dot -->
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
            </span>

            <!-- Sparkle / Chat Icon -->
            <svg x-show="!isOpen" class="w-4 h-4 text-amber-400 transition-transform group-hover:rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>

            <!-- Close 'X' Icon when open -->
            <svg x-show="isOpen" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>

            <span class="tracking-wide">
                <span x-text="isOpen ? 'Close Concierge' : 'Ask AI'"></span>
                <span class="text-amber-400 font-black">✦</span>
            </span>
        </button>
    </div>

    <!-- 2. Chat Window Popover Dialog -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
         x-transition:enter-start="opacity-0 translate-y-6 scale-95 origin-bottom-right"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100 origin-bottom-right"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100 origin-bottom-right"
         x-transition:leave-end="opacity-0 translate-y-6 scale-95 origin-bottom-right"
         @click.outside="if (window.innerWidth >= 640) isOpen = false"
         class="fixed bottom-20 right-3 sm:right-6 w-[calc(100vw-1.5rem)] sm:w-[420px] h-[590px] max-h-[82vh] bg-white rounded-3xl border border-slate-200/90 shadow-2xl z-50 flex flex-col overflow-hidden"
         style="display: none;">

        <!-- Header -->
        <div class="bg-[#1B0E24] text-white px-5 py-4 flex items-center justify-between border-b border-white/10 select-none">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-amber-400 via-amber-500 to-amber-600 flex items-center justify-center text-slate-950 font-black text-sm shadow-md shadow-amber-500/20">
                    ✦
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-extrabold text-sm tracking-tight text-white">UNCART Concierge</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold uppercase tracking-wider bg-white/15 text-amber-300">AI</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-[10px] text-slate-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span>Live Catalog & Order Assistant</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1">
                <!-- Clear History Button -->
                <button type="button" 
                        @click="clearHistory()"
                        title="Clear conversation"
                        class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/10 transition text-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>

                <!-- Minimize Button -->
                <button type="button" 
                        @click="isOpen = false"
                        title="Minimize"
                        class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/10 transition text-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Chat History Scroll View -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 text-xs bg-slate-50/70" x-ref="chatScroll">
            
            <!-- Welcome Message Bubble -->
            <div class="flex items-start gap-2.5">
                <div class="w-6 h-6 rounded-lg bg-[#1B0E24] text-amber-400 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                    ✦
                </div>
                <div class="bg-white border border-slate-200/80 rounded-2xl rounded-tl-sm p-3.5 text-slate-700 shadow-xs max-w-[85%] space-y-2">
                    <p class="font-semibold text-slate-900">
                        Hello! I am your UNCART Concierge.
                    </p>
                    <p class="text-slate-600 leading-relaxed">
                        I can help you explore premium gear, compare specs side-by-side, check live order delivery status, or connect you with human customer care.
                    </p>
                </div>
            </div>

            <!-- Starter Prompt Chips (shown when few messages) -->
            <template x-if="messages.length === 0">
                <div class="pt-2 pb-1 space-y-1.5">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-1">Quick Inquiries</p>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="(prompt, idx) in quickPrompts" :key="idx">
                            <button type="button" 
                                    @click="submitPrompt(prompt.query)"
                                    class="text-left py-1.5 px-3 rounded-xl bg-white hover:bg-indigo-50 border border-slate-200/90 hover:border-indigo-300 text-slate-700 hover:text-indigo-700 text-[11px] font-medium transition active:scale-95 shadow-xs">
                                <span x-text="prompt.label"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Dynamic Messages Loop -->
            <template x-for="(msg, index) in messages" :key="index">
                <div class="space-y-2.5">
                    
                    <!-- USER MESSAGE -->
                    <template x-if="msg.role === 'user'">
                        <div class="flex justify-end">
                            <div class="bg-gradient-to-r from-slate-900 to-[#1B0E24] text-white rounded-2xl rounded-tr-sm px-4 py-2.5 max-w-[85%] shadow-xs font-medium leading-relaxed"
                                 x-text="msg.text">
                            </div>
                        </div>
                    </template>

                    <!-- ASSISTANT MESSAGE -->
                    <template x-if="msg.role === 'assistant'">
                        <div class="flex items-start gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-[#1B0E24] text-amber-400 font-bold flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                                ✦
                            </div>
                            <div class="space-y-3 max-w-[90%] flex-1">
                                
                                <!-- Markdown Text Body -->
                                <div class="bg-white border border-slate-200/80 rounded-2xl rounded-tl-sm p-3.5 text-slate-700 shadow-xs leading-relaxed space-y-2 prose prose-xs prose-slate"
                                     x-html="renderMarkdown(msg.text)">
                                </div>

                                <!-- EMBEDDED PRODUCTS CARDS (IF ANY) -->
                                <template x-if="msg.products && msg.products.length > 0">
                                    <div class="space-y-2 pt-1">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Recommended Merchandise</p>
                                        <div class="grid grid-cols-1 gap-2">
                                            <template x-for="p in msg.products" :key="p.id">
                                                <div class="bg-white rounded-xl border border-slate-200 p-2.5 flex items-center gap-3 hover:border-indigo-400 transition shadow-xs group">
                                                    <img :src="p.image" :alt="p.name" class="w-12 h-12 rounded-lg object-cover bg-slate-100 flex-shrink-0 border border-slate-100">
                                                    
                                                    <div class="flex-1 min-w-0">
                                                        <a :href="p.url" class="font-bold text-slate-900 hover:text-indigo-600 truncate block text-[11px]">
                                                            <span x-text="p.name"></span>
                                                        </a>
                                                        <div class="flex items-center gap-2 mt-0.5 text-[10px]">
                                                            <span class="font-bold text-slate-900" x-text="'$' + p.current_price.toFixed(2)"></span>
                                                            <template x-if="p.is_on_sale">
                                                                <span class="text-slate-400 line-through" x-text="'$' + p.price.toFixed(2)"></span>
                                                            </template>
                                                            <span class="text-amber-500 font-semibold" x-text="'★ ' + p.rating_avg.toFixed(1)"></span>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center gap-1">
                                                        <!-- Add to Comparison Button -->
                                                        <button type="button" 
                                                                @click="toggleCompare(p)"
                                                                title="Compare"
                                                                class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:text-indigo-600 hover:bg-slate-50 transition">
                                                            ⚖️
                                                        </button>
                                                        <!-- Quick Add to Cart Button -->
                                                        <button type="button" 
                                                                @click="quickAddToCart(p)"
                                                                title="Add to cart"
                                                                class="py-1 px-2.5 rounded-lg bg-[#1B0E24] hover:bg-indigo-600 text-white font-bold text-[10px] transition active:scale-95 shadow-xs flex items-center gap-1">
                                                            <span>+ Cart</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- EMBEDDED ORDER CARD (IF ANY) -->
                                <template x-if="msg.order">
                                    <div class="bg-indigo-50/70 border border-indigo-200 rounded-xl p-3 space-y-2">
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="font-bold text-indigo-950 font-mono" x-text="msg.order.order_number"></span>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-indigo-600 text-white" x-text="msg.order.status"></span>
                                        </div>
                                        <div class="text-[11px] text-slate-600 space-y-0.5">
                                            <div>Carrier: <strong class="text-slate-900" x-text="msg.order.carrier"></strong></div>
                                            <div>Total Settled: <strong class="text-slate-900" x-text="msg.order.total"></strong></div>
                                        </div>
                                        <a :href="msg.order.track_url" class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-700 hover:underline pt-1">
                                            <span>Open Live Order Tracking &rarr;</span>
                                        </a>
                                    </div>
                                </template>

                                <!-- ACTION BUTTONS (IF ANY) -->
                                <template x-if="msg.actions && msg.actions.length > 0">
                                    <div class="flex flex-wrap gap-1.5 pt-1">
                                        <template x-for="(act, aIdx) in msg.actions" :key="aIdx">
                                            <template x-if="act.action === 'navigate'">
                                                <a :href="act.url" class="py-1 px-3 rounded-full bg-slate-900 hover:bg-indigo-600 text-white text-[10px] font-bold transition flex items-center gap-1 shadow-xs">
                                                    <span x-text="act.icon"></span>
                                                    <span x-text="act.label"></span>
                                                </a>
                                            </template>
                                            <template x-if="act.action === 'tel'">
                                                <a :href="act.url" class="py-1 px-3 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold transition flex items-center gap-1 shadow-xs">
                                                    <span x-text="act.icon"></span>
                                                    <span x-text="act.label"></span>
                                                </a>
                                            </template>
                                            <template x-if="act.action === 'support_modal'">
                                                <button type="button" @click="openHandoffModal()" class="py-1 px-3 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-bold transition flex items-center gap-1 shadow-xs">
                                                    <span x-text="act.icon"></span>
                                                    <span x-text="act.label"></span>
                                                </button>
                                            </template>
                                        </template>
                                    </div>
                                </template>

                            </div>
                        </div>
                    </template>

                </div>
            </template>

            <!-- TYPING / PROCESSING INDICATOR -->
            <div x-show="isLoading" class="flex items-center gap-2 text-slate-400 text-xs py-1" style="display: none;">
                <div class="w-6 h-6 rounded-lg bg-[#1B0E24] text-amber-400 font-bold flex items-center justify-center text-[10px] flex-shrink-0 animate-pulse">
                    ✦
                </div>
                <div class="bg-white border border-slate-200/80 rounded-2xl rounded-tl-sm px-3.5 py-2 text-slate-500 text-[11px] flex items-center gap-1.5 shadow-xs">
                    <span>Concierge is searching catalog</span>
                    <span class="inline-flex gap-1">
                        <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-bounce"></span>
                        <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                        <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                    </span>
                </div>
            </div>

        </div>

        <!-- Chat Input Footer -->
        <div class="p-3 bg-white border-t border-slate-100">
            <form @submit.prevent="submitMessage()" class="flex items-center gap-2">
                <input type="text" 
                       x-model="inputQuery"
                       :disabled="isLoading"
                       placeholder="Ask about products, compare specs, or track orders..." 
                       class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-600 focus:bg-white transition-all disabled:opacity-60 placeholder:text-slate-400">

                <button type="submit" 
                        :disabled="!inputQuery.trim() || isLoading"
                        class="w-9 h-9 rounded-xl bg-[#1B0E24] hover:bg-indigo-600 disabled:opacity-40 disabled:hover:bg-[#1B0E24] text-white flex items-center justify-center transition active:scale-95 shadow-sm flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <div class="flex items-center justify-between mt-2 px-1 text-[10px] text-slate-400">
                <span>✦ Powered by UNCART Intelligence</span>
                <button type="button" @click="openHandoffModal()" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                    Speak with Employee
                </button>
            </div>
        </div>

        <!-- 3. Human Handoff Modal (Sub-drawer inside widget) -->
        <div x-show="showHandoffModal" 
             x-transition 
             class="absolute inset-0 bg-white/95 backdrop-blur-md z-20 p-5 flex flex-col justify-between"
             style="display: none;">
            
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Direct Customer Support</h4>
                        <p class="text-[11px] text-slate-500">Connect with an UNCART human specialist.</p>
                    </div>
                    <button type="button" @click="showHandoffModal = false" class="text-slate-400 hover:text-slate-600 text-base font-bold">
                        &times;
                    </button>
                </div>

                <!-- Callout strip -->
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900 space-y-1">
                    <div>📞 <strong>Toll-Free Phone:</strong> <a href="tel:+18008920199" class="underline font-mono">+1 (800) 892-0199</a></div>
                    <div>✉️ <strong>Direct Support:</strong> <a href="mailto:support@uncart.com" class="underline">support@uncart.com</a></div>
                </div>

                <!-- Create Inquiry Form -->
                <div class="space-y-3">
                    <label class="block text-[11px] font-bold text-slate-700">Submit an Instant Support Ticket:</label>
                    <textarea x-model="ticketMessage" 
                              rows="4" 
                              placeholder="Describe your question or issue in detail..." 
                              class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-900 focus:outline-none focus:border-indigo-600"></textarea>
                </div>
            </div>

            <div class="pt-3 flex items-center justify-between border-t border-slate-100">
                <button type="button" @click="showHandoffModal = false" class="text-xs text-slate-500 hover:text-slate-700 font-semibold">
                    Cancel
                </button>
                <button type="button" 
                        @click="submitSupportTicket()"
                        :disabled="!ticketMessage.trim() || submittingTicket"
                        class="py-2 px-5 rounded-full bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white font-bold text-xs uppercase tracking-wider transition shadow">
                    <span x-text="submittingTicket ? 'Submitting...' : 'Send to Team'"></span>
                </button>
            </div>
        </div>

    </div>

</div>

<script>
function aiAssistantHandler() {
    return {
        isOpen: false,
        showGreetingBubble: true,
        inputQuery: '',
        isLoading: false,
        showHandoffModal: false,
        ticketMessage: '',
        submittingTicket: false,
        messages: [],
        quickPrompts: [
            { label: '🎧 Best headphones under $200', query: 'Show me best headphones under 200' },
            { label: '⌨️ Mechanical keyboards', query: 'Find top rated mechanical keyboards' },
            { label: '⚖️ Compare desk accessories', query: 'Compare walnut desk shelf vs monitor stand' },
            { label: '📦 Track my recent order', query: 'Track my recent order' },
            { label: '👤 Talk to human support', query: 'I want to speak with customer support' }
        ],

        init() {
            // Auto dismiss greeting bubble after 10 seconds if not clicked
            setTimeout(() => {
                this.showGreetingBubble = false;
            }, 10000);
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            this.showGreetingBubble = false;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            }
        },

        async submitMessage() {
            const query = this.inputQuery.trim();
            if (!query || this.isLoading) return;

            this.inputQuery = '';
            this.messages.push({ role: 'user', text: query });
            this.isLoading = true;
            this.scrollToBottom();

            try {
                const res = await fetch('{{ route('ai.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message: query,
                        history: this.messages
                    })
                });

                const data = await res.json();
                this.isLoading = false;

                if (data.success) {
                    this.messages.push({
                        role: 'assistant',
                        text: data.reply,
                        products: data.products || [],
                        comparison: data.comparison || null,
                        order: data.order || null,
                        actions: data.actions || []
                    });
                } else {
                    this.messages.push({
                        role: 'assistant',
                        text: "I encountered a brief hiccup retrieving that. Please try asking again!"
                    });
                }
            } catch (err) {
                this.isLoading = false;
                this.messages.push({
                    role: 'assistant',
                    text: "Sorry, I could not complete that request right now. You can also reach our team directly at +1 (800) 892-0199."
                });
            }

            this.scrollToBottom();
        },

        submitPrompt(query) {
            this.inputQuery = query;
            this.submitMessage();
        },

        async clearHistory() {
            try {
                await fetch('{{ route('ai.clear') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
            } catch (e) {}

            this.messages = [];
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Conversation history reset', type: 'info' } }));
        },

        async quickAddToCart(product) {
            try {
                const res = await fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: product.id, quantity: 1 })
                });
                const data = await res.json();
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message || (product.name + ' added to cart!'), type: 'success' } }));
                if (data.cart_count !== undefined) {
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                }
                window.dispatchEvent(new CustomEvent('open-cart-drawer'));
            } catch (err) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Could not add to cart.', type: 'error' } }));
            }
        },

        toggleCompare(product) {
            window.dispatchEvent(new CustomEvent('toggle-compare', { detail: product }));
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: product.name + ' added to compare tray!', type: 'info' } }));
        },

        openHandoffModal() {
            this.showHandoffModal = true;
            this.ticketMessage = 'Customer requested human assistance regarding: ' + (this.messages.length > 0 ? this.messages[this.messages.length - 1].text : 'General inquiry');
        },

        async submitSupportTicket() {
            if (!this.ticketMessage.trim() || this.submittingTicket) return;

            this.submittingTicket = true;
            try {
                const res = await fetch('{{ route('ai.handoff') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        subject: 'AI Concierge Support Request',
                        message: this.ticketMessage
                    })
                });

                const data = await res.json();
                this.submittingTicket = false;
                this.showHandoffModal = false;

                if (data.success) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                    this.messages.push({
                        role: 'assistant',
                        text: "✓ **Support Ticket Registered!**\n\nYour inquiry has been dispatched to our care specialists. We will follow up shortly by email."
                    });
                    this.scrollToBottom();
                }
            } catch (e) {
                this.submittingTicket = false;
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Failed to submit ticket. Please call support directly.', type: 'error' } }));
            }
        },

        renderMarkdown(text) {
            if (!text) return '';
            let html = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`(.*?)`/g, '<code class="bg-slate-100 px-1 py-0.5 rounded text-indigo-700 font-mono text-[10px]">$1</code>')
                .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" class="text-indigo-600 underline font-semibold hover:text-indigo-800">$1</a>')
                .replace(/\n\n/g, '<br><br>')
                .replace(/\n/g, '<br>');
            return html;
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.chatScroll;
                if (el) {
                    el.scrollTop = el.scrollHeight;
                }
            });
        }
    };
}
</script>
