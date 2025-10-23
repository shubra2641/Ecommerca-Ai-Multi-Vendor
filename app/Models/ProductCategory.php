<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    use HasFactory;

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

    /**
     * Override getAttribute to inject translation resolution for configured attributes.
     */
    public function getAttribute($key)
    {
        if (! isset($this->translatable) || ! in_array($key, $this->translatable, true)) {
            return parent::getAttribute($key);
        }

        $translations = parent::getAttribute($key . '_translations');
        if (! is_array($translations)) {
            return parent::getAttribute($key);
        }

        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale');
        return $translations[$locale] ?? ($fallback ? $translations[$fallback] ?? parent::getAttribute($key) : parent::getAttribute($key));
    }

    /**
     * Helper manual translation fetch if needed in code.
     */
    public function translate(string $field, ?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $fallback = config('app.fallback_locale');
        $translations = parent::getAttribute($field . '_translations');
        if (is_array($translations)) {
            return $translations[$locale] ?? ($fallback ? $translations[$fallback] ?? $this->getAttribute($field) : $this->getAttribute($field));
        }
        return $this->getAttribute($field);
    }
}
