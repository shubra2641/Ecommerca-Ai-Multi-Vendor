<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    /**
     * Get notification statistics for user
     */
    public function getStats(User $user): array
    {
        return [
            'total' => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
            'read' => $user->readNotifications()->count(),
        ];
    }

    /**
     * Get latest notifications for user
     */
    public function getLatest(User $user, int $limit = 10): Collection
    {
        return $user->notifications()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(User $user, string $id): bool
    {
        $notification = $user->notifications()->where('id', $id)->first();

        if (! $notification) {
            return false;
        }

        if ($notification->read_at) {
            return true; // Already read
        }

        $notification->markAsRead();

        return true;
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(User $user): int
    {
        return $user->unreadNotifications()->update(['read_at' => now()]);
    }

    /**
     * Delete notification
     */
    public function delete(User $user, string $id): bool
    {
        $notification = $user->notifications()->where('id', $id)->first();

        if (! $notification) {
            return false;
        }

        $notification->delete();

        return true;
    }

    /**
     * Clear all notifications
     */
    public function clearAll(User $user): int
    {
        $count = $user->notifications()->count();
        $user->notifications()->delete();

        return $count;
    }

    /**
     * Get unread count
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Get paginated notifications
     */
    public function getPaginated(User $user, int $perPage = 25)
    {
        return $user->notifications()->latest()->paginate($perPage);
    }
}
