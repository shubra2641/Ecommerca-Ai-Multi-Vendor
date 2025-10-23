<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    use HasFactory;

    protected array $translatable = [
        'name',
        'slug',
        'description',
        'seo_title',
        'seo_description',
        'seo_tags',
    ];

    protected $fillable = [
        'slug',
        'name',
        'description',
        'seo_title',
        'seo_description',
        'seo_tags',
        'parent_id',
        'name_translations',
        'slug_translations',
        'description_translations',
        'seo_title_translations',
        'seo_description_translations',
        'seo_tags_translations',
    ];

    protected $casts = [
        'name_translations' => 'array',
        'slug_translations' => 'array',
        'seo_title_translations' => 'array',
        'seo_description_translations' => 'array',
        'seo_tags_translations' => 'array',
    ];

    public function parent()
    {
        return $this->belongsTo(PostCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PostCategory::class, 'parent_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id');
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
