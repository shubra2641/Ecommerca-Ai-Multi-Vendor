<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'whatsapp_number',
        'approved_at',
        'balance',
        'transfer_details',
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
    ];

    // Relationships

    public function balanceHistories()
    {
        return $this->hasMany(BalanceHistory::class);
    }

    public function adminBalanceHistories()
    {
        return $this->hasMany(BalanceHistory::class, 'admin_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function products()
    {
        return $this->hasMany(\App\Models\Product::class, 'vendor_id');
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    // Scopes
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeActive($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('approved_at');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeVendors($query)
    {
        return $query->where('role', 'vendor');
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', 'user');
    }

    // Accessors
    public function getIsActiveAttribute(): bool
    {
        return ! is_null($this->approved_at);
    }

    public function getIsApprovedAttribute(): bool
    {
        return ! is_null($this->approved_at);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return number_format((float) $this->balance, 2);
    }

    public function getProfileCompletionAttribute(): int
    {
        $fields = 0;
        $filled = 0;
        $check = [
            'name' => 15,
            'email_verified_at' => 20,
            'phone_number' => 15,
            'whatsapp_number' => 10,
            'approved_at' => 15,
        ];
        foreach ($check as $field => $weight) {
            $fields += $weight;
            if (! empty($this->{$field})) {
                $filled += $weight;
            }
        }
        // addresses weight
        $fields += 25;
        if ($this->relationLoaded('addresses')) {
            $hasAddr = $this->addresses->count() > 0;
        } else {
            $hasAddr = $this->addresses()->exists();
        }
        if ($hasAddr) {
            $filled += 25;
        }

        return max(5, min(100, (int) round($filled / $fields * 100)));
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'user';
    }

    public function approve(): self
    {
        $this->update(['approved_at' => now()]);

        return $this;
    }

    public function suspend(): self
    {
        $this->update(['approved_at' => null]);

        return $this;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2',
            'transfer_details' => 'array',
        ];
    }
}
