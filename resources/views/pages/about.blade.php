@extends('layouts.app')

@section('title', 'About Our Platform — UNCART')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
    <div class="text-center space-y-4 max-w-2xl mx-auto" data-reveal>
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Platform Philosophy</span>
        <h1 class="text-4xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight">
            Elevating Modern Lifestyle & Commerce
        </h1>
        <p class="text-sm text-slate-600 leading-relaxed">
            UNCART is conceptualized and engineered by <strong class="text-slate-900">Chayan Jana</strong> (Roll No: <span class="font-bold text-[#F05A28]">34042724001</span>) as a high-fidelity multi-vendor shopping platform uniting verified merchants and quality-minded customers worldwide.
        </p>
    </div>

    <div class="aspect-video w-full rounded-3xl overflow-hidden shadow-sm border border-slate-100" data-reveal="scale">
        <img src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=1600&auto=format&fit=crop&q=80" 
             alt="Design Studio" class="w-full h-full object-cover">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
        <div class="p-6 rounded-3xl bg-white border border-slate-100 space-y-3 shadow-sm" data-reveal="scale">
            <span class="text-2xl">📐</span>
            <h3 class="font-bold text-base text-slate-900">Design Precision</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Clean, readable layouts, responsive mobile app dock, tactile feedback, and seamless page transitions make shopping fast and effortless.
            </p>
        </div>

        <div class="p-6 rounded-3xl bg-white border border-slate-100 space-y-3 shadow-sm" data-reveal="scale">
            <span class="text-2xl">🛡️</span>
            <h3 class="font-bold text-base text-slate-900">Verified Merchants</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Every merchant undergoes rigorous identity verification, quality compliance checks, and authenticity guarantees before inventory is listed.
            </p>
        </div>

        <div class="p-6 rounded-3xl bg-white border border-slate-100 space-y-3 shadow-sm" data-reveal="scale">
            <span class="text-2xl">⚡</span>
            <h3 class="font-bold text-base text-slate-900">Speed & Transparency</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Real-time package milestone tracking, transparent 7-day hassle-free returns, and direct customer-to-merchant inquiry channels.
            </p>
        </div>
    </div>
</div>
@endsection
