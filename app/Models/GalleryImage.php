<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = [
        'original_path',
        'webp_path',
        'thumbnail_path',
        'title',
        'description',
        'alt',
        'tags',
        'filesize',
        'mime',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'filesize' => 'integer',
    ];

    public function tagsList(): array
    {
        if (! $this->tags) {
            return [];
        }

        return array_values(array_filter(array_map(fn ($t) => trim($t), explode(',', $this->tags))));
    }

    protected function title(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value ? htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8') : null,
        );
    }

    protected function description(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value ? htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8') : null,
        );
    }

    protected function alt(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value ? htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8') : null,
        );
    }
}
