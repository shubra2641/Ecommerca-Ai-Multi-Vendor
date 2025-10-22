<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $fillable = ['name', 'code', 'active'];

    public function rules()
    {
        return $this->hasMany(ShippingRule::class, 'zone_id');
    }
}
