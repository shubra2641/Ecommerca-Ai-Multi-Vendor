<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class PaymentService
{
    public function getCredentials(PaymentGateway $gateway): array
    {
        if (is_array($gateway->config)) {
            return $this->mergeWithDedicatedFields($gateway, $gateway->config);
        }

        $decrypted = $this->decryptConfig($gateway->config);
        if ($decrypted) {
            return $this->mergeWithDedicatedFields($gateway, $decrypted);
        }

        return $this->getDefaultCredentials();
    }

    public function updateCredentials(PaymentGateway $gateway, array $credentials): void
    {
        [$dedicatedFields, $configFields] = $this->categorizeCredentials($credentials);

        $this->updateDedicatedFields($gateway, $dedicatedFields);
        $this->updateConfigFields($gateway, $configFields);
    }

    public function hasValidCredentials(PaymentGateway $gateway): bool
    {
        $credentials = $this->getCredentials($gateway);
        $requiredFields = $this->getRequiredFields($gateway->driver);

        foreach ($requiredFields as $field) {
            if (! $this->hasField($credentials, $field)) {
                return false;
            }
        }

        return true;
    }

    public function getGatewayConfig(PaymentGateway $gateway, ?string $key = null)
    {
        $credentials = $this->getCredentials($gateway);

        if (empty($credentials)) {
            return $key ? null : [];
        }

        return $key ? ($credentials[$key] ?? null) : $credentials;
    }

    public function getMaskedCredentials(PaymentGateway $gateway): array
    {
        $credentials = $this->getCredentials($gateway);
        $masked = [];

        foreach ($credentials as $key => $value) {
            if (is_string($value) && strlen($value) > 4) {
                $masked[$key] = substr($value, 0, 4) . '****';
            } else {
                $masked[$key] = $value;
            }
        }

        return $masked;
    }

    public function handlePayPalReturn(Payment $payment)
    {
        if ($payment->method !== 'paypal' || $payment->status !== 'pending') {
            return redirect('/')->with('error', __('Invalid payment state'));
        }

        $cfg = $payment->paymentGateway?->config ?? [];
        $clientId = $cfg['paypal_client_id'] ?? null;
        $secret = $cfg['paypal_secret'] ?? null;
        $mode = ($cfg['paypal_mode'] ?? 'sandbox') === 'live' ? 'live' : 'sandbox';

        $environment = $mode === 'live' ? new ProductionEnvironment($clientId, $secret) : new SandboxEnvironment($clientId, $secret);
        $client = new PayPalHttpClient($environment);

        $orderId = $payment->payload['paypal_order_id'] ?? null;
        if (! $orderId) {
            throw new \Exception('missing_order');
        }

        $request = new OrdersCaptureRequest($orderId);
        $response = $client->execute($request);

        if ($response->statusCode === 201) {
            $payment->status = 'completed';
            $payment->completed_at = now();
            $payment->payload = array_merge($payment->payload ?? [], ['paypal_capture' => $response->result]);
            $payment->save();

            if ($payment->order) {
                $payment->order->payment_status = 'paid';
                $payment->order->status = 'completed';
                $payment->order->save();
            }

            session()->forget('cart');
            return redirect()->route('orders.show', $payment->order_id)->with('success', __('Payment completed'));
        }
        $payment->status = 'failed';
        $payment->failure_reason = 'capture_failed';
        $payment->failed_at = now();
        $payment->save();

        $this->restoreCartFromSnapshot($payment);
        return view('payments.failure')->with('order', null)->with('payment', $payment)->with('error_message', __('Payment capture failed'));
    }

    public function handlePayPalCancel(Payment $payment)
    {
        if ($payment->method === 'paypal' && $payment->status === 'pending') {
            $payment->status = 'failed';
            $payment->failure_reason = 'user_cancelled';
            $payment->failed_at = now();
            $payment->save();
        }

        if ($payment->order_id) {
            return redirect()->route('orders.show', $payment->order_id)->with('error', __('Payment cancelled'));
        }

        $this->restoreCartFromSnapshot($payment);
        return view('payments.failure')->with('order', null)->with('payment', $payment)->with('error_message', __('Payment cancelled'));
    }

    private function categorizeCredentials(array $credentials): array
    {
        $sensitiveFields = ['api_key', 'secret_key', 'public_key', 'merchant_id', 'webhook_secret'];
        $dedicatedFields = [];
        $configFields = [];

        foreach ($credentials as $key => $value) {
            if (in_array($key, $sensitiveFields, true) && Schema::hasColumn('payment_gateways', $key)) {
                $dedicatedFields[$key] = $this->encryptValue($value);
            } else {
                $configFields[$key] = $value;
            }
        }

        return [$dedicatedFields, $configFields];
    }

    private function updateDedicatedFields(PaymentGateway $gateway, array $dedicatedFields): void
    {
        foreach ($dedicatedFields as $field => $value) {
            $gateway->{$field} = $value;
        }
    }

    private function updateConfigFields(PaymentGateway $gateway, array $configFields): void
    {
        $currentConfig = is_array($gateway->config) ? $gateway->config : [];
        $gateway->config = array_merge($currentConfig, $configFields);
    }

    private function decryptConfig($config): ?array
    {
        if (! is_string($config) || empty($config)) {
            return null;
        }

        try {
            $decrypted = Crypt::decryptString($config);

            return json_decode($decrypted, true);
        } catch (\Exception $e) {
            try {
                $decrypted = Crypt::decrypt($config);

                return json_decode($decrypted, true);
            } catch (\Exception $e) {
                return json_decode($config, true);
            }
        }
    }

    private function encryptValue($value): string
    {
        if ($value === null) {
            return '';
        }

        try {
            return Crypt::encryptString((string) $value);
        } catch (\Exception $e) {
            return (string) $value;
        }
    }

    private function mergeWithDedicatedFields(PaymentGateway $gateway, array $config): array
    {
        $dedicatedFields = [];
        $sensitiveFields = ['api_key', 'secret_key', 'public_key', 'merchant_id', 'webhook_secret'];

        foreach ($sensitiveFields as $field) {
            if (Schema::hasColumn('payment_gateways', $field) && isset($gateway->{$field})) {
                try {
                    $dedicatedFields[$field] = Crypt::decryptString($gateway->{$field});
                } catch (\Exception $e) {
                    $dedicatedFields[$field] = $gateway->{$field};
                }
            }
        }

        return array_merge($config, $dedicatedFields);
    }

    private function getDefaultCredentials(): array
    {
        return [
            'api_key' => null,
            'secret_key' => null,
            'public_key' => null,
            'merchant_id' => null,
            'webhook_secret' => null,
            'additional_config' => [],
            'sandbox_mode' => false,
        ];
    }

    private function getRequiredFields(string $driver): array
    {
        return match ($driver) {
            'stripe' => ['publishable_key', 'secret_key'],
            'paytabs' => ['paytabs_profile_id', 'paytabs_server_key'],
            'tap' => ['tap_secret_key', 'tap_public_key'],
            'weaccept' => ['weaccept_api_key', 'weaccept_hmac_secret', 'weaccept_integration_id'],
            'paypal' => ['paypal_client_id', 'paypal_secret'],
            'payeer' => ['payeer_merchant_id', 'payeer_secret_key'],
            default => [],
        };
    }

    private function hasField(array $credentials, string $field): bool
    {
        foreach ($credentials as $key => $value) {
            if (empty($value)) {
                continue;
            }

            if (
                strtolower($key) === strtolower($field) ||
                strpos(strtolower($key), strtolower($field)) !== false
            ) {
                return true;
            }
        }

        return false;
    }

    private function restoreCartFromSnapshot(Payment $payment): void
    {
        $snap = $payment->payload['checkout_snapshot'] ?? null;
        if ($snap && ! empty($snap['items'])) {
            $cart = [];
            foreach ($snap['items'] as $it) {
                if (empty($it['product_id'])) {
                    continue;
                }
                $cart[$it['product_id']] = [
                    'qty' => $it['qty'] ?? 1,
                    'price' => $it['price'] ?? 0,
                ];
            }
            session(['cart' => $cart]);
        }
    }
}
