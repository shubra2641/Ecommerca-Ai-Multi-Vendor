@extends('layouts.admin')

@section('title', __('Edit Currency'))

@section('content')
@include('admin.partials.page-header', ['title'=>__('Edit Currency').': '.$currency->name,'subtitle'=>__('Update currency information and exchange rate'),'actions'=>'<a href="'.route('admin.currencies.index').'" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> '.__('Back to Currencies').'</a> <a href="'.route('admin.currencies.show', $currency).'" class="btn btn-outline-primary"><i class="fas fa-eye"></i> '.__('View Details').'</a>'])

<div class="row">
    <div class="col-md-8">
    <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Currency Information') }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.currencies.update', $currency) }}" method="POST" class="currency-form">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">{{ __('Currency Name') }} <span
                                        class="required">*</span></label>
                                <input type="text" id="name" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $currency->name) }}" placeholder="{{ __('e.g., US Dollar') }}"
                                    required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code" class="form-label">{{ __('Currency Code') }} <span
                                        class="required">*</span></label>
                                <input type="text" id="code" name="code"
                                    class="form-control @error('code') is-invalid @enderror text-uppercase"
                                    value="{{ old('code', $currency->code) }}" placeholder="{{ __('e.g., USD') }}"
                                    maxlength="3" required>
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">{{ __('3-letter ISO currency code') }}</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="symbol" class="form-label">{{ __('Currency Symbol') }} <span
                                        class="required">*</span></label>
                                <input type="text" id="symbol" name="symbol"
                                    class="form-control @error('symbol') is-invalid @enderror"
                                    value="{{ old('symbol', $currency->symbol) }}" placeholder="{{ __('e.g., $') }}"
                                    maxlength="5" required>
                                @error('symbol')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exchange_rate" class="form-label">{{ __('Exchange Rate') }} <span
                                        class="required">*</span></label>
                                <input type="number" id="exchange_rate" name="exchange_rate"
                                    class="form-control @error('exchange_rate') is-invalid @enderror"
                                    value="{{ old('exchange_rate', $currency->exchange_rate) }}" step="0.0001" min="0"
                                    placeholder="{{ __('e.g., 1.0000') }}" required>
                                @error('exchange_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">{{ __('Exchange rate against default currency') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-options">
                                <div class="form-check">
                                    <input type="checkbox" id="is_active" name="is_active" class="form-check-input"
                                        value="1" {{ old('is_active', $currency->is_active) ? 'checked' : '' }}>
                                    <label for="is_active" class="form-check-label">
                                        {{ __('Active') }}
                                    </label>
                                    <small
                                        class="form-text">{{ __('Whether this currency is available for use') }}</small>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" id="is_default" name="is_default" class="form-check-input"
                                        value="1" {{ old('is_default', $currency->is_default) ? 'checked' : '' }}>
                                    <label for="is_default" class="form-check-label">
                                        {{ __('Set as Default Currency') }}
                                    </label>
                                    <small
                                        class="form-text">{{ __('This will replace the current default currency') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($currency->is_default)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        {{ __('This is the default currency. Exchange rates for other currencies are calculated relative to this currency.') }}
                    </div>
                    @endif

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Update Currency') }}
                        </button>
                        <a href="{{ route('admin.currencies.index') }}" class="btn btn-secondary">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Currency Statistics') }}</h3>
            </div>
            <div class="card-body">
                <div class="card modern-card stats-card mb-3">
                    <div class="stats-card-body">
                        <div class="stats-card-content">
                            <div class="stats-label">{{ __('Created') }}</div>
                            <div class="stats-number">{{ $currency->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>
                </div>
                <div class="card modern-card stats-card mb-3">
                    <div class="stats-card-body">
                        <div class="stats-card-content">
                            <div class="stats-label">{{ __('Last Updated') }}</div>
                            <div class="stats-number">{{ $currency->updated_at->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>
                </div>
                <div class="card modern-card stats-card">
                    <div class="stats-card-body">
                        <div class="stats-card-content">
                            <div class="stats-label">{{ __('Status') }}</div>
                            <div>
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

    <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Quick Actions') }}</h3>
            </div>
            <div class="card-body">
                @if(!$currency->is_default)
                <form action="{{ route('admin.currencies.set-default', $currency) }}" method="POST" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-block js-confirm" data-confirm="{{ __('Are you sure you want to set this as default currency?') }}">
                        <i class="fas fa-star"></i>
                        {{ __('Set as Default') }}
                    </button>
                </form>
                @endif

                @if(!$currency->is_default)
                <button type="button" class="btn btn-outline-danger btn-block" data-action="delete-currency">
                    <i class="fas fa-trash"></i> {{ __('Delete Currency') }}
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Form (hidden) -->
@if(!$currency->is_default)
<form id="deleteForm" action="{{ route('admin.currencies.destroy', $currency) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endif
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/currencies.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('admin/js/currencies-edit.js') }}"></script>
@endpush