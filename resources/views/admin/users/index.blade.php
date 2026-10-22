@extends('layouts.admin')

@section('title', 'User Accounts')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, editingUser: {} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                User Accounts
                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    {{ $users->total() }} Total
                </span>
            </h1>
            <p class="text-slate-400 text-xs mt-1">Manage global user credentials, customer roles, permissions, and security blocklists.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.export') }}" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:border-slate-700 text-xs font-semibold flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
            <button @click="addModal = true" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/20 flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Create User
            </button>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name, email, or telephone..." class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-200 placeholder-slate-500 transition">
                @if(request('q'))
                    <a href="{{ route('admin.users.index', array_filter(request()->except('q'))) }}" class="absolute right-3 top-2.5 text-xs text-slate-500 hover:text-white">&times;</a>
                @endif
            </div>

            <div>
                <select name="role" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-3 py-2 text-xs text-slate-300 transition">
                    <option value="">All Account Roles</option>
                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customers</option>
                    <option value="vendor" {{ request('role') === 'vendor' ? 'selected' : '' }}>Vendors / Merchants</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrators</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition">
                    Apply Filter
                </button>
                @if(request()->hasAny(['q', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white text-xs transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- User Accounts Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/50 text-slate-400 uppercase tracking-wider font-mono text-[10px]">
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">Phone</th>
                        <th class="py-3 px-4">2FA Status</th>
                        <th class="py-3 px-4">Account Status</th>
                        <th class="py-3 px-4">Registered</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100' }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover bg-slate-800">
                                    <div>
                                        <div class="font-bold text-white">{{ $user->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($user->role === 'admin')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 uppercase">Admin</span>
                                @elseif($user->role === 'vendor')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 uppercase">Vendor</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase">Customer</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-300">
                                {{ $user->phone ?? '—' }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($user->two_factor_enabled)
                                    <span class="inline-flex items-center gap-1 text-[11px] text-emerald-400 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        2FA Active
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-500">Disabled</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($user->is_blocked)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20">BLOCKED</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">ACTIVE</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-400 text-[11px]">
                                {{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Edit Trigger -->
                                    <button @click="editingUser = { id: {{ $user->id }}, name: '{{ addslashes($user->name) }}', email: '{{ addslashes($user->email) }}', phone: '{{ addslashes($user->phone ?? '') }}', role: '{{ $user->role }}' }; editModal = true" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition" title="Edit Profile">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>

                                    <!-- Block / Unblock -->
                                    <form action="{{ route('admin.users.toggle-block', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 rounded-lg {{ $user->is_blocked ? 'bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400' : 'bg-red-500/10 hover:bg-red-500/20 text-red-400' }} transition" title="{{ $user->is_blocked ? 'Unblock Account' : 'Block Account' }}">
                                            @if($user->is_blocked)
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Delete User -->
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Permanently delete {{ $user->name }}? This action cannot be reversed.')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-red-950/60 text-slate-400 hover:text-red-400 transition" title="Delete Account">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">No users match your query filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Create User Modal -->
    <div x-show="addModal" x-transition.opacity class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="addModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl relative">
            <h2 class="text-lg font-bold text-white mb-1">Create Platform User</h2>
            <p class="text-xs text-slate-400 mb-4">Provision a new account with specified role and credentials.</p>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name</label>
                    <input type="text" name="name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                        <input type="email" name="email" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Telephone</label>
                        <input type="text" name="phone" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Account Role</label>
                        <select name="role" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="customer">Customer</option>
                            <option value="vendor">Vendor / Merchant</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Temporary Password</label>
                        <input type="password" name="password" required minlength="8" placeholder="Min 8 characters" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white text-xs font-medium">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold">Create Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div x-show="editModal" x-transition.opacity class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="editModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl relative">
            <h2 class="text-lg font-bold text-white mb-1">Edit Account Profile</h2>
            <p class="text-xs text-slate-400 mb-4">Modify permissions, email contact, or assign new role.</p>

            <form :action="'{{ url('admin/users') }}/' + editingUser.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name</label>
                    <input type="text" name="name" x-model="editingUser.name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                        <input type="email" name="email" x-model="editingUser.email" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Telephone</label>
                        <input type="text" name="phone" x-model="editingUser.phone" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Account Role</label>
                        <select name="role" x-model="editingUser.role" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="customer">Customer</option>
                            <option value="vendor">Vendor / Merchant</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Reset Password (Optional)</label>
                        <input type="password" name="password" minlength="8" placeholder="Leave blank to keep current" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white text-xs font-medium">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
