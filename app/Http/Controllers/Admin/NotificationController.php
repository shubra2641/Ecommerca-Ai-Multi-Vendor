<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function latest(Request $request)
    {
        $user = $request->user();
        $limit = config('notifications.dropdown_limit', 10);
        $notifications = $user->notifications()->latest()->limit($limit)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'data' => $n->data,
                'read_at' => $n->read_at,
                'created_at' => $n->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'ok' => true,
            'notifications' => $notifications,
            'unread' => $user->unreadNotifications()->count(),
            'limit' => $limit,
            'poll_interval_ms' => config('notifications.poll_interval_ms', 30000),
        ]);
    }

    public function unreadCount(Request $request)
    {
        return response()->json(['ok' => true, 'unread' => $request->user()->unreadNotifications()->count()]);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->paginate(25);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, $id)
    {
        $n = $request->user()->notifications()->where('id', $id)->first();
        if ($n) {
            $n->markAsRead();
        }

        return response()->json(['ok' => true]);
    }

    public function markAll(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }
}
