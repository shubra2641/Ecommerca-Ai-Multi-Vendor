@extends('front.layout')
@section('title', __('Edit Address').' - '.config('app.name'))
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
                                <i class="fas fa-edit title-icon"></i>
                                {{ __('Edit Address') }}
                            </h1>
                            <p class="order-date-modern">
                                <i class="fas fa-check-circle"></i>
                                {{ __('Update your address information') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Edit Address Form -->
                <div class="modern-card">
                    <div class="card-header-modern">
                        <h3 class="card-title-modern">
                            <i class="fas fa-edit"></i>
                            {{ __('Address Information') }}
                        </h3>
                    </div>
                    <div class="card-body-padding">
                        <form method="POST" action="{{ route('user.addresses.update', $address) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="field-label">{{ __('Title') }}</label>
                                    <input type="text" name="title" value="{{ old('title', $address->title) }}"
                                        class="form-input @error('title') is-invalid @enderror">
                                    @error('title')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label class="field-label">{{ __('Name') }}</label>
                                    <input type="text" name="name" value="{{ old('name', $address->name) }}"
                                        class="form-input @error('name') is-invalid @enderror">
                                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="field-label">{{ __('Phone') }}</label>
                                    <input type="text" name="phone" value="{{ old('phone', $address->phone) }}"
                                        class="form-input @error('phone') is-invalid @enderror">
                                    @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="field-label">{{ __('Country') }}</label>
                                    <select name="country_id" id="edit_country"
                                        class="form-input @error('country_id') is-invalid @enderror"
                                        data-loading-text="{{ __('Loading...') }}">
                                        <option value="">{{ __('Select country') }}</option>
                                        @foreach($countries as $c)
                                        <option value="{{ $c->id }}" {{ old('country_id', $address->country_id) == $c->id ? 'selected':'' }}>
                                            {{ $c->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('country_id')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label class="field-label">{{ __('Governorate') }}</label>
                                    <select name="governorate_id" id="edit_governorate"
                                        class="form-input @error('governorate_id') is-invalid @enderror">
                                        <option value="">{{ __('Select governorate') }}</option>
                                        @foreach($governorates as $g)
                                        <option value="{{ $g->id }}" {{ old('governorate_id', $address->governorate_id) == $g->id ? 'selected':'' }}>
                                            {{ $g->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('governorate_id')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="field-label">{{ __('City') }}</label>
                                    <select name="city_id" id="edit_city"
                                        class="form-input @error('city_id') is-invalid @enderror">
                                        <option value="">{{ __('Select city') }}</option>
                                        @foreach($cities as $c)
                                        <option value="{{ $c->id }}" {{ old('city_id', $address->city_id) == $c->id ? 'selected':'' }}>
                                            {{ $c->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('city_id')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label class="field-label">{{ __('Postal Code') }}</label>
                                    <input type="text" name="postal_code" value="{{ old('postal_code', $address->postal_code) }}"
                                        class="form-input @error('postal_code') is-invalid @enderror">
                                    @error('postal_code')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="field-label">{{ __('Address Line 1') }}</label>
                                    <input type="text" name="line1" value="{{ old('line1', $address->line1) }}"
                                        class="form-input @error('line1') is-invalid @enderror">
                                    @error('line1')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label class="field-label">{{ __('Address Line 2') }}</label>
                                    <input type="text" name="line2" value="{{ old('line2', $address->line2) }}"
                                        class="form-input @error('line2') is-invalid @enderror">
                                    @error('line2')<div class="field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="is_default" value="1" {{ old('is_default', $address->is_default) ? 'checked':'' }}>
                                        {{ __('Set as default') }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-row">
                                <button class="btn-action-modern btn-primary" type="submit">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('Save address') }}
                                </button>
                                <a href="{{ route('user.addresses') }}" class="btn-action-modern btn-secondary">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</section>
@endsection