<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        $unreadCount = Notification::where('user_id', Auth::id())->where('is_read', false)->count();

        return view('pages.notifications', compact('notifications', 'unreadCount'));
    }

    public function markAsRead($id)
    {
        $notif = Notification::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $notif->update(['is_read' => true]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        if ($notif->link) {
            return redirect($notif->link);
        }

        return back()->with('success', 'Marked as read.');
    }

    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }
}
