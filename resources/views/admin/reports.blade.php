@extends('layouts.admin')

@section('title', __('Reports & Analytics'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item active">{{ __('Reports') }}</li>
@endsection

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
<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('Reports & Analytics') }}</h1>
        <p class="page-description">{{ __('Comprehensive system reports and detailed analytics') }}</p>
    </div>
    <div class="page-actions">
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-outline-secondary me-2" id="refreshReportsBtn"
                data-bs-toggle="tooltip" title="{{ __('Refresh reports data') }}">
                <i class="fas fa-sync-alt"></i>
                {{ __('Refresh') }}
            </button>
            <div class="dropdown d-inline-block">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download"></i>
                    {{ __('Export') }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-export="excel">
                            <i class="fas fa-file-excel"></i> {{ __('Export to Excel') }}
                        </a></li>
                    <li><a class="dropdown-item" href="#" data-export="pdf">
                            <i class="fas fa-file-pdf"></i> {{ __('Export to PDF') }}
                        </a></li>
                    <li><a class="dropdown-item" href="#" data-export="csv">
                            <i class="fas fa-file-csv"></i> {{ __('Export to CSV') }}
                        </a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main Statistics Cards -->
<div class="row mb-4">
    <!-- Total Users -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card modern-card stats-card stats-card-danger h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="report-total-users" data-countup data-target="{{ (int)($stats['totalUsers'] ?? 0) }}">
                        {{ $stats['totalUsers'] ?? 0 }}
                    </div>
                    <div class="stats-label">{{ __('Total Users') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-arrow-up text-success"></i>
                        <span class="text-success">+12%</span>
                        <small>{{ __('this month') }}</small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="{{ route('admin.reports.users') }}" class="stats-link">
                    {{ __('View Report') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Total Vendors -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card modern-card stats-card stats-card-success h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="report-total-vendors" data-countup data-target="{{ (int)($stats['totalVendors'] ?? 0) }}">
                        {{ $stats['totalVendors'] ?? 0 }}
                    </div>
                    <div class="stats-label">{{ __('Total Vendors') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-arrow-up text-success"></i>
                        <span class="text-success">+8%</span>
                        <small>{{ __('this month') }}</small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-store"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="{{ route('admin.reports.vendors') }}" class="stats-link">
                    {{ __('View Report') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Pending Users -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card modern-card stats-card stats-card-primary h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="report-pending-users" data-countup data-target="{{ (int)($stats['pendingUsers'] ?? 0) }}">
                        {{ $stats['pendingUsers'] ?? 0 }}
                    </div>
                    <div class="stats-label">{{ __('Pending Approvals') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-clock text-warning"></i>
                        <span class="text-muted">{{ __('Needs attention') }}</span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="{{ route('admin.users.pending') }}" class="stats-link">
                    {{ __('Review Pending') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Total Balance -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card modern-card stats-card stats-card-info h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="report-total-balance" data-countup data-decimals="2" data-target="{{ number_format($stats['totalBalance'] ?? 0, 2, '.', '') }}">
                        {{ $stats['totalBalance'] ?? '0.00' }}
                    </div>
                    <div class="stats-label">{{ __('Total Balance') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-dollar-sign text-info"></i>
                        <span class="text-info">{{ $defaultCurrency ? $defaultCurrency->code : '' }}</span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="{{ route('admin.reports.financial') }}" class="stats-link">
                    {{ __('Financial Report') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Analytics and Charts Section -->
<div class="row mb-4">
    <div class="col-lg-8">
    <div class="card modern-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    {{ __('User Registration Trends') }}
                </h5>
                <div class="chart-controls">
                    <select class="form-select form-select-sm" id="analytics-period">
                        <option value="7">{{ __('Last 7 days') }}</option>
                        <option value="30" selected>{{ __('Last 30 days') }}</option>
                        <option value="90">{{ __('Last 90 days') }}</option>
                        <option value="365">{{ __('Last year') }}</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container h-400">
                    <canvas id="userAnalyticsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
    <div class="card modern-card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    {{ __('User Distribution') }}
                </h5>
            </div>
            <div class="card-body">
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
    <div class="card modern-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    {{ __('Quick Reports') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('admin.reports.users') }}" class="card modern-card h-100 text-decoration-none">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stats-icon bg-primary">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ __('Users Report') }}</h6>
                                    <p class="mb-2 text-muted small">{{ __('Detailed user analytics and statistics') }}</p>
                                    <span class="badge badge-primary" data-countup data-target="{{ (int)($stats['totalUsers'] ?? 0) }}">{{ isset($stats['totalUsers']) ? $stats['totalUsers'] : '0' }} {{ __('users') }}</span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.reports.vendors') }}" class="card modern-card h-100 text-decoration-none">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stats-icon bg-success">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ __('Vendors Report') }}</h6>
                                    <p class="mb-2 text-muted small">{{ __('Vendor performance and activity') }}</p>
                                    <span class="badge badge-success" data-countup data-target="{{ (int)($stats['totalVendors'] ?? 0) }}">{{ isset($stats['totalVendors']) ? $stats['totalVendors'] : '0' }} {{ __('vendors') }}</span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.reports.financial') }}" class="card modern-card h-100 text-decoration-none">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stats-icon bg-info">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ __('Financial Report') }}</h6>
                                    <p class="mb-2 text-muted small">{{ __('Revenue, transactions and balances') }}</p>
                                    <span class="badge badge-info" data-countup data-decimals="2" data-target="{{ number_format($stats['totalBalance'] ?? 0, 2, '.', '') }}">{{ isset($stats['totalBalance']) ? number_format($stats['totalBalance'], 2) : '0.00' }} {{ $defaultCurrency ? $defaultCurrency->code : '' }}</span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.reports.system') }}" class="card modern-card h-100 text-decoration-none">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stats-icon bg-warning">
                                    <i class="fas fa-server"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ __('System Report') }}</h6>
                                    <p class="mb-2 text-muted small">{{ __('System health and performance metrics') }}</p>
                                    <span class="badge badge-warning">{{ __('Live monitoring') }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="col-lg-4">
        <!-- System Health -->
    <div class="card modern-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-heartbeat me-2"></i>
                    {{ __('System Health') }}
                </h5>
            </div>
        <div class="card-body">
        @if(isset($systemHealth))
            <div class="system-health-grid">
                {{-- Database --}}
                <div class="progress-item">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="progress-label">{{ __('Database') }}</div>
                        <div class="health-status badge bg-{{ $systemHealth['database']['status'] === 'healthy' ? 'success' : 'danger' }}">{{ $systemHealth['database']['status'] === 'healthy' ? __('Healthy') : __('Error') }}</div>
                    </div>
                    <div class="progress w-100">
                        <span class="progress-bar bg-{{ $systemHealth['database']['status'] === 'healthy' ? 'success' : 'danger' }} {{ $systemHealth['database']['status'] === 'healthy' ? 'w-100p' : 'w-0p' }}"></span>
                    </div>
                </div>

                {{-- Cache --}}
                <div class="progress-item">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="progress-label">{{ __('Cache') }}</div>
                        <div class="health-status badge bg-{{ $systemHealth['cache']['status'] === 'healthy' ? 'success' : 'danger' }}">{{ $systemHealth['cache']['status'] === 'healthy' ? __('Healthy') : __('Error') }}</div>
                    </div>
                    <div class="progress w-100">
                        <span class="progress-bar bg-{{ $systemHealth['cache']['status'] === 'healthy' ? 'success' : 'danger' }} {{ $systemHealth['cache']['status'] === 'healthy' ? 'w-100p' : 'w-0p' }}"></span>
                    </div>
                </div>

                {{-- Storage --}}
                <div class="progress-item">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="progress-label">{{ __('Storage') }}</div>
                        <div class="health-status badge bg-{{ $systemHealth['storage']['status'] === 'healthy' ? 'success' : 'danger' }}">{{ $systemHealth['storage']['status'] === 'healthy' ? __('Healthy') : __('Error') }}</div>
                    </div>
                    <div class="progress w-100">
                        <span class="progress-bar bg-{{ $systemHealth['storage']['status'] === 'healthy' ? 'success' : 'danger' }} {{ $systemHealth['storage']['status'] === 'healthy' ? 'w-100p' : 'w-0p' }}"></span>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-3">
                <i class="fas fa-exclamation-triangle text-warning mb-2"></i>
                <p class="text-muted mb-0">{{ __('System health data not available') }}</p>
            </div>
        @endif
            </div>
        </div>
    </div>
</div>

@endsection

