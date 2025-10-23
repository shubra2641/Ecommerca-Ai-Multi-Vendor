<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductApproved extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function build()
    {
        return $this->subject('Your product has been approved')
            ->view('emails.products.approved')
            ->with(['product' => $this->product]);
    }
}
