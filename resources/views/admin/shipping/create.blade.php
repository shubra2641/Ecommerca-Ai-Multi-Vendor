@extends('layouts.admin')
@section('title','Create Shipping Group')
@section('content')
@include('admin.partials.page-header', ['title'=>__('Create Shipping Group')])
<div class="card modern-card">
    <div class="card-header d-flex align-items-center gap-2">
        <h3 class="card-title mb-0">{{ __('Create Shipping Group') }}</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.shipping.store') }}" class="admin-form" aria-label="create-shipping-group">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ __('Name') }}</label>
                <input name="name" class="form-control" required placeholder="{{ __('e.g. Express / Standard') }}" aria-label="{{ __('Name') }}">
            </div>
            <div class="row g-2">
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Default Price') }}</label>
                    <input name="default_price" class="form-control" placeholder="{{ __('0.00') }}" aria-label="{{ __('Default Price') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Estimated Days') }}</label>
                    <input name="estimated_days" class="form-control" placeholder="{{ __('e.g. 3-5') }}" aria-label="{{ __('Estimated Days') }}">
                </div>
            </div>

            <div id="locations-wrapper" class="mt-3">
                <h5 class="mb-2">{{ __('Locations') }}</h5>
                <p class="text-muted">{{ __('You can add multiple locations with country/governorate/city and override price/days.') }}</p>
                <div id="locations-list" class="mb-2"></div>
                <button type="button" id="add-location" class="btn btn-sm btn-outline-secondary">{{ __('Add Location') }}</button>
            </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <div>
            <a href="{{ route('admin.shipping.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
        </div>
        <div>
            <button class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </div>
        </form>
</div>
@endsection