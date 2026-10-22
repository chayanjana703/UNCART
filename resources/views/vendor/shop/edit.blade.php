@extends('layouts.vendor')

@section('page-title', 'Shop Profile & Financial Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-8" x-data="{ payoutModal: false }">

    <!-- KYC Verification Alert Box -->
    <div class="p-6 rounded-3xl bg-slate-950 border border-slate-800 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-xl {{ $vendor->kyc_status === 'verified' ? 'bg-emerald-950 text-emerald-400' : 'bg-amber-950 text-amber-400' }}">
                {{ $vendor->kyc_status === 'verified' ? '✓' : '⚠️' }}
            </div>
            <div>
                <h4 class="font-bold text-white text-sm">KYC Identity Status: <span class="capitalize font-mono {{ $vendor->kyc_status === 'verified' ? 'text-emerald-400' : 'text-amber-400' }}">{{ $vendor->kyc_status }}</span></h4>
                <p class="text-slate-500 mt-0.5">Commercial platform seller certification and tax compliance</p>
            </div>
        </div>

        @if($vendor->kyc_status !== 'verified')
            <form action="{{ route('vendor.shop.kyc') }}" method="POST">
                @csrf
                <button type="submit" class="py-2.5 px-4 rounded-xl bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider hover:bg-indigo-700 shadow-md">
                    Submit KYC Documents
                </button>
            </form>
        @else
            <span class="px-3 py-1 rounded-full bg-emerald-950 text-emerald-400 font-bold uppercase tracking-wider font-mono text-[10px] border border-emerald-800/60">
                Verified Seller Badge Active
            </span>
        @endif
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
    <form action="{{ route('vendor.shop.update') }}" method="POST" class="space-y-6 text-xs">
        @csrf

        <!-- Shop Identity -->
        <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-4">
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

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-300 mb-1">Shop Logo URL</label>
                    <input type="url" name="shop_logo" value="{{ old('shop_logo', $vendor->shop_logo) }}"
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-300 mb-1">Shop Banner URL</label>
                    <input type="url" name="shop_banner" value="{{ old('shop_banner', $vendor->shop_banner) }}"
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
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

</div>
@endsection
