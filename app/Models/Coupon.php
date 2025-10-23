<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'uses_total',
        'used_count',
        'starts_at',
        'ends_at',
        'active',
        'min_order_total',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function isValidForTotal(float $total): bool
    {
        return $this->isActive() &&
            $this->isWithinDateRange() &&
            $this->hasUsesLeft() &&
            $this->meetsMinTotal($total);
    }

    public function isValid(float $total): bool
    {
        return $this->isValidForTotal($total);
    }

    public function applyTo(float $total): float
    {
        $discount = $this->type === 'percent' ? $total * $this->value / 100 : $this->value;

        return max(0, $total - $discount);
    }

    private function isActive(): bool
    {
        return $this->active;
    }

    private function isWithinDateRange(): bool
    {
        $now = now();

        return (! $this->starts_at || $now->gte($this->starts_at)) &&
            (! $this->ends_at || $now->lte($this->ends_at));
    }

    private function hasUsesLeft(): bool
    {
        return $this->uses_total === null || $this->used_count < $this->uses_total;
    }

    private function meetsMinTotal(float $total): bool
    {
        return $this->min_order_total === null || $total >= $this->min_order_total;
    }
}
