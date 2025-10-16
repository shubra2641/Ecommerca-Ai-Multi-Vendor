<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAttachment extends Model
{
    protected $fillable = ['payment_id', 'path', 'mime', 'user_id'];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
