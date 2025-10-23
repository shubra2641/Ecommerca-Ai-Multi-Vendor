<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\View\Builders\OrderViewBuilder;
use Illuminate\View\View;

class AdminOrderComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        if (! isset($data['order'])) {
            return;
        }
        $order = $data['order'];

        // Address text (shipping preferred then billing)
        $addressText = OrderViewBuilder::buildAddressText($order);

        // Variant labels per item
        $variantLabels = [];
        foreach ($order->items as $it) {
            $variantLabels[$it->id] = OrderViewBuilder::variantLabel($it);
        }

        // First payment note extraction & offline detection map
        $firstPaymentNote = '';
        $offlinePayments = [];
        foreach ($order->payments as $payment) {
            $payload = $payment->payload;
            $note = '';
            try {
                if (is_array($payload)) {
                    $note = $payload['note'] ?? '';
                } elseif (is_object($payload)) {
                    $note = $payload->note ?? '';
                } elseif (is_string($payload) && $payload !== '') {
                    $decoded = json_decode($payload, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $note = $decoded['note'] ?? '';
                    }
                }
            } catch (\Throwable $e) {
                $note = '';
            }
            if ($firstPaymentNote === '' && $note !== '') {
                $firstPaymentNote = $note;
            }

            $method = strtolower($payment->method ?? '');
            $gateway = strtolower($payment->payload['gateway'] ?? ($payment->payload['provider'] ?? ''));
            $offline = str_contains($method, 'offline') || $method === 'offline' || $gateway === 'offline';
            if ($offline) {
                $offlinePayments[$payment->id] = true;
            }
        }

        $view->with([
            'aovAddressText' => $addressText,
            'aovVariantLabels' => $variantLabels,
            'aovFirstPaymentNote' => $firstPaymentNote,
            'aovOfflinePayments' => $offlinePayments,
        ]);
    }
}
