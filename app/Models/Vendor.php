<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
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
        'balance' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::addGlobalScope('vendor', function ($builder) {
            $builder->where('role', 'vendor');
        });
    }

    public function getIsActiveAttribute()
    {
        return ! is_null($this->approved_at);
    }

    public function getIsApprovedAttribute()
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

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function getTotalBalanceAttribute()
    {
        return $this->balance ?? 0;
    }
}
