<?php

namespace App\Jobs;

use App\Mail\ProductInterestConfirmation;
use App\Models\ProductInterest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInterestConfirmationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $interestId;

    public function __construct(int $interestId)
    {
        $this->interestId = $interestId;
    }

    public function handle(): void
    {
        $interest = ProductInterest::find($this->interestId);
        if (! $interest) {
            return;
        }
        if (\App\Support\MailHelper::mailIsAvailable()) {
            Mail::to($interest->email)->send(new ProductInterestConfirmation($interest));
        }
    }
}
