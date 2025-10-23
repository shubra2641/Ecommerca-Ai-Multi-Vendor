<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageBanner extends Model
{
    protected $fillable = [
        'placement_key', 'image', 'link_url', 'alt_text', 'alt_text_i18n', 'sort_order', 'enabled', 'meta',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'meta' => 'array',
        'alt_text_i18n' => 'array',
    ];
}
