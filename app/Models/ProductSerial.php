<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSerial extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'serial', 'order_id', 'sold_at'];

    protected $casts = ['sold_at' => 'datetime'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
