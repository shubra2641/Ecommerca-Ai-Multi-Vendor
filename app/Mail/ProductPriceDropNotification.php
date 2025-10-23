<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ProductInterest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductPriceDropNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    private ProductInterest $interest;

    private float $oldPrice;

    private float $newPrice;

    private float $percent;

    public function __construct(ProductInterest $interest, float $oldPrice, float $newPrice, float $percent)
    {
        $this->interest = $interest;
        $this->oldPrice = $oldPrice;
        $this->newPrice = $newPrice;
        $this->percent = $percent;
    }

    public function build()
    {
        return $this->subject(__('Price dropped for a product you follow'))
            ->view('emails.product_price_drop', [
                'interest' => $this->interest,
                'oldPrice' => $this->oldPrice,
                'newPrice' => $this->newPrice,
                'percent' => $this->percent,
            ]);
    }
}
