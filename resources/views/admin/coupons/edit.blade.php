@extends('layouts.admin')
@section('content')
@include('admin.partials.page-header', ['title'=>__('Edit Coupon')])
<div class="card modern-card">
    <form method="post" action="{{ route('admin.coupons.update', $coupon) }}" class="admin-form">@csrf @method('put')
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">{{ __('Code') }}</label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $coupon->code) }}" />
            </div>
    <div class="mb-3">
        <label class="form-label">{{ __('Type') }}</label>
        <select name="type" class="form-select">
            <option value="fixed" {{ $coupon->type=='fixed'?'selected':'' }}>{{ __('Fixed') }}</option>
            <option value="percent" {{ $coupon->type=='percent'?'selected':'' }}>{{ __('Percent') }}</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('Value') }}</label>
        <input type="number" name="value" step="0.01" class="form-control" value="{{ old('value', $coupon->value) }}" />
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('Uses Total (leave empty for unlimited)') }}</label>
        <input type="number" name="uses_total" class="form-control" value="{{ old('uses_total', $coupon->uses_total) }}" />
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('Min Order Total (optional)') }}</label>
        <input type="number" step="0.01" name="min_total" class="form-control" value="{{ old('min_total', $coupon->min_total) }}" />
    </div>
    <div class="row g-2">
        <div class="col">
            <label class="form-label">{{ __('Starts At') }}</label>
            <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at', optional($coupon->starts_at)->format('Y-m-d\TH:i')) }}" />
        </div>
        <div class="col">
            <label class="form-label">{{ __('Ends At') }}</label>
            <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at', optional($coupon->ends_at)->format('Y-m-d\TH:i')) }}" />
        </div>
    </div>
    <div class="form-check my-3">
        <input type="checkbox" name="active" id="active" class="form-check-input" value="1" {{ old('active', $coupon->active) ? 'checked' : '' }} />
        <label class="form-check-label" for="active">{{ __('Active') }}</label>
    </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary me-2">{{ __('Cancel') }}</a>
            <button class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
</div>
@endsection