<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function latest(Request $request)
    {
        $user = $request->user();
        // Ensure only vendor role hits this
        if ($user->role !== 'vendor') {
            return response()->json(['ok' => false, 'error' => 'Unauthorized'], 403);
        }
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
        if ($request->user()->role !== 'vendor') {
            return response()->json(['ok' => false], 403);
        }

        return response()->json(['ok' => true, 'unread' => $request->user()->unreadNotifications()->count()]);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'vendor') {
            abort(403);
        }
        $notifications = $user->notifications()->latest()->paginate(25);

        return view('vendor.notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, $id)
    {
        $user = $request->user();
        if ($user->role !== 'vendor') {
            return response()->json(['ok' => false], 403);
        }
        $n = $user->notifications()->where('id', $id)->first();
        if ($n) {
            $n->markAsRead();
        }

        return response()->json(['ok' => true]);
    }

    public function markAll(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'vendor') {
            return response()->json(['ok' => false], 403);
        }
        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }
}
