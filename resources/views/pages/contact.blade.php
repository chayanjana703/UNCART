@extends('layouts.app')

@section('title', 'Contact Support — UNCART')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">

    <!-- Header Section -->
    <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16 space-y-4" data-reveal>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-[11px] font-bold uppercase tracking-wider">
            Customer Support & Concierge
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight">
            We're Here to Help
        </h1>
        <p class="text-sm text-slate-600 leading-relaxed">
            Have questions regarding an order, seller onboarding, or product specifications? Our dedicated support team responds within 2 business hours.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Support Channels & Live Status (5 cols) -->
        <div class="lg:col-span-5 space-y-6" data-reveal>
            
            <!-- Live Operations Status Card -->
            <div class="p-6 rounded-3xl bg-white border border-slate-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">System Telemetry</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[10px] font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                        99.98% Operational
                    </span>
                </div>
                <p class="text-xs text-slate-600">
                    All payment processing clusters, logistics tracking feeds, and multi-vendor order routing channels are operational at optimal latency.
                </p>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Average SLA Turnaround</span>
                    <span class="text-slate-900 font-bold">&lt; 114 minutes</span>
                </div>
            </div>

            <!-- Direct Channel Cards -->
            <div class="space-y-3">
                <!-- Location -->
                <div class="p-5 rounded-2xl bg-white border border-slate-100 hover:border-indigo-100 transition shadow-sm flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg flex-shrink-0">
                        📍
                    </div>
                    <div class="text-xs space-y-1">
                        <strong class="block text-slate-900 font-bold">Global Headquarters</strong>
                        <p class="text-slate-500">100 Silicon Way, Suite 500<br>San Francisco, CA 94107, USA</p>
                    </div>
                </div>

                <!-- Direct Email -->
                <div class="p-5 rounded-2xl bg-white border border-slate-100 hover:border-emerald-100 transition shadow-sm flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg flex-shrink-0">
                        ✉️
                    </div>
                    <div class="text-xs space-y-1">
                        <strong class="block text-slate-900 font-bold">Customer Support Email</strong>
                        <p class="text-slate-700 font-semibold">support@uncart.com</p>
                        <p class="text-[11px] text-slate-400">Merchant Help: sellers@uncart.com</p>
                    </div>
                </div>

                <!-- Phone / Hot Line -->
                <div class="p-5 rounded-2xl bg-white border border-slate-100 hover:border-amber-100 transition shadow-sm flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg flex-shrink-0">
                        📞
                    </div>
                    <div class="text-xs space-y-1">
                        <strong class="block text-slate-900 font-bold">Toll-Free Phone Line</strong>
                        <p class="text-slate-800 font-bold">+1 (800) 892-0199</p>
                        <p class="text-[11px] text-slate-400">Monday &ndash; Friday &bull; 08:00 &ndash; 18:00 PST</p>
                    </div>
                </div>
            </div>

            <!-- Project & Student Signature Card -->
            <div class="p-4 rounded-2xl bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-100 text-xs">
                <div class="text-[10px] uppercase tracking-wider text-indigo-700 font-bold mb-1">Academic Engineering Project</div>
                <div class="text-slate-900 font-bold">Chayan Jana</div>
                <div class="text-slate-500 text-[11px]">Roll No: 34042724001 &bull; Full-Stack Modern E-Commerce</div>
            </div>

        </div>

        <!-- Right Column: Interactive Contact Form (7 cols) -->
        <div class="lg:col-span-7" data-reveal="scale">
            <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-100 shadow-sm space-y-6"
                 x-data="{ topic: 'Order Support', messageLen: 0 }">
                
                <div>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Send a Message</h2>
                    <p class="text-xs text-slate-500 mt-1">Select an inquiry category and dispatch your request directly to our concierge team.</p>
                </div>

                <!-- Topic Selector Pills -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Category Focus</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach(['Order Support', 'Seller Inquiries', 'API Questions', 'General Help'] as $cat)
                            <button type="button" 
                                    @click="topic = '{{ $cat }}'"
                                    :class="topic === '{{ $cat }}' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100'"
                                    class="py-2 px-2.5 rounded-full text-[11px] font-semibold text-center transition active:scale-95">
                                {{ $cat }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <form action="{{ route('static.contact.submit') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="inquiry_type" :value="topic">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Your Full Name</label>
                            <input type="text" 
                                   name="name" 
                                   required 
                                   placeholder="e.g. Alex Vance"
                                   class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email Address</label>
                            <input type="email" 
                                   name="email" 
                                   required 
                                   placeholder="e.g. alex@example.com"
                                   class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Subject</label>
                        <input type="text" 
                               name="subject" 
                               required 
                               :value="topic + ' — New Ticket'"
                               placeholder="e.g. Inquiry regarding order delivery"
                               class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-700">Message</label>
                            <span class="text-[10px] text-slate-400" x-text="messageLen + ' chars'">0 chars</span>
                        </div>
                        <textarea name="message" 
                                  rows="5" 
                                  required 
                                  @input="messageLen = $event.target.value.length"
                                  placeholder="Please provide your order number or question details..."
                                  class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all resize-y"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-3.5 px-6 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider shadow-md transition-all active:scale-[0.98] flex items-center justify-center gap-2 group">
                            <span>Send Message</span>
                            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </button>
                    </div>

                    <p class="text-[11px] text-slate-400 text-center">
                        Protected by 256-bit TLS encryption &bull; Zero spam guarantee
                    </p>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection
