<div x-data="{ open: true }" class="fixed bottom-4 left-4 z-[9990] font-sans">
    <div x-show="open" 
         x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-200"
         x-transition:enter-start="opacity-0 translate-y-3 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-slate-900/90 text-white backdrop-blur-xl border border-slate-700/60 rounded-2xl shadow-2xl p-2.5 flex items-center gap-2">
        
        <div class="flex items-center gap-2 px-2 border-r border-slate-700/70 text-xs font-semibold tracking-wider uppercase text-slate-400">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Demo Switcher
        </div>

        <div class="flex items-center gap-1.5 text-xs">
            <!-- Customer -->
            <a href="{{ route('quick.login', 'customer') }}" 
               class="px-2.5 py-1.5 rounded-xl font-medium transition-all duration-150 flex items-center gap-1.5 active:scale-95 {{ Auth::check() && Auth::user()->isCustomer() ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-750 hover:text-white' }}">
                <span>👤</span>
                <span class="hidden sm:inline">Customer</span>
            </a>

            <!-- Vendor -->
            <a href="{{ route('quick.login', 'vendor') }}" 
               class="px-2.5 py-1.5 rounded-xl font-medium transition-all duration-150 flex items-center gap-1.5 active:scale-95 {{ Auth::check() && Auth::user()->isVendor() ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-750 hover:text-white' }}">
                <span>🏪</span>
                <span class="hidden sm:inline">Seller Portal</span>
            </a>

            <!-- Admin -->
            <a href="{{ route('quick.login', 'admin') }}" 
               class="px-2.5 py-1.5 rounded-xl font-medium transition-all duration-150 flex items-center gap-1.5 active:scale-95 {{ Auth::check() && Auth::user()->isAdmin() ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-750 hover:text-white' }}">
                <span>🛡️</span>
                <span class="hidden sm:inline">Admin</span>
            </a>

            <!-- Guest -->
            <a href="{{ route('quick.login', 'guest') }}" 
               class="px-2 py-1.5 rounded-xl font-medium transition-all duration-150 flex items-center gap-1 active:scale-95 {{ !Auth::check() ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-750 hover:text-white' }}">
                <span>👁️</span>
                <span class="hidden sm:inline">Guest</span>
            </a>
        </div>

        <button @click="open = false" title="Minimize switcher" class="ml-1 text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Collapsed pill button -->
    <button x-show="!open" @click="open = true" 
            x-transition
            class="bg-slate-900/90 text-white backdrop-blur-md border border-slate-700/80 rounded-full px-3 py-1.5 text-xs font-medium shadow-xl flex items-center gap-1.5 hover:bg-slate-800 transition-all active:scale-95">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <span>Quick Switcher</span>
    </button>
</div>
