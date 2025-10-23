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

    public function getAttribute($key)
    {
        if (! isset($this->translatable) || ! in_array($key, $this->translatable, true)) {
            return parent::getAttribute($key);
        }

        $translationsKey = $key . '_translations';
        $raw = parent::getAttribute($key);
        $translations = parent::getAttribute($translationsKey);
        if (! is_array($translations)) {
            return $raw;
        }

        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale');
        $translated = match (true) {
            isset($translations[$locale]) && $translations[$locale] !== '' => $translations[$locale],
            $fallback && isset($translations[$fallback]) && $translations[$fallback] !== '' => $translations[$fallback],
            default => null,
        };
        if ($translated !== null) {
            return $translated;
        }

        return $raw;
    }

    public function translate(string $field, ?string $locale = null)
    {
        if (! isset($this->translatable) || ! in_array($field, $this->translatable, true)) {
            return $this->getAttribute($field);
        }

        $translations = parent::getAttribute($field . '_translations');
        if (! is_array($translations)) {
            return $this->getAttribute($field);
        }

        $locale = $locale ?: app()->getLocale();
        $fallback = config('app.fallback_locale');
        $translated = $translations[$locale] ?? ($fallback ? $translations[$fallback] ?? null : null);
        if ($translated !== null && $translated !== '') {
            return $translated;
        }
        return $this->getAttribute($field);
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
