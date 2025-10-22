<?php

namespace App\Services;

use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;

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

    public function setCredentials(PaymentGateway $gateway, array $credentials): void
    {
        $sensitiveFields = ['api_key', 'secret_key', 'public_key', 'merchant_id', 'webhook_secret'];
        $dedicatedFields = [];
        $configFields = [];

        foreach ($credentials as $key => $value) {
            if (in_array($key, $sensitiveFields) && Schema::hasColumn('payment_gateways', $key)) {
                $dedicatedFields[$key] = $this->encryptValue($value);
            } else {
                $configFields[$key] = $value;
            }
        }

        foreach ($dedicatedFields as $field => $value) {
            $gateway->{$field} = $value;
        }

        $currentConfig = is_array($gateway->config) ? $gateway->config : [];
        $gateway->config = array_merge($currentConfig, $configFields);
    }

    public function hasValidCredentials(PaymentGateway $gateway): bool
    {
        $credentials = $this->getCredentials($gateway);
        $requiredFields = $this->getRequiredFields($gateway->driver);

        foreach ($requiredFields as $field) {
            if (!$this->hasField($credentials, $field)) {
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

    private function decryptConfig($config): ?array
    {
        if (!is_string($config) || empty($config)) {
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
}
