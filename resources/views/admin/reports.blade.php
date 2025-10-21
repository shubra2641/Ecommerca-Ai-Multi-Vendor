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
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Reports & Analytics') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Comprehensive system reports and detailed analytics') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <button type="button" class="admin-btn admin-btn-secondary" id="refreshReportsBtn"
                    data-bs-toggle="tooltip" title="{{ __('Refresh reports data') }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M23 4v6h-6M1 20v-6h6m15-4a9 9 0 11-18 0 9 9 0 0118 0zM1 10a9 9 0 0118 0" />
                    </svg>
                    {{ __('Refresh') }}
                </button>
                <div class="dropdown d-inline-block">
                    <button class="admin-btn admin-btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{ __('Export') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-export="excel">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg> {{ __('Export to Excel') }}
                            </a></li>
                        <li><a class="dropdown-item" href="#" data-export="pdf">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14,2 14,8 20,8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10,9 9,9 8,9" />
                                </svg> {{ __('Export to PDF') }}
                            </a></li>
                        <li><a class="dropdown-item" href="#" data-export="csv">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14,2 14,8 20,8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10,9 9,9 8,9" />
                                </svg> {{ __('Export to CSV') }}
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="admin-stats-grid">
            <!-- Total Users -->
            <div class="admin-stat-card admin-stat-primary">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="report-total-users" data-countup data-target="{{ (int)($stats['totalUsers'] ?? 0) }}">
                        {{ $stats['totalUsers'] ?? 0 }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Users') }}</div>
                    <div class="admin-stat-description">{{ __('All registered users in the system') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.reports.users') }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Report') }}
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                        <span>+12%</span>
                        <small>{{ __('this month') }}</small>
                    </div>
                </div>
            </div>

            <!-- Total Vendors -->
            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                        <span>+8%</span>
                        <small>{{ __('this month') }}</small>
                    </div>
                </div>
            </div>

            <!-- Pending Users -->
            <div class="admin-stat-card admin-stat-warning">
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
                    <div class="admin-stat-value" id="report-pending-users" data-countup data-target="{{ (int)($stats['pendingUsers'] ?? 0) }}">
                        {{ $stats['pendingUsers'] ?? 0 }}
                    </div>
                    <div class="admin-stat-label">{{ __('Pending Approvals') }}</div>
                    <div class="admin-stat-description">{{ __('Users waiting for approval') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.users.pending') }}" class="admin-btn admin-btn-secondary">
                        {{ __('Review Pending') }}
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ __('Needs attention') }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Balance -->
            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm9 7h-6v13h-2v-6h-2v6H9V9H3V7h18v2z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm9 7h-6v13h-2v-6h-2v6H9V9H3V7h18v2z" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm9 7h-6v13h-2v-6h-2v6H9V9H3V7h18v2z" />
                        </svg>
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
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M3 3v18h18M7 12l3-3 3 3 5-5" />
                            </svg>
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
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M21.21 15.89A10 10 0 1 1 8 2.83" />
                                <path d="M22 12A10 10 0 0 0 12 2v10l10 0z" />
                            </svg>
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
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
                            </svg>
                            {{ __('Quick Reports') }}
                        </h5>
                    </div>
                    <div class="admin-card-body">
                        <div class="admin-items-list">
                            <a href="{{ route('admin.reports.users') }}" class="admin-item-card text-decoration-none">
                                <div class="admin-item-placeholder admin-item-placeholder-primary">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="admin-item-details">
                                    <div class="admin-item-name">{{ __('Users Report') }}</div>
                                    <div class="admin-item-meta">
                                        <span class="admin-text-muted">{{ __('Detailed user analytics and statistics') }}</span>
                                    </div>
                                </div>
                                <div class="admin-item-price">
                                    <span class="admin-badge admin-badge-primary" data-countup data-target="{{ (int)($stats['totalUsers'] ?? 0) }}">{{ isset($stats['totalUsers']) ? $stats['totalUsers'] : '0' }} {{ __('users') }}</span>
                                </div>
                            </a>

                            <a href="{{ route('admin.reports.vendors') }}" class="admin-item-card text-decoration-none">
                                <div class="admin-item-placeholder admin-item-placeholder-success">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
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
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M3 3v18h18M7 12l3-3 3 3 5-5" />
                                    </svg>
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
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
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
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                            </svg>
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
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-warning mb-2">
                                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
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