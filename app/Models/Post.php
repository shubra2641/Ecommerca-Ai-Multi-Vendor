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
        return $this->markdownToHtml($this->body);
    }

    /**
     * Get the excerpt content converted from Markdown to HTML
     */
    public function getExcerptHtmlAttribute()
    {
        return $this->markdownToHtml($this->excerpt);
    }

    /**
     * Convert simple Markdown to HTML
     */
    private function markdownToHtml(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // Convert **text** to <strong>text</strong>
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
        
        // Convert *text* to <em>text</em>
        $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
        
        // Convert ## Heading to <h2>Heading</h2>
        $text = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $text);
        
        // Convert # Heading to <h1>Heading</h1>
        $text = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $text);
        
        // Convert ### Heading to <h3>Heading</h3>
        $text = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $text);
        
        // Convert #### Heading to <h4>Heading</h4>
        $text = preg_replace('/^#### (.*?)$/m', '<h4>$1</h4>', $text);
        
        // Convert - item to <li>item</li>
        $text = preg_replace('/^- (.*?)$/m', '<li>$1</li>', $text);
        
        // Wrap consecutive <li> elements in <ul>
        $text = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $text);
        
        // Convert line breaks to <br>
        $text = nl2br($text);
        
        // Convert paragraphs (double line breaks)
        $text = preg_replace('/<br\s*\/?>\s*<br\s*\/?>/', '</p><p>', $text);
        $text = '<p>' . $text . '</p>';
        
        return $text;
    }
}
