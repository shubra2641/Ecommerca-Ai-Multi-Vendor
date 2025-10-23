<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInterest extends Model
{
    use HasFactory;

    // Types & statuses
    public const TYPE_STOCK = 'stock'; // generic stock alert

    public const TYPE_BACK_IN_STOCK = 'back_in_stock'; // specifically when returning from zero

    public const TYPE_PRICE_DROP = 'price_drop';

    public const STATUS_PENDING = 'pending';

    public const STATUS_NOTIFIED = 'notified';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'product_id',
        'user_id',
        'email',
        'phone',
        'type',
        'status',
        'notified_at',
        'meta',
        'ip_address',
        'unsubscribe_token',
        'unsubscribed_at',
        'last_mail_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'notified_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'last_mail_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }

    public function scopeType($q, $type)
    {
        return $q->where('type', $type);
    }

    public static function allowedTypes(): array
    {
        return [
            self::TYPE_STOCK,
            self::TYPE_BACK_IN_STOCK,
            self::TYPE_PRICE_DROP,
        ];
    }

    public function scopeActive($q)
    {
        return $q->whereNull('unsubscribed_at')->whereNotIn('status', [self::STATUS_CANCELLED]);
    }

    public function markNotified(): void
    {
        $this->update([
            'status' => self::STATUS_NOTIFIED,
            'notified_at' => now(),
            'last_mail_at' => now(),
        ]);
    }

    public static function countForProduct(int $productId): int
    {
        $ttl = config('interest.cache_ttl', 600);

        return cache()->remember("interest_count:{$productId}", $ttl, function () use ($productId) {
            return static::where('product_id', $productId)->active()->count();
        });
    }

    protected static function booted(): void
    {
        $invalidate = function ($model): void {
            cache()->forget("interest_count:{$model->product_id}");
        };
        static::created($invalidate);
        static::updated($invalidate);
        static::deleted($invalidate);
    }
}
