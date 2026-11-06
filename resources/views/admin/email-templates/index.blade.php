@extends('layouts.admin')

@section('title', 'Transactional Email Templates')

@section('content')
<div class="space-y-6" x-data="{
    activeTemplate: 'order_confirmation',
    previewSubject: '',
    previewBody: '',
    templates: {{ json_encode($templates) }},
    init() {
        this.selectTemplate('order_confirmation');
    },
    selectTemplate(id) {
        this.activeTemplate = id;
        this.previewSubject = this.templates[id].subject;
        this.previewBody = this.templates[id].body;
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                Transactional Email Templates
                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    5 System Templates
                </span>
            </h1>
            <p class="text-slate-400 text-xs mt-1">Configure automated transactional notifications, customize subject lines, HTML bodies, and dispatch test previews.</p>
        </div>
    </div>

    <!-- Template Selector Grid -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
        @foreach($templates as $key => $tpl)
            <button type="button" 
                    @click="selectTemplate('{{ $key }}')"
                    :class="activeTemplate === '{{ $key }}' ? 'border-amber-500 bg-amber-500/10 shadow-lg shadow-amber-500/5' : 'border-slate-800 bg-slate-900/80 hover:border-slate-700'"
                    class="text-left p-3.5 rounded-2xl border transition-all relative overflow-hidden group">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="w-2 h-2 rounded-full" :class="activeTemplate === '{{ $key }}' ? 'bg-amber-400 ring-4 ring-amber-400/20' : 'bg-slate-600'"></span>
                    <span class="text-[9px] font-mono uppercase tracking-wider text-slate-500 font-bold">HTML</span>
                </div>
                <div class="font-bold text-xs text-white group-hover:text-amber-400 transition-colors line-clamp-1">{{ $tpl['name'] }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5 line-clamp-1 font-mono">ID: {{ $key }}</div>
            </button>
        @endforeach
    </div>

    <!-- Editor & Live Preview Split Canvas -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Form Editor (7 cols) -->
        <div class="lg:col-span-7 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl">
            @foreach($templates as $key => $tpl)
                <div x-show="activeTemplate === '{{ $key }}'" style="display: none;" class="space-y-5">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-mono uppercase tracking-wider text-amber-400 font-bold">Configuring Template</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Active In System</span>
                        </div>
                        <h2 class="text-lg font-black text-white mt-1">{{ $tpl['name'] }}</h2>
                        <p class="text-xs text-slate-400 mt-1">{{ $tpl['description'] }}</p>
                    </div>

                    <!-- Merge Tags Help Pill Box -->
                    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-3.5">
                        <div class="text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold mb-2">Available Merge Placeholders</div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($tpl['tags'] as $tag)
                                <code class="px-2 py-1 rounded-lg bg-slate-900 border border-slate-800 text-[11px] font-mono text-amber-300 font-semibold cursor-pointer hover:border-amber-500/50 hover:bg-slate-800 transition"
                                      title="Click to copy to clipboard"
                                      @click="navigator.clipboard.writeText('{{ $tag }}'); alert('Copied tag: {{ $tag }}')">
                                    {{ $tag }}
                                </code>
                            @endforeach
                        </div>
                    </div>

                    <!-- Template Update Form -->
                    <form method="POST" action="{{ route('admin.email-templates.update', $key) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Subject Line</label>
                            <input type="text" name="subject" value="{{ $tpl['subject'] }}" 
                                   x-model="templates['{{ $key }}'].subject"
                                   @input="previewSubject = $event.target.value"
                                   required
                                   class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-3.5 py-2.5 text-xs text-white font-medium transition">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                                <span>HTML Body Template</span>
                                <span class="text-[10px] text-slate-500 font-mono">Standard HTML supported</span>
                            </label>
                            <textarea name="body" rows="9" 
                                      x-model="templates['{{ $key }}'].body"
                                      @input="previewBody = $event.target.value"
                                      required
                                      class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl p-3.5 text-xs text-slate-200 font-mono transition leading-relaxed">{{ $tpl['body'] }}</textarea>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-[11px] text-slate-500">Changes apply to future transactional emails</span>
                            <button type="submit" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold transition shadow-lg shadow-amber-500/20">
                                Save Template
                            </button>
                        </div>
                    </form>

                    <!-- Test Email Dispatcher -->
                    <div class="pt-4 border-t border-slate-800">
                        <form method="POST" action="{{ route('admin.email-templates.send-test', $key) }}" class="flex items-center gap-2">
                            @csrf
                            <input type="email" name="test_email" value="admin@shopicart.com" placeholder="Recipient test email address..." 
                                   class="flex-1 bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-300 font-mono">
                            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Dispatch Test
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Right: Live Email Client Sandbox Preview (5 cols) -->
        <div class="lg:col-span-5 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl sticky top-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-mono font-bold text-slate-400 ml-2">Inbox Preview Simulation</span>
                </div>
                <span class="text-[10px] font-mono text-slate-500">Rendered HTML</span>
            </div>

            <!-- Faux Email Client Body -->
            <div class="bg-white text-slate-900 rounded-2xl p-5 shadow-inner min-h-[380px] font-sans text-xs">
                <!-- Email Header -->
                <div class="border-b border-slate-100 pb-3 mb-4">
                    <div class="text-[11px] text-slate-400 font-mono">From: <strong class="text-slate-700 font-sans">NovaShop Store &lt;noreply@shopicart.com&gt;</strong></div>
                    <div class="text-sm font-bold text-slate-900 mt-1" x-text="previewSubject || 'Subject goes here...'"></div>
                </div>

                <!-- NovaShop Branded Header in Email -->
                <div class="flex items-center justify-between py-2 mb-4 border-b border-slate-100">
                    <div class="font-black text-sm tracking-tight text-slate-950 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-lg bg-slate-950 text-white flex items-center justify-center text-[10px] font-black">N</span>
                        <span>NOVASHOP</span>
                    </div>
                    <span class="text-[10px] text-slate-400 font-mono">Official Notice</span>
                </div>

                <!-- Email HTML Content Container -->
                <div class="prose prose-xs text-slate-700 max-w-none leading-relaxed" x-html="previewBody"></div>

                <!-- Email Footer -->
                <div class="mt-8 pt-4 border-t border-slate-100 text-[10px] text-slate-400 leading-tight">
                    <p>© {{ date('Y') }} NovaShop Platforms Inc. All rights reserved.</p>
                    <p class="mt-0.5">Crafted with precision for student presentation (Roll: 34042724001).</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
