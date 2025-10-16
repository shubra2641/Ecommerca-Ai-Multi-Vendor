<?php

namespace App\Models;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'description',
        'data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['data_html'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->latest()->limit($limit);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getDataHtmlAttribute()
    {
        $d = $this->data;
        if (! $d || ! is_array($d) || empty($d)) {
            return null;
        }
        // Basic formatting rules per type
        try {
            switch ($this->type) {
                case 'auth.login':
                    return '<span class="text-success">' . __('User ID') . ': ' . e($d['id'] ?? '') . '</span>';
                case 'order.paid':
                    return '<span class="text-success">' . __('Order') . ': #' . e($d['order_id'] ?? '') . ' ' . __('paid') . '</span>';
                case 'order.cancelled':
                    return '<span class="text-warning">' . __('Order') . ': #' . e($d['order_id'] ?? '') . ' ' . __('cancelled') . '</span>';
                case 'order.refunded':
                    return '<span class="text-info">' . __('Order') . ': #' . e($d['order_id'] ?? '') . ' ' . __('refunded') . '</span>';
                default:
                    return '<code>' . e(substr(json_encode($d), 0, 200)) . '</code>';
            }
        } catch (\Throwable $e) {
            return null;
        }
    }

    // Broadcast channel & payload
    public function broadcastOn(): array
    {
        return [new PrivateChannel('activities')];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'description' => $this->description,
            'data_html' => $this->data_html,
            'time' => $this->created_at?->diffForHumans(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'activity.created';
    }

    public static function log($type, $description, $data = null, $userId = null)
    {
        $payload = [
            'user_id' => $userId ?? auth()->id(),
            'type' => $type,
            'description' => $description,
            'data' => $data,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ];

        if (config('activity.enabled', true) === false) {
            return null;
        }

        // Prevent exact duplicates within a short window to avoid noisy repeated logs.
        // Use a cache-based guard first to handle multiple calls within the same
        // request lifecycle (or before queued jobs have written to the DB).
        try {
            $recentWindow = config('activity.dedup_seconds', 60);

            // Create a stable hash of the payload we want to dedupe on.
            $dedupKey = 'activity:dedup:' . sha1(json_encode([
                'user_id' => $payload['user_id'],
                'type' => $payload['type'],
                'description' => $payload['description'],
                'data' => $payload['data'],
            ]));

            // Cache::add will return false if the key already exists — that's what we want.
            $cached = Cache::add($dedupKey, true, $recentWindow);
            if (! $cached) {
                // Another call has already queued/created this activity recently.
                return null;
            }

            // As a fallback, also check the DB for an existing recent record.
            $query = static::where('user_id', $payload['user_id'])
                ->where('type', $type)
                ->where('description', $description)
                ->where('created_at', '>=', now()->subSeconds($recentWindow));
            if ($data !== null) {
                $query->where('data', json_encode($data));
            }
            if ($query->exists()) {
                return null;
            }
        } catch (\Throwable $e) {
            // Ignore dedupe/cache failures and continue
        }

        if (config('activity.async', true)) {
            dispatch(new \App\Jobs\LogActivityJob($payload))->afterResponse();

            return true;
        }

        return static::create($payload);
    }
}
