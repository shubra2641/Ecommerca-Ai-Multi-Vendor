<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Language Model
 *
 * Represents a language configuration for the application.
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $flag
 * @property bool $is_default
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'flag',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];
}
