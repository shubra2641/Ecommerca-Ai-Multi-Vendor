<?php

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
        if (! $this->active) {
            return false;
        }
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }
        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }
        if ($this->uses_total !== null && $this->used_count >= $this->uses_total) {
            return false;
        }
        if ($this->min_order_total !== null && $total < $this->min_order_total) {
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
            return max(0, $total - ($total * ($this->value / 100)));
        }

        return max(0, $total - $this->value);
    }
}
