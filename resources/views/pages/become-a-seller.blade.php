@extends('layouts.app')

@section('title', 'Become a Certified Merchant — Sell on UNCART')

@section('content')
<div class="space-y-16 pb-20 overflow-hidden" x-data="{
    step: 1,
    shopName: '',
    shopSlug: '',
    logoPreview: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=400&auto=format&fit=crop&q=80',
    handleLogoSelect(e) {
        const file = e.target.files[0];
        if (file) {
            this.logoPreview = URL.createObjectURL(file);
        }
    },
    updateSlug() {
        this.shopSlug = this.shopName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    }
}">

    <!-- 1. Hero Showcase Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-10" data-reveal>
        <div class="relative bg-gradient-to-br from-indigo-950 via-slate-900 to-slate-950 rounded-[36px] overflow-hidden p-8 sm:p-14 lg:p-16 text-white shadow-xl flex flex-col lg:flex-row items-center justify-between gap-12">
            
            <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full bg-indigo-500/20 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 rounded-full bg-purple-500/20 blur-3xl pointer-events-none"></div>

            <!-- Left: Hero Headline & Value Props -->
            <div class="w-full lg:w-7/12 space-y-6 z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-400/20">
                    <span>🚀</span> Creator & Brand Onboarding
                </div>

                <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-[1.1]">
                    Scale Your Brand to <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-indigo-300 to-pink-400">
                        500,000+ Discerning Buyers
                    </span>
                </h1>

                <p class="text-xs sm:text-sm text-slate-300 max-w-xl leading-relaxed">
                    Join an exclusive collective of industrial designers, keyboard makers, acoustic engineers, and luxury lifestyle creators. We provide instant analytics, integrated global shipping labels, and industry-low commissions.
                </p>

                <!-- Value Strip -->
                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-white/10 max-w-lg">
                    <div>
                        <div class="text-xl sm:text-2xl font-black text-indigo-400">8.5%</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Low Commission</div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-black text-emerald-400">24h</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Express Payouts</div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-black text-amber-400">0 Setup</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">No Monthly Fees</div>
                    </div>
                </div>
            </div>

            <!-- Right: Fast-Track Registration Card Trigger -->
            <div class="w-full lg:w-5/12 z-10">
                <div class="bg-white/10 backdrop-blur-xl border border-white/15 rounded-3xl p-6 sm:p-8 space-y-4 shadow-2xl">
                    <h3 class="font-extrabold text-lg text-white">Ready to start selling?</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Complete our 3-minute application. Upon verification, your store profile goes live with direct inventory and sales controls.
                    </p>
                    <a href="#register-form" class="w-full py-3.5 px-6 rounded-full bg-white hover:bg-slate-100 text-slate-900 font-bold text-xs uppercase tracking-wider text-center block transition shadow-lg active:scale-95">
                        Start Application &darr;
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- 2. Interactive Registration Wizard Form -->
    <section id="register-form" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24" data-reveal>
        <div class="bg-white rounded-3xl sm:rounded-[36px] border border-slate-100 p-6 sm:p-12 shadow-sm space-y-8">
            
            <!-- Step Navigation Progress Bar -->
            <div class="space-y-4">
                <div class="flex items-center justify-between text-xs font-bold text-slate-500">
                    <button type="button" @click="step = 1" class="flex items-center gap-2" :class="step >= 1 ? 'text-indigo-600' : ''">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-black transition-all"
                              :class="step >= 1 ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400'">1</span>
                        <span>Shop Details</span>
                    </button>
                    <span class="text-slate-300">———</span>
                    <button type="button" @click="step = 2" class="flex items-center gap-2" :class="step >= 2 ? 'text-indigo-600' : ''">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-black transition-all"
                              :class="step >= 2 ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400'">2</span>
                        <span>Bank & Payouts</span>
                    </button>
                    <span class="text-slate-300">———</span>
                    <button type="button" @click="step = 3" class="flex items-center gap-2" :class="step >= 3 ? 'text-indigo-600' : ''">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-black transition-all"
                              :class="step >= 3 ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400'">3</span>
                        <span>Identity & Submit</span>
                    </button>
                </div>
                
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-indigo-600 h-1.5 transition-all duration-300"
                         :style="'width: ' + (step === 1 ? '33%' : (step === 2 ? '66%' : '100%'))"></div>
                </div>
            </div>

            <!-- Registration Form Element -->
            <form action="{{ route('vendor.become.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- STEP 1: Shop & Contact Info -->
                <div x-show="step === 1" class="space-y-5" x-transition>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">Step 1: Store & Contact Information</h3>
                        <p class="text-xs text-slate-500 mt-1">Tell us about your brand and what merchandise you'll be offering.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Store / Brand Name *</label>
                            <input type="text" 
                                   name="shop_name" 
                                   x-model="shopName" 
                                   @input="updateSlug()"
                                   required 
                                   placeholder="e.g. Apex Acoustic Labs" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                            <span class="text-[11px] text-slate-400 mt-1 block">
                                Store URL preview: <code class="text-indigo-600 font-mono" x-text="'uncart.com/shop/seller/' + (shopSlug || 'your-brand')"></code>
                            </span>
                        </div>

                        <!-- Device Shop Logo Picker -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <label class="block text-xs font-semibold text-slate-700">Shop Logo / Avatar</label>
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl overflow-hidden border border-slate-200 bg-white flex-shrink-0 shadow-sm">
                                    <img :src="logoPreview" alt="Shop Preview" class="w-full h-full object-cover">
                                </div>
                                <div class="space-y-1">
                                    <label class="cursor-pointer py-2 px-3.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-800 font-bold text-xs transition active:scale-95 inline-flex items-center gap-2 shadow-sm">
                                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        <span>Choose Logo from Device</span>
                                        <input type="file" name="shop_logo_file" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="hidden" @change="handleLogoSelect">
                                    </label>
                                    <p class="text-[10px] text-slate-400">PNG, JPG or WEBP up to 5MB (Square 1:1 recommended).</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Store Description & Specialty *</label>
                            <textarea name="shop_description" 
                                      rows="3" 
                                      required 
                                      placeholder="Describe your design engineering philosophy, key products, and quality standards..."
                                      class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Business Support Email *</label>
                                <input type="email" 
                                       name="business_email" 
                                       required 
                                       value="{{ Auth::user()->email ?? old('business_email') }}"
                                       placeholder="contact@yourbrand.com" 
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Business Phone Number *</label>
                                <input type="text" 
                                       name="business_phone" 
                                       required 
                                       value="{{ Auth::user()->phone ?? old('business_phone') }}"
                                       placeholder="+1 (555) 019-2831" 
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Physical Business / Warehouse Address *</label>
                            <input type="text" 
                                   name="address" 
                                   required 
                                   placeholder="Street, Suite, City, State, ZIP & Country" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="button" 
                                @click="if(shopName.trim()) step = 2; else alert('Please enter your store name')" 
                                class="py-3 px-8 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider transition active:scale-95 shadow">
                            Continue to Banking &rarr;
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Banking & Payouts -->
                <div x-show="step === 2" class="space-y-5" x-transition style="display: none;">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">Step 2: Banking & Settlement Setup</h3>
                        <p class="text-xs text-slate-500 mt-1">Enter the bank details where you wish to receive automated weekly earnings payouts.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Bank Name *</label>
                            <input type="text" 
                                   name="bank_name" 
                                   required 
                                   placeholder="e.g. JPMorgan Chase, Silicon Valley Bank, HDFC Bank" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Account Number *</label>
                                <input type="text" 
                                       name="account_number" 
                                       required 
                                       placeholder="Account / IBAN number" 
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Routing / IFSC / SWIFT Code *</label>
                                <input type="text" 
                                       name="ifsc_code" 
                                       required 
                                       placeholder="e.g. CHAS000108 or Routing #" 
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100 text-xs text-slate-600 flex items-start gap-3">
                            <span class="text-base text-indigo-600">ℹ️</span>
                            <div>
                                <strong class="font-bold text-slate-900">Standard Payout Terms</strong>
                                <p class="text-[11px] text-slate-500 mt-0.5">Earnings are calculated net of platform commission (8.50%) and automatically eligible for payout 7 days after verified order delivery.</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="step = 1" class="py-2.5 px-6 rounded-full border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold transition">
                            &larr; Back
                        </button>
                        <button type="button" @click="step = 3" class="py-3 px-8 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider transition active:scale-95 shadow">
                            Continue to Identity &rarr;
                        </button>
                    </div>
                </div>

                <!-- STEP 3: Identity & Submit -->
                <div x-show="step === 3" class="space-y-5" x-transition style="display: none;">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">Step 3: Identity & Account Security</h3>
                        <p class="text-xs text-slate-500 mt-1">Provide regulatory business proof for our automated KYC verification.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tax ID / EIN / Business Registration Number *</label>
                            <input type="text" 
                                   name="tax_id" 
                                   required 
                                   placeholder="e.g. US-EIN-94819231 or GSTIN" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Upload Business License / KYC Document (Optional)</label>
                            <input type="file" 
                                   name="kyc_file" 
                                   accept=".pdf,image/png,image/jpeg,image/webp" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs text-slate-900 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                            <span class="text-[10px] text-slate-400 mt-1 block">Accepted: PDF, PNG, JPG or WEBP up to 10MB. Speeds up KYC certification.</span>
                        </div>

                        @guest
                            <div class="pt-2 border-t border-slate-100 space-y-4">
                                <h4 class="font-bold text-xs text-slate-900 uppercase tracking-wider">Account Credentials</h4>
                                
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Your Full Name *</label>
                                    <input type="text" name="name" required placeholder="John Doe" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Account Login Email *</label>
                                    <input type="email" name="email" required placeholder="you@domain.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Account Password *</label>
                                        <input type="password" name="password" required minlength="8" placeholder="••••••••" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Confirm Password *</label>
                                        <input type="password" name="password_confirmation" required minlength="8" placeholder="••••••••" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                        @endguest

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-600 space-y-1">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input type="checkbox" required checked class="rounded text-indigo-600 focus:ring-indigo-500 mt-0.5">
                                <span>I agree to the <a href="{{ route('static.terms') }}" target="_blank" class="text-indigo-600 font-bold hover:underline">Merchant Terms & Conditions</a> and certify that all provided details and commercial licenses are authentic.</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="step = 2" class="py-2.5 px-6 rounded-full border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold transition">
                            &larr; Back
                        </button>
                        <button type="submit" class="py-3.5 px-10 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs uppercase tracking-wider transition active:scale-95 shadow-md shadow-indigo-500/20">
                            Submit Merchant Application &rarr;
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </section>

</div>
@endsection
