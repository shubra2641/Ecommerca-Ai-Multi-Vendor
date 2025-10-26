<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\WithdrawalRejected;
use Illuminate\Contracts\Queue\ShouldQueue;

class WithdrawalRejectedListener implements ShouldQueue
{
    public function handle(WithdrawalRejected $event): void
    {
        try {
            $event->withdrawal->user->notify(new \App\Notifications\VendorWithdrawalStatusUpdated($event->withdrawal, 'rejected'));
        } catch (\Throwable $e) {
            logger()->warning('Vendor notification failed: ' . $e->getMessage());
        }
    }
}
