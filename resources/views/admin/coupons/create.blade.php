@extends('layouts.admin')
@section('content')
@include('admin.partials.page-header', ['title'=>__('Create Coupon')])
<div class="card modern-card">
    <form method="post" action="{{ route('admin.coupons.store') }}" class="admin-form">@csrf
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">{{ __('Code') }}</label>
                <input type="text" name="code" class="form-control" />
            </div>
    <div class="mb-3">
        <label class="form-label">{{ __('Type') }}</label>
        <select name="type" class="form-select">
            <option value="fixed">{{ __('Fixed') }}</option>
            <option value="percent">{{ __('Percent') }}</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('Value') }}</label>
        <input type="number" step="0.01" name="value" class="form-control" />
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('Uses Total (leave empty for unlimited)') }}</label>
        <input type="number" name="uses_total" class="form-control" />
    </div>
    <div class="mb-3">
        <label class="form-label">{{ __('Min Order Total (optional)') }}</label>
        <input type="number" step="0.01" name="min_total" class="form-control" />
    </div>
    <div class="row g-2">
        <div class="col">
            <label class="form-label">{{ __('Starts At') }}</label>
            <input type="datetime-local" name="starts_at" class="form-control" />
        </div>
        <div class="col">
            <label class="form-label">{{ __('Ends At') }}</label>
            <input type="datetime-local" name="ends_at" class="form-control" />
        </div>
    </div>
    <div class="form-check my-3">
        <input type="checkbox" name="active" id="active" class="form-check-input" value="1" />
        <label class="form-check-label" for="active">{{ __('Active') }}</label>
    </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary me-2">{{ __('Cancel') }}</a>
            <button class="btn btn-primary">{{ __('Create') }}</button>
        </div>
    </form>
</div>
@endsection