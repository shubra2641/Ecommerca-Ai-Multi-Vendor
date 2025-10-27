@extends('front.layout')
@section('title', __('Addresses').' - '.config('app.name'))
@section('content')

<section class="account-section">
    <div class="container account-grid">
        @include('front.account._sidebar')
        <main class="account-main">
            <div class="dashboard-page">

                <!-- Header -->
                <div class="order-title-card">
                    <div class="title-row">
                        <div class="title-content">
                            <h1 class="modern-order-title">
                                <i class="fas fa-map-marker-alt title-icon"></i>
                                {{ __('Addresses') }}
                            </h1>
                            <p class="order-date-modern">
                                <i class="fas fa-check-circle"></i>
                                {{ __('Manage your addresses') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Add/Edit Address Form -->
                <div class="modern-card">
                    <div class="card-header-modern">
                        <h3 class="card-title-modern">
                            <i class="fas fa-plus"></i>
                            {{ $editingAddress ? __('Edit Address') : __('Add New Address') }}
                        </h3>
                    </div>
                    <div class="card-body-padding">
                        <form method="POST" action="{{ $editingAddress ? route('user.addresses.update', $editingAddress) : route('user.addresses.store') }}">
                            @csrf
                            @if($editingAddress)
                            @method('PUT')
                            @endif

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="field-label">{{ __('Title') }}</label>
                                    <input type="text" name="title" value="{{ old('title', $editingAddress->title ?? '') }}"
                                        class="form-input @error('title') is-invalid @enderror">
                                    @error('title')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label class="field-label">{{ __('Name') }}</label>
                                    <input type="text" name="name" value="{{ old('name', $editingAddress->name ?? '') }}"
                                        class="form-input @error('name') is-invalid @enderror">
                                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="field-label">{{ __('Phone') }}</label>
                                    <input type="text" name="phone" value="{{ old('phone', $editingAddress->phone ?? '') }}"
                                        class="form-input @error('phone') is-invalid @enderror">
                                    @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="field-label">{{ __('Country') }}</label>
                                    <select name="country_id" id="new_country"
                                        class="form-input @error('country_id') is-invalid @enderror"
                                        data-loading-text="{{ __('Loading...') }}">
                                        <option value="">{{ __('Select country') }}</option>
                                        @foreach($countries as $c)
                                        <option value="{{ $c->id }}" {{ old('country_id', $editingAddress->country_id ?? '') == $c->id ? 'selected':'' }}>
                                            {{ $c->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('country_id')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label class="field-label">{{ __('Governorate') }}</label>
                                    <select name="governorate_id" id="new_governorate"
                                        class="form-input @error('governorate_id') is-invalid @enderror" disabled>
                                        <option value="">{{ __('Select governorate') }}</option>
                                        @if($editingAddress && $editingAddress->governorate)
                                        <option value="{{ $editingAddress->governorate->id }}" selected>{{ $editingAddress->governorate->name }}</option>
                                        @endif
                                    </select>
                                    @error('governorate_id')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="field-label">{{ __('City') }}</label>
                                    <select name="city_id" id="new_city"
                                        class="form-input @error('city_id') is-invalid @enderror" disabled>
                                        <option value="">{{ __('Select city') }}</option>
                                        @if($editingAddress && $editingAddress->city)
                                        <option value="{{ $editingAddress->city->id }}" selected>{{ $editingAddress->city->name }}</option>
                                        @endif
                                    </select>
                                    @error('city_id')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label class="field-label">{{ __('Postal Code') }}</label>
                                    <input type="text" name="postal_code" value="{{ old('postal_code', $editingAddress->postal_code ?? '') }}"
                                        class="form-input @error('postal_code') is-invalid @enderror">
                                    @error('postal_code')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="field-label">{{ __('Address Line 1') }}</label>
                                    <input type="text" name="line1" value="{{ old('line1', $editingAddress->line1 ?? '') }}"
                                        class="form-input @error('line1') is-invalid @enderror">
                                    @error('line1')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label class="field-label">{{ __('Address Line 2') }}</label>
                                    <input type="text" name="line2" value="{{ old('line2', $editingAddress->line2 ?? '') }}"
                                        class="form-input @error('line2') is-invalid @enderror">
                                    @error('line2')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="is_default" value="1" {{ old('is_default', $editingAddress->is_default ?? false) ? 'checked':'' }}>
                                        {{ __('Set as default') }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-row">
                                <button class="btn-action-modern btn-primary btn-full" type="submit">
                                    <i class="fas fa-check-circle"></i>
                                    {{ $editingAddress ? __('Update address') : __('Save address') }}
                                </button>
                                @if($editingAddress)
                                <a href="{{ route('user.addresses') }}" class="btn-action-modern btn-secondary btn-full">
                                    <i class="fas fa-times"></i>
                                    {{ __('Cancel') }}
                                </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Address Groups -->
                <div class="address-groups-modern">
                    @if($addrDefault)
                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h3 class="card-title-modern">
                                <i class="fas fa-check-circle"></i>
                                {{ __('Default address') }}
                            </h3>
                        </div>
                        <div class="card-body-padding">
                            @include('front.account.partials.address_card',['ad'=>$addrDefault])
                        </div>
                    </div>
                    @endif

                    <div class="modern-card">
                        <div class="card-header-modern">
                            <h3 class="card-title-modern">
                                <i class="fas fa-address-book"></i>
                                {{ __('Other addresses') }}
                            </h3>
                        </div>
                        <div class="card-body-padding">
                            @if($addrOthers && $addrOthers->count() > 0)
                            <div class="address-cards-grid">
                                @foreach($addrOthers as $ad)
                                @include('front.account.partials.address_card',['ad'=>$ad])
                                @endforeach
                            </div>
                            @else
                            <div class="empty-state empty-state-small">
                                <i class="fas fa-box-open empty-icon icon-large"></i>
                                <p class="empty-text">{{ __('No other addresses.') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</section>
@endsection