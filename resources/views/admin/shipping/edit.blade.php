@extends('layouts.admin')
@section('title','Edit Shipping Group')
@section('content')
@include('admin.partials.page-header', ['title'=>__('Edit Shipping Group')])
@if(session('warning'))
 <div class="alert alert-warning">{{ session('warning') }}</div>
@endif
<div class="card modern-card">
    <div class="card-header d-flex align-items-center gap-2">
        <h3 class="card-title mb-0">{{ __('Edit Shipping Group') }}</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.shipping.update',$shipping) }}" class="admin-form" aria-label="edit-shipping-group">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">{{ __('Name') }}</label>
                <input name="name" class="form-control" value="{{ old('name',$shipping->name) }}" required placeholder="{{ __('e.g. Express') }}">
            </div>
            <div class="row g-2">
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Default Price') }}</label>
                    <input name="default_price" class="form-control" value="{{ old('default_price',$shipping->default_price) }}" placeholder="{{ __('0.00') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Estimated Days') }}</label>
                    <input name="estimated_days" class="form-control" value="{{ old('estimated_days',$shipping->estimated_days) }}" placeholder="{{ __('e.g. 3-5') }}">
                </div>
            </div>
            <h5 class="mt-3">{{ __('Locations') }}</h5>
                <div id="locations-list" class="mb-2"></div>
                <button type="button" id="add-location" class="btn btn-sm btn-outline-secondary">{{ __('Add Location') }}</button>
                <p class="text-muted small mt-2">{{ __('اترك المحافظة و المدينة فارغين لو السعر لكل الدولة. املأ المحافظة فقط ليطبق على كل مدنها. ضع المدينة ليكون سعر خاص بها.') }}</p>
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
@section('scripts')
<script id="shipping-group-config" type="application/json">{!! json_encode([
    'countries'=>$countries->map(fn($c)=>['id'=>$c->id,'name'=>$c->name])->values(),
    'existing'=>$locations->map(fn($l)=>['country_id'=>$l->country_id,'governorate_id'=>$l->governorate_id,'city_id'=>$l->city_id,'price'=>$l->price,'estimated_days'=>$l->estimated_days])->values(),
    'routes'=>[
        'governorates'=>route('admin.locations.governorates'),
        'cities'=>route('admin.locations.cities')
    ]
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)</script>
<script src="{{ asset('admin/js/shipping-group.js') }}" defer></script>
@endsection
