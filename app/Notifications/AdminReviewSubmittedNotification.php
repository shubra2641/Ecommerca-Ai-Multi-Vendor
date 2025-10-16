<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminReviewSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $review;

    public function __construct($review)
    {
        $this->review = $review;
    }

    public function via(object $notifiable): array
    {
        $via = ['database'];
        if (\App\Support\MailHelper::mailIsAvailable()) {
            $via[] = 'mail';
        }

        return $via;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'review_submitted',
            'title' => __('New product review'),
            'message' => __('Review for :product needs moderation', ['product' => $this->review->product?->name ?? '']),
            'url' => route('admin.products.reviews.show', $this->review->id),
            'icon' => 'star',
        ];
    }
}
