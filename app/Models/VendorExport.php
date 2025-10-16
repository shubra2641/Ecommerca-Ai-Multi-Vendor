<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorExport extends Model
{
    protected $fillable = ['vendor_id', 'filename', 'status', 'filters', 'path', 'completed_at'];

    protected $casts = ['filters' => 'array', 'completed_at' => 'datetime'];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
