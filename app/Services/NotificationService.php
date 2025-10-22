<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    public function getStats(User $user): array
    {
        $stats = $user->notifications()->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN read_at IS NULL THEN 1 ELSE 0 END) as unread,
            SUM(CASE WHEN read_at IS NOT NULL THEN 1 ELSE 0 END) as read
        ')->first();

        return ['total' => (int)$stats->total, 'unread' => (int)$stats->unread, 'read' => (int)$stats->read];
    }

    public function getLatest(User $user, int $limit = 10): Collection
    {
        return $user->notifications()->latest()->take($limit)->get(['id', 'data', 'read_at', 'created_at']);
    }

    public function markAsRead(User $user, string $id): bool
    {
        return $user->notifications()->where('id', $id)->whereNull('read_at')->update(['read_at' => now()]) > 0;
    }

    public function markAllAsRead(User $user): int
    {
        return $user->unreadNotifications()->update(['read_at' => now()]);
    }

    public function delete(User $user, string $id): bool
    {
        return $user->notifications()->where('id', $id)->delete() > 0;
    }

    public function clearAll(User $user): int
    {
        return $user->notifications()->delete();
    }

    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    public function getPaginated(User $user, int $perPage = 25)
    {
        return $user->notifications()->latest()->paginate($perPage);
    }
}
