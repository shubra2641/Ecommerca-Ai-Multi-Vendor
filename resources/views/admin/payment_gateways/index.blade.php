@extends('layouts.admin')

@section('title', __('Payment Gateways'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Payment Gateways') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage payment processing gateways') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Gateways Cards -->
        <div class="row">
            @foreach($gateways as $gateway)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">
                            <i class="fas fa-credit-card"></i>
                            {{ $gateway->name }}
                        </h3>
                        <div class="d-flex align-items-center gap-2">
                            <div class="admin-badge {{ $gateway->enabled ? 'admin-badge-success' : 'admin-badge-danger' }}">
                                {{ $gateway->enabled ? __('Enabled') : __('Disabled') }}
                            </div>
                            @if($gateway->driver === 'offline')
                            <div class="admin-badge {{ $gateway->requires_transfer_image ? 'admin-badge-info' : 'admin-badge-secondary' }}">
                                {{ $gateway->requires_transfer_image ? __('Image Required') : __('No Image') }}
                            </div>
                            @endif
                            <form action="{{ route('admin.payment-gateways.toggle', $gateway->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="admin-btn {{ $gateway->enabled ? 'admin-btn-danger admin-btn-sm' : 'admin-btn-success admin-btn-sm' }}">
                                    <i class="fas fa-toggle-{{ $gateway->enabled ? 'off' : 'on' }}"></i>
                                    {{ $gateway->enabled ? __('Disable') : __('Enable') }}
                                </button>
                            </form>
                            @if($gateway->driver === 'offline')
                            <form action="{{ route('admin.payment-gateways.toggle-transfer-image', $gateway->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="admin-btn {{ $gateway->requires_transfer_image ? 'admin-btn-warning admin-btn-sm' : 'admin-btn-secondary admin-btn-sm' }}">
                                    <i class="fas fa-image"></i>
                                    {{ $gateway->requires_transfer_image ? __('Disable Image') : __('Require Image') }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.payment-gateways.update', $gateway) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="driver" value="{{ $gateway->driver }}">
                        <div class="admin-card-body">
                            <div class="admin-form-grid">
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Name') }}</label>
                                    <input name="name" value="{{ old('name', $gateway->name) }}" class="admin-form-input" required>
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Slug') }}</label>
                                    <input name="slug" value="{{ old('slug', $gateway->slug) }}" class="admin-form-input">
                                </div>
                                <div class="admin-form-group">
                                    <div class="admin-checkbox">
                                        <input type="checkbox" name="enabled" value="1" id="enabled-{{ $gateway->id }}" class="admin-checkbox-input" {{ old('enabled', $gateway->enabled) ? 'checked' : '' }}>
                                        <label for="enabled-{{ $gateway->id }}" class="admin-checkbox-label">
                                            <i class="fas fa-check-circle"></i>
                                            {{ __('Enabled') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            @if($gateway->driver === 'stripe')
                            <hr>
                            <h5>{{ __('Stripe Configuration') }}</h5>
                            <div class="admin-form-grid">
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Publishable Key') }}</label>
                                    <input name="stripe_publishable_key" value="{{ old('stripe_publishable_key', $gateway->getStripeConfig()['publishable_key'] ?? '') }}" class="admin-form-input">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Secret Key') }}</label>
                                    <input name="stripe_secret_key" value="" placeholder="{{ !empty($gateway->getStripeConfig()['secret_key'] ?? '') ? '********' : '' }}" class="admin-form-input">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Webhook Secret') }}</label>
                                    <input name="stripe_webhook_secret" value="" placeholder="{{ !empty($gateway->getStripeConfig()['webhook_secret'] ?? '') ? '********' : '' }}" class="admin-form-input">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Mode') }}</label>
                                    <select name="stripe_mode" class="admin-form-select">
                                        <option value="test" {{ old('stripe_mode', $gateway->getStripeConfig()['mode'] ?? 'test') === 'test' ? 'selected' : '' }}>{{ __('Test') }}</option>
                                        <option value="live" {{ old('stripe_mode', $gateway->getStripeConfig()['mode'] ?? '') === 'live' ? 'selected' : '' }}>{{ __('Live') }}</option>
                                    </select>
                                </div>
                            </div>
                            @elseif($gateway->driver === 'offline')
                            <hr>
                            <h5>{{ __('Offline / Bank Transfer') }}</h5>
                            <div class="admin-form-grid">
                                <div class="admin-form-group admin-form-group-full">
                                    <label class="admin-form-label">{{ __('Transfer Instructions (HTML allowed)') }}</label>
                                    <textarea name="transfer_instructions" rows="3" class="admin-form-input">{{ old('transfer_instructions', $gateway->transfer_instructions) }}</textarea>
                                </div>
                                <div class="admin-form-group">
                                    <div class="admin-checkbox">
                                        <input type="checkbox" name="requires_transfer_image" value="1" id="requires_transfer_image-{{ $gateway->id }}" class="admin-checkbox-input" {{ old('requires_transfer_image', $gateway->requires_transfer_image) ? 'checked' : '' }}>
                                        <label for="requires_transfer_image-{{ $gateway->id }}" class="admin-checkbox-label">
                                            <i class="fas fa-image"></i>
                                            {{ __('Require transfer image') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @elseif($gateway->driver === 'paytabs')
                            <hr>
                            <h5>{{ __('PayTabs Configuration') }}</h5>
                            <div class="admin-form-grid">
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Profile ID') }}</label>
                                    <input name="paytabs_profile_id" value="{{ old('paytabs_profile_id', $gateway->config['paytabs_profile_id'] ?? '') }}" class="admin-form-input">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Server Key') }}</label>
                                    <input name="paytabs_server_key" value="" placeholder="{{ !empty($gateway->config['paytabs_server_key'] ?? '') ? '********' : '' }}" class="admin-form-input">
                                </div>
                            </div>
                            @elseif($gateway->driver === 'tap')
                            <hr>
                            <h5>{{ __('Tap Configuration') }}</h5>
                            <div class="admin-form-grid">
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Secret Key') }}</label>
                                    <input name="tap_secret_key" value="" placeholder="{{ !empty($gateway->config['tap_secret_key'] ?? '') ? '********' : '' }}" class="admin-form-input">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Public Key') }}</label>
                                    <input name="tap_public_key" value="{{ old('tap_public_key', $gateway->config['tap_public_key'] ?? '') }}" class="admin-form-input">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Currency') }}</label>
                                    <input name="tap_currency" value="{{ old('tap_currency', $gateway->config['tap_currency'] ?? 'USD') }}" class="admin-form-input">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Language Code') }}</label>
                                    <input name="tap_lang" value="{{ old('tap_lang', $gateway->config['tap_lang'] ?? 'en') }}" class="admin-form-input">
                                </div>
                            </div>
                            @elseif($gateway->driver === 'weaccept')
                            <hr>
                            <h5>{{ __('WeAccept Configuration') }}</h5>
                            <div class="admin-form-grid">
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('API Key') }}</label>
                                    <input name="weaccept_api_key" value="" placeholder="{{ !empty($gateway->config['weaccept_api_key'] ?? '') ? '********' : '' }}" class="admin-form-input">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('HMAC Secret') }}</label>
                                    <input name="weaccept_hmac_secret" value="" placeholder="{{ !empty($gateway->config['weaccept_hmac_secret'] ?? '') ? '********' : '' }}" class="admin-form-input">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Integration ID') }}</label>
                                    <input name="weaccept_integration_id" value="{{ old('weaccept_integration_id', $gateway->config['weaccept_integration_id'] ?? '') }}" class="admin-form-input">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Iframe ID') }}</label>
                                    <input name="weaccept_iframe_id" value="{{ old('weaccept_iframe_id', $gateway->config['weaccept_iframe_id'] ?? ($gateway->config['iframe_id'] ?? '')) }}" class="admin-form-input" placeholder="e.g. 371273">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Currency') }}</label>
                                    <input name="weaccept_currency" value="{{ old('weaccept_currency', $gateway->config['weaccept_currency'] ?? ($gateway->config['paymob_currency'] ?? 'EGP')) }}" class="admin-form-input" placeholder="EGP">
                                </div>
                                <div class="admin-form-group admin-form-group-full">
                                    <label class="admin-form-label">{{ __('API Base (optional)') }}</label>
                                    <input name="weaccept_api_base" value="{{ old('weaccept_api_base', $gateway->config['api_base'] ?? '') }}" class="admin-form-input" placeholder="https://accept.paymob.com">
                                </div>
                            </div>
                            @elseif($gateway->driver === 'paypal')
                            <hr>
                            <h5>{{ __('PayPal Configuration') }}</h5>
                            <div class="admin-form-grid">
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Client ID') }}</label>
                                    <input name="paypal_client_id" value="{{ old('paypal_client_id', $gateway->config['paypal_client_id'] ?? '') }}" class="admin-form-input">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Secret') }}</label>
                                    <input name="paypal_secret" value="" placeholder="{{ !empty($gateway->config['paypal_secret'] ?? '') ? '********' : '' }}" class="admin-form-input">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Mode') }}</label>
                                    <select name="paypal_mode" class="admin-form-select">
                                        <option value="sandbox" {{ old('paypal_mode', $gateway->config['paypal_mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                        <option value="live" {{ old('paypal_mode', $gateway->config['paypal_mode'] ?? '') === 'live' ? 'selected' : '' }}>Live</option>
                                    </select>
                                </div>
                            </div>
                            @elseif($gateway->driver === 'payeer')
                            <hr>
                            <h5>{{ __('Payeer Configuration') }}</h5>
                            <div class="admin-form-grid">
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Merchant ID') }}</label>
                                    <input name="payeer_merchant_id" value="{{ old('payeer_merchant_id', $gateway->config['payeer_merchant_id'] ?? '') }}" class="admin-form-input">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Secret Key') }}</label>
                                    <input name="payeer_secret_key" value="" placeholder="{{ !empty($gateway->config['payeer_secret_key'] ?? '') ? '********' : '' }}" class="admin-form-input">
                                </div>
                            </div>
                            @elseif($gateway->driver === 'cod')
                            <hr>
                            <h5>{{ __('Cash on Delivery') }}</h5>
                            <div class="admin-form-grid">
                                <div class="admin-form-group admin-form-group-full">
                                    <p class="small-muted">{{ __('No configuration required. Payment will be collected upon delivery.') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="admin-card-footer">
                            <button type="submit" class="admin-btn admin-btn-primary">{{ __('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection