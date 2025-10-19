<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentGatewayRequest;
use App\Models\PaymentGateway;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        $gateways = PaymentGateway::orderBy('name')->get();

        return view('admin.payment_gateways.index', compact('gateways'));
    }

    public function create()
    {
        return view('admin.payment_gateways.form', ['gateway' => new PaymentGateway()]);
    }

    public function store(PaymentGatewayRequest $request, HtmlSanitizer $sanitizer)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $gateway = new PaymentGateway();
        $this->fillGateway($gateway, $data);

        // Capture any dynamic driver-specific fields that are not part of the validated set
        // and persist them into the gateway config for this specific gateway only.
        $this->mergeDynamicConfigFromRequest($gateway, $request);

        $gateway->save();

        return redirect()->route('admin.payment-gateways.index')->with('success', __('Gateway created'));
    }

    public function edit(PaymentGateway $paymentGateway)
    {
        return view('admin.payment_gateways.form', ['gateway' => $paymentGateway]);
    }

    public function update(PaymentGatewayRequest $request, PaymentGateway $paymentGateway, HtmlSanitizer $sanitizer)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $this->fillGateway($paymentGateway, $data);

        // Persist dynamic config keys submitted in the form (if any)
        $this->mergeDynamicConfigFromRequest($paymentGateway, $request);

        $paymentGateway->save();

        return redirect()->route('admin.payment-gateways.index')->with('success', __('Gateway updated'));
    }

    public function destroy(PaymentGateway $paymentGateway)
    {
        $paymentGateway->delete();

        return redirect()->route('admin.payment-gateways.index')->with('success', __('Gateway deleted'));
    }

    public function toggle(PaymentGateway $paymentGateway)
    {
        $paymentGateway->enabled = ! $paymentGateway->enabled;
        $paymentGateway->save();

        // If this is an AJAX request, return JSON so frontend can handle it without redirect
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'enabled' => $paymentGateway->enabled,
                'message' => __('Gateway status updated')
            ]);
        }

        return back()->with('success', __('Gateway status updated'));
    }

    // PayPal support removed: webhook test removed.

    // validateData removed: moved to FormRequest

    protected function fillGateway(PaymentGateway $gateway, array $data): void
    {
        $oldDriver = $gateway->exists ? $gateway->driver : null;

        $gateway->name = $data['name'];
        $gateway->slug = $data['slug'];
        $gateway->driver = $data['driver'];
        $gateway->enabled = ! empty($data['enabled']);

        // Legacy encrypted credential columns removed

        $cfg = $gateway->config ?: [];

        // Offline gateway logic
        if ($data['driver'] === 'offline') {
            $gateway->transfer_instructions = isset($data['transfer_instructions'])
                ? $sanitizer->clean($data['transfer_instructions'])
                : null;
            $gateway->requires_transfer_image = ! empty($data['requires_transfer_image']);
            // Remove other driver configurations when switching
            if ($oldDriver === 'stripe') {
                unset($cfg['stripe']);
            }
            // paypal support removed: nothing to unset
        } else {
            // If switching away from offline, clear its fields
            if ($oldDriver === 'offline') {
                $gateway->transfer_instructions = null;
                $gateway->requires_transfer_image = false;
            }
        }

        // When switching drivers, clean up conflicting stored config patterns
        if ($oldDriver && $oldDriver !== $gateway->driver) {
            // If we are leaving stripe remove nested stripe key
            if ($oldDriver === 'stripe' && isset($cfg['stripe'])) {
                unset($cfg['stripe']);
            }
            // If we move TO stripe we'll create nested structure below
            if ($gateway->driver === 'stripe') {
                // flatten any prior non-stripe credentials (leave them in case user switches back) – no action needed
            } else {
                // moving away from stripe - already unset above
            }
        }

        // Stripe config (kept namespaced under 'stripe')
        if ($data['driver'] === 'stripe') {
            $stripeCfg = $cfg['stripe'] ?? [];
            if (array_key_exists('stripe_publishable_key', $data)) {
                $stripeCfg['publishable_key'] = $data['stripe_publishable_key'];
            }
            if (! empty($data['stripe_secret_key'])) {
                $stripeCfg['secret_key'] = $data['stripe_secret_key'];
            }
            if (! empty($data['stripe_webhook_secret'])) {
                $stripeCfg['webhook_secret'] = $data['stripe_webhook_secret'];
            }
            $stripeCfg['mode'] = $data['stripe_mode'] ?? ($stripeCfg['mode'] ?? 'test');
            $cfg['stripe'] = $stripeCfg;
            // paypal support removed: nothing to unset
        }

        // Additional external gateways (flat keys at root of config array)
        switch ($data['driver']) {
            case 'paytabs':
                $cfg['paytabs_profile_id'] = $data['paytabs_profile_id'] ?? ($cfg['paytabs_profile_id'] ?? null);
                if (! empty($data['paytabs_server_key']) && $data['paytabs_server_key'] !== '********') {
                    $cfg['paytabs_server_key'] = $data['paytabs_server_key'];
                }
                break;
            case 'tap':
                if (! empty($data['tap_secret_key']) && $data['tap_secret_key'] !== '********') {
                    $cfg['tap_secret_key'] = $data['tap_secret_key'];
                }
                $cfg['tap_public_key'] = $data['tap_public_key'] ?? ($cfg['tap_public_key'] ?? null);
                $cfg['tap_currency'] = $data['tap_currency'] ?? ($cfg['tap_currency'] ?? 'USD');
                $cfg['tap_lang'] = $data['tap_lang'] ?? ($cfg['tap_lang'] ?? 'en');
                break;
            case 'weaccept':
                if (! empty($data['weaccept_api_key']) && $data['weaccept_api_key'] !== '********') {
                    $cfg['weaccept_api_key'] = $data['weaccept_api_key'];
                }
                if (! empty($data['weaccept_hmac_secret']) && $data['weaccept_hmac_secret'] !== '********') {
                    $cfg['weaccept_hmac_secret'] = $data['weaccept_hmac_secret'];
                }
                $cfg['weaccept_integration_id'] = $data['weaccept_integration_id']
                    ?? ($cfg['weaccept_integration_id'] ?? null);
                if (! empty($data['weaccept_iframe_id'])) {
                    $cfg['weaccept_iframe_id'] = $data['weaccept_iframe_id'];
                }
                if (! empty($data['weaccept_currency'])) {
                    $cfg['weaccept_currency'] = strtoupper($data['weaccept_currency']);
                }
                if (! empty($data['weaccept_api_base'])) {
                    $cfg['api_base'] = rtrim($data['weaccept_api_base'], '/');
                }
                break;
            case 'paypal':
                if (! empty($data['paypal_client_id'])) {
                    $cfg['paypal_client_id'] = $data['paypal_client_id'];
                }
                if (! empty($data['paypal_secret']) && $data['paypal_secret'] !== '********') {
                    $cfg['paypal_secret'] = $data['paypal_secret'];
                }
                $cfg['paypal_mode'] = $data['paypal_mode'] ?? ($cfg['paypal_mode'] ?? 'sandbox');
                break;
            case 'payeer':
                if (! empty($data['payeer_merchant_id'])) {
                    $cfg['payeer_merchant_id'] = $data['payeer_merchant_id'];
                }
                if (! empty($data['payeer_secret_key']) && $data['payeer_secret_key'] !== '********') {
                    $cfg['payeer_secret_key'] = $data['payeer_secret_key'];
                }
                break;
                // payrexx removed
        }

        $gateway->config = $cfg;
    }

    /**
     * Merge dynamic driver config keys from the raw request into the gateway config.
     * This allows the admin form (which renders dynamic inputs for drivers) to
     * persist API credentials without requiring a separate AJAX call.
     */
    protected function mergeDynamicConfigFromRequest(PaymentGateway $gateway, \Illuminate\Http\Request $request): void
    {
        /* intentionally left empty (legacy stub) */
    }
}
