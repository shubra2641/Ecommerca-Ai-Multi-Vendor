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
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Edit Currency') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Update currency information and exchange rate') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.currencies.show', $currency) }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-eye"></i>
                    {{ __('View Details') }}
                </a>
                <a href="{{ route('admin.currencies.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back to Currencies') }}
                </a>
            </div>
        </div>

        <div class="admin-order-grid-modern">
            <!-- Edit Currency Form -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <i class="fas fa-edit"></i>
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
                                        <i class="fas fa-check-circle me-2"></i>
                                        {{ __('Active') }}
                                    </label>
                                </div>
                                <div class="admin-text-muted small">{{ __('Whether this currency is available for use') }}</div>
                            </div>
                            <div class="admin-form-group">
                                <div class="form-check">
                                    <input type="checkbox" name="is_default" id="is_default" class="form-check-input" value="1" {{ old('is_default', $currency->is_default) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        <i class="fas fa-star me-2"></i>
                                        {{ __('Set as Default Currency') }}
                                    </label>
                                </div>
                                <div class="admin-text-muted small">{{ __('This will replace the current default currency') }}</div>
                            </div>
                        </div>

                        @if($currency->is_default)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('This is the default currency. Exchange rates for other currencies are calculated relative to this currency.') }}
                        </div>
                        @endif

                        <div class="admin-flex-end">
                            <button type="submit" class="admin-btn admin-btn-primary">
                                <i class="fas fa-check"></i>
                                {{ __('Update Currency') }}
                            </button>
                            <a href="{{ route('admin.currencies.index') }}" class="admin-btn admin-btn-secondary">
                                <i class="fas fa-times"></i>
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
                        <i class="fas fa-chart-bar"></i>
                        {{ __('Currency Statistics') }}
                    </h2>
                </div>
                <div class="admin-card-body">
                    <div class="admin-info-grid">
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <i class="fas fa-calendar"></i>
                                {{ __('Created') }}
                            </div>
                            <div class="admin-info-value">{{ $currency->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <i class="fas fa-clock"></i>
                                {{ __('Last Updated') }}
                            </div>
                            <div class="admin-info-value">{{ $currency->updated_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <i class="fas fa-check-circle"></i>
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