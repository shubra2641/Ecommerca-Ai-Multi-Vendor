@extends('layouts.admin')

@section('title', __('Edit Shipping Zone'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Edit Shipping Zone') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Update shipping zone and rules') }}</p>
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

        <form method="POST" action="{{ route('admin.shipping-zones.update',$zone) }}" class="admin-form" aria-label="edit-shipping-zone">
            @csrf
            @method('PUT')

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
                            <input name="name" class="admin-form-input" value="{{ $zone->name }}" required>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Code (optional)') }}</label>
                            <input name="code" class="admin-form-input" value="{{ $zone->code }}">
                        </div>
                        <div class="admin-form-group admin-flex-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="active" value="1" id="zone-active" {{ $zone->active ? 'checked' : '' }}>
                                <label class="form-check-label" for="zone-active">{{ __('Active') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Rules -->
            @if($rules->count() > 0)
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <h3 class="admin-card-title">{{ __('Existing Rules') }}</h3>
                    <span class="admin-badge-count">{{ $rules->count() }}</span>
                </div>
                <div class="admin-card-body">
                    @foreach($rules as $index => $rule)
                    <div class="admin-modern-card admin-mb-1-5">
                        <div class="admin-card-body">
                            <div class="admin-form-grid">
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Country') }}</label>
                                    <select name="rules[{{ $index }}][country_id]" class="admin-form-select">
                                        <option value="">{{ __('Select Country') }}</option>
                                        @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ $rule->country_id == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Governorate') }}</label>
                                    <select name="rules[{{ $index }}][governorate_id]" class="admin-form-select">
                                        <option value="">{{ __('Select Governorate') }}</option>
                                        @foreach($countries as $country)
                                        @foreach($country->governorates as $governorate)
                                        <option value="{{ $governorate->id }}" {{ $rule->governorate_id == $governorate->id ? 'selected' : '' }} data-country="{{ $country->id }}">
                                            {{ $governorate->name }}
                                        </option>
                                        @endforeach
                                        @endforeach
                                    </select>
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('City') }}</label>
                                    <select name="rules[{{ $index }}][city_id]" class="admin-form-select">
                                        <option value="">{{ __('Select City') }}</option>
                                        @foreach($countries as $country)
                                        @foreach($country->governorates as $governorate)
                                        @foreach($governorate->cities as $city)
                                        <option value="{{ $city->id }}" {{ $rule->city_id == $city->id ? 'selected' : '' }} data-governorate="{{ $governorate->id }}" data-country="{{ $country->id }}">
                                            {{ $city->name }}
                                        </option>
                                        @endforeach
                                        @endforeach
                                        @endforeach
                                    </select>
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Price') }}</label>
                                    <input type="number" name="rules[{{ $index }}][price]" class="admin-form-input" value="{{ $rule->price }}" step="0.01" min="0" required>
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Estimated Days') }}</label>
                                    <input type="number" name="rules[{{ $index }}][estimated_days]" class="admin-form-input" value="{{ $rule->estimated_days }}" min="1" required>
                                </div>
                                <div class="admin-form-group admin-flex-end">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="rules[{{ $index }}][active]" value="1" id="rule-active-{{ $index }}" {{ $rule->active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="rule-active-{{ $index }}">{{ __('Active') }}</label>
                                    </div>
                                </div>
                                <div class="admin-form-group admin-flex-end">
                                    <a href="{{ route('admin.shipping-zones.edit', ['shipping_zone' => $zone, 'remove_rule' => $index]) }}" class="admin-btn-small admin-btn-danger admin-btn-block js-confirm" data-confirm="{{ __('Are you sure you want to remove this rule?') }}">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        {{ __('Remove') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="admin-modern-card">
                <div class="admin-card-body">
                    <div class="admin-empty-state">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="admin-notification-icon">
                            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>{{ __('No rules found. Add a rule below to get started.') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Add New Rule -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    <h3 class="admin-card-title">{{ __('Add New Rule') }}</h3>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Country') }}</label>
                            <select name="rules[new][country_id]" class="admin-form-select" required>
                                <option value="">{{ __('Select Country') }}</option>
                                @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Governorate') }}</label>
                            <select name="rules[new][governorate_id]" class="admin-form-select">
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
                            <select name="rules[new][city_id]" class="admin-form-select">
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
                            <input type="number" name="rules[new][price]" class="admin-form-input" step="0.01" min="0" required>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Estimated Days') }}</label>
                            <input type="number" name="rules[new][estimated_days]" class="admin-form-input" min="1" required>
                        </div>
                        <div class="admin-form-group admin-flex-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="rules[new][active]" value="1" id="new-rule-active" checked>
                                <label class="form-check-label" for="new-rule-active">{{ __('Active') }}</label>
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
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </div>
</section>
@endsection