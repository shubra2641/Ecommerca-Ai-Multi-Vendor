<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorWithdrawal extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'status',
        'notes',
        'admin_note',
        'approved_at',
        'held_at',
        'proof_path',
        'payment_method',
        'reference',
        'gross_amount',
        'commission_amount',
        'commission_amount_exact',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_amount_exact' => 'decimal:4',
        'approved_at' => 'datetime',
        'held_at' => 'datetime',
        'proof_path' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payout()
    {
        return $this->hasOne(\App\Models\Payout::class, 'vendor_withdrawal_id');
    }
}
