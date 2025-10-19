<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRule extends Model
{
    protected $fillable = [
        'zone_id',
        'country_id',
        'governorate_id',
        'city_id',
        'price',
        'estimated_days',
        'active',
    ];

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'zone_id');
    }

    public function scopeMatchLocation($query, $country = null, $governorate = null, $city = null)
    {
        return $query->when($city, fn ($q) => $q->where('city_id', $city))
            ->when(! $city && $governorate, fn ($q) => $q->where('governorate_id', $governorate)->whereNull('city_id'))
            ->when(! $city && ! $governorate && $country, fn ($q) => $q->where('country_id', $country)->whereNull('governorate_id')->whereNull('city_id'));
    }
}
