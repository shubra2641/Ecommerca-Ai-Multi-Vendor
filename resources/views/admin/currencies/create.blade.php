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
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Add Currency') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Create a new currency with exchange rate') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.currencies.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back to Currencies') }}
                </a>
            </div>
        </div>

        <div class="admin-order-grid-modern">
            <!-- Create Currency Form -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <i class="fas fa-dollar-sign"></i>
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
                                        <i class="fas fa-check-circle me-2"></i>
                                        {{ __('Active') }}
                                    </label>
                                </div>
                                <div class="admin-text-muted small">{{ __('Active currencies can be used for transactions') }}</div>
                            </div>
                            <div class="admin-form-group">
                                <div class="form-check">
                                    <input type="checkbox" name="is_default" id="is_default" class="form-check-input" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        <i class="fas fa-star me-2"></i>
                                        {{ __('Set as Default Currency') }}
                                    </label>
                                </div>
                                <div class="admin-text-muted small">{{ __('Default currency is used as base for exchange rates') }}</div>
                            </div>
                        </div>

                        <div class="admin-flex-end">
                            <button type="submit" class="admin-btn admin-btn-primary">
                                <i class="fas fa-check"></i>
                                {{ __('Create Currency') }}
                            </button>
                            <a href="{{ route('admin.currencies.index') }}" class="admin-btn admin-btn-secondary">
                                <i class="fas fa-times"></i>
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
                        <i class="fas fa-lightbulb"></i>
                        {{ __('Currency Guidelines') }}
                    </h2>
                </div>
                <div class="admin-card-body">
                    <div class="admin-info-grid">
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <i class="fas fa-info-circle"></i>
                                {{ __('Currency Code') }}
                            </div>
                            <div class="admin-info-value">
                                <div class="admin-text-muted">{{ __('Use standard 3-letter ISO currency codes (e.g., USD, EUR, GBP)') }}</div>
                            </div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <i class="fas fa-chart-line"></i>
                                {{ __('Exchange Rate') }}
                            </div>
                            <div class="admin-info-value">
                                <div class="admin-text-muted">{{ __('Set the exchange rate relative to your default currency') }}</div>
                            </div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <i class="fas fa-star"></i>
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