@extends('layouts.admin')
@section('title', __('Currency Details'))
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.currencies.index') }}">{{ __('Currencies') }}</a></li>
<li class="breadcrumb-item active">{{ __('Currency Details') }}</li>
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
                        <h1 class="admin-order-title">{{ __('Currency Details') }}</h1>
                        <p class="admin-order-subtitle">{{ __('View and manage currency information') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.currencies.edit', $currency) }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-edit"></i>
                    {{ __('Edit Currency') }}
                </a>
                <a href="{{ route('admin.currencies.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>

        <div class="admin-order-grid-modern">
            <!-- Currency Information -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Currency Information') }}
                    </h2>
                </div>
                <div class="admin-card-body">
                    <div class="admin-info-grid">
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <i class="fas fa-dollar-sign"></i>
                                {{ __('Name') }}
                            </div>
                            <div class="admin-info-value">{{ $currency->name }}</div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <i class="fas fa-file-alt"></i>
                                {{ __('Code') }}
                            </div>
                            <div class="admin-info-value">
                                <span class="admin-badge">{{ strtoupper($currency->code) }}</span>
                            </div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <i class="fas fa-dollar-sign"></i>
                                {{ __('Symbol') }}
                            </div>
                            <div class="admin-info-value">
                                <span class="fw-bold text-primary fs-4">{{ $currency->symbol }}</span>
                            </div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <i class="fas fa-chart-line"></i>
                                {{ __('Exchange Rate') }}
                            </div>
                            <div class="admin-info-value">
                                <span class="fw-bold">{{ number_format($currency->exchange_rate, 4) }}</span>
                                <small class="admin-text-muted">{{ __('to USD') }}</small>
                            </div>
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
                                <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <i class="fas fa-star"></i>
                                {{ __('Default Currency') }}
                            </div>
                            <div class="admin-info-value">
                                @if($currency->is_default)
                                <span class="badge bg-warning">{{ __('Yes') }}</span>
                                @else
                                <span class="badge bg-secondary">{{ __('No') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <i class="fas fa-calendar"></i>
                                {{ __('Created At') }}
                            </div>
                            <div class="admin-info-value">{{ $currency->created_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <i class="fas fa-clock"></i>
                                {{ __('Last Updated') }}
                            </div>
                            <div class="admin-info-value">{{ $currency->updated_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <i class="fas fa-bolt"></i>
                        {{ __('Quick Actions') }}
                    </h2>
                </div>
                <div class="admin-card-body">
                    <div class="admin-actions-grid">
                        @if(!$currency->is_default)
                        <form action="{{ route('admin.currencies.set-default', $currency) }}" method="POST" class="admin-manage-form">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn-primary admin-btn-block">
                                <i class="fas fa-star"></i>
                                {{ __('Set as Default') }}
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('admin.currencies.edit', $currency) }}" class="admin-btn admin-btn-warning admin-btn-block">
                            <i class="fas fa-edit"></i>
                            {{ __('Edit Currency') }}
                        </a>

                        @if(!$currency->is_default)
                        <form action="{{ route('admin.currencies.destroy', $currency) }}" method="POST" class="admin-manage-form js-confirm" data-confirm="{{ __('Are you sure you want to delete this currency?') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-btn admin-btn-danger admin-btn-block">
                                <i class="fas fa-trash"></i>
                                {{ __('Delete Currency') }}
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection