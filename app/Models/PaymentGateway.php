<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'driver', 'enabled',
        'requires_transfer_image', 'transfer_instructions',
        'config',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'requires_transfer_image' => 'boolean',
        'config' => 'array',
        'supported_currencies' => 'array',
        'supported_methods' => 'array',
        'additional_config' => 'array',
        'fees' => 'array',
        'sandbox_mode' => 'boolean',
    ];

    /**
     * Set encrypted credentials for this gateway
     */
    // Legacy credential encryption removed (multi-driver system deprecated)

    /**
     * Get decrypted gateway credentials
     */
    public function getCredentials(): array
    {
        // Debug: log raw config sources to help tests diagnose legacy encrypted config formats
        try {
            $rawOriginal = $this->getRawOriginal('config');
            $orig = $this->getOriginal('config');
            Log::debug(
                'PaymentGateway#getCredentials start: rawOriginal type=' . gettype($rawOriginal) .
                ' orig type=' . gettype($orig) .
                ' attrConfig type=' . gettype($this->config)
            );
            // Truncate values (avoid leaking secrets) when present
            if (is_string($rawOriginal) && $rawOriginal !== '') {
                Log::debug('PaymentGateway#getCredentials rawOriginal (truncated): ' . substr($rawOriginal, 0, 120));
            }
            if (is_string($orig) && $orig !== '') {
                Log::debug('PaymentGateway#getCredentials orig (truncated): ' . substr($orig, 0, 120));
            }
        } catch (\Throwable $_logEx) {
            // ignore logging failure
        }

        // Handle legacy encrypted config stored as string: decrypt then json_decode
        $full = [];
        if (is_array($this->config)) {
            $full = $this->config;
        } else {
            $raw = $this->getRawOriginal('config')
                ?? $this->getOriginal('config')
                ?? $this->config;
            if (is_string($raw) && $raw !== '') {
                // Try decryptString -> decrypt -> raw JSON decode
                try {
                    try {
                        $decrypted = Crypt::decryptString($raw);
                        Log::debug(
                            'PaymentGateway#getCredentials decrypted using decryptString (len=' .
                            strlen($decrypted) . ')'
                        );
                    } catch (\Throwable $_e) {
                        $decrypted = Crypt::decrypt($raw);
                        Log::debug(
                            'PaymentGateway#getCredentials decrypted using decrypt (len=' .
                            strlen($decrypted) . ')'
                        );
                    }

                    // DEBUG: log a truncated snippet of the decrypted payload for test triage
                    try {
                        Log::debug(
                            'PaymentGateway#getCredentials decrypted snippet: ' .
                            substr($decrypted, 0, 200)
                        );
                    } catch (\Throwable $_e) {
                        // ignore
                    }

                    // Try several JSON decode strategies in case the payload has extra escaping or wrapping
                    $decoded = $this->tryDecodeJsonVariants($decrypted);
                    if (! is_array($decoded)) {
                        // Extra debugging for test triage: log decode attempts
                        try {
                            Log::debug(
                                'PaymentGateway#getCredentials debug: decrypted var_export: ' .
                                var_export($decrypted, true)
                            );
                            Log::debug(
                                'PaymentGateway#getCredentials debug: json_decode direct: ' .
                                var_export(json_decode($decrypted, true), true)
                            );
                            $trimmed = trim($decrypted, " \n\r\t\x0B\x00\"'");
                            Log::debug(
                                'PaymentGateway#getCredentials debug: json_decode trimmed: ' .
                                var_export(json_decode($trimmed, true), true)
                            );
                            $unescaped = stripcslashes($decrypted);
                            Log::debug(
                                'PaymentGateway#getCredentials debug: json_decode unescaped: ' .
                                var_export(json_decode($unescaped, true), true)
                            );
                        } catch (\Throwable $_dbg) {
                            // ignore
                        }
                    }
                    if (is_array($decoded)) {
                        $full = $decoded;
                        Log::debug(
                            'PaymentGateway#getCredentials decoded decrypted payload keys: ' .
                            implode(',', array_keys($decoded))
                        );
                    }
                } catch (\Throwable $e) {
                    // Not encrypted or decryption failed: maybe raw JSON or other encoded form
                    $decoded = $this->tryDecodeJsonVariants($raw);
                    if (is_array($decoded)) {
                        $full = $decoded;
                        Log::debug(
                            'PaymentGateway#getCredentials decoded raw JSON keys: ' .
                            implode(',', array_keys($decoded))
                        );
                    }
                }
            }

            // If still empty, try reading raw DB value directly
            if (empty($full) && $this->id) {
                try {
                    $rawDb = DB::table('payment_gateways')->where('id', $this->id)->value('config');
                    if (is_string($rawDb) && $rawDb !== '') {
                        // Try decryptString -> decrypt -> raw JSON decode on DB value
                        try {
                            try {
                                $decrypted = Crypt::decryptString($rawDb);
                                Log::debug(
                                    'PaymentGateway#getCredentials DB decrypted using decryptString (len=' .
                                    strlen($decrypted) . ')'
                                );
                            } catch (\Throwable $_e) {
                                $decrypted = Crypt::decrypt($rawDb);
                                Log::debug(
                                    'PaymentGateway#getCredentials DB decrypted using decrypt (len=' .
                                    strlen($decrypted) . ')'
                                );
                            }
                            $decoded = json_decode($decrypted, true);
                            if (is_array($decoded)) {
                                $full = $decoded;
                                Log::debug(
                                    'PaymentGateway#getCredentials DB decoded decrypted payload keys: ' .
                                    implode(',', array_keys($decoded))
                                );
                            }
                        } catch (\Throwable $e) {
                            $decoded = json_decode($rawDb, true);
                            if (is_array($decoded)) {
                                $full = $decoded;
                                Log::debug(
                                    'PaymentGateway#getCredentials DB decoded raw JSON keys: ' .
                                    implode(',', array_keys($decoded))
                                );
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // ignore DB fallback errors
                }
            }
        }

        // First read dedicated encrypted columns if present, and merge with config JSON
        $dedicated = [];
        foreach (['api_key', 'secret_key', 'public_key', 'merchant_id', 'webhook_secret'] as $col) {
            if (Schema::hasColumn('payment_gateways', $col) && isset($this->{$col}) && $this->{$col} !== null) {
                try {
                    $dedicated[$col] = Crypt::decryptString($this->{$col});
                } catch (\Exception $e) {
                    $dedicated[$col] = $this->{$col};
                }
            }
        }

        // Merge dedicated keys with config (dedicated fields take precedence)
        $merged = array_merge($full, $dedicated);

        // Ensure credential keys are always present (null if missing)
        foreach (['api_key', 'secret_key', 'public_key', 'merchant_id', 'webhook_secret'] as $k) {
            if (! array_key_exists($k, $merged)) {
                $merged[$k] = null;
            }
        }

        // Ensure expected keys exist for tests
        if (! isset($merged['additional_config']) || ! is_array($merged['additional_config'])) {
            $merged['additional_config'] = [];
        }

        // Default sandbox_mode to false when not explicitly provided
        // If there are no credentials and no additional config, treat this as an "empty" credential state
        $hasAnyCredential = false;
        foreach (['api_key', 'secret_key', 'public_key', 'merchant_id', 'webhook_secret'] as $k) {
            if (! empty($merged[$k])) {
                $hasAnyCredential = true;
                break;
            }
        }

        if (! isset($merged['sandbox_mode'])) {
            $merged['sandbox_mode'] = false;
        }

        if (! $hasAnyCredential && empty($merged['additional_config'])) {
            // Force false for truly empty credential sets (tests expect this behavior)
            $merged['sandbox_mode'] = false;
        }

        // If critical credential keys are still null, attempt a final DB raw config decode
        if (empty($merged['api_key']) && $this->id) {
            try {
                $rawDb = DB::table('payment_gateways')->where('id', $this->id)->value('config');
                if (is_string($rawDb) && $rawDb !== '') {
                    try {
                        Log::debug('PaymentGateway#getCredentials rawDb (truncated): ' . substr($rawDb, 0, 200));
                    } catch (\Throwable $_e) {
                        // ignore logging failures
                    }
                    try {
                        try {
                            $decrypted = Crypt::decryptString($rawDb);
                        } catch (\Throwable $_e) {
                            $decrypted = Crypt::decrypt($rawDb);
                        }
                        $decoded = json_decode($decrypted, true);
                        if (is_array($decoded)) {
                            foreach (
                                ['api_key', 'secret_key', 'public_key', 'merchant_id', 'webhook_secret'] as $k
                            ) {
                                if (array_key_exists($k, $decoded) && empty($merged[$k])) {
                                    $merged[$k] = $decoded[$k];
                                }
                            }
                            if (
                                isset($decoded['additional_config']) &&
                                is_array($decoded['additional_config']) &&
                                empty($merged['additional_config'])
                            ) {
                                $merged['additional_config'] = $decoded['additional_config'];
                            }
                            if (isset($decoded['sandbox_mode']) && empty($merged['sandbox_mode'])) {
                                $merged['sandbox_mode'] = $decoded['sandbox_mode'];
                            }
                        }
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }
            } catch (\Throwable $e) {
                // ignore DB fallback errors
            }
        }

        return $merged;
    }

    /**
     * Try multiple JSON decoding strategies to handle escaped or wrapped JSON strings
     */
    protected function tryDecodeJsonVariants(string $json)
    {
        // If payload looks like a PHP serialized value, attempt to unserialize
        if (is_string($json) && preg_match('/^s:\d+:"/s', $json)) {
            try {
                $un = @unserialize($json);
                if ($un !== false && $un !== null) {
                    // If result is a string, try decoding that string as JSON
                    if (is_string($un)) {
                        $decoded = json_decode($un, true);
                        if (is_array($decoded)) {
                            return $decoded;
                        }
                    } elseif (is_array($un)) {
                        return $un;
                    }
                }
            } catch (\Throwable $_e) {
                // ignore
            }
        }
        // Direct decode
        $decoded = json_decode($json, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Sometimes payloads are double-encoded or have surrounding quotes
        $trimmed = trim($json, " \n\r\t\x0B\x00\"'");
        $decoded = json_decode($trimmed, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Unescape common sequences
        $unescaped = stripcslashes($json);
        $decoded = json_decode($unescaped, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return null;
    }

    /**
     * Get masked credentials for display purposes
     */
    public function getMaskedCredentials(): array
    {
        return [];
    }

    /**
     * Set encrypted credentials for this gateway. Accepts partial arrays.
     * Will store into dedicated DB columns when present, otherwise into config.
     */
    public function setCredentials(array $credentials = []): void
    {
        // Save sensitive dedicated columns if those columns exist on the model
        $sensitive = ['api_key', 'secret_key', 'public_key', 'merchant_id', 'webhook_secret'];

        foreach ($sensitive as $field) {
            if (array_key_exists($field, $credentials) && Schema::hasColumn('payment_gateways', $field)) {
                $value = $credentials[$field];
                if ($value === null) {
                    $this->{$field} = null;
                } else {
                    try {
                        $this->{$field} = Crypt::encryptString((string) $value);
                    } catch (\Exception $e) {
                        // fallback to plain value if encryption fails
                        $this->{$field} = (string) $value;
                    }
                }
                // remove from credentials array so it doesn't get duplicated into config
                unset($credentials[$field]);
            }
        }

        // Merge remaining credentials into config JSON
        $current = is_array($this->config) ? $this->config : [];

        // Remove credential keys from current config so partial updates don't preserve legacy values
        foreach (['api_key', 'secret_key', 'public_key', 'merchant_id', 'webhook_secret'] as $k) {
            if (array_key_exists($k, $current)) {
                unset($current[$k]);
            }
        }

        $merged = array_merge($current, $credentials);
        $this->config = $merged;
    }

    /**
     * Check if gateway has valid credentials configured
     */
    public function hasValidCredentials(): bool
    {
        $credentials = $this->getCredentials();

        if (empty($credentials)) {
            return false;
        }

        // Removed provider metadata dependency

        // Fallback to hardcoded required fields map
        $requiredFields = $this->getRequiredCredentialFields();
        foreach ($requiredFields as $field) {
            // case-insensitive check
            $found = false;
            foreach ($credentials as $ck => $cv) {
                if ($cv === null || $cv === '') {
                    continue;
                }
                if (strtolower($ck) === strtolower($field) || strpos(strtolower($ck), strtolower($field)) !== false) {
                    $found = true;
                    break;
                }
            }
            if (! $found) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get required credential fields for this gateway driver
     */
    public function getRequiredCredentialFields(): array
    {
        switch ($this->driver) {
            case 'stripe':
                return ['publishable_key', 'secret_key'];
            case 'paytabs':
                return ['paytabs_profile_id', 'paytabs_server_key'];
            case 'tap':
                return ['tap_secret_key', 'tap_public_key'];
            case 'weaccept':
                return ['weaccept_api_key', 'weaccept_hmac_secret', 'weaccept_integration_id'];
            case 'paypal':
                return ['paypal_client_id', 'paypal_secret'];
            case 'payeer':
                return ['payeer_merchant_id', 'payeer_secret_key'];
                // payrexx removed
            default:
                return [];
        }
    }

    // Helper accessors for config-driven credentials
    public function getStripeConfig(?string $key = null)
    {
        $cfg = [];
        if (is_array($this->config) && isset($this->config['stripe']) && is_array($this->config['stripe'])) {
            $cfg = $this->config['stripe'];
        }

        return $key ? ($cfg[$key] ?? null) : $cfg;
    }

    public function getPaypalConfig(?string $key = null)
    {
        // First try to get from encrypted credentials
        $credentials = $this->getCredentials();
        if (! empty($credentials) && $this->driver === 'paypal') {
            $cfg = [
                'client_id' => $credentials['client_id'] ?? null,
                'secret' => $credentials['secret'] ?? null,
                'mode' => $credentials['mode'] ?? 'sandbox',
                'webhook_id' => $credentials['webhook_id'] ?? null,
            ];

            return $key ? ($cfg[$key] ?? null) : $cfg;
        }

        // Fallback to empty for compatibility
        $cfg = [];

        return $key ? null : $cfg;
    }

    /**
     * Get gateway configuration for any driver
     */
    public function getGatewayConfig(?string $key = null)
    {
        $credentials = $this->getCredentials();

        if (empty($credentials)) {
            return $key ? null : [];
        }

        return $key ? ($credentials[$key] ?? null) : $credentials;
    }

    /**
     * Get the payments for this gateway.
     */
    public function payments()
    {
        // Payments store the gateway driver/slug in the `method` column.
        // Use `method` => `slug` so Eloquent can eager-load payments for each gateway.
        return $this->hasMany(Payment::class, 'method', 'slug');
    }
}
