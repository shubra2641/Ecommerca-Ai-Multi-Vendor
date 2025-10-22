<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductRejected extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $product;

    public $reason;

    public function __construct($product, $reason = null)
    {
        $this->product = $product;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Your product was rejected')
            ->view('emails.products.rejected')
            ->with(['product' => $this->product, 'reason' => $this->reason]);
    }
}
