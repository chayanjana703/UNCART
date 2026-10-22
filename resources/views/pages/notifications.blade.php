@extends('layouts.app')

@section('title', 'Notifications - ShopiCart')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Notifications Center</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Real-time alerts, courier tracking updates and flash sale announcements</p>
        </div>

        @if($unreadCount > 0)
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="py-2 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs">
                    Mark All as Read
                </button>
            </form>
        @endif
    </div>

    @if($notifications->isEmpty())
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-16 text-center space-y-3">
            <span class="text-4xl">🔔</span>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">You're all caught up!</h3>
            <p class="text-xs text-slate-500">No unread notifications or system alerts at this moment.</p>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 divide-y divide-slate-100 dark:divide-slate-800 overflow-hidden shadow-sm">
            @foreach($notifications as $notif)
                <div class="p-6 flex items-start justify-between gap-4 transition-colors {{ !$notif->is_read ? 'bg-indigo-50/30 dark:bg-indigo-950/20' : '' }}">
                    <div class="flex items-start gap-3.5">
                        <span class="w-2.5 h-2.5 rounded-full mt-1.5 flex-shrink-0 {{ !$notif->is_read ? 'bg-indigo-600 animate-pulse' : 'bg-transparent' }}"></span>
                        <div>
                            <h4 class="font-bold text-xs text-slate-900 dark:text-white">{{ $notif->title }}</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5 leading-relaxed">{{ $notif->message }}</p>
                            <span class="text-[10px] text-slate-400 font-mono mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if($notif->link)
                            <a href="{{ $notif->link }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                View &rarr;
                            </a>
                        @endif
                        @if(!$notif->is_read)
                            <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                    Dismiss
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
@endsection
