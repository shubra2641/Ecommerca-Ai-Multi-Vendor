@extends('layouts.admin')

@section('title', __('Reports & Analytics'))

<!-- Reports Data Bridge (base64 JSON) -->
<div id="reports-data" class="d-none" data-payload="{{ base64_encode(json_encode([
    'chartData' => $chartData ?? [
        'labels' => $chartData['labels'] ?? [],
        'userData' => $chartData['userData'] ?? []
    ],
    'systemHealth' => $systemHealth ?? [],
    'stats' => [
        'activeUsers' => $stats['activeUsers'] ?? 0,
        'pendingUsers' => $stats['pendingUsers'] ?? 0,
        'inactiveUsers' => $stats['inactiveUsers'] ?? 0,
        'totalUsers' => $stats['totalUsers'] ?? 0,
        'totalVendors' => $stats['totalVendors'] ?? 0,
        'totalBalance' => $stats['totalBalance'] ?? 0,
    ]
])) }}"></div>

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Reports & Analytics') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Comprehensive system reports and detailed analytics') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="admin-stats-grid">
            <!-- Total Users -->
            <div class="admin-stat-card admin-stat-primary">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="report-total-users" data-countup data-target="{{ (int)($stats['totalUsers'] ?? 0) }}">
                        {{ $stats['totalUsers'] ?? 0 }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Users') }}</div>
                    <div class="admin-stat-description">{{ __('All registered users in the system') }}</div>
                </div>
            </div>

            <!-- Total Vendors -->
            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="report-total-vendors" data-countup data-target="{{ (int)($stats['totalVendors'] ?? 0) }}">
                        {{ $stats['totalVendors'] ?? 0 }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Vendors') }}</div>
                    <div class="admin-stat-description">{{ __('Active vendors selling products') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.reports.vendors') }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Report') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>+8%</span>
                        <small>{{ __('this month') }}</small>
                    </div>
                </div>
            </div>

            <!-- Pending Users -->
            <div class="admin-stat-card admin-stat-warning">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="report-pending-users" data-countup data-target="{{ (int)($stats['pendingUsers'] ?? 0) }}">
                        {{ $stats['pendingUsers'] ?? 0 }}
                    </div>
                    <div class="admin-stat-label">{{ __('Pending Approvals') }}</div>
                    <div class="admin-stat-description">{{ __('Users waiting for approval') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.users.pending') }}" class="admin-btn admin-btn-secondary">
                        {{ __('Review Pending') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>{{ __('Needs attention') }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Balance -->
            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="report-total-balance" data-countup data-decimals="2" data-target="{{ number_format($stats['totalBalance'] ?? 0, 2, '.', '') }}">
                        {{ $stats['totalBalance'] ?? '0.00' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Balance') }}</div>
                    <div class="admin-stat-description">{{ __('System-wide financial balance') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.reports.financial') }}" class="admin-btn admin-btn-secondary">
                        {{ __('Financial Report') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <i class="fas fa-dollar-sign"></i>
                        <span>{{ $defaultCurrency ? $defaultCurrency->code : '' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics and Charts Section -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="admin-modern-card">
                    <div class="admin-card-header d-flex justify-content-between align-items-center">
                        <h5 class="admin-card-title mb-0">
                            <i class="fas fa-chart-line"></i>
                            {{ __('User Registration Trends') }}
                        </h5>
                        <div class="chart-controls">
                            <select class="admin-form-select" id="analytics-period">
                                <option value="7">{{ __('Last 7 days') }}</option>
                                <option value="30" selected>{{ __('Last 30 days') }}</option>
                                <option value="90">{{ __('Last 90 days') }}</option>
                                <option value="365">{{ __('Last year') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="admin-card-body">
                        <div class="chart-container h-400">
                            <canvas id="userAnalyticsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="admin-modern-card h-100">
                    <div class="admin-card-header">
                        <h5 class="admin-card-title mb-0">
                            <i class="fas fa-chart-pie"></i>
                            {{ __('User Distribution') }}
                        </h5>
                    </div>
                    <div class="admin-card-body">
                        <div class="chart-container h-380">
                            <canvas id="userDistributionChart"></canvas>
                        </div>
                        <div class="chart-legend mt-3">
                            <div class="legend-item">
                                <span class="legend-color bg-primary"></span>
                                <span class="legend-label">{{ __('Active Users') }}</span>
                                <span class="legend-value" data-countup data-target="{{ (int)($stats['activeUsers'] ?? 0) }}">{{ isset($stats['activeUsers']) ? $stats['activeUsers'] : '0' }}</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color bg-warning"></span>
                                <span class="legend-label">{{ __('Pending Users') }}</span>
                                <span class="legend-value" data-countup data-target="{{ (int)($stats['pendingUsers'] ?? 0) }}">{{ isset($stats['pendingUsers']) ? $stats['pendingUsers'] : '0' }}</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color bg-danger"></span>
                                <span class="legend-label">{{ __('Inactive Users') }}</span>
                                <span class="legend-value" data-countup data-target="{{ (int)($stats['inactiveUsers'] ?? 0) }}">{{ isset($stats['inactiveUsers']) ? $stats['inactiveUsers'] : '0' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Grid -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Quick Reports -->
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <h5 class="admin-card-title mb-0">
                            <i class="fas fa-bolt"></i>
                            {{ __('Quick Reports') }}
                        </h5>
                    </div>
                    <div class="admin-card-body">
                        <div class="admin-items-list">

                            <a href="{{ route('admin.reports.vendors') }}" class="admin-item-card text-decoration-none">
                                <div class="admin-item-placeholder admin-item-placeholder-success">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="admin-item-details">
                                    <div class="admin-item-name">{{ __('Vendors Report') }}</div>
                                    <div class="admin-item-meta">
                                        <span class="admin-text-muted">{{ __('Vendor performance and activity') }}</span>
                                    </div>
                                </div>
                                <div class="admin-item-price">
                                    <span class="admin-badge admin-badge-success" data-countup data-target="{{ (int)($stats['totalVendors'] ?? 0) }}">{{ isset($stats['totalVendors']) ? $stats['totalVendors'] : '0' }} {{ __('vendors') }}</span>
                                </div>
                            </a>

                            <a href="{{ route('admin.reports.financial') }}" class="admin-item-card text-decoration-none">
                                <div class="admin-item-placeholder admin-item-placeholder-info">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="admin-item-details">
                                    <div class="admin-item-name">{{ __('Financial Report') }}</div>
                                    <div class="admin-item-meta">
                                        <span class="admin-text-muted">{{ __('Revenue, transactions and balances') }}</span>
                                    </div>
                                </div>
                                <div class="admin-item-price">
                                    <span class="admin-badge admin-badge-info" data-countup data-decimals="2" data-target="{{ number_format($stats['totalBalance'] ?? 0, 2, '.', '') }}">{{ isset($stats['totalBalance']) ? number_format($stats['totalBalance'], 2) : '0.00' }} {{ $defaultCurrency ? $defaultCurrency->code : '' }}</span>
                                </div>
                            </a>

                            <a href="{{ route('admin.reports.system') }}" class="admin-item-card text-decoration-none">
                                <div class="admin-item-placeholder admin-item-placeholder-warning">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="admin-item-details">
                                    <div class="admin-item-name">{{ __('System Report') }}</div>
                                    <div class="admin-item-meta">
                                        <span class="admin-text-muted">{{ __('System health and performance metrics') }}</span>
                                    </div>
                                </div>
                                <div class="admin-item-price">
                                    <span class="admin-badge admin-badge-warning">{{ __('Live monitoring') }}</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="col-lg-4">
                <!-- System Health -->
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <h5 class="admin-card-title mb-0">
                            <i class="fas fa-heartbeat"></i>
                            {{ __('System Health') }}
                        </h5>
                    </div>
                    <div class="admin-card-body">
                        @if(isset($systemHealth))
                        <div class="system-health-grid">
                            {{-- Database --}}
                            <div class="progress-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="progress-label">{{ __('Database') }}</div>
                                    <div class="health-status admin-status-badge admin-status-badge-{{ $systemHealth['database']['status'] === 'healthy' ? 'completed' : 'warning' }}">{{ $systemHealth['database']['status'] === 'healthy' ? __('Healthy') : __('Error') }}</div>
                                </div>
                                <div class="progress w-100">
                                    <span class="progress-bar bg-{{ $systemHealth['database']['status'] === 'healthy' ? 'success' : 'danger' }} {{ $systemHealth['database']['status'] === 'healthy' ? 'w-100p' : 'w-0p' }}"></span>
                                </div>
                            </div>

                            {{-- Cache --}}
                            <div class="progress-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="progress-label">{{ __('Cache') }}</div>
                                    <div class="health-status admin-status-badge admin-status-badge-{{ $systemHealth['cache']['status'] === 'healthy' ? 'completed' : 'warning' }}">{{ $systemHealth['cache']['status'] === 'healthy' ? __('Healthy') : __('Error') }}</div>
                                </div>
                                <div class="progress w-100">
                                    <span class="progress-bar bg-{{ $systemHealth['cache']['status'] === 'healthy' ? 'success' : 'danger' }} {{ $systemHealth['cache']['status'] === 'healthy' ? 'w-100p' : 'w-0p' }}"></span>
                                </div>
                            </div>

                            {{-- Storage --}}
                            <div class="progress-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="progress-label">{{ __('Storage') }}</div>
                                    <div class="health-status admin-status-badge admin-status-badge-{{ $systemHealth['storage']['status'] === 'healthy' ? 'completed' : 'warning' }}">{{ $systemHealth['storage']['status'] === 'healthy' ? __('Healthy') : __('Error') }}</div>
                                </div>
                                <div class="progress w-100">
                                    <span class="progress-bar bg-{{ $systemHealth['storage']['status'] === 'healthy' ? 'success' : 'danger' }} {{ $systemHealth['storage']['status'] === 'healthy' ? 'w-100p' : 'w-0p' }}"></span>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-3">
                            <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 48px;"></i>
                            <p class="text-muted mb-0">{{ __('System health data not available') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection