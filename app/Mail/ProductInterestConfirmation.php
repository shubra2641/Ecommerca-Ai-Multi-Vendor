<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ProductInterest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductInterestConfirmation extends Mailable
{
    use Queueable;
    use SerializesModels;

    public ProductInterest $interest;

    public function __construct(ProductInterest $interest)
    {
        $this->interest = $interest;
    }

    public function build()
    {
        return $this->subject(__('Subscription confirmed'))
            ->view('emails.product_interest_confirmation', ['interest' => $this->interest]);
    }
}
