<?php

namespace App\Jobs;

use App\Mail\ProductPriceDropNotification;
use App\Models\Product;
use App\Models\ProductInterest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyPriceDropJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $productId;

    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }

    public function handle(): void
    {
        $product = Product::find($this->productId);
        if (! $product) {
            return;
        }
        $chunkSize = (int) config('interest.mail_chunk', 100);
        $oldPrice = (float) ($product->last_sale_price ?? $product->last_price ?? 0);
        $currentBase = $product->sale_price && $product->sale_price < $product->price
            ? (float) $product->sale_price
            : (float) $product->price;
        if ($oldPrice <= 0 || $currentBase <= 0 || $currentBase >= $oldPrice) {
            return;
        }
        $percent = (($oldPrice - $currentBase) / $oldPrice) * 100;
        ProductInterest::where('product_id', $product->id)
            ->active()
            ->where('type', ProductInterest::TYPE_PRICE_DROP)
            ->where('status', ProductInterest::STATUS_PENDING)
            ->orderBy('id')
            ->chunk($chunkSize, function ($chunk) use ($oldPrice, $currentBase, $percent) {
                foreach ($chunk as $interest) {
                    if (\App\Support\MailHelper::mailIsAvailable()) {
                        Mail::to($interest->email)->queue(new ProductPriceDropNotification(
                            $interest,
                            $oldPrice,
                            $currentBase,
                            $percent
                        ));
                    }
                    $interest->markNotified();
                }
            });
    }
}
