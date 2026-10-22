@extends('layouts.app')

@section('title', 'About Our Mission - ShopiCart')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
    <div class="text-center space-y-4 max-w-2xl mx-auto">
        <span class="text-xs font-bold tracking-widest text-indigo-600 dark:text-indigo-400 uppercase font-mono">Our Manifesto</span>
        <h1 class="text-4xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
            Elevating Everyday Craft & Modern Commerce
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
            ShopiCart was conceptualized and engineered by <strong>Chayan Jana</strong> as a premier high-fidelity online shopping ecosystem that bridges artisanal craft makers with discerning buyers worldwide.
        </p>
    </div>

    <div class="aspect-video w-full rounded-3xl overflow-hidden shadow-2xl">
        <img src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=1600&auto=format&fit=crop&q=80" 
             alt="Design Studio" class="w-full h-full object-cover">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-4">
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-3 shadow-sm">
            <span class="text-2xl">📐</span>
            <h3 class="font-black text-base text-slate-900 dark:text-white">Design Integrity</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                We believe software and physical goods should share the same high craft floor: deliberate typography, tactile micro-interactions, and zero AI-slop predictability.
            </p>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-3 shadow-sm">
            <span class="text-2xl">🛡️</span>
            <h3 class="font-black text-base text-slate-900 dark:text-white">Vetted Merchant KYC</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Every seller undergoes identity verification, bank account validation, and quality reviews before their portfolio is approved for public commerce.
            </p>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-3 shadow-sm">
            <span class="text-2xl">⚡</span>
            <h3 class="font-black text-base text-slate-900 dark:text-white">Speed & Transparency</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Real-time telemetric tracking, transparent 30-day money-back guarantees, and responsive customer-merchant dialogue channels.
            </p>
        </div>
    </div>
</div>
@endsection
