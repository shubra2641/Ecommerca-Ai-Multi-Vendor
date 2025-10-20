@extends('layouts.admin')
@section('title', __('Edit Currency'))
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.currencies.index') }}">{{ __('Currencies') }}</a></li>
<li class="breadcrumb-item active">{{ __('Edit') }}</li>
@endsection
@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Edit Currency') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Update currency information and exchange rate') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.currencies.show', $currency) }}" class="admin-btn admin-btn-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    {{ __('View Details') }}
                </a>
                <a href="{{ route('admin.currencies.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back to Currencies') }}
                </a>
            </div>
        </div>

        <div class="admin-order-grid-modern">
            <!-- Edit Currency Form -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ __('Currency Information') }}
                    </h2>
                </div>
                <div class="admin-card-body">
                    <form action="{{ route('admin.currencies.update', $currency) }}" method="POST" class="admin-form">
                        @csrf
                        @method('PUT')
                        <div class="admin-form-grid">
                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Currency Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="admin-form-input @error('name') is-invalid @enderror" value="{{ old('name', $currency->name) }}" placeholder="{{ __('e.g., US Dollar') }}" required>
                                @error('name')
                                    <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Currency Code') }} <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="admin-form-input @error('code') is-invalid @enderror text-uppercase" value="{{ old('code', $currency->code) }}" placeholder="{{ __('e.g., USD') }}" maxlength="3" required>
                                <div class="admin-text-muted small">{{ __('3-letter ISO currency code') }}</div>
                                @error('code')
                                    <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Currency Symbol') }} <span class="text-danger">*</span></label>
                                <input type="text" name="symbol" class="admin-form-input @error('symbol') is-invalid @enderror" value="{{ old('symbol', $currency->symbol) }}" placeholder="{{ __('e.g., $') }}" maxlength="5" required>
                                @error('symbol')
                                    <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Exchange Rate') }} <span class="text-danger">*</span></label>
                                <input type="number" name="exchange_rate" class="admin-form-input @error('exchange_rate') is-invalid @enderror" value="{{ old('exchange_rate', $currency->exchange_rate) }}" step="0.0001" min="0" placeholder="{{ __('e.g., 1.0000') }}" required>
                                <div class="admin-text-muted small">{{ __('Exchange rate against default currency') }}</div>
                                @error('exchange_rate')
                                    <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="admin-form-group">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $currency->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-2">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('Active') }}
                                    </label>
                                </div>
                                <div class="admin-text-muted small">{{ __('Whether this currency is available for use') }}</div>
                            </div>
                            <div class="admin-form-group">
                                <div class="form-check">
                                    <input type="checkbox" name="is_default" id="is_default" class="form-check-input" value="1" {{ old('is_default', $currency->is_default) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-2">
                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        {{ __('Set as Default Currency') }}
                                    </label>
                                </div>
                                <div class="admin-text-muted small">{{ __('This will replace the current default currency') }}</div>
                            </div>
                        </div>

                        @if($currency->is_default)
                        <div class="alert alert-info">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-2">
                                <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('This is the default currency. Exchange rates for other currencies are calculated relative to this currency.') }}
                        </div>
                        @endif

                        <div class="admin-flex-end">
                            <button type="submit" class="admin-btn admin-btn-primary">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Update Currency') }}
                            </button>
                            <a href="{{ route('admin.currencies.index') }}" class="admin-btn admin-btn-secondary">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Currency Statistics & Actions -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        {{ __('Currency Statistics') }}
                    </h2>
                </div>
                <div class="admin-card-body">
                    <div class="admin-info-grid">
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ __('Created') }}
                            </div>
                            <div class="admin-info-value">{{ $currency->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Last Updated') }}
                            </div>
                            <div class="admin-info-value">{{ $currency->updated_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Status') }}
                            </div>
                            <div class="admin-info-value">
                                @if($currency->is_active)
                                <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                @endif
                                @if($currency->is_default)
                                <span class="badge bg-warning ms-2">{{ __('Default') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection