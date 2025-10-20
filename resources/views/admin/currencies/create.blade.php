@extends('layouts.admin')
@section('title', __('Add Currency'))
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.currencies.index') }}">{{ __('Currencies') }}</a></li>
<li class="breadcrumb-item active">{{ __('Create') }}</li>
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
                        <h1 class="admin-order-title">{{ __('Add Currency') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Create a new currency with exchange rate') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.currencies.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back to Currencies') }}
                </a>
            </div>
        </div>

        <div class="admin-order-grid-modern">
            <!-- Create Currency Form -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('Currency Information') }}
                    </h2>
                </div>
                <div class="admin-card-body">
                    <form action="{{ route('admin.currencies.store') }}" method="POST" class="admin-form">
                        @csrf
                        <div class="admin-form-grid">
                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Currency Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="admin-form-input @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="{{ __('e.g., US Dollar') }}" required>
                                @error('name')
                                    <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Currency Code') }} <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="admin-form-input @error('code') is-invalid @enderror text-uppercase" value="{{ old('code') }}" placeholder="{{ __('e.g., USD') }}" maxlength="3" required>
                                <div class="admin-text-muted small">{{ __('3-letter ISO currency code') }}</div>
                                @error('code')
                                    <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Currency Symbol') }} <span class="text-danger">*</span></label>
                                <input type="text" name="symbol" class="admin-form-input @error('symbol') is-invalid @enderror" value="{{ old('symbol') }}" placeholder="{{ __('e.g., $') }}" maxlength="5" required>
                                @error('symbol')
                                    <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Exchange Rate') }} <span class="text-danger">*</span></label>
                                <input type="number" name="exchange_rate" class="admin-form-input @error('exchange_rate') is-invalid @enderror" value="{{ old('exchange_rate', '1.00') }}" step="0.0001" min="0" placeholder="{{ __('e.g., 1.0000') }}" required>
                                <div class="admin-text-muted small">{{ __('Exchange rate relative to') }} {{ $defaultCurrency->name ?? 'USD' }}</div>
                                @error('exchange_rate')
                                    <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="admin-form-group">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-2">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('Active') }}
                                    </label>
                                </div>
                                <div class="admin-text-muted small">{{ __('Active currencies can be used for transactions') }}</div>
                            </div>
                            <div class="admin-form-group">
                                <div class="form-check">
                                    <input type="checkbox" name="is_default" id="is_default" class="form-check-input" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-2">
                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        {{ __('Set as Default Currency') }}
                                    </label>
                                </div>
                                <div class="admin-text-muted small">{{ __('Default currency is used as base for exchange rates') }}</div>
                            </div>
                        </div>

                        <div class="admin-flex-end">
                            <button type="submit" class="admin-btn admin-btn-primary">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Create Currency') }}
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

            <!-- Currency Guidelines -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        {{ __('Currency Guidelines') }}
                    </h2>
                </div>
                <div class="admin-card-body">
                    <div class="admin-info-grid">
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Currency Code') }}
                            </div>
                            <div class="admin-info-value">
                                <div class="admin-text-muted">{{ __('Use standard 3-letter ISO currency codes (e.g., USD, EUR, GBP)') }}</div>
                            </div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M7 14l3-3 3 3 4-4M8 21l4-4 4 4M3 4l6 6 6-6" />
                                </svg>
                                {{ __('Exchange Rate') }}
                            </div>
                            <div class="admin-info-value">
                                <div class="admin-text-muted">{{ __('Set the exchange rate relative to your default currency') }}</div>
                            </div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                                {{ __('Default Currency') }}
                            </div>
                            <div class="admin-info-value">
                                <div class="admin-text-muted">{{ __('Only one currency can be set as default at a time') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection