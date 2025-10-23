<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
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

    public function isValidForTotal($total)
    {
        $now = now();
        if (! $this->active || ($this->starts_at && $now->lt($this->starts_at)) || ($this->ends_at && $now->gt($this->ends_at)) || ($this->uses_total !== null && $this->used_count >= $this->uses_total) || ($this->min_order_total !== null && $total < $this->min_order_total)) {
            return false;
        }

        return true;
    }

    public function isValid($total)
    {
        return $this->isValidForTotal($total);
    }

    public function applyTo($total)
    {
        if ($this->type === 'percent') {
            return max(0, $total - ($total * $this->value / 100));
        }

        return max(0, $total - $this->value);
    }
}
