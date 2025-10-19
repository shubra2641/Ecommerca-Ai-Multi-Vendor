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

    public function markReadOld(Request $request, $id)
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

    /**
     * Get notification statistics
     */
    public function getStats(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total' => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
            'read' => $user->readNotifications()->count(),
            'today' => $user->notifications()->whereDate('created_at', today())->count(),
            'this_week' => $user->notifications()->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Mark notification as read (improved)
     */
    public function markRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => __('Notification not found')
            ], 404);
        }

        if ($notification->read_at) {
            return response()->json([
                'success' => true,
                'message' => __('Notification already marked as read')
            ]);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => __('Notification marked as read'),
            'unread_count' => $user->unreadNotifications()->count()
        ]);
    }

    /**
     * Mark all notifications as read (improved)
     */
    public function markAllRead(Request $request)
    {
        $user = $request->user();
        $unreadCount = $user->unreadNotifications()->count();

        if ($unreadCount === 0) {
            return response()->json([
                'success' => true,
                'message' => __('No unread notifications to mark')
            ]);
        }

        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => __('All notifications marked as read'),
            'marked_count' => $unreadCount
        ]);
    }

    /**
     * Delete notification
     */
    public function delete(Request $request, $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => __('Notification not found')
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => __('Notification deleted successfully')
        ]);
    }

    /**
     * Clear all notifications
     */
    public function clearAll(Request $request)
    {
        $user = $request->user();
        $count = $user->notifications()->count();

        $user->notifications()->delete();

        return response()->json([
            'success' => true,
            'message' => __('All notifications cleared'),
            'cleared_count' => $count
        ]);
    }
}
