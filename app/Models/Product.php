<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\TranslatableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Product extends Model
{
    use HasFactory, TranslatableTrait;

    protected array $translatable = [
        'name',
        'slug',
        'short_description',
        'description',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected $fillable = [
        'vendor_id',
        'product_category_id',
        'type',
        'physical_type',
        'sku',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'sale_price',
        'sale_start',
        'sale_end',
        'main_image',
        'gallery',
        'manage_stock',
        'stock_qty',
        'reserved_qty',
        'backorder',
        'is_featured',
        'is_best_seller',
        'approved_reviews_count',
        'approved_reviews_avg',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'active',
        'download_file',
        'download_url',
        'has_serials',
        'used_attributes',

        'name_translations',
        'slug_translations',
        'short_description_translations',
        'description_translations',
        'seo_title_translations',
        'seo_description_translations',
        'seo_keywords_translations',

        // logistics
        'refund_days',
        'weight',
        'length',
        'width',
        'height',
    ];

    protected $casts = [
        'sale_start' => 'datetime',
        'sale_end' => 'datetime',
        'gallery' => 'array',
        'manage_stock' => 'boolean',
        'backorder' => 'boolean',
        'is_featured' => 'boolean',
        'is_best_seller' => 'boolean',
        'active' => 'boolean',
        'has_serials' => 'boolean',

        'name_translations' => 'array',
        'slug_translations' => 'array',
        'short_description_translations' => 'array',
        'description_translations' => 'array',
        'seo_title_translations' => 'array',
        'seo_description_translations' => 'array',
        'seo_keywords_translations' => 'array',

        'refund_days' => 'integer',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'used_attributes' => 'array',
    ];

    // reviews relation
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'vendor_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class, 'product_product_tag');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function serials(): HasMany
    {
        return $this->hasMany(\App\Models\ProductSerial::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($q)
    {
        return $q->where('active', 1);
    }

    public function scopeFeatured($q)
    {
        return $q->where('is_featured', 1);
    }

    public function scopeBestSeller($q)
    {
        return $q->where('is_best_seller', 1);
    }

    public function scopeOnSale($q)
    {
        $now = Carbon::now();

        return $q->whereNotNull('sale_price')
            ->whereColumn('sale_price', '<', 'price')
            ->where(function ($qq) use ($now): void {
                $qq->whereNull('sale_start')->orWhere('sale_start', '<=', $now);
            })
            ->where(function ($qq) use ($now): void {
                $qq->whereNull('sale_end')->orWhere('sale_end', '>=', $now);
            });
    }

    public function isOnSale(): bool
    {
        return $this->scopeOnSale(static::query()->where('id', $this->id))->exists();
    }

    public function effectivePrice(): float
    {
        if ($this->type === 'variable') {
            return $this->variations->filter(fn($v) => $v->active)->map(fn($v) => $v->effectivePrice())->min() ?? (float) $this->price;
        }

        return $this->isOnSale() ? (float) $this->sale_price : (float) $this->price;
    }

    public function availableStock(): ?int
    {
        // If product is digital (either type or physical_type indicates digital)
        if (($this->type ?? null) === 'digital' || ($this->physical_type ?? null) === 'digital') {
            // if serials are used, count unsold serials; if none exist, treat as unlimited (null)
            $totalSerials = $this->serials()->count();
            if ($totalSerials > 0) {
                return $this->serials()->whereNull('sold_at')->count();
            }

            return null;
        }
        if (! $this->manage_stock) {
            return null;
        }

        return max(0, (int) $this->stock_qty - (int) $this->reserved_qty);
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Product $p): void {
            if ($p->vendor_id && $p->active === null) {
                $p->active = false; // force review state
            }
        });
    }
}
