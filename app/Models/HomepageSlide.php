<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSlide extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'title_i18n', 'subtitle_i18n', 'button_text', 'button_text_i18n', 'image', 'link_url', 'sort_order', 'enabled', 'meta',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'meta' => 'array',
        'title_i18n' => 'array',
        'subtitle_i18n' => 'array',
        'button_text_i18n' => 'array',
    ];
}
