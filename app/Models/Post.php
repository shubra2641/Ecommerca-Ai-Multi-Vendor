<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\TranslatableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes, TranslatableTrait;

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

    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image ? \App\Helpers\GlobalHelper::storageImageUrl($this->featured_image) : asset('images/placeholder.png');
    }

    /**
     * Get the body content converted from Markdown to HTML
     */
    public function getBodyHtmlAttribute()
    {
        // Simple conversion: **text** to <strong>text</strong>
        $text = str_replace('**', '<strong>', $this->body);
        $text = str_replace('**', '</strong>', $text);
        
        // Convert line breaks to <br>
        return nl2br($text);
    }
}
