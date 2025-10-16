<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    protected $fillable = [
        'key', 'title_i18n', 'subtitle_i18n', 'enabled', 'sort_order', 'item_limit', 'config',
        'cta_enabled', 'cta_url', 'cta_label_i18n',
    ];

    protected $casts = [
        'title_i18n' => 'array',
        'subtitle_i18n' => 'array',
        'cta_label_i18n' => 'array',
        'enabled' => 'boolean',
        'cta_enabled' => 'boolean',
        'config' => 'array',
    ];
}
