<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'method',
        'amount',
        'currency',
        'status',
        'transaction_id',
        'payload',
        'failure_reason',
        'reference_number',
        'cancelled_at',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payload' => 'array',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the order that owns the payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'method', 'slug');
    }

    /**
     * Get the user that owns the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payment attachments.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(PaymentAttachment::class);
    }

    /**
     * Get the payment gateway.
     */
    public function gateway(): BelongsTo
    {
        // Payments store the gateway driver/slug in the `method` column.
        return $this->belongsTo(PaymentGateway::class, 'method', 'slug');
    }

    /**
     * Check if payment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment has failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark payment as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(?string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'failure_reason' => $reason,
        ]);
    }

    /**
     * Mark payment as cancelled.
     */
    public function markAsCancelled(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Generate unique payment ID.
     */
    public static function generatePaymentId(): string
    {
        do {
            $paymentId = 'PAY_'.strtoupper(uniqid());
        } while (self::where('payment_id', $paymentId)->exists());

        return $paymentId;
    }

    /**
     * Scope for specific gateway.
     */
    public function scopeForGateway($query, string $gateway)
    {
        // Accept either numeric id, slug, or driver name. Prefer slug matching method.
        return $query->where('method', $gateway)->orWhere('method', 'like', "%{$gateway}%");
    }

    /**
     * Scope for specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
