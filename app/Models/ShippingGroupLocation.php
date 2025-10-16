<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingGroupLocation extends Model
{
    use HasFactory;

    protected $fillable = ['shipping_group_id', 'country_id', 'governorate_id', 'city_id', 'price', 'estimated_days'];

    public function group()
    {
        return $this->belongsTo(ShippingGroup::class, 'shipping_group_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
