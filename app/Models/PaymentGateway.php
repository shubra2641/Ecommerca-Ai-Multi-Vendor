<?php

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
}
