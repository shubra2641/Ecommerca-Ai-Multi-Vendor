<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\TranslatableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
