@extends('layouts.admin')

@section('title', __('Inventory Report'))

@section('content')
<div class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="admin-order-title">
                <h1>
                    <i class="fas fa-boxes"></i>
                    {{ __('Inventory Report') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Comprehensive inventory analysis and stock management') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.reports.export', 'inventory') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-download"></i>
                    {{ __('Export Report') }}
                </a>
            </div>
        </div>

        <!-- Enhanced Stats Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card admin-stat-primary">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($totals['total_products'] ?? 0) }}">{{ $totals['total_products'] ?? 0 }}</div>
                    <div class="admin-stat-label">{{ __('Total Products') }}</div>
                    <div class="admin-stat-description">{{ __('All products in catalog') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        {{ __('Growing') }}
                    </span>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($totals['manage_stock_count'] ?? 0) }}">{{ $totals['manage_stock_count'] ?? 0 }}</div>
                    <div class="admin-stat-label">{{ __('Stock Tracked') }}</div>
                    <div class="admin-stat-description">{{ __('Products with stock management') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        +{{ number_format((($totals['manage_stock_count'] ?? 0) / max($totals['total_products'] ?? 1, 1)) * 100, 1) }}%
                    </span>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-danger">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($totals['out_of_stock'] ?? 0) }}">{{ $totals['out_of_stock'] ?? 0 }}</div>
                    <div class="admin-stat-label">{{ __('Out of Stock') }}</div>
                    <div class="admin-stat-description">{{ __('Products need restocking') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-neutral">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ __('Action needed') }}
                    </span>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-warning">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($totals['serials_low'] ?? 0) }}">{{ $totals['serials_low'] ?? 0 }}</div>
                    <div class="admin-stat-label">{{ __('Low Stock') }}</div>
                    <div class="admin-stat-description">{{ __('Products with ≤5 items') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-neutral">
                        <i class="fas fa-clock"></i>
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
                                        <i class="fas fa-check"></i>
                                        {{ __('Yes') }}
                                    </span>
                                    @else
                                    <span class="admin-status-badge admin-status-badge-secondary">
                                        <i class="fas fa-times"></i>
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
                                        <i class="fas fa-check"></i>
                                        {{ __('Yes') }}
                                    </span>
                                    @else
                                    <span class="admin-status-badge admin-status-badge-secondary">
                                        <i class="fas fa-times"></i>
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
                                        <i class="fas fa-chevron-right"></i>
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
                        <i class="fas fa-boxes"></i>
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