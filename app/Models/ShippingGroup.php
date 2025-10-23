<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'default_price', 'estimated_days', 'active'];

    public function locations()
    {
        return $this->hasMany(ShippingGroupLocation::class);
    }
}
