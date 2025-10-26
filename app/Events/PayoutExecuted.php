<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Payout;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PayoutExecuted
{
    use Dispatchable, SerializesModels;

    public Payout $payout;

    public function __construct(Payout $payout)
    {
        $this->payout = $payout;
    }
}
