@extends('layouts.vendor')

@section('page-title', 'Shop Profile & Financial Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-8" x-data="{ 
    payoutModal: false,
    kycModal: false,
    logoPreview: '{{ $vendor->shop_logo_url }}',
    bannerPreview: '{{ $vendor->shop_banner_url }}',
    showUrlInput: false,
    handleLogoSelect(event) {
        const file = event.target.files[0];
        if (file) {
            this.logoPreview = URL.createObjectURL(file);
        }
    },
    handleBannerSelect(event) {
        const file = event.target.files[0];
        if (file) {
            this.bannerPreview = URL.createObjectURL(file);
        }
    }
}">

    <!-- KYC Verification Alert Box -->
    <div class="p-6 rounded-3xl bg-slate-950 border border-slate-800 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-xl {{ $vendor->kyc_status === 'verified' ? 'bg-emerald-950 text-emerald-400' : 'bg-amber-950 text-amber-400' }}">
                {{ $vendor->kyc_status === 'verified' ? '✓' : '⚠️' }}
            </div>
            <div>
                <h4 class="font-bold text-white text-sm">KYC Identity Status: <span class="capitalize font-mono {{ $vendor->kyc_status === 'verified' ? 'text-emerald-400' : 'text-amber-400' }}">{{ $vendor->kyc_status }}</span></h4>
                <p class="text-slate-500 mt-0.5">Commercial platform seller certification and tax compliance</p>
                @if($vendor->kyc_document)
                    <p class="text-[11px] text-slate-400 mt-1 font-mono">Document Record: <span class="text-indigo-400">{{ basename($vendor->kyc_document) }}</span></p>
                @endif
            </div>
        </div>

        <div>
            @if($vendor->kyc_status !== 'verified')
                <button type="button" @click="kycModal = true" class="py-2.5 px-4 rounded-xl bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider hover:bg-indigo-700 shadow-md transition active:scale-95">
                    Upload KYC Verification Documents
                </button>
            @else
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full bg-emerald-950 text-emerald-400 font-bold uppercase tracking-wider font-mono text-[10px] border border-emerald-800/60">
                        Verified Seller Badge Active
                    </span>
                    <button type="button" @click="kycModal = true" class="text-xs text-slate-400 hover:text-white underline">
                        Update Documents
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Payouts & Revenue Card -->
    <div class="p-6 rounded-3xl bg-slate-950 border border-slate-800 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-6 text-xs">
        <div>
            <span class="text-slate-400 text-[10px] font-mono uppercase font-bold block">Disbursed Payouts to Date</span>
            <div class="text-2xl font-black text-white mt-1">${{ number_format($vendor->total_payouts, 2) }}</div>
            <p class="text-slate-500 mt-1">Platform commission rate: <strong class="text-white font-mono">{{ $vendor->commission_rate }}%</strong></p>
        </div>

        <button @click="payoutModal = true" class="py-2.5 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/30 transition-all active:scale-95">
            💸 Request Payout Transfer
        </button>
    </div>

    <!-- Shop Details & Policies Form -->
    <form action="{{ route('vendor.shop.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
        @csrf

        <!-- Shop Identity -->
        <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-5">
            <h3 class="font-bold text-white uppercase tracking-wider text-xs pb-3 border-b border-slate-850">
                Storefront Branding & Contact
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-300 mb-1">Shop Name</label>
                    <input type="text" name="shop_name" value="{{ old('shop_name', $vendor->shop_name) }}" required
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Support Contact Email</label>
                    <input type="email" name="email" value="{{ old('email', $vendor->email) }}"
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Support Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $vendor->phone) }}"
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Dispatch Studio Address</label>
                    <input type="text" name="address" value="{{ old('address', $vendor->address) }}"
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <!-- Device Logo Upload Card -->
                <div class="sm:col-span-2 p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block font-bold text-white text-xs mb-0.5">Shop Logo / Brand Avatar</label>
                            <p class="text-[11px] text-slate-400">Upload your storefront brand icon directly from your device (PNG, JPG, WEBP, or SVG up to 5MB).</p>
                        </div>
                        <button type="button" @click="showUrlInput = !showUrlInput" class="text-xs text-indigo-400 hover:text-indigo-300 font-medium">
                            <span x-text="showUrlInput ? 'Hide URL input' : 'Use image URL instead'"></span>
                        </button>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-4 pt-1">
                        <div class="relative w-20 h-20 rounded-2xl overflow-hidden border-2 border-slate-700 bg-slate-950 flex-shrink-0 shadow-inner">
                            <img :src="logoPreview" alt="{{ $vendor->shop_name }}" class="w-full h-full object-cover">
                        </div>

                        <div class="flex-1 space-y-2 text-center sm:text-left">
                            <div class="flex flex-wrap items-center gap-2 justify-center sm:justify-start">
                                <label class="cursor-pointer py-2 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition active:scale-95 inline-flex items-center gap-2 shadow-md shadow-indigo-600/20">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    <span>Upload Logo from Device</span>
                                    <input type="file" name="shop_logo_file" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="hidden" @change="handleLogoSelect">
                                </label>
                            </div>
                            <p class="text-[10px] text-slate-500">Square 1:1 ratio recommended. Displays on merchant storefront and product listings.</p>
                        </div>
                    </div>

                    <div x-show="showUrlInput" x-transition class="pt-2 border-t border-slate-800" style="display: none;">
                        <label class="block font-semibold text-slate-300 mb-1 text-[11px]">External Shop Logo Image URL (Optional Fallback)</label>
                        <input type="url" name="shop_logo" value="{{ old('shop_logo', $vendor->shop_logo) }}" placeholder="https://..." class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3.5 py-2 text-white text-xs">
                    </div>
                </div>

                <!-- Device Banner Upload Card -->
                <div class="sm:col-span-2 p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3">
                    <div>
                        <label class="block font-bold text-white text-xs mb-0.5">Storefront Hero Banner</label>
                        <p class="text-[11px] text-slate-400">Panoramic banner displayed atop your public seller storefront (PNG, JPG, WEBP up to 5MB).</p>
                    </div>

                    <div class="space-y-3">
                        <div class="h-28 w-full rounded-2xl overflow-hidden border border-slate-800 bg-slate-950">
                            <img :src="bannerPreview" alt="Store Banner" class="w-full h-full object-cover">
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="cursor-pointer py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs transition active:scale-95 inline-flex items-center gap-2">
                                <span>📁 Upload Banner from Device</span>
                                <input type="file" name="shop_banner_file" accept="image/*" class="hidden" @change="handleBannerSelect">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-300 mb-1">Shop Bio / Maker Statement</label>
                    <textarea name="shop_description" rows="3" class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">{{ old('shop_description', $vendor->shop_description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Bank Account Setup -->
        <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <h3 class="font-bold text-white uppercase tracking-wider text-xs pb-3 border-b border-slate-850">
                Bank Settlement Account
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-300 mb-1">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $vendor->bank_name) }}" placeholder="e.g. JPMorgan Chase"
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Account Number / IBAN</label>
                    <input type="text" name="account_number" value="{{ old('account_number', $vendor->account_number) }}" placeholder="•••• •••• 9102"
                           class="w-full font-mono rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">SWIFT / IFSC / Routing Code</label>
                    <input type="text" name="ifsc_code" value="{{ old('ifsc_code', $vendor->ifsc_code) }}" placeholder="CHAS000108"
                           class="w-full font-mono rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white uppercase">
                </div>
            </div>
        </div>

        <!-- Shop Policies -->
        <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <h3 class="font-bold text-white uppercase tracking-wider text-xs pb-3 border-b border-slate-850">
                Merchant Store Policies
            </h3>

            <div class="space-y-4">
                <div>
                    <label class="block font-bold text-slate-300 mb-1">Return & Refund Policy</label>
                    <textarea name="return_policy" rows="3" class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">{{ old('return_policy', $vendor->return_policy) }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Shipping & Packaging Guarantee Terms</label>
                    <textarea name="shipping_policy" rows="3" class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">{{ old('shipping_policy', $vendor->shipping_policy) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="py-3 px-8 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold uppercase tracking-wider shadow-xl shadow-indigo-600/30 transition-all active:scale-95">
                Save Store Profile
            </button>
        </div>
    </form>

    <!-- Request Payout Modal -->
    <div x-show="payoutModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div class="max-w-md w-full bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl space-y-4" @click.outside="payoutModal = false">
            <h4 class="font-bold text-sm text-white">Request Merchant Settlement Payout</h4>
            <p class="text-xs text-slate-400">Withdraw available merchant balance directly to your registered bank account.</p>

            <form action="{{ route('vendor.shop.payout') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-300 mb-1">Transfer Amount ($)</label>
                    <input type="number" step="0.01" min="50" name="amount" value="500.00" required
                           class="w-full rounded-xl border border-slate-800 bg-slate-950 p-2.5 text-white font-mono text-base font-bold">
                    <span class="text-[10px] text-slate-500 mt-1 block">Minimum settlement threshold: $50.00</span>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="payoutModal = false" class="px-4 py-2 text-slate-400">Cancel</button>
                    <button type="submit" class="py-2.5 px-5 rounded-xl bg-emerald-600 text-white font-bold uppercase tracking-wider hover:bg-emerald-700 shadow-md">
                        Confirm Transfer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- KYC Identity Verification Modal -->
    <div x-show="kycModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" style="display: none;">
        <div class="max-w-lg w-full bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl space-y-4" @click.outside="kycModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <h4 class="font-bold text-sm text-white">Merchant KYC & Identity Verification</h4>
                    <p class="text-xs text-slate-400">Upload business licenses, registration certificate, or tax ID.</p>
                </div>
                <button type="button" @click="kycModal = false" class="text-slate-400 hover:text-white text-base">✕</button>
            </div>

            <form action="{{ route('vendor.shop.kyc') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-300 mb-1">Business Registration / Tax ID (EIN / GSTIN / VAT)</label>
                    <input type="text" name="tax_id" value="{{ old('tax_id') }}" placeholder="e.g. US-TAX-9081249"
                           class="w-full rounded-xl border border-slate-800 bg-slate-950 p-2.5 text-white font-mono text-xs focus:border-indigo-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Identity & Registration Document File (from Device)</label>
                    <div class="border-2 border-dashed border-slate-700 hover:border-indigo-500 rounded-2xl p-5 text-center bg-slate-950/50 transition">
                        <svg class="w-8 h-8 text-indigo-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-xs text-white font-medium">Choose document from device</p>
                        <p class="text-[10px] text-slate-400 mt-1">Accepts PDF, PNG, JPG, or WEBP up to 10MB</p>
                        <input type="file" name="kyc_file" accept=".pdf,image/png,image/jpeg,image/webp" class="mt-3 block w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="kycModal = false" class="px-4 py-2 text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="py-2.5 px-5 rounded-xl bg-indigo-600 text-white font-bold uppercase tracking-wider hover:bg-indigo-500 shadow-md transition active:scale-95">
                        Submit & Verify
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
