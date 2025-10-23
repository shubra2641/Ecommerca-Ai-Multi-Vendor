<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'iso_code', 'active'];

    public function governorates()
    {
        return $this->hasMany(Governorate::class);
    }
}
