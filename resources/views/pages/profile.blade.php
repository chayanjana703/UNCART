@extends('layouts.app')

@section('title', 'My Account & Profile - ShopiCart')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
    tab: 'profile',
    newAddrModal: false
}">

    <!-- Profile Header -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm mb-8 flex flex-col sm:flex-row items-center gap-6">
        <img src="{{ $user->avatar ?: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=200' }}" 
             alt="{{ $user->name }}" 
             class="w-20 h-20 rounded-2xl object-cover border-2 border-indigo-600 shadow-lg">

        <div class="flex-1 text-center sm:text-left space-y-1">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $user->name }}</h1>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">
                    {{ ucfirst($user->role) }} Member
                </span>
            </div>
            <p class="text-xs text-slate-500 font-mono">{{ $user->email }} &bull; {{ $user->phone ?: 'No phone registered' }}</p>
            <p class="text-xs text-slate-600 dark:text-slate-300 italic pt-1">{{ $user->bio ?: 'Design and technology enthusiast.' }}</p>
        </div>

        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('orders.index') }}" class="py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold transition-colors">
                📦 My Orders
            </a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 mb-8 overflow-x-auto pb-1 text-xs font-bold">
        <button @click="tab = 'profile'" 
                class="px-4 py-3 rounded-xl transition-all"
                :class="tab === 'profile' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'">
            Personal Info
        </button>
        <button @click="tab = 'addresses'" 
                class="px-4 py-3 rounded-xl transition-all"
                :class="tab === 'addresses' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'">
            Address Book ({{ $addresses->count() }})
        </button>
        <button @click="tab = 'security'" 
                class="px-4 py-3 rounded-xl transition-all"
                :class="tab === 'security' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'">
            Security & 2FA
        </button>
        <button @click="tab = 'preferences'" 
                class="px-4 py-3 rounded-xl transition-all"
                :class="tab === 'preferences' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'">
            Preferences
        </button>
    </div>

    <!-- Tab 1: Personal Info -->
    <div x-show="tab === 'profile'" class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6">
        <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-100 dark:border-slate-800">
            Edit Personal Profile
        </h3>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4 max-w-lg">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Phone Number</label>
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Avatar Image URL</label>
                <input type="url" name="avatar" value="{{ old('avatar', $user->avatar) }}" placeholder="https://..."
                       class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bio / Profile Note</label>
                <textarea name="bio" rows="3" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">{{ old('bio', $user->bio) }}</textarea>
            </div>

            <button type="submit" class="py-2.5 px-6 rounded-xl bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider hover:bg-indigo-700 shadow-md transition-all active:scale-95">
                Save Profile Changes
            </button>
        </form>
    </div>

    <!-- Tab 2: Addresses -->
    <div x-show="tab === 'addresses'" class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Saved Shipping & Billing Addresses</h3>
            <button @click="newAddrModal = true" class="py-2 px-4 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 shadow-md">
                + Add New Address
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($addresses as $addr)
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3 relative">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $addr->name }}</span>
                        @if($addr->is_default)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                Default {{ ucfirst($addr->type) }}
                            </span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-600 dark:text-slate-300 space-y-0.5">
                        <p>{{ $addr->address_line1 }}</p>
                        @if($addr->address_line2) <p>{{ $addr->address_line2 }}</p> @endif
                        <p>{{ $addr->city }}, {{ $addr->state }} {{ $addr->postal_code }}</p>
                        <p>{{ $addr->country }}</p>
                        <p class="font-mono text-slate-400 pt-1 text-[11px]">{{ $addr->phone }}</p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                        @if(!$addr->is_default)
                            <form action="{{ route('profile.address.default', $addr->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                                    Set as Default
                                </button>
                            </form>
                        @else
                            <span></span>
                        @endif

                        <form action="{{ route('profile.address.delete', $addr->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Tab 3: Security & 2FA -->
    <div x-show="tab === 'security'" class="space-y-6">
        
        <!-- 2FA Box -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Two-Factor Authentication (2FA)</h4>
                    <p class="text-xs text-slate-500">Add an extra layer of protection requiring a token code during sign in</p>
                </div>
                <form action="{{ route('profile.2fa') }}" method="POST">
                    @csrf
                    <button type="submit" class="py-2 px-4 rounded-xl text-xs font-bold transition-all shadow-sm {{ $user->two_factor_enabled ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        {{ $user->two_factor_enabled ? '✓ Enabled' : 'Enable 2FA' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Password Change -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 max-w-lg">
            <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-100 dark:border-slate-800">
                Change Account Password
            </h4>

            <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Current Password</label>
                    <input type="password" name="current_password" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">New Password</label>
                    <input type="password" name="password" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                </div>
                <button type="submit" class="py-2.5 px-6 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-950 font-bold text-xs uppercase tracking-wider hover:bg-slate-800 transition-colors">
                    Update Password
                </button>
            </form>
        </div>
    </div>

    <!-- Tab 4: Preferences -->
    <div x-show="tab === 'preferences'" class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 max-w-lg">
        <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-100 dark:border-slate-800">
            Account Preferences
        </h4>

        <form action="{{ route('profile.preferences') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Preferred Display Currency</label>
                <select name="currency" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5 text-slate-900 dark:text-white">
                    <option value="USD">USD ($) - United States Dollar</option>
                    <option value="INR">INR (₹) - Indian Rupee</option>
                    <option value="EUR">EUR (€) - Euro</option>
                    <option value="GBP">GBP (£) - British Pound</option>
                </select>
            </div>

            <div class="space-y-2 pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="order_notifications" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-slate-700 dark:text-slate-300">Receive order status alerts and carrier updates</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="promo_notifications" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-slate-700 dark:text-slate-300">Receive flash sale announcements and curated maker briefs</span>
                </label>
            </div>

            <button type="submit" class="py-2.5 px-6 rounded-xl bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider hover:bg-indigo-700 shadow-md">
                Save Preferences
            </button>
        </form>
    </div>

    <!-- New Address Modal -->
    <div x-show="newAddrModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
        <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4" @click.outside="newAddrModal = false">
            <h4 class="font-bold text-sm text-slate-900 dark:text-white">Add Delivery Address</h4>

            <form action="{{ route('profile.address.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold mb-1">Name</label>
                    <input type="text" name="name" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5">
                </div>
                <div>
                    <label class="block font-bold mb-1">Phone</label>
                    <input type="tel" name="phone" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5">
                </div>
                <div>
                    <label class="block font-bold mb-1">Address Line</label>
                    <input type="text" name="address_line1" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="city" placeholder="City" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5">
                    <input type="text" name="state" placeholder="State" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="postal_code" placeholder="Postal Code" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5">
                    <input type="text" name="country" placeholder="Country" required value="India" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2.5">
                </div>
                <input type="hidden" name="type" value="shipping">
                <label class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_default" value="1" class="rounded border-slate-300 text-indigo-600">
                    <span>Set as primary default address</span>
                </label>
                <div class="flex items-center justify-end gap-2 pt-3">
                    <button type="button" @click="newAddrModal = false" class="px-4 py-2 text-slate-500">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-bold">Save Address</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
