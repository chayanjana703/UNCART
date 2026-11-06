@extends('layouts.admin')

@section('title', 'System Activity & Audit Logs')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                System Activity Logs
                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    {{ $logs->total() }} Logged Events
                </span>
            </h1>
            <p class="text-slate-400 text-xs mt-1">Real-time audit trail of administrative actions, merchant updates, order lifecycles, and security events.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.activity-logs.export') }}" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:border-slate-700 text-xs font-semibold flex items-center gap-2 transition">
                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Audit CSV
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by action, description or keyword..." class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl px-3.5 py-2 text-xs text-slate-200 placeholder-slate-500 transition">
                @if(request('q'))
                    <a href="{{ route('admin.activity-logs.index', array_filter(request()->except('q'))) }}" class="absolute right-3 top-2.5 text-xs text-slate-500 hover:text-white">&times;</a>
                @endif
            </div>

            <div>
                <select name="action_type" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-slate-300">
                    <option value="">All Action Types</option>
                    @foreach($actionTypes as $key => $label)
                        <option value="{{ $key }}" {{ request('action_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                @if(request()->hasAny(['q', 'action_type']))
                    <a href="{{ route('admin.activity-logs.index') }}" class="w-full text-center px-3 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white text-xs transition">
                        Reset Filters
                    </a>
                @else
                    <span class="text-[11px] font-mono text-slate-500">Live monitoring active</span>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/60 text-slate-400 uppercase tracking-wider font-mono text-[10px]">
                        <th class="py-3 px-4">Timestamp</th>
                        <th class="py-3 px-4">Actor / Performer</th>
                        <th class="py-3 px-4">Action Event</th>
                        <th class="py-3 px-4">Description / Details</th>
                        <th class="py-3 px-4">Origin IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-sans">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-mono text-slate-200 text-xs">{{ $log->created_at->format('M d, Y H:i:s') }}</div>
                                <div class="text-[10px] text-slate-500">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($log->user)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-[11px] text-amber-400 flex-shrink-0">
                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-200 text-xs">{{ $log->user->name }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono">{{ $log->user->email }}</div>
                                        </div>
                                        <span class="ml-1 text-[9px] uppercase tracking-wider font-bold px-1.5 py-0.5 rounded {{ $log->user->role === 'admin' ? 'bg-amber-500/20 text-amber-400' : ($log->user->role === 'vendor' ? 'bg-indigo-500/20 text-indigo-400' : 'bg-slate-700/50 text-slate-300') }}">
                                            {{ $log->user->role }}
                                        </span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 text-[10px]">
                                            ⚙️
                                        </div>
                                        <span class="text-xs text-slate-400 font-medium italic">System Bot / Cron</span>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold font-mono
                                    {{ str_contains(strtolower($log->action), 'cancel') || str_contains(strtolower($log->action), 'delete') ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 
                                       (str_contains(strtolower($log->action), 'refund') || str_contains(strtolower($log->action), 'dispute') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 
                                       (str_contains(strtolower($log->action), 'create') || str_contains(strtolower($log->action), 'approved') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 
                                       'bg-slate-800 text-slate-300 border border-slate-700')) }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ str_contains(strtolower($log->action), 'cancel') || str_contains(strtolower($log->action), 'delete') ? 'bg-rose-400' : (str_contains(strtolower($log->action), 'refund') ? 'bg-amber-400' : 'bg-emerald-400') }}"></span>
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-300">
                                <p class="line-clamp-2 max-w-xl text-xs leading-relaxed">{{ $log->description }}</p>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-mono text-[11px] text-slate-400 bg-slate-950 px-2 py-0.5 rounded border border-slate-800/80">
                                    {{ $log->ip_address ?: '127.0.0.1' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">
                                <svg class="w-10 h-10 mx-auto mb-2 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm font-semibold text-slate-400">No activity log entries found matching criteria</p>
                                <p class="text-xs mt-1">Try resetting search filters or checking back as users interact with the store.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
