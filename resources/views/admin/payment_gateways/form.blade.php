@extends('layouts.admin')

@section('title', $gateway->exists ? __('Edit Gateway') : __('Create Gateway'))

@section('content')
@include('admin.partials.page-header', ['title'=>$gateway->exists ? __('Edit Gateway') : __('Create Gateway'),'actions'=>'<a href="'.route('admin.payment-gateways.index').'" class="btn btn-secondary">'.__('Back').'</a>'])

<div class="card modern-card">
  <form method="POST"
      action="{{ $gateway->exists ? route('admin.payment-gateways.update', $gateway) : route('admin.payment-gateways.store') }}"
      class="admin-form">
    @csrf
    @if($gateway->exists)
    @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">{{ __('Name') }}</label>
            <input name="name" value="{{ old('name', $gateway->name) }}"
                class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('Slug') }}</label>
            <input name="slug" value="{{ old('slug', $gateway->slug) }}"
                class="form-control @error('slug') is-invalid @enderror">
            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('Driver') }}</label>
            @php($availableDrivers = [])
            @php($existingConfig = $gateway->getCredentials() ?? ($gateway->config ?? []))
            <select name="driver" id="driver" class="form-control @error('driver') is-invalid @enderror"
                {{ $gateway->exists ? 'disabled' : '' }} required
                data-config-base-url="{{ url('admin/payment-gateways-management/config-fields') }}"
                data-existing-config='{{ e(json_encode($existingConfig, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}'>
                <option value="">-- {{ __('Choose') }} --</option>
                {{-- include built-in options first for clarity --}}
                <option value="stripe" {{ old('driver', $gateway->driver) === 'stripe' ? 'selected' : '' }}>Stripe
                </option>
                <option value="offline" {{ old('driver', $gateway->driver) === 'offline' ? 'selected' : '' }}>
                    {{ __('Offline / Bank Transfer') }}</option>
                <option value="paytabs" {{ old('driver', $gateway->driver) === 'paytabs' ? 'selected' : '' }}>PayTabs</option>
                <option value="tap" {{ old('driver', $gateway->driver) === 'tap' ? 'selected' : '' }}>Tap</option>
                <option value="weaccept" {{ old('driver', $gateway->driver) === 'weaccept' ? 'selected' : '' }}>WeAccept</option>
                <option value="paypal" {{ old('driver', $gateway->driver) === 'paypal' ? 'selected' : '' }}>PayPal</option>
                <option value="payeer" {{ old('driver', $gateway->driver) === 'payeer' ? 'selected' : '' }}>Payeer</option>
                {{-- Payrexx removed --}}
                {{-- dynamic drivers removed --}}
            </select>
            @if($gateway->exists)
            <input type="hidden" name="driver" value="{{ $gateway->driver }}">
            @endif
            @error('driver')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2 d-flex align-items-center mt-4">
            <div class="form-check">
                <input type="checkbox" name="enabled" value="1" id="enabled" class="form-check-input"
                    {{ old('enabled', $gateway->enabled) ? 'checked' : '' }}>
                <label for="enabled" class="form-check-label">{{ __('Enabled') }}</label>
            </div>
        </div>
    </div>

    {{-- PayPal driver removed from admin UI --}}

    <hr>

    <!-- Stripe Fields -->
    <div id="driver-stripe" class="driver-fields envato-hidden">
        <h5 class="mt-2">Stripe</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('Publishable Key') }}</label>
                @php($stripeCfg = $gateway->getStripeConfig())
                <input name="stripe_publishable_key"
                    value="{{ old('stripe_publishable_key', $stripeCfg['publishable_key'] ?? '') }}"
                    class="form-control @error('stripe_publishable_key') is-invalid @enderror">
                @error('stripe_publishable_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('Secret Key') }}</label>
                <input name="stripe_secret_key" value=""
                    placeholder="{{ ($gateway->exists && !empty($stripeCfg['secret_key'])) ? '********' : '' }}"
                    class="form-control @error('stripe_secret_key') is-invalid @enderror">
                @error('stripe_secret_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('Webhook Secret') }}</label>
                <input name="stripe_webhook_secret" value=""
                    placeholder="{{ ($gateway->exists && !empty($stripeCfg['webhook_secret'])) ? '********' : '' }}"
                    class="form-control @error('stripe_webhook_secret') is-invalid @enderror">
                @error('stripe_webhook_secret')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('Mode') }}</label>
                <select name="stripe_mode" class="form-control @error('stripe_mode') is-invalid @enderror">
                    <option value="test"
                        {{ old('stripe_mode', $stripeCfg['mode'] ?? 'test')==='test' ? 'selected' : '' }}>
                        {{ __('Test') }}</option>
                    <option value="live"
                        {{ old('stripe_mode', $stripeCfg['mode'] ?? null) === 'live' ? 'selected' : '' }}>
                        {{ __('Live') }}</option>
                </select>
                @error('stripe_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <!-- Offline Fields -->
    <div id="driver-offline" class="driver-fields envato-hidden">
        <h5 class="mt-2">{{ __('Offline / Bank Transfer') }}</h5>
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">{{ __('Transfer Instructions (HTML allowed)') }}</label>
                <textarea name="transfer_instructions" rows="5"
                    class="form-control @error('transfer_instructions') is-invalid @enderror">{{ old('transfer_instructions', $gateway->transfer_instructions) }}</textarea>
                @error('transfer_instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <div class="form-check mt-4">
                    <input type="checkbox" name="requires_transfer_image" value="1" id="requires_transfer_image"
                        class="form-check-input"
                        {{ old('requires_transfer_image', $gateway->requires_transfer_image) ? 'checked' : '' }}>
                    <label for="requires_transfer_image"
                        class="form-check-label">{{ __('Require transfer image') }}</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic driver config -->
    <div id="dynamic-driver-config" class="mt-3">
        <h5 class="mt-2">{{ __('Gateway Configuration') }}</h5>
        <div id="dynamic-config-fields">
            {{-- Fields will be rendered here by JS when a dynamic driver is selected --}}
        </div>
        
        <!-- PayTabs Fields -->
        <div id="driver-paytabs" class="driver-fields envato-hidden">
            <h5 class="mt-2">PayTabs</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('Profile ID') }}</label>
                    <input name="paytabs_profile_id" value="{{ old('paytabs_profile_id', $gateway->config['paytabs_profile_id'] ?? '') }}" class="form-control">
                </div>
                <div class="col-md-8">
                    <label class="form-label">{{ __('Server Key') }}</label>
                    <input name="paytabs_server_key" value="" placeholder="{{ !empty(($gateway->config['paytabs_server_key'] ?? null)) ? '********' : '' }}" class="form-control">
                </div>
            </div>
        </div>

        <!-- Tap Fields -->
        <div id="driver-tap" class="driver-fields envato-hidden">
            <h5 class="mt-2">Tap</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('Secret Key') }}</label>
                    <input name="tap_secret_key" value="" placeholder="{{ !empty(($gateway->config['tap_secret_key'] ?? null)) ? '********' : '' }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Public Key') }}</label>
                    <input name="tap_public_key" value="{{ old('tap_public_key', $gateway->config['tap_public_key'] ?? '') }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('Currency') }}</label>
                    <input name="tap_currency" value="{{ old('tap_currency', $gateway->config['tap_currency'] ?? 'USD') }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('Language Code') }}</label>
                    <input name="tap_lang" value="{{ old('tap_lang', $gateway->config['tap_lang'] ?? 'en') }}" class="form-control">
                </div>
            </div>
        </div>

        <!-- WeAccept Fields -->
        <div id="driver-weaccept" class="driver-fields envato-hidden">
            <h5 class="mt-2">WeAccept (Accept / PayMob)</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('API Key') }}</label>
                    <input name="weaccept_api_key" value="" placeholder="{{ !empty(($gateway->config['weaccept_api_key'] ?? null)) ? '********' : '' }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('HMAC Secret') }}</label>
                    <input name="weaccept_hmac_secret" value="" placeholder="{{ !empty(($gateway->config['weaccept_hmac_secret'] ?? null)) ? '********' : '' }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Integration ID') }}</label>
                    <input name="weaccept_integration_id" value="{{ old('weaccept_integration_id', $gateway->config['weaccept_integration_id'] ?? '') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Iframe ID') }}</label>
                    <input name="weaccept_iframe_id" value="{{ old('weaccept_iframe_id', $gateway->config['weaccept_iframe_id'] ?? ($gateway->config['iframe_id'] ?? '')) }}" class="form-control" placeholder="e.g. 371273">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('Currency') }}</label>
                    <input name="weaccept_currency" value="{{ old('weaccept_currency', $gateway->config['weaccept_currency'] ?? ($gateway->config['paymob_currency'] ?? 'EGP')) }}" class="form-control" placeholder="EGP">
                </div>
                <div class="col-md-8">
                    <label class="form-label">{{ __('API Base (optional)') }}</label>
                    <input name="weaccept_api_base" value="{{ old('weaccept_api_base', $gateway->config['api_base'] ?? '') }}" class="form-control" placeholder="https://accept.paymob.com">
                    <small class="text-muted">{{ __('Leave empty to use default https://accept.paymob.com') }}</small>
                </div>
            </div>
        </div>

        <!-- PayPal Fields -->
        <div id="driver-paypal" class="driver-fields envato-hidden">
            <h5 class="mt-2">PayPal</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('Client ID') }}</label>
                    <input name="paypal_client_id" value="{{ old('paypal_client_id', $gateway->config['paypal_client_id'] ?? '') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Secret') }}</label>
                    <input name="paypal_secret" value="" placeholder="{{ !empty(($gateway->config['paypal_secret'] ?? null)) ? '********' : '' }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('Mode') }}</label>
                    <select name="paypal_mode" class="form-control">
                        <option value="sandbox" {{ old('paypal_mode', $gateway->config['paypal_mode'] ?? 'sandbox')==='sandbox' ? 'selected' : '' }}>Sandbox</option>
                        <option value="live" {{ old('paypal_mode', $gateway->config['paypal_mode'] ?? '')==='live' ? 'selected' : '' }}>Live</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Payeer Fields -->
        <div id="driver-payeer" class="driver-fields envato-hidden">
            <h5 class="mt-2">Payeer</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('Merchant ID') }}</label>
                    <input name="payeer_merchant_id" value="{{ old('payeer_merchant_id', $gateway->config['payeer_merchant_id'] ?? '') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Secret Key') }}</label>
                    <input name="payeer_secret_key" value="" placeholder="{{ !empty(($gateway->config['payeer_secret_key'] ?? null)) ? '********' : '' }}" class="form-control">
                </div>
            </div>
        </div>

    {{-- Payrexx removed --}}
         
         <div id="custom-rows" class="mt-2">
            {{-- legacy custom key/value rows may be appended here --}}
        </div>
        <div class="mt-2">
            <button type="button" id="add-custom"
                class="btn btn-sm btn-outline-secondary">{{ __('Add custom key') }}</button>
        </div>
    </div>

    <div class="card-footer text-end">
        <a href="{{ route('admin.payment-gateways.index') }}" class="btn btn-secondary me-2">{{ __('Cancel') }}</a>
        <button class="btn btn-primary">{{ $gateway->exists ? __('Update') : __('Create') }}</button>
    </div>
  </form>
</div>

<script src="{{ asset('admin/js/payment-gateway-form.js') }}" defer></script>
@endsection