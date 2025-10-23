<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BalanceHistory extends Model
{
    use HasFactory;

    /**
     * Transaction types
     */
    public const TYPE_CREDIT = 'credit';

    public const TYPE_DEBIT = 'debit';

    public const TYPE_ADJUSTMENT = 'adjustment';

    public const TYPE_REFUND = 'refund';

    public const TYPE_BONUS = 'bonus';

    public const TYPE_PENALTY = 'penalty';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected array $fillable = [
        'user_id',
        'admin_id',
        'type',
        'amount',
        'previous_balance',
        'new_balance',
        'note',
        'reference_id',
        'reference_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected array $casts = [
        'amount' => 'decimal:2',
        'previous_balance' => 'decimal:2',
        'new_balance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the balance history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who performed the transaction.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the polymorphic reference model.
     */
    public function reference(): Model|\Illuminate\Database\Eloquent\Relations\MorphTo|null
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include credit transactions.
     */
    public function scopeCredits($query)
    {
        return $query->where('type', self::TYPE_CREDIT);
    }

    /**
     * Scope a query to only include debit transactions.
     */
    public function scopeDebits($query)
    {
        return $query->where('type', self::TYPE_DEBIT);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        $symbol = Currency::defaultSymbol();

        return number_format((float) $this->amount, 2).' '.$symbol;
    }

    /**
     * Get formatted previous balance with currency.
     */
    public function getFormattedPreviousBalanceAttribute(): string
    {
        $symbol = Currency::defaultSymbol();

        return number_format((float) $this->previous_balance, 2).' '.$symbol;
    }

    /**
     * Get formatted new balance with currency.
     */
    public function getFormattedNewBalanceAttribute(): string
    {
        $symbol = Currency::defaultSymbol();

        return number_format((float) $this->new_balance, 2).' '.$symbol;
    }

    /**
     * Get formatted date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at?->format('M d, Y H:i') ?? '';
    }

    /**
     * Get transaction type label.
     */
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_CREDIT => __('Credit'),
            self::TYPE_DEBIT => __('Debit'),
            self::TYPE_ADJUSTMENT => __('Adjustment'),
            self::TYPE_REFUND => __('Refund'),
            self::TYPE_BONUS => __('Bonus'),
            self::TYPE_PENALTY => __('Penalty'),
            default => __('Unknown'),
        };
    }

    /**
     * Get transaction type color class.
     */
    public function getTypeColorClass(): string
    {
        return match ($this->type) {
            self::TYPE_CREDIT, self::TYPE_REFUND, self::TYPE_BONUS => 'success',
            self::TYPE_DEBIT, self::TYPE_PENALTY => 'danger',
            self::TYPE_ADJUSTMENT => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get transaction icon.
     */
    public function getTypeIcon(): string
    {
        return match ($this->type) {
            self::TYPE_CREDIT => 'fas fa-plus-circle',
            self::TYPE_DEBIT => 'fas fa-minus-circle',
            self::TYPE_ADJUSTMENT => 'fas fa-edit',
            self::TYPE_REFUND => 'fas fa-undo',
            self::TYPE_BONUS => 'fas fa-gift',
            self::TYPE_PENALTY => 'fas fa-exclamation-triangle',
            default => 'fas fa-circle',
        };
    }

    /**
     * Create a new balance history record.
     */
    public static function createTransaction(
        User $user,
        string $type,
        float $amount,
        float $previousBalance,
        float $newBalance,
        ?string $note = null,
        ?int $adminId = null,
        ?Model $reference = null
    ): self {
        return self::create([
            'user_id' => $user->id,
            'admin_id' => $adminId ?? auth()->id(),
            'type' => $type,
            'amount' => $amount,
            'previous_balance' => $previousBalance,
            'new_balance' => $newBalance,
            'note' => $note,
            'reference_id' => $reference?->id,
            'reference_type' => $reference ? $reference::class : null,
        ]);
    }
}
