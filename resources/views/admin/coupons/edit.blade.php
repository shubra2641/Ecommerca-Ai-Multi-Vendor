@extends('layouts.admin')

@section('title', __('Edit Coupon'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <i class="fas fa-ticket-alt"></i>
                    {{ __('Edit Coupon') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Update coupon information') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.coupons.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">{{ __('Coupon Information') }}</h2>
            </div>

            <div class="admin-card-body">
                <form method="post" action="{{ route('admin.coupons.update', $coupon) }}" class="admin-form">
                    @csrf
                    @method('put')

                    <div class="admin-form-grid">
                        <div class="admin-form-group admin-form-group-wide">
                            <label class="admin-form-label">{{ __('Code') }}</label>
                            <input type="text" name="code" class="admin-form-input @error('code') is-invalid @enderror" value="{{ old('code', $coupon->code) }}" required />
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Type') }}</label>
                            <select name="type" class="admin-form-select @error('type') is-invalid @enderror" required>
                                <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>{{ __('Fixed') }}</option>
                                <option value="percent" {{ old('type', $coupon->type) == 'percent' ? 'selected' : '' }}>{{ __('Percent') }}</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Value') }}</label>
                            <input type="number" name="value" step="0.01" class="admin-form-input @error('value') is-invalid @enderror" value="{{ old('value', $coupon->value) }}" required />
                            @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Uses Total') }}</label>
                            <input type="number" name="uses_total" class="admin-form-input @error('uses_total') is-invalid @enderror" value="{{ old('uses_total', $coupon->uses_total) }}" placeholder="{{ __('Leave empty for unlimited') }}" />
                            @error('uses_total')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Min Order Total') }}</label>
                            <input type="number" step="0.01" name="min_total" class="admin-form-input @error('min_total') is-invalid @enderror" value="{{ old('min_total', $coupon->min_total) }}" placeholder="{{ __('Optional') }}" />
                            @error('min_total')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Starts At') }}</label>
                            <input type="datetime-local" name="starts_at" class="admin-form-input @error('starts_at') is-invalid @enderror" value="{{ old('starts_at', optional($coupon->starts_at)->format('Y-m-d\TH:i')) }}" />
                            @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Ends At') }}</label>
                            <input type="datetime-local" name="ends_at" class="admin-form-input @error('ends_at') is-invalid @enderror" value="{{ old('ends_at', optional($coupon->ends_at)->format('Y-m-d\TH:i')) }}" />
                            @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="admin-form-group admin-form-group-wide">
                            <label class="admin-form-label">{{ __('Status') }}</label>
                            <div class="form-check">
                                <input type="checkbox" name="active" id="active" class="form-check-input" value="1" {{ old('active', $coupon->active) ? 'checked' : '' }} />
                                <label class="form-check-label" for="active">{{ __('Active') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="admin-card-footer admin-flex-end">
                        <a href="{{ route('admin.coupons.index') }}" class="admin-btn admin-btn-secondary">{{ __('Cancel') }}</a>
                        <button class="admin-btn admin-btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>
@endsection