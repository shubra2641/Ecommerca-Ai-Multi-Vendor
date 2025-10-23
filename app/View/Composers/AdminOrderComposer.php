<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\View\Builders\OrderViewBuilder;
use Illuminate\View\View;

final class AdminOrderComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        if (! isset($data['order'])) {
            return;
        }

        $order = $data['order'];
        $addressText = OrderViewBuilder::buildAddressText($order);
        $variantLabels = $this->buildVariantLabels($order);
        $paymentData = $this->extractPaymentData($order);

        $view->with([
            'aovAddressText' => $addressText,
            'aovVariantLabels' => $variantLabels,
            'aovFirstPaymentNote' => $paymentData['firstNote'],
            'aovOfflinePayments' => $paymentData['offlinePayments'],
        ]);
    }

    private function buildVariantLabels($order): array
    {
        $variantLabels = [];
        foreach ($order->items as $item) {
            $variantLabels[$item->id] = OrderViewBuilder::variantLabel($item);
        }

        return $variantLabels;
    }

    private function extractPaymentData($order): array
    {
        $firstPaymentNote = '';
        $offlinePayments = [];

        foreach ($order->payments as $payment) {
            $note = $this->extractPaymentNote($payment);
            if ($firstPaymentNote === '' && $note !== '') {
                $firstPaymentNote = $note;
            }

            if ($this->isOfflinePayment($payment)) {
                $offlinePayments[$payment->id] = true;
            }
        }

        return [
            'firstNote' => $firstPaymentNote,
            'offlinePayments' => $offlinePayments,
        ];
    }

    private function extractPaymentNote($payment): string
    {
        $payload = $payment->payload;

        return match (true) {
            is_array($payload) => $payload['note'] ?? '',
            is_object($payload) => $payload->note ?? '',
            is_string($payload) && $payload !== '' => $this->getNoteFromJson($payload),
            default => '',
        };
    }

    private function getNoteFromJson(string $payload): string
    {
        $decoded = json_decode($payload, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded['note'] ?? '';
        }

        return '';
    }

    private function isOfflinePayment($payment): bool
    {
        $method = strtolower($payment->method ?? '');
        $gateway = strtolower(
            $payment->payload['gateway'] ??
                ($payment->payload['provider'] ?? '')
        );

        return str_contains($method, 'offline') ||
            $method === 'offline' ||
            $gateway === 'offline';
    }
}
