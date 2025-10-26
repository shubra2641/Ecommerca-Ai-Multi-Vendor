<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\VendorWithdrawal;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WithdrawalApproved
{
    use Dispatchable, SerializesModels;

    public VendorWithdrawal $withdrawal;

    public function __construct(VendorWithdrawal $withdrawal)
    {
        $this->withdrawal = $withdrawal;
    }
}
