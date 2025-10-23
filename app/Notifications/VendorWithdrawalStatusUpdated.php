<?php

namespace App\Notifications;

use App\Models\VendorWithdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VendorWithdrawalStatusUpdated extends Notification
{
    use Queueable;

    protected VendorWithdrawal $withdrawal;

    protected string $status;

    public function __construct(VendorWithdrawal $withdrawal, string $status)
    {
        $this->withdrawal = $withdrawal;
        $this->status = $status;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $payload = [
            'type' => 'vendor_withdrawal_status_updated',
            'title' => __('Withdrawal :status', ['status' => $this->status]),
            'message' => __('Your withdrawal request #:id status changed to :status', [
                'id' => $this->withdrawal->id,
                'status' => $this->status,
            ]),
            'url' => route('vendor.withdrawals.index'),
            'withdrawal_id' => $this->withdrawal->id,
            'status' => $this->status,
        ];

        if (! empty($this->withdrawal->proof_path)) {
            $payload['proof_url'] = asset('storage/' . $this->withdrawal->proof_path);
        }

        return $payload;
    }
}
