@extends('layouts.admin')
@section('title', __('Edit Shipping Zone'))
@section('content')
@include('admin.partials.page-header', ['title'=>__('Edit Shipping Zone')])
<div class="card modern-card">
    <div class="card-header d-flex align-items-center gap-2">
        <h3 class="card-title mb-0">{{ __('Edit Shipping Zone') }}</h3>
    </div>
    <div class="card-body">
            <form method="POST" action="{{ route('admin.shipping-zones.update',$zone) }}" class="admin-form" aria-label="edit-shipping-zone">
                @csrf
                @method('PUT')
                <div class="mb-3"><label class="form-label fw-semibold">{{ __('Name') }}</label><input name="name"
                        class="form-control" value="{{ $zone->name }}" required></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-semibold">{{ __('Code (optional)') }}</label><input
                            name="code" class="form-control" value="{{ $zone->code }}"></div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="active" value="1" id="zone-active"
                                {{ $zone->active ? 'checked' : '' }}>
                            <label class="form-check-label" for="zone-active">{{ __('Active') }}</label>
                        </div>
                    </div>
                </div>

                @if($rules->count() > 0)
                    <div class="mb-3">
                        <h6>{{ __('Existing Rules') }}</h6>
                        @foreach($rules as $index => $rule)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">{{ __('Country') }}</label>
                                            <select name="rules[{{ $index }}][country_id]" class="form-select">
                                                <option value="">{{ __('Select Country') }}</option>
                                                @foreach($countries as $country)
                                                    <option value="{{ $country->id }}" {{ $rule->country_id == $country->id ? 'selected' : '' }}>
                                                        {{ $country->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">{{ __('Governorate') }}</label>
                                            <select name="rules[{{ $index }}][governorate_id]" class="form-select">
                                                <option value="">{{ __('Select Governorate') }}</option>
                                                @foreach($countries as $country)
                                                    @foreach($country->governorates as $governorate)
                                                        <option value="{{ $governorate->id }}" 
                                                                {{ $rule->governorate_id == $governorate->id ? 'selected' : '' }}
                                                                data-country="{{ $country->id }}">
                                                            {{ $governorate->name }}
                                                        </option>
                                                    @endforeach
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">{{ __('City') }}</label>
                                            <select name="rules[{{ $index }}][city_id]" class="form-select">
                                                <option value="">{{ __('Select City') }}</option>
                                                @foreach($countries as $country)
                                                    @foreach($country->governorates as $governorate)
                                                        @foreach($governorate->cities as $city)
                                                            <option value="{{ $city->id }}" 
                                                                    {{ $rule->city_id == $city->id ? 'selected' : '' }}
                                                                    data-governorate="{{ $governorate->id }}" 
                                                                    data-country="{{ $country->id }}">
                                                                {{ $city->name }}
                                                            </option>
                                                        @endforeach
                                                    @endforeach
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">{{ __('Price') }}</label>
                                            <input type="number" name="rules[{{ $index }}][price]" class="form-control" 
                                                   value="{{ $rule->price }}" step="0.01" min="0" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">{{ __('Estimated Days') }}</label>
                                            <input type="number" name="rules[{{ $index }}][estimated_days]" class="form-control" 
                                                   value="{{ $rule->estimated_days }}" min="1" required>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="rules[{{ $index }}][active]" value="1" 
                                                       id="rule-active-{{ $index }}" {{ $rule->active ? 'checked' : '' }}>
                                                <label class="form-check-label" for="rule-active-{{ $index }}">{{ __('Active') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <a href="{{ route('admin.shipping-zones.edit', ['shipping_zone' => $zone, 'remove_rule' => $index]) }}" 
                                               class="btn btn-outline-danger btn-sm" 
                                               onclick="return confirm('{{ __('Are you sure you want to remove this rule?') }}')">
                                                {{ __('Remove') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ __('No rules found. Add a rule below to get started.') }}
                    </div>
                @endif
                
                <div class="mb-3">
                    <h6>{{ __('Add New Rule') }}</h6>
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">{{ __('Country') }}</label>
                                    <select name="rules[new][country_id]" class="form-select" required>
                                        <option value="">{{ __('Select Country') }}</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">{{ __('Governorate') }}</label>
                                    <select name="rules[new][governorate_id]" class="form-select">
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
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">{{ __('City') }}</label>
                                    <select name="rules[new][city_id]" class="form-select">
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
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">{{ __('Price') }}</label>
                                    <input type="number" name="rules[new][price]" class="form-control" 
                                           step="0.01" min="0" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">{{ __('Estimated Days') }}</label>
                                    <input type="number" name="rules[new][estimated_days]" class="form-control" 
                                           min="1" required>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="rules[new][active]" value="1" 
                                               id="new-rule-active" checked>
                                        <label class="form-check-label" for="new-rule-active">{{ __('Active') }}</label>
                                    </div>
                                </div>
        </div>
            </div>
            </div>
        </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.shipping-zones.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
    </div>
    </div>
@endsection