<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'vendor_withdrawal_id',
        'user_id',
        'amount',
        'currency',
        'status',
        'admin_note',
        'executed_at',
        'proof_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'executed_at' => 'datetime',
        'proof_path' => 'string',
    ];

    public function withdrawal()
    {
        return $this->belongsTo(VendorWithdrawal::class, 'vendor_withdrawal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
