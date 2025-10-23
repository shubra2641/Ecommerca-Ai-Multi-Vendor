<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'driver',
        'enabled',
        'requires_transfer_image',
        'transfer_instructions',
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
     * Get the payments for this gateway.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'method', 'slug');
    }

    /**
     * Check if gateway is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Check if gateway requires transfer image
     */
    public function requiresTransferImage(): bool
    {
        return $this->requires_transfer_image;
    }

    /**
     * Get transfer instructions
     */
    public function getTransferInstructions(): ?string
    {
        return $this->transfer_instructions;
    }

    /**
     * Get gateway driver
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * Get gateway slug
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Get gateway name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get gateway credentials/config
     */
    public function getCredentials(): array
    {
        return $this->config ?? [];
    }

    /**
     * Get specific credential by key
     */
    public function getCredential(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Update gateway credentials
     */
    public function updateCredentials(array $credentials): void
    {
        $this->config = $credentials;
    }

    /**
     * Check if gateway has specific credential
     */
    public function hasCredential(string $key): bool
    {
        return isset($this->config[$key]);
    }

    /**
     * Get Stripe configuration
     */
    public function getStripeConfig(): array
    {
        if ($this->driver !== 'stripe') {
            return [];
        }

        return [
            'public_key' => $this->getCredential('public_key'),
            'secret_key' => $this->getCredential('secret_key'),
            'webhook_secret' => $this->getCredential('webhook_secret'),
            'currency' => $this->getCredential('currency', 'usd'),
            'sandbox_mode' => $this->getCredential('sandbox_mode', false),
        ];
    }

    /**
     * Get PayPal configuration
     */
    public function getPayPalConfig(): array
    {
        if ($this->driver !== 'paypal') {
            return [];
        }

        return [
            'client_id' => $this->getCredential('client_id'),
            'client_secret' => $this->getCredential('client_secret'),
            'mode' => $this->getCredential('mode', 'sandbox'),
            'currency' => $this->getCredential('currency', 'USD'),
        ];
    }

    /**
     * Get bank transfer configuration
     */
    public function getBankTransferConfig(): array
    {
        if ($this->driver !== 'bank_transfer') {
            return [];
        }

        return [
            'account_name' => $this->getCredential('account_name'),
            'account_number' => $this->getCredential('account_number'),
            'bank_name' => $this->getCredential('bank_name'),
            'routing_number' => $this->getCredential('routing_number'),
            'instructions' => $this->getCredential('instructions'),
        ];
    }

    /**
     * Get gateway configuration based on driver
     */
    public function getDriverConfig(): array
    {
        switch ($this->driver) {
            case 'stripe':
                return $this->getStripeConfig();
            case 'paypal':
                return $this->getPayPalConfig();
            case 'bank_transfer':
                return $this->getBankTransferConfig();
            default:
                return $this->getCredentials();
        }
    }
}
