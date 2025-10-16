<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'label', 'title', 'name', 'phone', 'country_id', 'governorate_id', 'city_id', 'line1', 'line2', 'postal_code', 'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
