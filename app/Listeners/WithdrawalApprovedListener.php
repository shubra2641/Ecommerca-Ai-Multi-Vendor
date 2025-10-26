<?php

namespace App\Listeners;

use App\Events\WithdrawalApproved;
use Illuminate\Contracts\Queue\ShouldQueue;

class WithdrawalApprovedListener implements ShouldQueue
{
    public function handle(WithdrawalApproved $event): void
    {
        try {
            $event->withdrawal->user->notify(new \App\Notifications\VendorWithdrawalStatusUpdated($event->withdrawal, 'approved'));
        } catch (\Throwable $e) {
            logger()->warning('Vendor notification failed: ' . $e->getMessage());
        }
    }
}