<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductRejected extends Mailable
{
    use Queueable;
    use SerializesModels;

    private $product;

    private $reason;

    public function __construct($product, $reason = null)
    {
        $this->product = $product;
        $this->reason = $reason;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getReason()
    {
        return $this->reason;
    }

    public function build()
    {
        return $this->subject('Your product was rejected')
            ->view('emails.products.rejected')
            ->with(['product' => $this->product, 'reason' => $this->reason]);
    }
}
