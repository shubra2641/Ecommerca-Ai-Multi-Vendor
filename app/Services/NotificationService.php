<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    public function getStats(User $user): array
    {
        $cacheKey = "user.{$user->id}.notifications.stats";

        return Cache::remember($cacheKey, 300, function () use ($user) {
            $total = $user->notifications()->count();
            $unread = $user->unreadNotifications()->count();

            return [
                'total' => $total,
                'unread' => $unread,
                'read' => $total - $unread,
            ];
        });
    }

    public function getLatest(User $user, int $limit = 10): Collection
    {
        return $user->notifications()
            ->latest()
            ->take($limit)
            ->get(['id', 'data', 'read_at', 'created_at']);
    }

    public function markAsRead(User $user, string $id): bool
    {
        $updated = $user->notifications()
            ->where('id', $id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($updated > 0) {
            $this->clearStatsCache($user);
            return true;
        }

        return false;
    }

    public function markAllAsRead(User $user): int
    {
        $count = $user->unreadNotifications()->update(['read_at' => now()]);

        if ($count > 0) {
            $this->clearStatsCache($user);
        }

        return $count;
    }

    public function delete(User $user, string $id): bool
    {
        $deleted = $user->notifications()->where('id', $id)->delete();

        if ($deleted > 0) {
            $this->clearStatsCache($user);
            return true;
        }

        return false;
    }

    public function clearAll(User $user): int
    {
        $count = $user->notifications()->delete();
        $this->clearStatsCache($user);
        return $count;
    }

    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    public function getPaginated(User $user, int $perPage = 25)
    {
        return $user->notifications()->latest()->paginate($perPage);
    }

    private function clearStatsCache(User $user): void
    {
        Cache::forget("user.{$user->id}.notifications.stats");
    }
}
