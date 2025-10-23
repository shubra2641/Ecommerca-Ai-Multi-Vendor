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

    /**
     * Override getAttribute to inject translation resolution for configured attributes.
     */
    public function getAttribute($key)
    {
        // if key is explicitly requested translations array, return normal behavior
        if (isset($this->translatable) && in_array($key, $this->translatable, true)) {
            $translationsKey = $key . '_translations';
            $raw = parent::getAttribute($key); // base stored value
            $translations = parent::getAttribute($translationsKey);
            if (is_array($translations)) {
                $locale = app()->getLocale();
                $fallback = config('app.fallback_locale');
                $translated = $translations[$locale] ?? ($fallback ? $translations[$fallback] ?? null : null);

                return $translated !== null && $translated !== '' ? $translated : $raw;
            }

            return $raw; // fallback to raw column value
        }

        return parent::getAttribute($key);
    }

    /**
     * Helper manual translation fetch if needed in code.
     */
    public function translate(string $field, ?string $locale = null)
    {
        if (! isset($this->translatable)) {
            return $this->getAttribute($field);
        }

        if (! in_array($field, $this->translatable, true)) {
            return $this->getAttribute($field);
        }

        $translations = parent::getAttribute($field . '_translations');
        if (! is_array($translations)) {
            return $this->getAttribute($field);
        }

        $locale = $locale ? $locale : app()->getLocale();
        $fallback = config('app.fallback_locale');
        $translated = $translations[$locale] ?? ($fallback ? $translations[$fallback] ?? null : null);

        return $translated !== null && $translated !== '' ? $translated : $this->getAttribute($field);
    }

    // backward compatibility for existing blades calling ->translated('field')
    public function translated(string $field, ?string $lang = null)
    {
        return $this->translate($field, $lang);
    }

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
