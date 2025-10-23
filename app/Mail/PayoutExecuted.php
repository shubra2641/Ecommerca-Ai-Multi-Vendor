<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Payout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayoutExecuted extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    private Payout $payout;

    public function __construct(Payout $payout)
    {
        $this->payout = $payout;
    }

    public function build()
    {
        return $this->subject(__('Your payout has been executed'))
            ->view('emails.vendors.payout_executed')
            ->with(['payout' => $this->payout]);
    }
}
