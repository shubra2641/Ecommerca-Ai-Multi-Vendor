@extends('layouts.admin')
@section('title', __('Create Shipping Zone'))
@section('content')
@include('admin.partials.page-header', ['title'=>__('Create Shipping Zone')])
<div class="card modern-card">
    <div class="card-header d-flex align-items-center gap-2">
        <h3 class="card-title mb-0">{{ __('Create Shipping Zone') }}</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.shipping-zones.store') }}" class="admin-form" aria-label="create-shipping-zone">
            @csrf
            <div class="mb-3"><label class="form-label fw-semibold">{{ __('Name') }}</label><input name="name"
                    class="form-control" required placeholder="{{ __('EU Zone') }}"></div>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label fw-semibold">{{ __('Code (optional)') }}</label><input
                        name="code" class="form-control" placeholder="{{ __('EU') }}"></div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="active" value="1" id="zone-active" checked>
                        <label class="form-check-label" for="zone-active">{{ __('Active') }}</label>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <h6>{{ __('Add Rule') }} <span class="text-muted small">({{ __('Required') }})</span></h6>
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">{{ __('Country') }}</label>
                                <select name="rules[0][country_id]" class="form-select" required>
                                    <option value="">{{ __('Select Country') }}</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">{{ __('Governorate') }}</label>
                                <select name="rules[0][governorate_id]" class="form-select">
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
                                <select name="rules[0][city_id]" class="form-select">
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
                                <input type="number" name="rules[0][price]" class="form-control" 
                                       step="0.01" min="0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">{{ __('Estimated Days') }}</label>
                                <input type="number" name="rules[0][estimated_days]" class="form-control" 
                                       min="1" required>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="rules[0][active]" value="1" 
                                           id="rule-active-0" checked>
                                    <label class="form-check-label" for="rule-active-0">{{ __('Active') }}</label>
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