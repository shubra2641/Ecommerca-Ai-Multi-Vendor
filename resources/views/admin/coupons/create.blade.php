@extends('layouts.admin')

@section('title', __('Create Coupon'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    {{ __('Create Coupon') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Add a new discount coupon') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.coupons.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
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
                <form method="post" action="{{ route('admin.coupons.store') }}" class="admin-form">
                    @csrf

                    <div class="admin-form-grid">
                        <div class="admin-form-group admin-form-group-wide">
                            <label class="admin-form-label">{{ __('Code') }}</label>
                            <input type="text" name="code" class="admin-form-input @error('code') is-invalid @enderror" value="{{ old('code') }}" required />
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Type') }}</label>
                            <select name="type" class="admin-form-select @error('type') is-invalid @enderror" required>
                                <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>{{ __('Fixed') }}</option>
                                <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>{{ __('Percent') }}</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Value') }}</label>
                            <input type="number" step="0.01" name="value" class="admin-form-input @error('value') is-invalid @enderror" value="{{ old('value') }}" required />
                            @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Uses Total') }}</label>
                            <input type="number" name="uses_total" class="admin-form-input @error('uses_total') is-invalid @enderror" value="{{ old('uses_total') }}" placeholder="{{ __('Leave empty for unlimited') }}" />
                            @error('uses_total')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Min Order Total') }}</label>
                            <input type="number" step="0.01" name="min_total" class="admin-form-input @error('min_total') is-invalid @enderror" value="{{ old('min_total') }}" placeholder="{{ __('Optional') }}" />
                            @error('min_total')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Starts At') }}</label>
                            <input type="datetime-local" name="starts_at" class="admin-form-input @error('starts_at') is-invalid @enderror" value="{{ old('starts_at') }}" />
                            @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Ends At') }}</label>
                            <input type="datetime-local" name="ends_at" class="admin-form-input @error('ends_at') is-invalid @enderror" value="{{ old('ends_at') }}" />
                            @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="admin-form-group admin-form-group-wide">
                            <label class="admin-form-label">{{ __('Status') }}</label>
                            <div class="form-check">
                                <input type="checkbox" name="active" id="active" class="form-check-input" value="1" {{ old('active') ? 'checked' : '' }} />
                                <label class="form-check-label" for="active">{{ __('Active') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="admin-card-footer admin-flex-end">
                        <a href="{{ route('admin.coupons.index') }}" class="admin-btn admin-btn-secondary">{{ __('Cancel') }}</a>
                        <button class="admin-btn admin-btn-primary">{{ __('Create') }}</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>
@endsection