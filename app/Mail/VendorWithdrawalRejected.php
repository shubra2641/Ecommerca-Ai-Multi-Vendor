<?php

namespace App\Mail;

use App\Models\VendorWithdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorWithdrawalRejected extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public $withdrawal;

    public function __construct(VendorWithdrawal $withdrawal)
    {
        $this->withdrawal = $withdrawal;
    }

    public function build()
    {
        return $this->subject(__('Your withdrawal request was rejected'))
            ->view('emails.vendors.withdrawal_rejected')
            ->with(['withdrawal' => $this->withdrawal]);
    }
}
