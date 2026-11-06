@extends('layouts.app')

@section('title', 'My Account — UNCART')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
    tab: (new URLSearchParams(window.location.search).get('tab')) || '{{ session('tab') ?? 'profile' }}',
    newAddrModal: false,
    editAddrModal: false,
    editingAddr: {
        id: '',
        name: '',
        phone: '',
        type: 'shipping',
        address_line1: '',
        address_line2: '',
        city: '',
        state: '',
        postal_code: '',
        country: 'United States',
        is_default: false
    },
    openEditAddress(addr) {
        this.editingAddr = {
            id: addr.id,
            name: addr.name || addr.full_name || '',
            phone: addr.phone || '',
            type: addr.type || 'shipping',
            address_line1: addr.address_line1 || '',
            address_line2: addr.address_line2 || '',
            city: addr.city || '',
            state: addr.state || '',
            postal_code: addr.postal_code || '',
            country: addr.country || 'United States',
            is_default: !!addr.is_default
        };
        this.editAddrModal = true;
    },
    avatarPreview: '{{ $user->avatar_url }}',
    defaultAvatar: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=200&auto=format&fit=crop&q=80',
    isUploadingAvatar: false,
    hasCustomAvatar: {{ !empty($user->avatar) ? 'true' : 'false' }},
    avatarMode: 'file',

    async handleAvatarFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (!file.type.match('image.*')) {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Please select a valid image file (JPG, PNG, WebP, GIF).', type: 'error' } }));
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Image size exceeds 5MB limit. Please choose a smaller photo.', type: 'error' } }));
            return;
        }

        this.avatarPreview = URL.createObjectURL(file);
        this.isUploadingAvatar = true;

        const formData = new FormData();
        formData.append('avatar_file', file);
        formData.append('_token', '{{ csrf_token() }}');

        try {
            const res = await fetch('{{ route('profile.avatar.update') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok && data.success) {
                this.avatarPreview = data.avatar_url;
                this.hasCustomAvatar = true;
                document.querySelectorAll('.global-user-avatar').forEach(img => {
                    img.src = data.avatar_url;
                });
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message || 'Profile picture updated!', type: 'success' } }));
            } else {
                throw new Error(data.message || (data.errors ? Object.values(data.errors).flat()[0] : 'Failed to upload photo.'));
            }
        } catch (err) {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: err.message || 'Failed to upload photo.', type: 'error' } }));
        } finally {
            this.isUploadingAvatar = false;
        }
    },

    async removeAvatar() {
        if (!confirm('Are you sure you want to remove your custom profile picture?')) return;
        this.isUploadingAvatar = true;

        try {
            const res = await fetch('{{ route('profile.avatar.delete') }}', {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                this.avatarPreview = data.avatar_url || this.defaultAvatar;
                this.hasCustomAvatar = false;
                document.querySelectorAll('.global-user-avatar').forEach(img => {
                    img.src = this.avatarPreview;
                });
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Profile picture reset to default.', type: 'info' } }));
            } else {
                throw new Error(data.message || 'Failed to remove photo.');
            }
        } catch (err) {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: err.message || 'Error removing photo.', type: 'error' } }));
        } finally {
            this.isUploadingAvatar = false;
        }
    }
}">

    <!-- Profile Header Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm mb-8 flex flex-col sm:flex-row items-center gap-6" data-reveal>
        
        <!-- Interactive Avatar Container with Camera Badge -->
        <div class="relative group">
            <div class="relative w-24 h-24 rounded-full overflow-hidden border-2 border-indigo-600 shadow-md bg-slate-100 cursor-pointer"
                 @click="$refs.avatarInput.click()"
                 title="Click to update profile photo">
                <img :src="avatarPreview" 
                     alt="{{ $user->name }}" 
                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                     :class="{ 'opacity-50 blur-[1px]': isUploadingAvatar }">

                <!-- Hover Overlay -->
                <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col items-center justify-center text-white">
                    <svg class="w-6 h-6 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="text-[10px] font-bold tracking-tight">Change</span>
                </div>

                <!-- Spinner during upload -->
                <div x-show="isUploadingAvatar" x-cloak class="absolute inset-0 bg-slate-950/70 flex items-center justify-center text-white">
                    <svg class="animate-spin w-6 h-6 text-indigo-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <!-- Floating Quick-Action Camera Badge -->
            <button type="button" 
                    @click="$refs.avatarInput.click()" 
                    :disabled="isUploadingAvatar"
                    class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white flex items-center justify-center shadow-lg border-2 border-white transition-all transform hover:scale-110 active:scale-95 cursor-pointer disabled:opacity-50"
                    title="Upload new picture">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </button>

            <!-- Hidden File Input for Avatar -->
            <input type="file" 
                   x-ref="avatarInput" 
                   @change="handleAvatarFileSelect" 
                   accept="image/png,image/jpeg,image/jpg,image/webp,image/gif" 
                   class="hidden">
        </div>

        <div class="flex-1 text-center sm:text-left space-y-1">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h1>
                <span class="inline-block px-3 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-100">
                    {{ ucfirst($user->role) }} Member
                </span>
            </div>
            <p class="text-xs text-slate-500">{{ $user->email }} &bull; {{ $user->phone ?: 'No phone registered' }}</p>
            <p class="text-xs text-slate-600 italic pt-1">{{ $user->bio ?: 'Tech, lifestyle, and design enthusiast.' }}</p>

            <!-- Avatar action links -->
            <div class="pt-2 flex items-center justify-center sm:justify-start gap-3 text-xs">
                <button type="button" @click="$refs.avatarInput.click()" class="font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1.5 transition">
                    <span>📷</span>
                    <span>Update Photo</span>
                </button>
                <span x-show="hasCustomAvatar" class="text-slate-300">&bull;</span>
                <button type="button" 
                        x-show="hasCustomAvatar" 
                        @click="removeAvatar()" 
                        class="text-rose-600 hover:text-rose-700 font-medium inline-flex items-center gap-1 transition"
                        title="Reset to default photo">
                    <span>🗑️</span>
                    <span>Remove Photo</span>
                </button>
            </div>
        </div>

        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('orders.index') }}" class="py-2.5 px-5 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold transition shadow-sm active:scale-95">
                📦 My Orders
            </a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200/80 mb-8 overflow-x-auto pb-1 text-xs font-bold" data-reveal>
        <button @click="tab = 'profile'" 
                class="px-4 py-2.5 rounded-full transition-all"
                :class="tab === 'profile' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'">
            Personal Info
        </button>
        <button @click="tab = 'addresses'" 
                class="px-4 py-2.5 rounded-full transition-all"
                :class="tab === 'addresses' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'">
            Address Book ({{ $addresses->count() }})
        </button>
        <button @click="tab = 'security'" 
                class="px-4 py-2.5 rounded-full transition-all"
                :class="tab === 'security' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'">
            Security & 2FA
        </button>
        <button @click="tab = 'preferences'" 
                class="px-4 py-2.5 rounded-full transition-all"
                :class="tab === 'preferences' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'">
            Preferences
        </button>
    </div>

    <!-- Tab 1: Personal Info -->
    <div x-show="tab === 'profile'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6" data-reveal="scale">
        <h3 class="text-base font-bold text-slate-900">Personal Information</h3>
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Dedicated Profile Picture Card -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 flex flex-col sm:flex-row items-center gap-5">
                <div class="relative w-16 h-16 rounded-full overflow-hidden border-2 border-slate-200 flex-shrink-0 bg-white shadow-sm">
                    <img :src="avatarPreview" alt="{{ $user->name }}" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 text-center sm:text-left space-y-1">
                    <span class="text-xs font-bold text-slate-900 block">Profile Picture</span>
                    <p class="text-[11px] text-slate-500">Upload a square image (PNG, JPG, WEBP, or GIF up to 5MB). Photo updates immediately across the platform.</p>
                    
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-1.5">
                        <label class="cursor-pointer py-1.5 px-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition shadow-sm inline-flex items-center gap-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span>Choose New Photo</span>
                            <input type="file" name="avatar_file" accept="image/*" class="hidden" @change="handleAvatarFileSelect">
                        </label>

                        <button type="button" 
                                x-show="hasCustomAvatar" 
                                @click="removeAvatar()" 
                                class="py-1.5 px-3 rounded-xl border border-slate-200 text-rose-600 hover:bg-rose-50 text-xs font-semibold transition active:scale-95">
                            Remove
                        </button>

                        <button type="button" 
                                @click="avatarMode = (avatarMode === 'url' ? 'file' : 'url')" 
                                class="py-1.5 px-3 rounded-xl text-slate-500 hover:text-slate-800 text-xs font-semibold transition">
                            <span x-text="avatarMode === 'url' ? 'Use Device File' : 'Link Image URL'">Link Image URL</span>
                        </button>
                    </div>

                    <!-- Supplemental URL input toggle -->
                    <div x-show="avatarMode === 'url'" class="pt-2" style="display: none;">
                        <input type="url" name="avatar" value="{{ $user->avatar }}" placeholder="https://images.unsplash.com/..." class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ $user->name }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                    <input type="email" value="{{ $user->email }}" disabled class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-500 cursor-not-allowed">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ $user->phone }}" placeholder="+1 (555) 000-0000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Account Role</label>
                    <input type="text" value="{{ ucfirst($user->role) }} Member" disabled class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-500 cursor-not-allowed font-medium">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Bio / Profile Description</label>
                <textarea name="bio" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">{{ $user->bio }}</textarea>
            </div>
            <div class="pt-2">
                <button type="submit" class="py-2.5 px-6 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider shadow-sm transition active:scale-95">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Tab 2: Addresses -->
    <div x-show="tab === 'addresses'" class="space-y-6" style="display: none;">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Saved Delivery Addresses</h3>
                <p class="text-xs text-slate-500">Manage shipping destinations and default delivery addresses.</p>
            </div>
            <button @click="newAddrModal = true" class="py-2.5 px-4 rounded-full bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold transition shadow-sm active:scale-95 flex items-center gap-1.5">
                <span>+</span>
                <span>Add Address</span>
            </button>
        </div>

        @if($addresses->isEmpty())
            <div class="p-12 text-center rounded-3xl bg-slate-50 border-2 border-dashed border-slate-200">
                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-indigo-600 flex items-center justify-center mx-auto mb-3 text-xl shadow-sm">
                    📍
                </div>
                <h4 class="text-sm font-bold text-slate-900">No saved addresses yet</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-4">Add your shipping and billing addresses for fast, one-click checkout.</p>
                <button type="button" @click="newAddrModal = true" class="py-2 px-5 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition shadow-sm active:scale-95">
                    + Add New Address
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($addresses as $addr)
                    <div class="bg-white rounded-3xl p-5 border border-slate-200/90 hover:border-slate-300 shadow-sm space-y-3 relative transition">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="font-bold text-slate-900 text-sm block">{{ $addr->name ?: $addr->full_name }}</span>
                                <span class="inline-block mt-0.5 text-[10px] font-mono font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-600">
                                    {{ ucfirst($addr->type) }}
                                </span>
                            </div>
                            @if($addr->is_default)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    ✓ Default
                                </span>
                            @endif
                        </div>

                        <div class="text-xs text-slate-600 space-y-0.5">
                            <p class="font-medium text-slate-800">{{ $addr->address_line1 }}</p>
                            @if($addr->address_line2)
                                <p class="text-slate-500">{{ $addr->address_line2 }}</p>
                            @endif
                            <p>{{ $addr->city }}, {{ $addr->state }} {{ $addr->postal_code }}</p>
                            <p class="text-slate-500 font-medium">{{ $addr->country }}</p>
                            <p class="text-indigo-600 font-mono text-[11px] pt-1">📞 {{ $addr->phone }}</p>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <div>
                                @if(!$addr->is_default)
                                    <form action="{{ route('profile.address.default', $addr->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="font-semibold text-slate-500 hover:text-indigo-600 transition">
                                            Make Default
                                        </button>
                                    </form>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <button type="button" 
                                        @click="openEditAddress({{ json_encode($addr) }})" 
                                        class="font-bold text-indigo-600 hover:text-indigo-700 transition">
                                    Edit
                                </button>
                                <span class="text-slate-200">&bull;</span>
                                <form action="{{ route('profile.address.delete', $addr->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this address?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-semibold text-rose-500 hover:text-rose-700 transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Tab 3: Security & 2FA -->
    <div x-show="tab === 'security'" class="space-y-6" style="display: none;">
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Two-Factor Authentication (2FA)</h3>
                <p class="text-xs text-slate-500 max-w-md mt-1">
                    Adds an extra security barrier by requiring a 6-digit code from your authenticator app during login.
                </p>
                <div class="mt-2 text-xs font-bold {{ $user->two_factor_enabled ? 'text-emerald-600' : 'text-slate-400' }}">
                    Status: {{ $user->two_factor_enabled ? 'Active & Protected' : 'Disabled' }}
                </div>
            </div>

            <form action="{{ route('profile.2fa') }}" method="POST">
                @csrf
                <button type="submit" class="py-2.5 px-5 rounded-full font-bold text-xs uppercase tracking-wider transition {{ $user->two_factor_enabled ? 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100' : 'bg-slate-900 hover:bg-indigo-600 text-white' }}">
                    {{ $user->two_factor_enabled ? 'Disable 2FA' : 'Enable 2FA' }}
                </button>
            </form>
        </div>

        <!-- Password Change -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900">Change Password</h3>
            <form action="{{ route('profile.password') }}" method="POST" class="space-y-4 max-w-md">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">New Password</label>
                    <input type="password" name="password" required minlength="8" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required minlength="8" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                </div>
                <button type="submit" class="py-2.5 px-6 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider transition">
                    Update Password
                </button>
            </form>
        </div>
    </div>

    <!-- Tab 4: Preferences -->
    <div x-show="tab === 'preferences'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6" style="display: none;">
        <h3 class="text-sm font-bold text-slate-900">Communication Preferences</h3>
        <form action="{{ route('profile.preferences') }}" method="POST" class="space-y-4">
            @csrf
            <label class="flex items-center gap-3 text-xs text-slate-700 hover:text-indigo-600 cursor-pointer">
                <input type="checkbox" name="email_notifications" value="1" {{ ($user->preferences['email_notifications'] ?? true) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                <span>Receive Order Dispatch and Delivery Notification Emails</span>
            </label>

            <label class="flex items-center gap-3 text-xs text-slate-700 hover:text-indigo-600 cursor-pointer">
                <input type="checkbox" name="marketing_notifications" value="1" {{ ($user->preferences['marketing_notifications'] ?? false) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                <span>Receive Exclusive Deals & Secret Voucher Codes</span>
            </label>

            <div class="pt-3 border-t border-slate-100">
                <button type="submit" class="py-2.5 px-6 rounded-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider">
                    Save Preferences
                </button>
            </div>
        </form>
    </div>

    <!-- New Address Pop-Up Modal Window -->
    <div x-show="newAddrModal" 
         x-transition.opacity 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;"
         @keydown.escape.window="newAddrModal = false">
        <div @click.away="newAddrModal = false" class="bg-white border border-slate-100 rounded-3xl w-full max-w-lg p-6 sm:p-8 shadow-2xl relative">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Add Delivery Destination</h3>
                    <p class="text-xs text-slate-500">Save a new address to your address book.</p>
                </div>
                <button type="button" @click="newAddrModal = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">✕</button>
            </div>

            <form action="{{ route('profile.address.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Full Recipient Name</label>
                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required placeholder="e.g. John Doe" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Address Type</label>
                        <select name="type" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                            <option value="shipping" {{ old('type') === 'shipping' ? 'selected' : '' }}>Shipping Address</option>
                            <option value="billing" {{ old('type') === 'billing' ? 'selected' : '' }}>Billing Address</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Telephone Contact</label>
                        <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone) }}" required placeholder="+1 (555) 000-0000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Street Address</label>
                    <input type="text" name="address_line1" value="{{ old('address_line1') }}" required placeholder="123 Market Street" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Apartment, Suite, Unit (Optional)</label>
                    <input type="text" name="address_line2" value="{{ old('address_line2') }}" placeholder="Apt 4B / Suite 200" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" required placeholder="San Francisco" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">State / Province</label>
                        <input type="text" name="state" value="{{ old('state') }}" required placeholder="CA" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Postal Code</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" required placeholder="94107" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Country</label>
                    <input type="text" name="country" value="{{ old('country', 'United States') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="add_is_default" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="add_is_default" class="text-xs text-slate-700 select-none cursor-pointer">Set as default address for this type</label>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="newAddrModal = false" class="px-4 py-2 rounded-full border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-full bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold shadow-sm transition active:scale-95">Save Address</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Address Modal Window -->
    <div x-show="editAddrModal" 
         x-transition.opacity 
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;"
         @keydown.escape.window="editAddrModal = false">
        <div @click.away="editAddrModal = false" class="bg-white border border-slate-100 rounded-3xl w-full max-w-lg p-6 sm:p-8 shadow-2xl relative">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Edit Delivery Address</h3>
                    <p class="text-xs text-slate-500">Update destination or contact details.</p>
                </div>
                <button type="button" @click="editAddrModal = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">✕</button>
            </div>

            <form :action="'{{ url('profile/address') }}/' + editingAddr.id + '/update'" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Full Recipient Name</label>
                    <input type="text" name="name" x-model="editingAddr.name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Address Type</label>
                        <select name="type" x-model="editingAddr.type" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                            <option value="shipping">Shipping Address</option>
                            <option value="billing">Billing Address</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Telephone Contact</label>
                        <input type="text" name="phone" x-model="editingAddr.phone" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Street Address</label>
                    <input type="text" name="address_line1" x-model="editingAddr.address_line1" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Apartment, Suite, Unit (Optional)</label>
                    <input type="text" name="address_line2" x-model="editingAddr.address_line2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">City</label>
                        <input type="text" name="city" x-model="editingAddr.city" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">State / Province</label>
                        <input type="text" name="state" x-model="editingAddr.state" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Postal Code</label>
                        <input type="text" name="postal_code" x-model="editingAddr.postal_code" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Country</label>
                    <input type="text" name="country" x-model="editingAddr.country" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:border-indigo-500">
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="edit_is_default" name="is_default" value="1" :checked="editingAddr.is_default" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="edit_is_default" class="text-xs text-slate-700 select-none cursor-pointer">Set as default address for this type</label>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="editAddrModal = false" class="px-4 py-2 rounded-full border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition active:scale-95">Update Address</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
