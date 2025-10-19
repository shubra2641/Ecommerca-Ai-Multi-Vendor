<?php

namespace App\Services\Payments\Gateways;

use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WeacceptGateway
{
    private array $config;
    private string $apiBase;
    private string $apiKey;
    private string $integrationId;
    private string $iframeId;
    private string $currency;
    private bool $mock;
    private int $timeoutSec;
    private int $connectTimeout;
    private ?string $caBundle;
    private bool $allowInsecure;
    private ?string $proxy;

    public function initFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        $this->initializeConfig($gateway);
        
        return DB::transaction(function () use ($snapshot, $gateway) {
            $payment = $this->createPayment($snapshot);
            
            if ($this->mock) {
                return $this->handleMockPayment($payment);
            }
            
            $this->validateConfig();
            
            $authToken = $this->getAuthToken();
            $orderData = $this->createOrder($snapshot, $authToken);
            $paymentToken = $this->getPaymentToken($snapshot, $orderData, $authToken);
            
            return $this->buildPaymentResult($payment, $orderData, $paymentToken);
        });
    }

    public function verifyCharge(Payment $payment, PaymentGateway $gateway): array
    {
        $svc = app(\App\Services\Payments\PaymentGatewayService::class);
        return $svc->verifyGenericGatewayCharge($payment, $gateway);
    }

    private function initializeConfig(PaymentGateway $gateway): void
    {
        $cfg = $gateway->config ?? [];
        
        $this->config = $cfg;
        $this->apiBase = rtrim($cfg['api_base'] ?? env('WEACCEPT_API_BASE', env('PAYMOB_API_BASE', 'https://accept.paymob.com')), '/');
        $this->apiKey = $cfg['api_key'] ?? $cfg['weaccept_api_key'] ?? $cfg['paymob_api_key'] ?? env('PAYMOB_API_KEY');
        $this->integrationId = $cfg['integration_id'] ?? $cfg['weaccept_integration_id'] ?? $cfg['paymob_integration_id'] ?? env('PAYMOB_INTEGRATION_ID');
        $this->iframeId = $cfg['iframe_id'] ?? $cfg['weaccept_iframe_id'] ?? $cfg['paymob_iframe_id'] ?? env('PAYMOB_IFRAME_ID');
        $this->currency = strtoupper($cfg['weaccept_currency'] ?? $cfg['paymob_currency'] ?? env('PAYMOB_CURRENCY', 'EGP'));
        $this->mock = (bool) ($cfg['mock'] ?? env('WEACCEPT_MOCK', false));
        $this->timeoutSec = (int) ($cfg['http_timeout'] ?? env('PAYMOB_HTTP_TIMEOUT', 20));
        $this->connectTimeout = (int) ($cfg['http_connect_timeout'] ?? env('PAYMOB_HTTP_CONNECT_TIMEOUT', 10));
        $this->caBundle = $cfg['ca_bundle_path'] ?? env('CURL_CA_BUNDLE');
        $this->allowInsecure = (bool) ($cfg['allow_insecure_ssl'] ?? env('PAYMOB_ALLOW_INSECURE_SSL', false));
        $this->proxy = $cfg['http_proxy'] ?? env('HTTP_PROXY') ?? env('http_proxy');
    }

    private function createPayment(array $snapshot): Payment
    {
        return Payment::create([
            'order_id' => null,
            'user_id' => $snapshot['user_id'] ?? null,
            'method' => 'weaccept',
            'amount' => $snapshot['total'] ?? 0,
            'currency' => $this->currency,
            'status' => 'pending',
            'payload' => ['checkout_snapshot' => $snapshot],
        ]);
    }

    private function handleMockPayment(Payment $payment): array
    {
        $mockToken = 'mock-' . $payment->id . '-' . time();
        $payment->payload = array_merge($payment->payload ?? [], [
            'weaccept_order_id' => 'mock-order-' . $payment->id,
            'weaccept_payment_token' => $mockToken,
            'weaccept_integration_id' => $this->integrationId,
            'weaccept_mock' => true,
        ]);
        $payment->save();
        
        $redirectUrl = route('weaccept.return', ['payment' => $payment->id]) . '?mock=1';
        
        return [
            'payment' => $payment,
            'redirect_url' => $redirectUrl,
            'raw' => ['mock' => true]
        ];
    }

    private function validateConfig(): void
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Missing PAYMOB_API_KEY in gateway config');
        }
        if (empty($this->integrationId)) {
            throw new \Exception('Missing integration_id in gateway config');
        }
        if (empty($this->iframeId)) {
            throw new \Exception('Missing iframe_id in gateway config');
        }
    }

    private function getAuthToken(): string
    {
        $apiPrefix = $this->getApiPrefix();
        $authUrl = $apiPrefix . '/auth/tokens';
        
        $candidates = $this->getApiKeyCandidates();
        
        foreach ($candidates as $apiKey) {
            try {
                $response = $this->makeHttpRequest($authUrl, ['api_key' => $apiKey]);
                
                if ($response->successful()) {
                    $authJson = $response->json();
                    $token = $authJson['token'] ?? $authJson['access_token'] ?? null;
                    
                    if ($token) {
                        return $token;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('weaccept.auth.attempt_error', ['error' => $e->getMessage()]);
                continue;
            }
        }
        
        throw new \Exception('Failed to authenticate with PayMob API');
    }

    private function getApiKeyCandidates(): array
    {
        $candidates = [];
        
        if (!empty($this->apiKey)) {
            $candidates[] = trim(preg_replace('/\s+/', '', $this->apiKey));
        }
        
        if (!empty($this->config['api_key'])) {
            $candidates[] = trim(preg_replace('/\s+/', '', $this->config['api_key']));
        }
        
        if (!empty($this->config['weaccept_api_key'])) {
            $candidates[] = trim(preg_replace('/\s+/', '', $this->config['weaccept_api_key']));
        }
        
        if (!empty($this->config['paymob_api_key'])) {
            $candidates[] = trim(preg_replace('/\s+/', '', $this->config['paymob_api_key']));
        }
        
        $envKey = trim(preg_replace('/\s+/', '', env('PAYMOB_API_KEY') ?? ''));
        if ($envKey) {
            $candidates[] = $envKey;
        }
        
        return array_values(array_filter(array_unique($candidates)));
    }

    private function createOrder(array $snapshot, string $authToken): array
    {
        $apiPrefix = $this->getApiPrefix();
        $amountCents = (int) round(($snapshot['total'] ?? 0) * 100);
        
        $items = $this->formatOrderItems($snapshot['items'] ?? [], $amountCents);
        
        $orderPayload = [
            'merchant_order_id' => (string) $snapshot['payment_id'] ?? time(),
            'amount_cents' => $amountCents,
            'currency' => $this->currency,
            'items' => $items,
        ];
        
        $response = $this->makeAuthenticatedRequest($authToken, $apiPrefix . '/ecommerce/orders', $orderPayload);
        
        if (!$response->successful()) {
            throw new \Exception('Order creation error: ' . $response->status());
        }
        
        $orderJson = $response->json();
        $orderId = $orderJson['id'] ?? $orderJson['data']['id'] ?? null;
        
        if (empty($orderId)) {
            throw new \Exception('Missing order id from Paymob');
        }
        
        return $orderJson;
    }

    private function formatOrderItems(array $items, int $amountCents): array
    {
        $formattedItems = [];
        
        foreach ($items as $item) {
            $price = $item['amount_cents'] ?? (isset($item['price']) ? (int) round($item['price'] * 100) : $amountCents);
            $quantity = $item['qty'] ?? $item['quantity'] ?? 1;
            
            $formattedItems[] = [
                'name' => $item['name'] ?? $item['product_name'] ?? 'Item',
                'description' => $item['description'] ?? $item['name'] ?? '',
                'amount_cents' => (int) $price,
                'quantity' => (int) $quantity,
            ];
        }
        
        return $formattedItems;
    }

    private function getPaymentToken(array $snapshot, array $orderData, string $authToken): string
    {
        $apiPrefix = $this->getApiPrefix();
        $amountCents = (int) round(($snapshot['total'] ?? 0) * 100);
        $orderId = $orderData['id'] ?? $orderData['data']['id'];
        
        $billingData = $this->prepareBillingData($snapshot);
        $returnUrl = route('weaccept.return', ['payment' => $snapshot['payment_id'] ?? time()]);
        
        $paymentKeyPayload = [
            'amount_cents' => $amountCents,
            'currency' => $this->currency,
            'order' => ['id' => $orderId, 'amount_cents' => $amountCents],
            'billing_data' => $billingData,
            'expiration' => 3600,
            'integration_id' => (int) $this->integrationId,
            'redirection_url' => $returnUrl,
        ];
        
        $response = $this->makeAuthenticatedRequest($authToken, $apiPrefix . '/acceptance/payment_keys', $paymentKeyPayload);
        
        if (!$response->successful()) {
            throw new \Exception('Payment key error: ' . $response->status());
        }
        
        $responseJson = $response->json();
        $paymentToken = $responseJson['token'] ?? $responseJson['payment_token'] ?? null;
        
        if (empty($paymentToken)) {
            throw new \Exception('Missing payment token from Paymob');
        }
        
        return $paymentToken;
    }

    private function prepareBillingData(array $snapshot): array
    {
        $fullName = $snapshot['customer_name'] ?? 'Customer';
        $nameParts = preg_split('/\s+/', trim($fullName));
        $firstName = $nameParts[0] ?? 'Customer';
        $lastName = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : 'Customer';
        
        $city = $snapshot['billing_city'] ?? $snapshot['customer_city'] ?? 'Cairo';
        $country = $snapshot['billing_country'] ?? $snapshot['customer_country'] ?? 'EG';
        
        if (is_numeric($city) || empty($city)) {
            $city = 'Cairo';
        }
        if (is_numeric($country) || empty($country)) {
            $country = 'EG';
        }
        
        return [
            'apartment' => $snapshot['billing_apartment'] ?? '12A',
            'email' => $snapshot['customer_email'] ?? 'customer@example.com',
            'floor' => $snapshot['billing_floor'] ?? '3',
            'first_name' => $firstName,
            'last_name' => $lastName,
            'street' => $snapshot['billing_street'] ?? 'Tahrir St',
            'building' => $snapshot['billing_building'] ?? '15',
            'phone_number' => $snapshot['customer_phone'] ?? '201000000000',
            'shipping_method' => 'NO',
            'postal_code' => $snapshot['billing_postal_code'] ?? '11511',
            'city' => $city,
            'country' => $country,
        ];
    }

    private function buildPaymentResult(Payment $payment, array $orderData, string $paymentToken): array
    {
        $orderId = $orderData['id'] ?? $orderData['data']['id'];
        $iframeUrl = $this->apiBase . '/api/acceptance/iframes/' . $this->iframeId . '?payment_token=' . $paymentToken;
        $standaloneUrl = $orderData['order_url'] ?? $orderData['url'] ?? null;
        
        $newPayload = [
            'weaccept_order_id' => $orderId,
            'weaccept_payment_token' => $paymentToken,
            'weaccept_integration_id' => $this->integrationId,
            'weaccept_iframe_id' => $this->iframeId,
            'weaccept_api_base' => $this->apiBase,
            'weaccept_iframe_url' => $iframeUrl,
            'weaccept_order_url' => $standaloneUrl,
        ];
        
        $payment->payload = array_merge($payment->payload ?? [], $newPayload);
        $payment->save();
        
        $result = [
            'payment' => $payment,
            'redirect_url' => $iframeUrl,
            'raw' => ['order' => $orderData, 'payment_key' => ['token' => $paymentToken]],
        ];
        
        if ($standaloneUrl) {
            $result['fallback_url'] = $standaloneUrl;
        }
        
        // Use standalone URL if preferred in config
        $preferStandalone = data_get($this->config, 'weaccept_prefer_standalone');
        if ($preferStandalone && $standaloneUrl) {
            $result['redirect_url'] = $standaloneUrl;
        }
        
        return $result;
    }

    private function getApiPrefix(): string
    {
        return str_contains($this->apiBase, '/api') 
            ? rtrim($this->apiBase, '/') 
            : rtrim($this->apiBase, '/') . '/api';
    }

    private function makeHttpRequest(string $url, array $data = []): \Illuminate\Http\Client\Response
    {
        $client = Http::acceptJson()
            ->timeout($this->timeoutSec)
            ->retry(2, 500)
            ->withOptions(['connect_timeout' => $this->connectTimeout]);
        
        if ($this->proxy) {
            $client = $client->withOptions(['proxy' => $this->proxy]);
        }
        
        if ($this->caBundle) {
            $client = $client->withOptions(['verify' => $this->caBundle]);
        }
        
        if ($this->allowInsecure) {
            $client = $client->withOptions(['verify' => false]);
        }
        
        return empty($data) ? $client->get($url) : $client->post($url, $data);
    }

    private function makeAuthenticatedRequest(string $token, string $url, array $data = []): \Illuminate\Http\Client\Response
    {
        $client = Http::withToken($token)
            ->acceptJson()
            ->timeout($this->timeoutSec)
            ->retry(2, 500)
            ->withOptions(['connect_timeout' => $this->connectTimeout]);
        
        if ($this->proxy) {
            $client = $client->withOptions(['proxy' => $this->proxy]);
        }
        
        if ($this->caBundle) {
            $client = $client->withOptions(['verify' => $this->caBundle]);
        }
        
        if ($this->allowInsecure) {
            $client = $client->withOptions(['verify' => false]);
        }
        
        return empty($data) ? $client->get($url) : $client->post($url, $data);
    }
}