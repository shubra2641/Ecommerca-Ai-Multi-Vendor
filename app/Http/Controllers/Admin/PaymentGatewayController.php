<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentGatewayRequest;
use App\Models\PaymentGateway;
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

    public function store(PaymentGatewayRequest $request)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $gateway = new PaymentGateway();
        $this->fillGateway($gateway, $data);

        $gateway->save();

        return redirect()->route('admin.payment-gateways.index')->with('success', __('Gateway created'));
    }

    public function edit(PaymentGateway $paymentGateway)
    {
        return view('admin.payment_gateways.form', ['gateway' => $paymentGateway]);
    }

    public function update(PaymentGatewayRequest $request, PaymentGateway $paymentGateway)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $this->fillGateway($paymentGateway, $data);

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
                'message' => __('Gateway status updated'),
            ]);
        }

        return back()->with('success', __('Gateway status updated'));
    }

    // PayPal support removed: webhook test removed.

    // validateData removed: moved to FormRequest

    protected function fillGateway(PaymentGateway $gateway, array $data): void
    {
        $oldDriver = $gateway->exists ? $gateway->driver : null;

        $this->setBasicGatewayProperties($gateway, $data);
        $cfg = $gateway->config ?: [];

        $this->handleOfflineGateway($gateway, $data, $cfg, $oldDriver);
        $this->handleDriverSwitching($cfg, $oldDriver, $data['driver']);
        $this->configureStripeGateway($cfg, $data);
        $this->configureExternalGateway($cfg, $data);

        $gateway->config = $cfg;
    }

    private function setBasicGatewayProperties(PaymentGateway $gateway, array $data): void
    {
        $gateway->name = $data['name'];
        $gateway->slug = $data['slug'];
        $gateway->driver = $data['driver'];
        $gateway->enabled = !empty($data['enabled']);
    }

    private function handleOfflineGateway(PaymentGateway $gateway, array $data, array &$cfg, ?string $oldDriver): void
    {
        if ($data['driver'] === 'offline') {
            $gateway->transfer_instructions = $data['transfer_instructions'] ?? null;
            $gateway->requires_transfer_image = !empty($data['requires_transfer_image']);
            if ($oldDriver === 'stripe') {
                unset($cfg['stripe']);
            }
        } elseif ($oldDriver === 'offline') {
            $gateway->transfer_instructions = null;
            $gateway->requires_transfer_image = false;
        }
    }

    private function handleDriverSwitching(array &$cfg, ?string $oldDriver, string $newDriver): void
    {
        if ($oldDriver && $oldDriver !== $newDriver) {
            if ($oldDriver === 'stripe' && isset($cfg['stripe'])) {
                unset($cfg['stripe']);
            }
        }
    }

    private function configureStripeGateway(array &$cfg, array $data): void
    {
        if ($data['driver'] === 'stripe') {
            $stripeCfg = $cfg['stripe'] ?? [];
            if (array_key_exists('stripe_publishable_key', $data)) {
                $stripeCfg['publishable_key'] = $data['stripe_publishable_key'];
            }
            if (!empty($data['stripe_secret_key'])) {
                $stripeCfg['secret_key'] = $data['stripe_secret_key'];
            }
            if (!empty($data['stripe_webhook_secret'])) {
                $stripeCfg['webhook_secret'] = $data['stripe_webhook_secret'];
            }
            $stripeCfg['mode'] = $data['stripe_mode'] ?? ($stripeCfg['mode'] ?? 'test');
            $cfg['stripe'] = $stripeCfg;
        }
    }

    private function configureExternalGateway(array &$cfg, array $data): void
    {
        switch ($data['driver']) {
            case 'paytabs':
                $this->configurePaytabsGateway($cfg, $data);
                break;
            case 'tap':
                $this->configureTapGateway($cfg, $data);
                break;
            case 'weaccept':
                $this->configureWeacceptGateway($cfg, $data);
                break;
            case 'paypal':
                $this->configurePaypalGateway($cfg, $data);
                break;
            case 'payeer':
                $this->configurePayeerGateway($cfg, $data);
                break;
        }
    }

    private function configurePaytabsGateway(array &$cfg, array $data): void
    {
        $cfg['paytabs_profile_id'] = $data['paytabs_profile_id'] ?? ($cfg['paytabs_profile_id'] ?? null);
        if (!empty($data['paytabs_server_key']) && $data['paytabs_server_key'] !== '********') {
            $cfg['paytabs_server_key'] = $data['paytabs_server_key'];
        }
    }

    private function configureTapGateway(array &$cfg, array $data): void
    {
        if (!empty($data['tap_secret_key']) && $data['tap_secret_key'] !== '********') {
            $cfg['tap_secret_key'] = $data['tap_secret_key'];
        }
        $cfg['tap_public_key'] = $data['tap_public_key'] ?? ($cfg['tap_public_key'] ?? null);
        $cfg['tap_currency'] = $data['tap_currency'] ?? ($cfg['tap_currency'] ?? 'USD');
        $cfg['tap_lang'] = $data['tap_lang'] ?? ($cfg['tap_lang'] ?? 'en');
    }

    private function configureWeacceptGateway(array &$cfg, array $data): void
    {
        if (!empty($data['weaccept_api_key']) && $data['weaccept_api_key'] !== '********') {
            $cfg['weaccept_api_key'] = $data['weaccept_api_key'];
        }
        if (!empty($data['weaccept_hmac_secret']) && $data['weaccept_hmac_secret'] !== '********') {
            $cfg['weaccept_hmac_secret'] = $data['weaccept_hmac_secret'];
        }
        $cfg['weaccept_integration_id'] = $data['weaccept_integration_id'] ?? ($cfg['weaccept_integration_id'] ?? null);
        if (!empty($data['weaccept_iframe_id'])) {
            $cfg['weaccept_iframe_id'] = $data['weaccept_iframe_id'];
        }
        if (!empty($data['weaccept_currency'])) {
            $cfg['weaccept_currency'] = strtoupper($data['weaccept_currency']);
        }
        if (!empty($data['weaccept_api_base'])) {
            $cfg['api_base'] = rtrim($data['weaccept_api_base'], '/');
        }
    }

    private function configurePaypalGateway(array &$cfg, array $data): void
    {
        if (!empty($data['paypal_client_id'])) {
            $cfg['paypal_client_id'] = $data['paypal_client_id'];
        }
        if (!empty($data['paypal_secret']) && $data['paypal_secret'] !== '********') {
            $cfg['paypal_secret'] = $data['paypal_secret'];
        }
        $cfg['paypal_mode'] = $data['paypal_mode'] ?? ($cfg['paypal_mode'] ?? 'sandbox');
    }

    private function configurePayeerGateway(array &$cfg, array $data): void
    {
        if (!empty($data['payeer_merchant_id'])) {
            $cfg['payeer_merchant_id'] = $data['payeer_merchant_id'];
        }
        if (!empty($data['payeer_secret_key']) && $data['payeer_secret_key'] !== '********') {
            $cfg['payeer_secret_key'] = $data['payeer_secret_key'];
        }
    }
}
