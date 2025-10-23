<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentGatewayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $paymentGateway = $this->route('paymentGateway');
        $ignoreId = $paymentGateway && isset($paymentGateway->id) ? $paymentGateway->id : null;

        $slugUnique = 'unique:payment_gateways,slug' . ($ignoreId ? ',' . $ignoreId : '');

        // Allowed drivers expanded
        $allowedDrivers = [
            'stripe', 'offline', 'paytabs', 'tap', 'weaccept', 'paypal', 'payeer',
        ];

        return [
            'name' => ['required', 'string', 'max:190'],
            'slug' => ['nullable', 'string', 'max:190', $slugUnique],
            'driver' => ['required', Rule::in($allowedDrivers)],
            'enabled' => ['sometimes', 'boolean'],
            'requires_transfer_image' => ['sometimes', 'boolean'],
            'transfer_instructions' => ['nullable', 'string'],
            // Stripe
            'stripe_publishable_key' => ['nullable', 'string'],
            'stripe_secret_key' => ['nullable', 'string'],
            'stripe_webhook_secret' => ['nullable', 'string'],
            'stripe_mode' => ['nullable', Rule::in(['test', 'live'])],
            // PayTabs
            'paytabs_profile_id' => ['nullable', 'string', 'max:255'],
            'paytabs_server_key' => ['nullable', 'string', 'max:500'],
            // Tap
            'tap_secret_key' => ['nullable', 'string', 'max:500'],
            'tap_public_key' => ['nullable', 'string', 'max:500'],
            'tap_currency' => ['nullable', 'string', 'max:10'],
            'tap_lang' => ['nullable', 'string', 'max:5'],
            // WeAccept
            'weaccept_api_key' => ['nullable', 'string', 'max:500'],
            'weaccept_hmac_secret' => ['nullable', 'string', 'max:500'],
            'weaccept_integration_id' => ['nullable', 'string', 'max:255'],
            'weaccept_iframe_id' => ['nullable', 'string', 'max:255'],
            'weaccept_currency' => ['nullable', 'string', 'max:10'],
            'weaccept_api_base' => ['nullable', 'url'],
            // PayPal
            'paypal_client_id' => ['nullable', 'string', 'max:255'],
            'paypal_secret' => ['nullable', 'string', 'max:255'],
            'paypal_mode' => ['nullable', Rule::in(['sandbox', 'live'])],
            // Payeer
            'payeer_merchant_id' => ['nullable', 'string', 'max:255'],
            'payeer_secret_key' => ['nullable', 'string', 'max:500'],
            // Payrexx removed
        ];
    }
}
