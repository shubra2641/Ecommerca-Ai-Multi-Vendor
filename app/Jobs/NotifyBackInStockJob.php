<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\ProductBackInStockNotification;
use App\Models\Product;
use App\Models\ProductInterest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyBackInStockJob implements ShouldQueue
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
        // Only proceed if stock now available
        $available = $product->availableStock();
        if ($available === null || $available <= 0) {
            return;
        }

        $chunkSize = (int) config('interest.mail_chunk', 100);
        $perMinute = (int) config('interest.rate_per_minute', 600);
        $delaySeconds = 0;
        $sentThisMinute = 0;
        $nowMinuteKey = 'notify_rate:'.now()->format('YmdHi');
        $baseCount = cache()->get($nowMinuteKey, 0);
        ProductInterest::where('product_id', $product->id)
            ->active()
            ->whereIn('type', [ProductInterest::TYPE_BACK_IN_STOCK, ProductInterest::TYPE_STOCK])
            ->where('status', ProductInterest::STATUS_PENDING)
            ->orderBy('id')
            ->chunk($chunkSize, function ($chunk) use (&$delaySeconds, &$sentThisMinute, $perMinute, $nowMinuteKey): void {
                /** @var \App\Models\ProductInterest $interest */
                foreach ($chunk as $interest) {
                    if ($sentThisMinute >= $perMinute) {
                        $delaySeconds += 60; // push next batch one minute later
                        $sentThisMinute = 0;
                    }
                    if (\App\Support\MailHelper::mailIsAvailable()) {
                        Mail::to($interest->email)->later(
                            now()->addSeconds($delaySeconds),
                            new ProductBackInStockNotification($interest)
                        );
                    }
                    $interest->markNotified();
                    $sentThisMinute++;
                }
                cache()->put($nowMinuteKey, $sentThisMinute, 120);
            });
    }
}
