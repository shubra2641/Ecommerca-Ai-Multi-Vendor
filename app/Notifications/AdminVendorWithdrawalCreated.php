<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\VendorWithdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminVendorWithdrawalCreated extends Notification
{
    use Queueable;

    protected VendorWithdrawal $withdrawal;

    public function __construct(VendorWithdrawal $withdrawal)
    {
        $this->withdrawal = $withdrawal;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'vendor_withdrawal_created',
            'title' => __('New vendor withdrawal'),
            'message' => __('Vendor :name requested a withdrawal of :amount :currency', [
                'name' => $this->withdrawal->user?->name ?? 'User',
                'amount' => $this->withdrawal->amount,
                'currency' => $this->withdrawal->currency,
            ]),
            'url' => route('admin.vendor.withdrawals.show', $this->withdrawal->id),
            'withdrawal_id' => $this->withdrawal->id,
        ];
    }
}
