<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ProductInterest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductBackInStockNotification extends Mailable
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
        return $this->subject(__('Product back in stock'))
            ->view('emails.product_back_in_stock', ['interest' => $this->interest]);
    }
}
