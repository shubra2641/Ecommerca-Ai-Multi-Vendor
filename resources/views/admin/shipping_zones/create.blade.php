@extends('layouts.admin')

@section('title', __('Create Shipping Zone'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Create Shipping Zone') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Add a new shipping zone with rules') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.shipping-zones.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.shipping-zones.store') }}" class="admin-form" aria-label="create-shipping-zone">
            @csrf

            <!-- Zone Details -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="admin-card-title">{{ __('Zone Details') }}</h3>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-grid">
                        <div class="admin-form-group admin-form-group-wide">
                            <label class="admin-form-label">{{ __('Name') }}</label>
                            <input name="name" class="admin-form-input" required placeholder="{{ __('EU Zone') }}">
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Code (optional)') }}</label>
                            <input name="code" class="admin-form-input" placeholder="{{ __('EU') }}">
                        </div>
                        <div class="admin-form-group admin-flex-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="active" value="1" id="zone-active" checked>
                                <label class="form-check-label" for="zone-active">{{ __('Active') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Rule -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <h3 class="admin-card-title">{{ __('Add Rule') }}</h3>
                    <span class="admin-badge admin-badge-warning">{{ __('Required') }}</span>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Country') }}</label>
                            <select name="rules[0][country_id]" class="admin-form-select" required>
                                <option value="">{{ __('Select Country') }}</option>
                                @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Governorate') }}</label>
                            <select name="rules[0][governorate_id]" class="admin-form-select">
                                <option value="">{{ __('Select Governorate') }}</option>
                                @foreach($countries as $country)
                                @foreach($country->governorates as $governorate)
                                <option value="{{ $governorate->id }}" data-country="{{ $country->id }}">
                                    {{ $governorate->name }}
                                </option>
                                @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('City') }}</label>
                            <select name="rules[0][city_id]" class="admin-form-select">
                                <option value="">{{ __('Select City') }}</option>
                                @foreach($countries as $country)
                                @foreach($country->governorates as $governorate)
                                @foreach($governorate->cities as $city)
                                <option value="{{ $city->id }}" data-governorate="{{ $governorate->id }}" data-country="{{ $country->id }}">
                                    {{ $city->name }}
                                </option>
                                @endforeach
                                @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Price') }}</label>
                            <input type="number" name="rules[0][price]" class="admin-form-input" step="0.01" min="0" required>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Estimated Days') }}</label>
                            <input type="number" name="rules[0][estimated_days]" class="admin-form-input" min="1" required>
                        </div>
                        <div class="admin-form-group admin-flex-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="rules[0][active]" value="1" id="rule-active-0" checked>
                                <label class="form-check-label" for="rule-active-0">{{ __('Active') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="admin-modern-card">
                <div class="admin-card-body">
                    <div class="admin-form-actions">
                        <a href="{{ route('admin.shipping-zones.index') }}" class="admin-btn admin-btn-secondary">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="admin-btn admin-btn-success admin-btn-large">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Create Zone') }}
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </div>
</section>
@endsection