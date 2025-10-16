<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'status', 'total', 'currency', 'shipping_address', 'shipping_address_id', 'payment_method', 'payment_status',
        'items_subtotal', 'shipping_price', 'shipping_zone_id', 'shipping_estimated_days', 'has_backorder',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'total' => 'decimal:2',
        'has_backorder' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
