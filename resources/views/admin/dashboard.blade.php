@extends('layouts.admin')

@section('title', __('Admin Dashboard'))

@section('breadcrumb')
<li class="breadcrumb-item active">{{ __('Dashboard') }}</li>
@endsection

@section('content')

<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('Welcome to the Admin Panel') }}</h1>
        <p class="page-description">{{ __('Overview of your system statistics and quick actions') }}</p>
    </div>
    <div class="page-actions">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-plus"></i>
                    <span class="d-none d-sm-inline ms-1">{{ __('Quick Actions') }}</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.users.create') }}">
                            <i class="fas fa-user-plus"></i> {{ __('Add New User') }}
                        </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.currencies.create') }}">
                            <i class="fas fa-coins"></i> {{ __('Add Currency') }}
                        </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.languages.create') }}">
                            <i class="fas fa-language"></i> {{ __('Add Language') }}
                        </a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<!-- Dashboard Data Bridge for Unified Charts -->
<script id="dashboard-data" type="application/json">{!! json_encode([
    'charts' => [
        'users' => [
            'labels' => $chartData['labels'] ?? [],
            'data' => $chartData['data'] ?? [],
        ],
        'sales' => [
            'labels' => $salesChartData['labels'] ?? [],
            'orders' => $salesChartData['orders'] ?? [],
            'revenue' => $salesChartData['revenue'] ?? [],
        ],
        'ordersStatus' => [
            'labels' => $orderStatusChartData['labels'] ?? [],
            'data' => $orderStatusChartData['data'] ?? [],
            'colors' => $orderStatusChartData['colors'] ?? [],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

<!-- Statistics Cards -->
<div class="row mb-4">
    <!-- Total Users -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-danger" data-stat="totalUsers">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="total-users" data-bs-toggle="tooltip"
                        title="{{ __('Total registered users count') }}" data-countup
                        data-target="{{ (int)($stats['totalUsers'] ?? 0) }}">
                        {{ isset($stats['totalUsers']) ? number_format($stats['totalUsers']) : '0' }}
                    </div>
                    <div class="stats-label">{{ __('Total Users') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-arrow-up text-success"></i>
                        <span class="text-success">+12%</span>
                        <small>{{ __('from last month') }}</small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="{{ route('admin.users.index') }}" class="stats-link">
                    {{ __('View Details') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Total Vendors -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-success" data-stat="totalVendors">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="total-vendors" data-bs-toggle="tooltip"
                        title="{{ __('Total registered vendors count') }}" data-countup
                        data-target="{{ (int)($stats['totalVendors'] ?? 0) }}">
                        {{ isset($stats['totalVendors']) ? number_format($stats['totalVendors']) : '0' }}
                    </div>
                    <div class="stats-label">{{ __('Total Vendors') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-arrow-up text-success"></i>
                        <span class="text-success">+8%</span>
                        <small>{{ __('from last month') }}</small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-store"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="{{ route('admin.users.index', ['role' => 'vendor']) }}" class="stats-link">
                    {{ __('View Details') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Pending Users -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-primary" data-stat="pendingUsers">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="pending-users" data-bs-toggle="tooltip"
                        title="{{ __('Users waiting for approval') }}" data-countup
                        data-target="{{ (int)($stats['pendingUsers'] ?? 0) }}">
                        {{ isset($stats['pendingUsers']) ? number_format($stats['pendingUsers']) : '0' }}
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
                    {{ __('View Details') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Active Today -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-info" data-stat="activeToday">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="active-today" data-bs-toggle="tooltip"
                        title="{{ __('Users active today') }}" data-countup
                        data-target="{{ (int)($stats['activeToday'] ?? 0) }}">
                        {{ isset($stats['activeToday']) ? number_format($stats['activeToday']) : '0' }}
                    </div>
                    <div class="stats-label">{{ __('Active Today') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-chart-line text-info"></i>
                        <span class="text-info">{{ __('Real-time') }}</span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="{{ route('admin.users.index', ['filter' => 'active_today']) }}" class="stats-link">
                    {{ __('View Details') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Additional Statistics Row -->
<div class="row mb-4">
    <!-- Total Balance -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-success" data-stat="totalBalance">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="total-balance" data-bs-toggle="tooltip"
                        title="{{ __('Total balance in the system') }}" data-countup data-decimals="2"
                        data-target="{{ number_format($stats['totalBalance'] ?? 0, 2, '.', '') }}">
                        {{ isset($stats['totalBalance']) ? number_format($stats['totalBalance'], 2) : '0.00' }}
                    </div>
                    <div class="stats-label">{{ __('Total Balance') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-dollar-sign text-success"></i>
                        <span class="text-success">{{ $defaultCurrency ? $defaultCurrency->code : '' }}</span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="#" class="stats-link">
                    {{ __('View Details') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- New Users Today -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-danger" data-stat="newUsersToday">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="new-users-today" data-bs-toggle="tooltip"
                        title="{{ __('Users registered today') }}" data-countup
                        data-target="{{ (int)($stats['newUsersToday'] ?? 0) }}">
                        {{ isset($stats['newUsersToday']) ? number_format($stats['newUsersToday']) : '0' }}
                    </div>
                    <div class="stats-label">{{ __('New Users Today') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-user-plus text-primary"></i>
                        <span class="text-primary">{{ __('Today') }}</span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="{{ route('admin.users.index', ['filter' => 'today']) }}" class="stats-link">
                    {{ __('View Details') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Total Admins -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-danger" data-stat="totalAdmins">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="total-admins" data-bs-toggle="tooltip"
                        title="{{ __('Total administrators in the system') }}" data-countup
                        data-target="{{ (int)($stats['totalAdmins'] ?? 0) }}">
                        {{ isset($stats['totalAdmins']) ? number_format($stats['totalAdmins']) : '0' }}
                    </div>
                    <div class="stats-label">{{ __('Total Admins') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-user-shield text-danger"></i>
                        <span class="text-danger">{{ __('Admins') }}</span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" class="stats-link">
                    {{ __('View Details') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-info" data-stat="totalCustomers">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="total-customers" data-bs-toggle="tooltip"
                        title="{{ __('Total customers in the system') }}" data-countup
                        data-target="{{ (int)($stats['totalCustomers'] ?? 0) }}">
                        {{ isset($stats['totalCustomers']) ? number_format($stats['totalCustomers']) : '0' }}
                    </div>
                    <div class="stats-label">{{ __('Total Customers') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-users text-info"></i>
                        <span class="text-info">{{ __('Customers') }}</span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="{{ route('admin.users.index', ['role' => 'customer']) }}" class="stats-link">
                    {{ __('View Details') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- New Users This Week -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-danger" data-stat="newUsersThisWeek">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="new-users-week" data-bs-toggle="tooltip"
                        title="{{ __('Users registered this week') }}" data-countup
                        data-target="{{ (int)($stats['newUsersThisWeek'] ?? 0) }}">
                        {{ isset($stats['newUsersThisWeek']) ? number_format($stats['newUsersThisWeek']) : '0' }}
                    </div>
                    <div class="stats-label">{{ __('New Users This Week') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-calendar-week text-primary"></i>
                        <span class="text-primary">{{ __('This Week') }}</span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="{{ route('admin.users.index', ['filter' => 'this_week']) }}" class="stats-link">
                    {{ __('View Details') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- New Users This Month -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-success" data-stat="newUsersThisMonth">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="new-users-month" data-bs-toggle="tooltip"
                        title="{{ __('Users registered this month') }}" data-countup
                        data-target="{{ (int)($stats['newUsersThisMonth'] ?? 0) }}">
                        {{ isset($stats['newUsersThisMonth']) ? number_format($stats['newUsersThisMonth']) : '0' }}
                    </div>
                    <div class="stats-label">{{ __('New Users This Month') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-calendar-alt text-success"></i>
                        <span class="text-success">{{ __('This Month') }}</span>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="{{ route('admin.users.index', ['filter' => 'this_month']) }}" class="stats-link">
                    {{ __('View Details') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Approved Users -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-info" data-stat="approvedUsers">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="approved-users" data-bs-toggle="tooltip"
                        title="{{ __('Total approved users') }}" data-countup
                        data-target="{{ (int)($stats['approvedUsers'] ?? 0) }}">
                        {{ isset($stats['approvedUsers']) ? number_format($stats['approvedUsers']) : '0' }}
                    </div>
                    <div class="stats-label">{{ __('Approved Users') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-check-circle text-info"></i>
                        <span
                            class="text-info">{{ isset($topStats['approval_rate']) ? $topStats['approval_rate'] . '%' : '0%' }}</span>
                        <small>{{ __('approval rate') }}</small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stats-card-footer">
                <a href="{{ route('admin.users.index', ['status' => 'approved']) }}" class="stats-link">
                    {{ __('View Details') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Orders & Revenue Statistics Row -->
<div class="row mb-4">
    <!-- Total Orders -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-danger" data-stat="totalOrders">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="total-orders" data-bs-toggle="tooltip"
                        title="{{ __('Total Orders') }}" data-countup
                        data-target="{{ (int)($stats['totalOrders'] ?? 0) }}">
                        {{ isset($stats['totalOrders']) ? number_format($stats['totalOrders']) : '0' }}
                    </div>
                    <div class="stats-label">{{ __('Total Orders') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-shopping-cart text-primary"></i>
                        <small class="text-muted">{{ __('All time') }}</small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-success" data-stat="revenueTotal">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="total-revenue" data-bs-toggle="tooltip"
                        title="{{ __('Total Revenue') }}" data-countup data-decimals="2"
                        data-target="{{ number_format($stats['revenueTotal'] ?? 0, 2, '.', '') }}">
                        {{ isset($stats['revenueTotal']) ? number_format($stats['revenueTotal'], 2) : '0.00' }}
                    </div>
                    <div class="stats-label">{{ __('Total Revenue') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-dollar-sign text-success"></i>
                        <small class="text-success">{{ $defaultCurrency->code ?? '' }}</small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Today -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-primary" data-stat="ordersToday">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="orders-today" data-bs-toggle="tooltip"
                        title="{{ __('Orders Today') }}" data-countup
                        data-target="{{ (int)($stats['ordersToday'] ?? 0) }}">
                        {{ isset($stats['ordersToday']) ? number_format($stats['ordersToday']) : '0' }}
                    </div>
                    <div class="stats-label">{{ __('Orders Today') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-calendar-day text-warning"></i>
                        <small class="text-muted">{{ __('Today') }}</small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Today -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-info" data-stat="revenueToday">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" id="revenue-today" data-bs-toggle="tooltip"
                        title="{{ __('Revenue Today') }}" data-countup data-decimals="2"
                        data-target="{{ number_format($stats['revenueToday'] ?? 0, 2, '.', '') }}">
                        {{ isset($stats['revenueToday']) ? number_format($stats['revenueToday'], 2) : '0.00' }}
                    </div>
                    <div class="stats-label">{{ __('Revenue Today') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-chart-line text-info"></i>
                        <small class="text-info">{{ $defaultCurrency->code ?? '' }}</small>
                    </div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Dashboard Content -->
<div class="row">
    <!-- Chart Section -->
    <div class="col-lg-8 mb-4">
        <div class="modern-card">
            <div class="card-header">
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div>
                        <h5 class="card-title mb-0">{{ __('User Registration Trends') }}</h5>
                        <small class="text-muted" id="chart-last-updated">
                            @if($period === '6m')
                            {{ __('Last 6 months overview') }}
                            @elseif($period === '1y')
                            {{ __('Last 12 months overview') }}
                            @else
                            {{ __('All time overview') }}
                            @endif
                        </small>
                    </div>
                    <div
                        class="chart-controls-wrapper d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2 @if(app()->getLocale()==='ar') ms-sm-auto flex-sm-row-reverse @else ms-sm-auto @endif">
                        <a href="{{ route('admin.dashboard', ['refresh' => '1']) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip"
                            title="{{ __('Refresh dashboard data') }}">
                            <i class="fas fa-sync-alt"></i>
                            <span class="d-none d-md-inline ms-1">{{ __('Refresh') }}</span>
                        </a>
                        <div class="btn-group btn-group-sm chart-period-buttons @if(app()->getLocale()==='ar') order-sm-first @endif"
                            role="group">
                            <a href="{{ route('admin.dashboard', ['period' => '6m']) }}"
                                class="btn btn-outline-secondary {{ $period === '6m' ? 'active' : '' }}">6M</a>
                            <a href="{{ route('admin.dashboard', ['period' => '1y']) }}"
                                class="btn btn-outline-secondary {{ $period === '1y' ? 'active' : '' }}">1Y</a>
                            <a href="{{ route('admin.dashboard', ['period' => 'all']) }}"
                                class="btn btn-outline-secondary {{ $period === 'all' ? 'active' : '' }}">{{ __('All') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body position-relative">
                <!-- Chart Container -->
                <div class="chart-container h-400 pos-relative">
                    <canvas id="userChart" aria-describedby="userChartFallback"></canvas>
                    <noscript>
                        <ul id="userChartFallback" class="chart-fallback list-unstyled small mt-2">
                            @foreach(($chartData['labels'] ?? []) as $i => $lbl)
                            <li>{{ $lbl }}: {{ $chartData['data'][$i] ?? 0 }}</li>
                            @endforeach
                            @if(empty($chartData['labels']))
                            <li>{{ __('No chart data available') }}</li>
                            @endif
                        </ul>
                    </noscript>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Active Users -->
    <div class="col-lg-4 mb-4">
        <div class="card modern-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">{{ __('Top Active Users') }}</h5>
                        <small class="text-muted">{{ __('Most active users this week') }}</small>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="collapse"
                        data-bs-target="#topUsersCollapseNew" aria-expanded="true">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="collapse show d-lg-block" id="topUsersCollapseNew">
                    @if(isset($topUsers) && count($topUsers) > 0)
                    <div class="user-list">
                        @foreach($topUsers as $user)
                        <div class="user-item">
                            <div class="user-avatar">
                                <div
                                    class="bg-primary text-white d-flex align-items-center justify-content-center h-100 rounded">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ $user->name }}</div>
                                <div class="user-activity">{{ __('Last login') }}:
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : __('Never') }}
                                </div>
                            </div>
                            <div class="user-badge">
                                <span class="badge badge-{{ $user->is_active ? 'success' : 'warning' }}">
                                    {{ $user->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="fas fa-users text-muted"></i>
                        <p class="text-muted mt-2">{{ __('No active users data available') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales & Orders Charts -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="modern-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-chart-area me-2"></i>{{ __('Sales & Revenue') }}</h5>
                <small class="text-muted">{{ __('Last 30 days') }}</small>
            </div>
            <div class="card-body h-380 pos-relative">
                <canvas id="salesChart" aria-describedby="salesChartFallback"></canvas>
                <noscript>
                    <ul id="salesChartFallback" class="chart-fallback list-unstyled small mt-2">
                        @php($labels = $salesChartData['labels'] ?? [])
                        @foreach($labels as $i => $lbl)
                        <li>{{ $lbl }} — {{ __('Orders') }}: {{ $salesChartData['orders'][$i] ?? 0 }},
                            {{ __('Revenue') }}: {{ $salesChartData['revenue'][$i] ?? 0 }}
                        </li>
                        @endforeach
                        @if(empty($labels))
                        <li>{{ __('No sales data available') }}</li>
                        @endif
                    </ul>
                </noscript>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="modern-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2"></i>{{ __('Order Status Distribution') }}
                </h5>
            </div>
            <div class="card-body h-380 pos-relative">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>


@endsection