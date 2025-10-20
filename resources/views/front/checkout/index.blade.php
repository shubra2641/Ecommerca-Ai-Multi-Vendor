@extends('front.layout')
@section('title', __('Checkout').' - '.config('app.name'))
@section('content')
<div id="checkout-root" hidden data-config='{{ $checkoutConfigJson ?? "[]" }}' data-select-governorate="{{ __('Select Governorate') }}" data-select-city="{{ __('Select City') }}" data-select-shipping-company="{{ __('Select Shipping Company') }}" data-currency-symbol="{{ $currency_symbol }}" data-base-total="{{ $displayDiscountedTotal ?? $total }}"></div>
<section class="products-section products-section--checkout">
    <div class="container container--wide">
        <x-breadcrumb :items="[
            ['title' => __('Home'), 'url' => route('home'), 'icon' => 'fas fa-home'],
            ['title' => __('Cart'), 'url' => route('cart.index'), 'icon' => 'fas fa-shopping-cart'],
            ['title' => __('Checkout'), 'url' => '#']
        ]" />
    </div>
</section>
<section class="checkout-section">
    <div class="container container--wide">
        <form action="/checkout/submit" method="post" class="checkout-form" enctype="multipart/form-data">
            @csrf
            @if($errors->any())
            <div class="alert alert-danger small">
                <ul class="list-reset">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            @push('styles')
            <link rel="stylesheet" href="{{ asset('front/css/checkout.css') }}">
            @endpush
            <div class="checkout-row">
                <div class="checkout-left">
                    <div class="panel-card">
                        <h3 class="panel-title">{{ __('Shipping Address') }}</h3>
                        <div class="address-card">
                            <div class="address-left">
                                <div class="address-title">{{ __('Deliver to') }}</div>
                                <div class="small-muted">
                                    {{ __('Choose one of your saved addresses or enter a new one') }}
                                </div>
                            </div>
                            <div class="address-actions"><a href="#addresses-manage"
                                    class="btn btn-sm btn-outline">{{ __('Manage') }}</a></div>
                        </div>

                        {{-- Addresses list: clickable/selectable cards --}}
                        <div id="addresses-list" class="mt-2">
                            @if(!empty($addresses) && $addresses->count())
                            <div class="addresses-grid">
                                @foreach($addresses as $addr)
                                <label class="address-card-selectable" data-addr-id="{{ $addr->id }}"
                                    data-country="{{ $addr->country_id }}"
                                    data-governorate="{{ $addr->governorate_id }}" data-city="{{ $addr->city_id }}"
                                    data-line1="{{ e($addr->line1) }}" data-line2="{{ e($addr->line2) }}"
                                    data-phone="{{ e($addr->phone) }}">
                                    <input type="radio" name="selected_address" value="{{ $addr->id }}"
                                        {{ $addr->is_default ? 'checked' : '' }}>
                                    <div class="address-card-body">
                                        <div class="address-name">{{ $addr->name ?? auth()->user()->name }}</div>
                                        <div class="small-muted">
                                            {{ $addr->line1 }}{{ $addr->line2 ? ', ' . $addr->line2 : '' }}
                                        </div>
                                        <div class="small-muted">{{ $addr->phone }}</div>
                                        @if($addr->is_default)<div class="badge small">{{ __('Default') }}</div>@endif
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @else
                            <div class="small-muted">
                                {{ __('No saved addresses. Please enter a delivery address below.') }}
                            </div>
                            @endif
                        </div>
                        <div class="mt-3">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Name') }}</label>
                                    <input type="text" name="customer_name" class="form-control" required minlength="2"
                                        maxlength="120"
                                        value="{{ old('customer_name', $defaultAddress->name ?? auth()->user()->name ?? '') }}">
                                    @error('customer_name') <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Email') }}</label>
                                    <input type="email" name="customer_email" class="form-control" required
                                        value="{{ old('customer_email', auth()->user()->email ?? '') }}">
                                    @error('customer_email') <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Phone') }}</label>
                                    <!-- Use data-pattern to avoid browsers compiling the regex before our sanitizer runs (CSP-safe) -->
                                    <input type="tel" name="customer_phone" class="form-control" required
                                        data-pattern="^[0-9+() \-]{6,20}$" title="{{ __('Valid phone required') }}"
                                        value="{{ old('customer_phone', $defaultAddress->phone ?? auth()->user()->phone ?? '') }}">
                                    @error('customer_phone') <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Country') }}</label>
                                    <select name="country" id="country-select" class="form-control" required>
                                        <option value="">{{ __('Select Country') }}</option>
                                        @foreach(\App\Models\Country::where('active',1)->get() as $c)
                                        <option value="{{ $c->id }}"
                                            {{ (old('country', $defaultAddress->country_id ?? auth()->user()->country_id ?? '') == $c->id) ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Governorate') }}</label>
                                    <select name="governorate" id="governorate-select" class="form-control" required>
                                        <option value="">{{ __('Select Governorate') }}</option>
                                        @if(old('country') || (!empty($defaultAddress) && $defaultAddress->country_id) || (auth()->user() && auth()->user()->country_id))
                                        @php
                                        $selectedCountry = old('country', $defaultAddress->country_id ?? auth()->user()->country_id ?? '');
                                        $governorates = \App\Models\Governorate::where('country_id', $selectedCountry)->where('active', 1)->get();
                                        $selectedGovernorate = old('governorate', $defaultAddress->governorate_id ?? auth()->user()->governorate_id ?? '');

                                        // Auto-select first governorate if none selected and country is selected
                                        if (!$selectedGovernorate && $selectedCountry && $governorates->count() > 0) {
                                        $selectedGovernorate = $governorates->first()->id;
                                        }
                                        @endphp
                                        @foreach($governorates as $gov)
                                        <option value="{{ $gov->id }}"
                                            {{ ($selectedGovernorate == $gov->id) ? 'selected' : '' }}>
                                            {{ $gov->name }}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('City') }}</label>
                                    <select name="city" id="city-select" class="form-control" required>
                                        <option value="">{{ __('Select City') }}</option>
                                        @if(old('governorate') || (!empty($defaultAddress) && $defaultAddress->governorate_id) || (auth()->user() && auth()->user()->governorate_id))
                                        @php
                                        $selectedGovernorate = old('governorate', $defaultAddress->governorate_id ?? auth()->user()->governorate_id ?? '');
                                        $cities = \App\Models\City::where('governorate_id', $selectedGovernorate)->where('active', 1)->get();
                                        $selectedCity = old('city', $defaultAddress->city_id ?? auth()->user()->city_id ?? '');

                                        // Auto-select first city if none selected and governorate is selected
                                        if (!$selectedCity && $selectedGovernorate && $cities->count() > 0) {
                                        $selectedCity = $cities->first()->id;
                                        }
                                        @endphp
                                        @foreach($cities as $city)
                                        <option value="{{ $city->id }}"
                                            {{ ($selectedCity == $city->id) ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Shipping Company') }}</label>
                                    <select name="shipping_zone_id" id="shipping-zone-select" class="form-control">
                                        <option value="">{{ __('Select Shipping Company') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Delivery Address') }}</label>
                                <input type="text" name="customer_address" class="form-control" required minlength="5"
                                    maxlength="190"
                                    value="{{ old('customer_address', $defaultAddress->line1 ?? auth()->user()->address ?? '') }}">
                                @error('customer_address') <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Notes') }}</label>
                                <textarea name="notes" class="form-control" rows="12">{{ old('notes') }}</textarea>
                                @error('notes') <div class="text-danger small">
                                    {{ $message }}
                                </div>@enderror
                            </div>
                            {{-- Shipping Cost Display --}}
                            <div id="shipping-info" class="shipping-info mt-3 hidden">
                                <div class="shipping-cost">
                                    <strong>{{ __('Shipping Cost') }}: <span id="shipping-cost-display"></span></strong>
                                </div>
                                <div class="shipping-company small text-muted">
                                    {{ __('Shipping Company') }}: <span id="shipping-company-display"></span>
                                </div>
                                <div id="shipping-days" class="shipping-days small text-muted hidden">
                                    {{ __('Estimated delivery') }}: <span id="shipping-days-display"></span> {{ __('days') }}
                                </div>
                                <input type="hidden" name="shipping_price" id="shipping-cost-input" value="">
                                <input type="hidden" name="shipping_estimated_days" id="shipping-days-input" value="">
                            </div>
                        </div>
                    </div>
                    <div class="panel-card">
                        <h3 class="panel-title">{{ __('Your Order') }}</h3>
                        @foreach(($coItems ?? $items) as $it)
                        <div class="order-item">
                            <img src="{{ $it['display_image'] ?? '' }}" alt="">
                            <div class="order-item-details">
                                <div class="order-item-title">{{ $it['product']->name }}
                                    @if(!empty($it['variant_label']))
                                    <small class="small-muted">{{ $it['variant_label'] }}</small>
                                    @endif
                                </div>
                                <div class="small-muted">{{ $it['qty'] }} ×
                                    {{ $currency_symbol ?? '$' }}{{ number_format($it['product']->price,2) }}
                                </div>
                            </div>
                            <div class="order-item-price">
                                {{ $currency_symbol ?? '$' }}{{ number_format($it['lineTotal'],2) }}
                            </div>
                        </div>
                        @endforeach
                        <div class="mt-2 small text-muted">
                            {{ __('Get it soon based on shipping option') }}
                        </div>
                    </div>
                    <div class="panel-card">
                        <h3 class="panel-title">{{ __('Payment') }}</h3>
                        @foreach($gateways as $gw)
                        <label class="gateway-item">
                            <input type="radio" name="gateway" value="{{ $gw->slug }}"
                                {{ (old('gateway', ($loop->first ? $gw->slug : null)) == $gw->slug) ? 'checked' : '' }}>
                            <span class="gateway-name">{{ $gw->name }}
                                @if($gw->driver==='offline')<small>({{ __('Offline') }})</small>@endif</span>
                            @if($gw->driver === 'offline' && $gw->transfer_instructions)
                            <div class="gateway-instructions small-muted">{!! \App\Services\HtmlSanitizer::sanitizeEmbed($gw->transfer_instructions) !!}</div>
                            @endif
                        </label>
                        @endforeach
                        {{-- Transfer image upload area (shown when selected gateway requires it) --}}
                        <div id="transfer-image-area" class="mt-2 envato-hidden"
                            data-requiring='{{ e(json_encode($gateways->filter(fn($g)=>$g->requires_transfer_image)->pluck('id'), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}'
                            slug'))'>
                            <label class="form-label">{{ __('Upload proof of transfer') }}</label>
                            <div class="small-muted mb-1">
                                {{ __('If your chosen payment method requires a payment receipt, please upload an image here.') }}
                            </div>
                            <input type="file" name="transfer_image" id="transfer_image" accept="image/*"
                                class="form-control-file">
                            @error('transfer_image') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        @if(!count($gateways))
                        <div class="alert alert-warning small">
                            {{ __('No payment gateways available') }}
                        </div>
                        @endif
                    </div>
                </div>
                <aside class="checkout-right">
                    <div class="summary-box panel-card">
                        <h3 class="panel-title">{{ __('Order Summary') }}</h3>
                        <ul class="summary-lines">
                            @foreach(($coItems ?? $items) as $it)
                            <li class="summary-line">
                                <span class="order-item-title">
                                    <span class="title-row"><span class="title-text">{{ $it['product']->name }}</span>
                                        <span class="qty">× {{ $it['qty'] }}</span></span>
                                    @if(!empty($it['variant_label']))
                                    <span class="product-meta">{{ $it['variant_label'] }}</span>
                                    @endif
                                </span>
                                <span>{{ $currency_symbol ?? '$' }}{{ number_format($it['lineTotal'],2) }}</span>
                            </li>
                            @endforeach
                            <li class="summary-line">
                                <span>{{ __('Shipping Fee') }}</span><span class="shipping-amount">-</span>
                            </li>
                            @if(isset($coupon) && $coupon)
                            <li class="summary-line">
                                <span>{{ __('Coupon') }} (<strong>{{ $coupon->code }}</strong>)</span>
                                <span
                                    class="coupon-discount-amount">-{{ $currency_symbol ?? '$' }}{{ number_format($discount,2) }}</span>
                            </li>
                            @endif
                            <li class="summary-total">
                                <span>{{ __('Total Incl. VAT') }}</span><span
                                    class="order-total">{{ $currency_symbol ?? '$' }}{{ number_format($displayDiscountedTotal ?? $total,2) }}</span>
                            </li>
                        </ul>
                        <div class="mt-2">
                            <input type="hidden" name="shipping_zone_id" id="input-shipping-zone-id"
                                value="{{ old('shipping_zone_id', '') }}">
                            <input type="hidden" name="shipping_price" id="input-shipping-price"
                                value="{{ old('shipping_price', '') }}">
                            <input type="hidden" name="shipping_country" id="input-shipping-country"
                                value="{{ old('shipping_country', old('country', $defaultAddress->country_id ?? optional(auth()->user())->country_id ?? '')) }}">
                            <input type="hidden" name="shipping_governorate" id="input-shipping-governorate"
                                value="{{ old('shipping_governorate', old('governorate', $defaultAddress->governorate_id ?? optional(auth()->user())->governorate_id ?? '')) }}">
                            <input type="hidden" name="shipping_city" id="input-shipping-city"
                                value="{{ old('shipping_city', old('city', $defaultAddress->city_id ?? optional(auth()->user())->city_id ?? '')) }}">
                            <input type="hidden" name="selected_address_id" id="selected-address-id"
                                value="{{ old('selected_address_id', '') }}">
                            <button class="btn btn-primary btn-place" type="submit">{{ __('Place Order') }}</button>
                        </div>
                    </div>
                </aside>
            </div>
        </form>
    </div>
</section>
@endsection