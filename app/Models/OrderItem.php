<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'sku',
        'name',
        'qty',
        'price',
        'vendor_commission_rate',
        'vendor_commission_amount',
        'vendor_earnings',
        'meta',
        'is_backorder',
        'committed',
        'restocked',
        'purchased_at',
        'refund_expires_at',
        'return_requested',
        'return_status',
        'return_reason',
    ];

    protected $casts = [
        'meta' => 'array',
        'price' => 'decimal:2',
        'vendor_commission_rate' => 'decimal:2',
        'vendor_commission_amount' => 'decimal:2',
        'vendor_earnings' => 'decimal:2',
        'is_backorder' => 'boolean',
        'committed' => 'boolean',
        'restocked' => 'boolean',
        'purchased_at' => 'datetime',
        'refund_expires_at' => 'datetime',
        'return_requested' => 'boolean',
    ];

    public function isWithinReturnWindow(): bool
    {
        if (! $this->refund_expires_at) {
            return false;
        }

        return now()->lte($this->refund_expires_at);
    }

    public function returnStatusLabel(): string
    {
        return $this->return_status ?? 'none';
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
