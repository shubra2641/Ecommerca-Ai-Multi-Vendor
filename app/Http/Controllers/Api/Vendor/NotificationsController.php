<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index(Request $r)
    {
        $notifications = $r->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($notifications);
    }

    public function markAsRead(Request $r, $id)
    {
        $notification = $r->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (! $notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['ok' => true, 'message' => 'Notification marked as read']);
    }

    public function markAllAsRead(Request $r)
    {
        $r->user()->unreadNotifications->markAsRead();

        return response()->json(['ok' => true, 'message' => 'All notifications marked as read']);
    }

    public function destroy(Request $r, $id)
    {
        $notification = $r->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (! $notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->delete();

        return response()->json(['ok' => true, 'message' => 'Notification deleted']);
    }
}
