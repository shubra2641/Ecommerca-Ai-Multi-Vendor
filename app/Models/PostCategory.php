<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TranslatableTrait;

class PostCategory extends Model
{
    use HasFactory, TranslatableTrait;

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
        'description_translations' => 'array',
        'seo_title_translations' => 'array',
        'seo_description_translations' => 'array',
        'seo_tags_translations' => 'array',
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
                if (isset($translations[$locale]) && $translations[$locale] !== '') {
                    return $translations[$locale];
                }
                if ($fallback && isset($translations[$fallback]) && $translations[$fallback] !== '') {
                    return $translations[$fallback];
                }
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
        if (! isset($this->translatable) || ! in_array($field, $this->translatable, true)) {
            return $this->getAttribute($field);
        }
        $translations = parent::getAttribute($field . '_translations');
        $locale = $locale ? $locale : app()->getLocale();
        $fallback = config('app.fallback_locale');
        if (is_array($translations)) {
            if (isset($translations[$locale]) && $translations[$locale] !== '') {
                return $translations[$locale];
            }
            if ($fallback && isset($translations[$fallback]) && $translations[$fallback] !== '') {
                return $translations[$fallback];
            }
        }

        return parent::getAttribute($field);
    }

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
}
