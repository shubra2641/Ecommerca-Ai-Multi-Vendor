<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductPendingForReview extends Mailable
{
    use Queueable;
    use SerializesModels;

    private $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function build()
    {
        return $this->subject('New product pending review')
            ->view('emails.products.pending_review')
            ->with(['product' => $this->product]);
    }
}
