<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BalanceHistory extends Model
{
    use HasFactory;

    const TYPE_CREDIT = 'credit';
    const TYPE_DEBIT = 'debit';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_REFUND = 'refund';
    const TYPE_BONUS = 'bonus';
    const TYPE_PENALTY = 'penalty';
    const TYPE_WITHDRAWAL_REQUEST = 'withdrawal_request';
    const TYPE_WITHDRAWAL_APPROVED = 'withdrawal_approved';
    const TYPE_WITHDRAWAL_REJECTED = 'withdrawal_rejected';
    const TYPE_WITHDRAWAL_EXECUTED = 'withdrawal_executed';

    protected $fillable = [
        'user_id',
        'admin_id',
        'type',
        'amount',
        'previous_balance',
        'new_balance',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'previous_balance' => 'decimal:2',
        'new_balance' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount, 2);
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->created_at?->format('M d, Y H:i') ?? '';
    }

    public static function createTransaction(
        User $user,
        string $type,
        float $amount,
        float $previousBalance,
        float $newBalance,
        ?string $note = null,
        ?int $adminId = null
    ): self {
        return self::create([
            'user_id' => $user->id,
            'admin_id' => $adminId,
            'type' => $type,
            'amount' => $amount,
            'previous_balance' => $previousBalance,
            'new_balance' => $newBalance,
            'note' => $note,
        ]);
    }
}
