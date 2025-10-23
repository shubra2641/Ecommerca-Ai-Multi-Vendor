<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Concerns\TranslatableTrait;

class ProductCategory extends Model
{
    use HasFactory, TranslatableTrait;

    protected array $translatable = [
        'name',
        'description',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'name_translations',
        'description_translations',
        'image',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'position',
        'commission_rate',
        'active',
    ];

    protected $casts = [
        'name_translations' => 'array',
        'description_translations' => 'array',
        'commission_rate' => 'decimal:2',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
