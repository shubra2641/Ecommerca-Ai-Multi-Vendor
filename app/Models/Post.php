<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected array $translatable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'seo_title',
        'seo_description',
        'seo_tags',
    ];

    protected $fillable = [
        'slug',
        'title',
        'body',
        'excerpt',
        'seo_title',
        'seo_description',
        'seo_tags',
        'published',
        'published_at',
        'user_id',
        'category_id',
        'featured_image',

        'title_translations',
        'slug_translations',
        'excerpt_translations',
        'body_translations',
        'seo_title_translations',
        'seo_description_translations',
        'seo_tags_translations',
    ];

    protected $casts = [
        'published' => 'boolean',
        'published_at' => 'datetime',

        'title_translations' => 'array',
        'slug_translations' => 'array',
        'excerpt_translations' => 'array',
        'body_translations' => 'array',
        'seo_title_translations' => 'array',
        'seo_description_translations' => 'array',
        'seo_tags_translations' => 'array',
    ];

    public function scopePublished($q)
    {
        return $q->whereNotNull('published_at')->where('published_at', '<=', now());
    }

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

    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'category_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }
}
