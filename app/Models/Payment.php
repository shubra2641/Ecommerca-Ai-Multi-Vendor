<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_id',
        'amount',
        'currency',
        'method',
        'status',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function attachments()
    {
        return $this->hasMany(PaymentAttachment::class);
    }
}
