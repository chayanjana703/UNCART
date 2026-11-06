@extends('layouts.app')

@section('title', 'Notifications Telemetry — UNCART')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4" data-reveal>
        <div>
            <span class="text-xs font-mono font-bold uppercase tracking-widest text-indigo-400">System Telemetry</span>
            <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1">Notifications Center</h1>
            <p class="text-xs text-slate-400 mt-1 font-mono">Real-time alerts, courier tracking updates, and merchant replies</p>
        </div>

        @if($unreadCount > 0)
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="py-2.5 px-4 rounded-2xl bg-white/5 hover:bg-white/10 text-white font-bold text-xs border border-white/10 transition active:scale-95 shadow">
                    Mark All as Read
                </button>
            </form>
        @endif
    </div>

    @if($notifications->isEmpty())
        <div class="bg-slate-900/60 backdrop-blur-2xl rounded-3xl border border-white/10 p-16 text-center space-y-3 shadow-2xl" data-reveal>
            <span class="text-4xl">🔔</span>
            <h3 class="text-base font-bold text-white">You're all caught up!</h3>
            <p class="text-xs text-slate-400">No unread notifications or courier updates at this moment.</p>
        </div>
    @else
        <div class="bg-slate-900/60 backdrop-blur-2xl rounded-3xl border border-white/10 divide-y divide-white/5 overflow-hidden shadow-2xl" data-reveal="scale">
            @foreach($notifications as $notif)
                <div class="p-6 flex items-start justify-between gap-4 transition-colors {{ !$notif->is_read ? 'bg-indigo-950/20' : '' }}">
                    <div class="flex items-start gap-3.5">
                        <span class="w-2.5 h-2.5 rounded-full mt-1.5 flex-shrink-0 {{ !$notif->is_read ? 'bg-indigo-400 animate-pulse' : 'bg-transparent' }}"></span>
                        <div>
                            <h4 class="font-bold text-xs text-white">{{ $notif->title }}</h4>
                            <p class="text-xs text-slate-300 mt-0.5 leading-relaxed">{{ $notif->message }}</p>
                            <span class="text-[10px] text-slate-500 font-mono mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if($notif->link)
                            <a href="{{ $notif->link }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300">
                                View &rarr;
                            </a>
                        @endif
                        @if(!$notif->is_read)
                            <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-[11px] text-slate-400 hover:text-white">
                                    Acknowledge
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-6" data-reveal>
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
