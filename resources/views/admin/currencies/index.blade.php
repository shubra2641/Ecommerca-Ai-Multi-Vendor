@extends('layouts.admin')
@section('title', __('Currencies'))
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
                        <h1 class="admin-order-title">{{ __('Currencies') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage system currencies and exchange rates') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.currencies.create') }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('Add Currency') }}
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)$currencies->count() }}">{{ $currencies->count() }}</div>
                    <div class="admin-stat-label">{{ __('Total Currencies') }}</div>
                    <div class="admin-stat-description">{{ __('System Currencies') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-dollar-sign"></i>
                        <span>{{ __('System Currencies') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)$currencies->where('is_active', true)->count() }}">{{ $currencies->where('is_active', true)->count() }}</div>
                    <div class="admin-stat-label">{{ __('Active Currencies') }}</div>
                    <div class="admin-stat-description">{{ number_format((($currencies->where('is_active', true)->count() / max($currencies->count(), 1)) * 100), 1) }}% {{ __('active') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-chart-line"></i>
                        <span>{{ __('Active Rate') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value">{{ $currencies->where('is_default', true)->first()->code ?? __('N/A') }}</div>
                    <div class="admin-stat-label">{{ __('Default Currency') }}</div>
                    <div class="admin-stat-description">{{ __('Primary currency') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <i class="fas fa-star"></i>
                        <span>{{ __('Primary') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value">{{ $currencies->max('updated_at')?->diffForHumans() ?? __('N/A') }}</div>
                    <div class="admin-stat-label">{{ __('Last Updated') }}</div>
                    <div class="admin-stat-description">{{ __('Recent activity') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <i class="fas fa-clock"></i>
                        <span>{{ __('Activity') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Currencies Table -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <i class="fas fa-dollar-sign"></i>
                    {{ __('Currencies List') }}
                </h2>
                <div class="admin-badge-count">{{ $currencies->count() }} {{ __('currencies') }}</div>
            </div>
            <div class="admin-card-body">
                @if($currencies->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Currency') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Symbol') }}</th>
                                <th>{{ __('Exchange Rate') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Default') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th width="250">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($currencies as $currency)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="admin-item-placeholder admin-item-placeholder-primary me-3">
                                            <span class="fw-bold">{{ strtoupper(substr($currency->code, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $currency->name }}</div>
                                            <small class="admin-text-muted">{{ $currency->full_name ?? $currency->name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="admin-badge">{{ strtoupper($currency->code) }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary fs-5">{{ $currency->symbol }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ number_format($currency->exchange_rate, 4) }}</div>
                                    <small class="admin-text-muted">{{ __('to USD') }}</small>
                </div>
                </td>
                <td>
                    @if($currency->is_active)
                    <span class="badge bg-success">{{ __('Active') }}</span>
                    @else
                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                    @endif
                </td>
                <td>
                    @if($currency->is_default)
                    <span class="badge bg-warning">{{ __('Default') }}</span>
                    @else
                    <span class="admin-text-muted">-</span>
                    @endif
                </td>
                <td>
                    <div class="fw-bold">{{ $currency->created_at->format('M d, Y') }}</div>
                    <small class="admin-text-muted">{{ $currency->created_at->format('H:i') }}</small>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.currencies.show', $currency) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('View') }}">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.currencies.edit', $currency) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if(!$currency->is_default)
                        <form action="{{ route('admin.currencies.toggle-status', $currency) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-{{ $currency->is_active ? 'warning' : 'success' }}" title="{{ $currency->is_active ? __('Deactivate') : __('Activate') }}">
                                <i class="fas fa-play"></i>
                            </button>
                        </form>
                        @endif
                        @if(!$currency->is_default)
                        <form action="{{ route('admin.currencies.set-default', $currency) }}" method="POST" class="d-inline js-confirm" data-confirm="{{ __('Are you sure you want to set this as default currency?') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-warning" title="{{ __('Set as Default') }}">
                                <i class="fas fa-star"></i>
                            </button>
                        </form>
                        @endif
                        @if(!$currency->is_default && !$currency->is_active)
                        <form action="{{ route('admin.currencies.destroy', $currency) }}" method="POST" class="d-inline js-confirm" data-confirm="{{ __('Are you sure you want to delete this currency?') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
                </tr>
                @endforeach
                </tbody>
                </table>
            </div>
            @else
            <div class="admin-empty-state">
                <i class="fas fa-dollar-sign" style="font-size: 48px;"></i>
                <h3>{{ __('No Currencies Found') }}</h3>
                <p>{{ __('Start by adding your first currency to the system.') }}</p>
                <a href="{{ route('admin.currencies.create') }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('Add First Currency') }}
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Exchange Rate Info -->
    <div class="admin-modern-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">
                <i class="fas fa-info-circle"></i>
                {{ __('Exchange Rate Information') }}
            </h2>
        </div>
        <div class="admin-card-body">
            <div class="alert alert-info border-0">
                <i class="fas fa-info-circle me-2"></i>
                <strong>{{ __('Note:') }}</strong>
                {{ __('Exchange rates are relative to USD. The default currency serves as the base for all transactions.') }}
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-3">{{ __('Currency Conversion Examples') }}</h6>
                    @if($currencies->count() >= 2)
                    <div class="conversion-list">
                        @foreach($currencies->take(3) as $currency)
                        <div class="conversion-item d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="fw-semibold">1 USD</span>
                            <span class="text-primary fw-bold">{{ number_format($currency->exchange_rate, 2) }} {{ $currency->code }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-3">{{ __('Last Update') }}</h6>
                    <div class="update-info mb-3">
                        <p class="admin-text-muted mb-2">{{ __('Rates last updated:') }}</p>
                        <div class="fw-semibold text-dark">{{ $currencies->max('updated_at')?->diffForHumans() ?? __('Never') }}</div>
                    </div>
                    <button type="button" class="admin-btn admin-btn-primary" data-action="update-rates">
                        <i class="fas fa-sync-alt"></i>
                        {{ __('Update All Rates') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>
@endsection