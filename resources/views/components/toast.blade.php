<div x-data="{
    toasts: [],
    add(message, type = 'success') {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, message, type });
        setTimeout(() => this.remove(id), 4500);
    },
    remove(id) {
        this.toasts = this.toasts.filter(t => t.id !== id);
    }
}" 
@toast.window="add($event.detail.message, $event.detail.type)"
x-init="
    @if(session('success')) add('{{ addslashes(session('success')) }}', 'success'); @endif
    @if(session('error')) add('{{ addslashes(session('error')) }}', 'error'); @endif
    @if(session('info')) add('{{ addslashes(session('info')) }}', 'info'); @endif
    @if(session('warning')) add('{{ addslashes(session('warning')) }}', 'warning'); @endif
"
class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-2 pointer-events-none max-w-sm w-full">
    <template x-for="t in toasts" :key="t.id">
        <div class="pointer-events-auto transform transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] flex items-start gap-3 p-4 rounded-xl shadow-2xl border backdrop-blur-md"
             :class="{
                 'bg-slate-900/95 text-white border-slate-800 shadow-slate-900/20': t.type === 'success',
                 'bg-rose-950/95 text-rose-50 border-rose-800 shadow-rose-950/30': t.type === 'error',
                 'bg-slate-900/95 text-sky-100 border-sky-800 shadow-sky-950/20': t.type === 'info',
                 'bg-amber-950/95 text-amber-50 border-amber-800 shadow-amber-950/30': t.type === 'warning'
             }">
            <div class="flex-shrink-0 mt-0.5">
                <template x-if="t.type === 'success'">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </template>
                <template x-if="t.type === 'error'">
                    <svg class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </template>
                <template x-if="t.type === 'info'">
                    <svg class="w-5 h-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="t.type === 'warning'">
                    <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </template>
            </div>
            <div class="flex-1 text-sm font-medium leading-snug" x-text="t.message"></div>
            <button @click="remove(t.id)" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>
