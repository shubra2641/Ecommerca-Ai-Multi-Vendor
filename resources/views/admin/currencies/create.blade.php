@extends('layouts.admin')

@section('title', __('Add Currency'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.currencies.index') }}">{{ __('Currencies') }}</a></li>
<li class="breadcrumb-item active">{{ __('Add Currency') }}</li>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('Add Currency') }}</h1>
        <p class="page-description">{{ __('Create a new currency with exchange rate') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.currencies.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            {{ __('Back to Currencies') }}
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
    <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-coins text-primary"></i>
                    {{ __('Currency Information') }}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.currencies.store') }}" method="POST" class="currency-form">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="name" class="form-label fw-semibold">{{ __('Currency Name') }} <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-coins"></i></span>
                                    <input type="text" id="name" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" placeholder="{{ __('e.g., US Dollar') }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="code" class="form-label fw-semibold">{{ __('Currency Code') }} <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-code"></i></span>
                                    <input type="text" id="code" name="code"
                                        class="form-control @error('code') is-invalid @enderror text-uppercase"
                                        value="{{ old('code') }}" placeholder="{{ __('e.g., USD') }}" maxlength="3"
                                        required>
                                    @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">{{ __('3-letter ISO currency code') }}</small>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="symbol" class="form-label fw-semibold">{{ __('Currency Symbol') }} <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    <input type="text" id="symbol" name="symbol"
                                        class="form-control @error('symbol') is-invalid @enderror"
                                        value="{{ old('symbol') }}" placeholder="{{ __('e.g., $') }}" maxlength="5"
                                        required>
                                    @error('symbol')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="exchange_rate" class="form-label fw-semibold">{{ __('Exchange Rate') }}
                                    <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-exchange-alt"></i></span>
                                    <input type="number" id="exchange_rate" name="exchange_rate"
                                        class="form-control @error('exchange_rate') is-invalid @enderror"
                                        value="{{ old('exchange_rate', '1.00') }}" step="0.0001" min="0"
                                        placeholder="{{ __('e.g., 1.0000') }}" required>
                                    @error('exchange_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">{{ __('Exchange rate relative to') }}
                                    {{ $defaultCurrency->name ?? 'USD' }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="form-options p-3 bg-light rounded">
                                <h6 class="fw-semibold mb-3">{{ __('Currency Settings') }}</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input type="checkbox" id="is_active" name="is_active"
                                                class="form-check-input" value="1"
                                                {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label for="is_active" class="form-check-label fw-semibold">
                                                <i class="fas fa-toggle-on text-success me-1"></i>
                                                {{ __('Active') }}
                                            </label>
                                            <div><small
                                                    class="text-muted">{{ __('Active currencies can be used for transactions') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input type="checkbox" id="is_default" name="is_default"
                                                class="form-check-input" value="1"
                                                {{ old('is_default') ? 'checked' : '' }}>
                                            <label for="is_default" class="form-check-label fw-semibold">
                                                <i class="fas fa-star text-warning me-1"></i>
                                                {{ __('Set as Default Currency') }}
                                            </label>
                                            <div><small
                                                    class="text-muted">{{ __('Default currency is used as base for exchange rates') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions d-flex gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> {{ __('Create Currency') }}
                        </button>
                        <a href="{{ route('admin.currencies.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-times me-1"></i> {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-lightbulb text-primary"></i>
                    {{ __('Currency Guidelines') }}
                </h3>
            </div>
            <div class="card-body">
                    <div class="stats-list">
                    <div class="card modern-card stats-card d-flex align-items-start py-3 border-bottom">
                        <div class="stats-card-body d-flex align-items-start gap-3 p-0">
                            <div class="stats-icon me-3">
                                <i class="fas fa-info-circle text-info"></i>
                            </div>
                            <div class="stats-card-content">
                                <h6 class="fw-semibold mb-1">{{ __('Currency Code') }}</h6>
                                <p class="text-muted mb-0">
                                    {{ __('Use standard 3-letter ISO currency codes (e.g., USD, EUR, GBP)') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card modern-card stats-card d-flex align-items-start py-3 border-bottom">
                        <div class="stats-card-body d-flex align-items-start gap-3 p-0">
                            <div class="stats-icon me-3">
                                <i class="fas fa-exchange-alt text-warning"></i>
                            </div>
                            <div class="stats-card-content">
                                <h6 class="fw-semibold mb-1">{{ __('Exchange Rate') }}</h6>
                                <p class="text-muted mb-0">
                                    {{ __('Set the exchange rate relative to your default currency') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card modern-card stats-card d-flex align-items-start py-3">
                        <div class="stats-card-body d-flex align-items-start gap-3 p-0">
                            <div class="stats-icon me-3">
                                <i class="fas fa-star text-success"></i>
                            </div>
                            <div class="stats-card-content">
                                <h6 class="fw-semibold mb-1">{{ __('Default Currency') }}</h6>
                                <p class="text-muted mb-0">{{ __('Only one currency can be set as default at a time') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt text-primary"></i>
                    {{ __('Quick Actions') }}
                </h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.currencies.index') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-list me-1"></i>
                        {{ __('View All Currencies') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection