<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;

    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment, string $status)
    {
        $this->payment = $payment;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Add email for important status changes
        if (in_array($this->status, ['completed', 'paid', 'success', 'failed', 'refunded'])) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->payment->order;
        $subject = $this->getEmailSubject();
        $greeting = $this->getEmailGreeting($notifiable);

        $mail = (new MailMessage())
            ->subject($subject)
            ->greeting($greeting)
            ->line($this->getStatusMessage())
            ->line('Payment Details:')
            ->line('Payment ID: ' . $this->payment->payment_id)
            ->line('Amount: ' . $this->payment->amount . ' ' . $this->payment->currency)
            ->line('Gateway: ' . ucfirst($this->payment->gateway))
            ->line('Status: ' . ucfirst($this->status));

        if ($order) {
            $mail->line('Order ID: ' . $order->id);
        }

        // Add action button based on status
        if (in_array($this->status, ['completed', 'paid', 'success'])) {
            $mail->action('View Order', url('/orders/' . $order->id));
        } elseif (in_array($this->status, ['failed', 'cancelled'])) {
            $mail->action('Try Again', url('/checkout/' . $order->id));
        }

        $mail->line('Thank you for using our service!');

        return $mail;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'payment_status_updated',
            'payment_id' => $this->payment->payment_id,
            'order_id' => $this->payment->order_id,
            'status' => $this->status,
            'gateway' => $this->payment->gateway,
            'amount' => $this->payment->amount,
            'currency' => $this->payment->currency,
            'message' => $this->getStatusMessage(),
            'action_url' => $this->getActionUrl(),
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable): bool
    {
        // Don't send duplicate notifications
        $existingNotification = $notifiable->notifications()
            ->where('type', self::class)
            ->where('data->payment_id', $this->payment->payment_id)
            ->where('data->status', $this->status)
            ->where('created_at', '>', now()->subMinutes(5))
            ->exists();

        return ! $existingNotification;
    }

    /**
     * Get email subject based on status.
     */
    private function getEmailSubject(): string
    {
        switch ($this->status) {
            case 'completed':
            case 'paid':
            case 'success':
                return 'Payment Successful - Order Confirmed';
            case 'failed':
                return 'Payment Failed - Action Required';
            case 'cancelled':
                return 'Payment Cancelled';
            case 'refunded':
                return 'Payment Refunded';
            case 'pending':
                return 'Payment Pending';
            default:
                return 'Payment Status Updated';
        }
    }

    /**
     * Get email greeting.
     */
    private function getEmailGreeting(object $notifiable): string
    {
        $name = $notifiable->name ?? 'Customer';

        return "Hello {$name},";
    }

    /**
     * Get status message.
     */
    private function getStatusMessage(): string
    {
        switch ($this->status) {
            case 'completed':
            case 'paid':
            case 'success':
                return 'Your payment has been successfully processed and your order is confirmed.';
            case 'failed':
                return 'Unfortunately, your payment could not be processed. Please try again or use a different payment method.';
            case 'cancelled':
                return 'Your payment has been cancelled. You can try again if you wish to complete your order.';
            case 'refunded':
                return 'Your payment has been refunded. The amount will be credited back to your original payment method.';
            case 'pending':
                return 'Your payment is being processed. We will notify you once the payment is confirmed.';
            case 'expired':
                return 'Your payment session has expired. Please start a new payment process.';
            default:
                return "Your payment status has been updated to: {$this->status}";
        }
    }

    /**
     * Get action URL based on status.
     */
    private function getActionUrl(): ?string
    {
        $order = $this->payment->order;

        if (! $order) {
            return null;
        }

        switch ($this->status) {
            case 'completed':
            case 'paid':
            case 'success':
                return url('/orders/' . $order->id);
            case 'failed':
            case 'cancelled':
            case 'expired':
                return url('/checkout/' . $order->id);
            default:
                return url('/orders/' . $order->id);
        }
    }
}
