<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class PaymentEncryptionService
{
    /**
     * Encrypt sensitive payment data.
     */
    public function encryptPaymentData(array $data): array
    {
        $sensitiveFields = [
            'card_number',
            'cvv',
            'card_holder_name',
            'bank_account',
            'api_key',
            'secret_key',
            'access_token',
            'refresh_token',
        ];

        $encryptedData = $data;

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field]) && ! empty($data[$field])) {
                try {
                    $encryptedData[$field] = Crypt::encryptString($data[$field]);
                } catch (\Exception $e) {
                    // Remove sensitive data if encryption fails
                    unset($encryptedData[$field]);
                }
            }
        }

        return $encryptedData;
    }

    /**
     * Decrypt sensitive payment data.
     */
    public function decryptPaymentData(array $data): array
    {
        $sensitiveFields = [
            'card_number',
            'cvv',
            'card_holder_name',
            'bank_account',
            'api_key',
            'secret_key',
            'access_token',
            'refresh_token',
        ];

        $decryptedData = $data;

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field]) && ! empty($data[$field])) {
                try {
                    $decryptedData[$field] = Crypt::decryptString($data[$field]);
                } catch (\Exception $e) {
                    // Keep encrypted data if decryption fails
                    $decryptedData[$field] = '[ENCRYPTED]';
                }
            }
        }

        return $decryptedData;
    }

    /**
     * Mask sensitive data for logging.
     */
    public function maskSensitiveData(array $data): array
    {
        $sensitiveFields = [
            'card_number' => function ($value) {
                return substr($value, 0, 4) . '****' . substr($value, -4);
            },
            'cvv' => function ($value) {
                return '***';
            },
            'api_key' => function ($value) {
                return substr($value, 0, 8) . '...';
            },
            'secret_key' => function ($value) {
                return substr($value, 0, 8) . '...';
            },
            'access_token' => function ($value) {
                return substr($value, 0, 10) . '...';
            },
            'bank_account' => function ($value) {
                return '****' . substr($value, -4);
            },
        ];

        $maskedData = $data;

        foreach ($sensitiveFields as $field => $maskFunction) {
            if (isset($data[$field]) && ! empty($data[$field])) {
                $maskedData[$field] = $maskFunction($data[$field]);
            }
        }

        return $maskedData;
    }

    /**
     * Generate secure payment reference.
     */
    public function generateSecureReference(string $prefix = 'PAY'): string
    {
        $timestamp = now()->format('YmdHis');
        $random = bin2hex(random_bytes(8));
        $hash = substr(hash('sha256', $timestamp . $random . config('app.key')), 0, 8);

        return strtoupper($prefix . '_' . $timestamp . '_' . $hash);
    }

    /**
     * Validate payment signature.
     */
    public function validateSignature(array $data, string $signature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', json_encode($data), $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate payment signature.
     */
    public function generateSignature(array $data, string $secret): string
    {
        return hash_hmac('sha256', json_encode($data), $secret);
    }

    /**
     * Sanitize payment data for storage.
     */
    public function sanitizeForStorage(array $data): array
    {
        // Remove completely sensitive fields that should never be stored
        $forbiddenFields = ['cvv', 'card_number', 'pin'];

        $sanitizedData = $data;

        foreach ($forbiddenFields as $field) {
            unset($sanitizedData[$field]);
        }

        // Encrypt remaining sensitive data
        return $this->encryptPaymentData($sanitizedData);
    }
}
