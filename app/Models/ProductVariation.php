<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price',
        'sale_price',
        'sale_start',
        'sale_end',
        'manage_stock',
        'stock_qty',
        'reserved_qty',
        'backorder',
        'attribute_data',
        'attribute_hash',
        'image',
        'active',
        'name_translations',
    ];

    protected $casts = [
        'sale_start' => 'datetime',
        'sale_end' => 'datetime',
        'manage_stock' => 'boolean',
        'backorder' => 'boolean',
        'attribute_data' => 'array',
        'active' => 'boolean',
        'name_translations' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isOnSale(): bool
    {
        return match (true) {
            !$this->sale_price => false,
            $this->sale_start && Carbon::now()->lt($this->sale_start) => false,
            $this->sale_end && Carbon::now()->gt($this->sale_end) => false,
            default => $this->sale_price < $this->price,
        };
    }

    public function effectivePrice(): float
    {
        return $this->isOnSale() ? (float) $this->sale_price : (float) $this->price;
    }

    protected static function boot(): void
    {
        parent::boot();
        static::saving(function ($model): void {
            $data = $model->attribute_data;
            if (is_string($data)) {
                $decoded = json_decode($data, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data = $decoded;
                } else {
                    $data = [];
                }
            }
            if (is_array($data)) {
                ksort($data);
                // if all attribute values are empty/null, keep attribute_data empty and avoid hashing
                $hasAny = false;
                foreach ($data as $v) {
                    if ($v !== null && $v !== '') {
                        $hasAny = true;
                        break;
                    }
                }
                if (! $hasAny) {
                    $model->attribute_data = [];
                    $model->attribute_hash = null;
                } else {
                    $model->attribute_data = $data; // reassign after sort
                    $model->attribute_hash = hash('sha256', json_encode($data));
                }
            } elseif (! $model->attribute_hash) {
                $model->attribute_hash = null;
            }
        });
    }
}
