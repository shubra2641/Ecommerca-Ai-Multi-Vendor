@extends('layouts.admin')

@section('title', __('Inventory Report'))

@section('content')
<div class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="admin-order-title">
                <h1>
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    {{ __('Inventory Report') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Comprehensive inventory analysis and stock management') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.reports.export', 'inventory') }}" class="admin-btn admin-btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    {{ __('Export Report') }}
                </a>
            </div>
        </div>

        <!-- Enhanced Stats Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card admin-stat-primary">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($totals['total_products'] ?? 0) }}">{{ $totals['total_products'] ?? 0 }}</div>
                    <div class="admin-stat-label">{{ __('Total Products') }}</div>
                    <div class="admin-stat-description">{{ __('All products in catalog') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-up">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                        {{ __('Growing') }}
                    </span>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 3v18h18M7 12l3-3 3 3 5-5" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($totals['manage_stock_count'] ?? 0) }}">{{ $totals['manage_stock_count'] ?? 0 }}</div>
                    <div class="admin-stat-label">{{ __('Stock Tracked') }}</div>
                    <div class="admin-stat-description">{{ __('Products with stock management') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-up">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                        +{{ number_format((($totals['manage_stock_count'] ?? 0) / max($totals['total_products'] ?? 1, 1)) * 100, 1) }}%
                    </span>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-danger">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($totals['out_of_stock'] ?? 0) }}">{{ $totals['out_of_stock'] ?? 0 }}</div>
                    <div class="admin-stat-label">{{ __('Out of Stock') }}</div>
                    <div class="admin-stat-description">{{ __('Products need restocking') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-neutral">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        {{ __('Action needed') }}
                    </span>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-warning">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($totals['serials_low'] ?? 0) }}">{{ $totals['serials_low'] ?? 0 }}</div>
                    <div class="admin-stat-label">{{ __('Low Stock') }}</div>
                    <div class="admin-stat-description">{{ __('Products with ≤5 items') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-neutral">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('Monitor closely') }}
                    </span>
                </div>
            </div>
        </div>
        <!-- Products Table -->
        <div class="card modern-card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <h3 class="card-title mb-0">{{ __('Products Inventory') }}</h3>
                <div class="card-actions">
                    <div class="bulk-actions d-flex flex-column flex-sm-row gap-2" id="bulkActions">
                        <span class="selected-count text-muted">0</span> <span class="text-muted d-none d-sm-inline">{{ __('selected') }}</span>
                        <button type="button" class="btn btn-sm btn-success" data-action="bulk-export">
                            <i class="fas fa-download"></i>
                            <span class="d-none d-md-inline">{{ __('Export Selected') }}</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-action="bulk-print">
                            <i class="fas fa-print"></i>
                            <span class="d-none d-md-inline">{{ __('Print Report') }}</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(count($products ?? []) > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="30"><input type="checkbox" id="select-all"></th>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('SKU') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th class="d-none d-md-table-cell">{{ __('Stock Management') }}</th>
                                <th class="d-none d-lg-table-cell">{{ __('Available Stock') }}</th>
                                <th class="d-none d-lg-table-cell">{{ __('Serials') }}</th>
                                <th class="d-none d-xl-table-cell">{{ __('Unsold Serials') }}</th>
                                <th class="d-none d-lg-table-cell">{{ __('Variations') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $p)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input" id="select-{{ $p['id'] }}">
                                </td>
                                <td>
                                    <span class="admin-badge admin-badge-secondary">{{ $p['id'] }}</span>
                                </td>
                                <td>
                                    <code class="admin-code">{{ $p['sku'] }}</code>
                                </td>
                                <td>
                                    <div class="admin-item-name">{{ $p['name'] }}</div>
                                </td>
                                <td>
                                    @if($p['manage_stock'])
                                    <span class="admin-status-badge admin-status-badge-success">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('Yes') }}
                                    </span>
                                    @else
                                    <span class="admin-status-badge admin-status-badge-secondary">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                        {{ __('No') }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    @if($p['available_stock'] === null)
                                    <span class="admin-status-badge admin-status-badge-info">{{ __('Unlimited') }}</span>
                                    @else
                                    <span class="admin-stock-value">{{ $p['available_stock'] }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($p['has_serials'])
                                    <span class="admin-status-badge admin-status-badge-success">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('Yes') }}
                                    </span>
                                    @else
                                    <span class="admin-status-badge admin-status-badge-secondary">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                        {{ __('No') }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="admin-stock-value">{{ $p['unsold_serials'] }}</span>
                                </td>
                                <td>
                                    @if(!empty($p['variations']) && $p['variations']->count() > 0)
                                    <button class="admin-btn admin-btn-small admin-btn-outline" data-bs-toggle="collapse" data-bs-target="#vars-{{ $p['id'] }}">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M9 5l7 7-7 7" />
                                        </svg>
                                        {{ $p['variations']->count() }} {{ __('Variations') }}
                                    </button>
                                    <div class="collapse mt-2" id="vars-{{ $p['id'] }}">
                                        <div class="admin-variations-table">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('SKU') }}</th>
                                                        <th>{{ __('Name') }}</th>
                                                        <th>{{ __('Stock') }}</th>
                                                        <th>{{ __('Available') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($p['variations'] as $v)
                                                    <tr>
                                                        <td><code class="admin-code">{{ $v['sku'] }}</code></td>
                                                        <td>{{ e($v['name']) }}</td>
                                                        <td>
                                                            @if($v['manage_stock'])
                                                            <span class="admin-status-badge admin-status-badge-success">{{ __('Yes') }}</span>
                                                            @else
                                                            <span class="admin-status-badge admin-status-badge-secondary">{{ __('No') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($v['available_stock'] === null)
                                                            <span class="admin-status-badge admin-status-badge-info">{{ __('Unlimited') }}</span>
                                                            @else
                                                            <span class="admin-stock-value">{{ $v['available_stock'] }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @else
                                    <span class="admin-text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="admin-empty-state">
                    <div class="admin-notification-icon">
                        <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3>{{ __('No Products Found') }}</h3>
                    <p>{{ __('No products available for inventory report.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection