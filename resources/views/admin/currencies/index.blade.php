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
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Currencies') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage system currencies and exchange rates') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.currencies.create') }}" class="admin-btn admin-btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 5v14m7-7H5" />
                    </svg>
                    {{ __('Add Currency') }}
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)$currencies->count() }}">{{ $currencies->count() }}</div>
                    <div class="admin-stat-label">{{ __('Total Currencies') }}</div>
                    <div class="admin-stat-description">{{ __('System Currencies') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ __('System Currencies') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)$currencies->where('is_active', true)->count() }}">{{ $currencies->where('is_active', true)->count() }}</div>
                    <div class="admin-stat-label">{{ __('Active Currencies') }}</div>
                    <div class="admin-stat-description">{{ number_format((($currencies->where('is_active', true)->count() / max($currencies->count(), 1)) * 100), 1) }}% {{ __('active') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 4-4M8 21l4-4 4 4M3 4l6 6 6-6" />
                        </svg>
                        <span>{{ __('Active Rate') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value">{{ $currencies->where('is_default', true)->first()->code ?? __('N/A') }}</div>
                    <div class="admin-stat-label">{{ __('Default Currency') }}</div>
                    <div class="admin-stat-description">{{ __('Primary currency') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        <span>{{ __('Primary') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value">{{ $currencies->max('updated_at')?->diffForHumans() ?? __('N/A') }}</div>
                    <div class="admin-stat-label">{{ __('Last Updated') }}</div>
                    <div class="admin-stat-description">{{ __('Recent activity') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ __('Activity') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Currencies Table -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
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
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </a>
                        <a href="{{ route('admin.currencies.edit', $currency) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        @if(!$currency->is_default)
                        <form action="{{ route('admin.currencies.toggle-status', $currency) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-{{ $currency->is_active ? 'warning' : 'success' }}" title="{{ $currency->is_active ? __('Deactivate') : __('Activate') }}">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </button>
                        </form>
                        @endif
                        @if(!$currency->is_default)
                        <form action="{{ route('admin.currencies.set-default', $currency) }}" method="POST" class="d-inline js-confirm" data-confirm="{{ __('Are you sure you want to set this as default currency?') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-warning" title="{{ __('Set as Default') }}">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </button>
                        </form>
                        @endif
                        @if(!$currency->is_default && !$currency->is_active)
                        <form action="{{ route('admin.currencies.destroy', $currency) }}" method="POST" class="d-inline js-confirm" data-confirm="{{ __('Are you sure you want to delete this currency?') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
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
                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3>{{ __('No Currencies Found') }}</h3>
                <p>{{ __('Start by adding your first currency to the system.') }}</p>
                <a href="{{ route('admin.currencies.create') }}" class="admin-btn admin-btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 5v14m7-7H5" />
                    </svg>
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
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('Exchange Rate Information') }}
            </h2>
        </div>
        <div class="admin-card-body">
            <div class="alert alert-info border-0">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-2">
                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ __('Update All Rates') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>
@endsection