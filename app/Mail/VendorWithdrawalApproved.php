<?php

namespace App\Mail;

use App\Models\VendorWithdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorWithdrawalApproved extends Mailable implements ShouldQueue
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
        return $this->subject(__('Your withdrawal has been approved'))
            ->view('emails.vendors.withdrawal_approved')
            ->with(['withdrawal' => $this->withdrawal]);
    }
}
