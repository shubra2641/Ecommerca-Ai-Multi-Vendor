<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends BaseAdminController
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function latest(Request $request)
    {
        $user = $this->getCurrentUser($request);
        $limit = config('notifications.dropdown_limit', 10);
        $notifications = $this->notificationService->getLatest($user, $limit);

        return $this->successResponse(__('Notifications retrieved successfully'), [
            'notifications' => $notifications,
            'unread' => $this->notificationService->getUnreadCount($user),
            'limit' => $limit,
            'poll_interval_ms' => config('notifications.poll_interval_ms', 30000),
        ]);
    }

    public function unreadCount(Request $request)
    {
        $user = $this->getCurrentUser($request);

        return $this->successResponse(__('Unread count retrieved'), [
            'unread' => $this->notificationService->getUnreadCount($user),
        ]);
    }

    public function index(Request $request)
    {
        $user = $this->getCurrentUser($request);
        $notifications = $this->notificationService->getPaginated($user, 25);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markReadOld(Request $request, $notificationId)
    {
        $notification = $request->user()->notifications()->where('id', $notificationId)->first();
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['ok' => true]);
    }

    public function markAll(Request $request)
    {
        $user = $this->getCurrentUser($request);
        $count = $this->notificationService->markAllAsRead($user);

        return $this->successResponse(__('All notifications marked as read'), [
            'marked_count' => $count,
        ]);
    }

    /**
     * Get notification statistics
     */
    public function getStats(Request $request)
    {
        $user = $this->getCurrentUser($request);
        $stats = $this->notificationService->getStats($user);

        return $this->successResponse(__('Statistics retrieved successfully'), $stats);
    }

    /**
     * Mark notification as read (improved)
     */
    public function markRead(Request $request, $notificationId)
    {
        $user = $this->getCurrentUser($request);
        $success = $this->notificationService->markAsRead($user, $notificationId);

        if (! $success) {
            return $this->errorResponse(__('Notification not found'), null, 404);
        }

        return $this->successResponse(__('Notification marked as read'), [
            'unread_count' => $this->notificationService->getUnreadCount($user),
        ]);
    }

    /**
     * Mark all notifications as read (improved)
     */
    public function markAllRead(Request $request)
    {
        $user = $this->getCurrentUser($request);
        $count = $this->notificationService->markAllAsRead($user);

        if ($count === 0) {
            return $this->successResponse(__('No unread notifications to mark'));
        }

        return $this->successResponse(__('All notifications marked as read'), [
            'marked_count' => $count,
        ]);
    }

    /**
     * Delete notification
     */
    public function delete(Request $request, $notificationId)
    {
        $user = $this->getCurrentUser($request);
        $success = $this->notificationService->delete($user, $notificationId);

        if (! $success) {
            return $this->errorResponse(__('Notification not found'), null, 404);
        }

        return $this->successResponse(__('Notification deleted successfully'));
    }

    /**
     * Clear all notifications
     */
    public function clearAll(Request $request)
    {
        $user = $this->getCurrentUser($request);
        $count = $this->notificationService->clearAll($user);

        return $this->successResponse(__('All notifications cleared'), [
            'cleared_count' => $count,
        ]);
    }
}
