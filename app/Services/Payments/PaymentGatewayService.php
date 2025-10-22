<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    public function initPayPal(Order $order, PaymentGateway $gateway): array
    {
        return $this->initPayPalPayment($order, $gateway, $order->id);
    }

    public function initPayPalFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->initPayPalPayment(null, $gateway, null, $snapshot);
    }

    public function initTap(Order $order, PaymentGateway $gateway): array
    {
        return $this->initTapPayment($order, $gateway, $order->id);
    }

    public function initTapFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->initTapPayment(null, $gateway, null, $snapshot);
    }

    public function initPaytabsFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->initGenericGateway($snapshot, $gateway, 'paytabs');
    }

    public function initWeacceptFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->initGenericGateway($snapshot, $gateway, 'weaccept');
    }

    public function initPayeerFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->initGenericGateway($snapshot, $gateway, 'payeer');
    }

    public function verifyTapCharge(Payment $payment, PaymentGateway $gateway): array
    {
        $gw = new \App\Services\Payments\Gateways\TapGateway();
        return $gw->verifyCharge($payment, $gateway);
    }

    public function verifyGenericGatewayCharge(Payment $payment, PaymentGateway $gateway): array
    {
        $chargeId = $payment->payload[$gateway->slug . '_charge_id'] ?? $payment->payload['charge_id'] ?? null;
        $cfg = $gateway->config ?? [];
        $secret = $cfg['secret_key'] ?? ($cfg['api_key'] ?? null);

        if (!$secret || !$chargeId) {
            throw new \RuntimeException('Missing gateway secret or charge id for verify');
        }

        $apiBase = rtrim($cfg['api_base'] ?? ('https://api.' . $gateway->slug . '.com'), '/');

        try {
            $resp = Http::withToken($secret)->acceptJson()->get($apiBase . '/charges/' . $chargeId);

            if (!$resp->ok()) {
                return ['payment' => $gateway, 'status' => 'pending', 'charge' => null];
            }

            $json = $resp->json();
            $status = $json['status'] ?? $json['data']['status'] ?? null;

            if (in_array(strtoupper($status), ['CAPTURED', 'AUTHORIZED', 'PAID', 'SUCCESS'], true)) {
                $finalStatus = 'paid';
            } elseif (in_array(strtoupper($status), ['FAILED', 'CANCELLED', 'DECLINED'], true)) {
                $finalStatus = 'failed';
            } else {
                $finalStatus = 'processing';
            }

            $payment->status = $finalStatus;
            $payment->payload = array_merge($payment->payload ?? [], [
                $gateway->slug . '_charge_status' => $finalStatus
            ]);
            $payment->save();

            if ($payment->status === 'paid') {
                $order = $payment->order;
                if (!$order) {
                    $order = $this->createOrderFromSnapshot($payment);
                }

                if ($order && $order->status !== 'paid') {
                    $order->status = 'paid';
                    $order->save();
                }

                try {
                    session()->forget('cart');
                } catch (\Throwable $_) {
                    // Ignore cart clearing errors
                }
            }

            return ['payment' => $payment, 'status' => $payment->status, 'charge' => $json];
        } catch (\Throwable $e) {
            return ['success' => false, 'status' => 'pending', 'data' => null];
        }
    }
}
