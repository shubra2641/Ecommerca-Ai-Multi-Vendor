<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $fillable = ['order_id', 'status', 'note'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
