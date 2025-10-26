<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PayoutExecuted;
use Illuminate\Contracts\Queue\ShouldQueue;

class PayoutExecutedListener implements ShouldQueue
{
    public function handle(PayoutExecuted $event): void
    {
        try {
            $user = $event->payout->user;
            \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\PayoutExecuted($event->payout));
        } catch (\Throwable $e) {
            logger()->warning('Failed to queue payout executed mail: ' . $e->getMessage());
        }
    }
}
