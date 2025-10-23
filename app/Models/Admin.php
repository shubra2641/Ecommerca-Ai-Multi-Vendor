<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'phone_number',
        'whatsapp_number',
        'approved_at',
        'balance',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'approved_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getIsActiveAttribute()
    {
        return ! is_null($this->approved_at);
    }

    public function scopeActive($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('approved_at');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('admin', function ($builder): void {
            $builder->where('role', 'admin');
        });
    }
}
