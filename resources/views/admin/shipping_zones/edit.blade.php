@extends('layouts.admin')

@section('title', __('Edit Shipping Zone'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <i class="fas fa-edit"></i>
                    {{ __('Edit Shipping Zone') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Update shipping zone and rules') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.shipping-zones.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
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
                    <i class="fas fa-clipboard"></i>
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
                    <i class="fas fa-list"></i>
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
                                        <i class="fas fa-trash"></i>
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
                        <i class="fas fa-info-circle admin-notification-icon"></i>
                        <p>{{ __('No rules found. Add a rule below to get started.') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Add New Rule -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <i class="fas fa-plus"></i>
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
                            <i class="fas fa-check"></i>
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </div>
</section>
@endsection