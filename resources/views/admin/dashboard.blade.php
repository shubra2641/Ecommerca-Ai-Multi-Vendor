@extends('layouts.admin')

@section('title', __('Admin Dashboard'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Admin Dashboard') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Overview of your system statistics and quick actions') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <div class="dropdown d-inline-block">
                    <button class="admin-btn admin-btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-plus"></i>
                        {{ __('Quick Actions') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.users.create') }}">
                                <i class="fas fa-user-plus"></i> {{ __('Add New User') }}
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.currencies.create') }}">
                                <i class="fas fa-dollar-sign"></i> {{ __('Add Currency') }}
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.languages.create') }}">
                                <i class="fas fa-language"></i> {{ __('Add Language') }}
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Dashboard Data Bridge for Unified Charts -->
        <script id="dashboard-data" type="application/json">
            {
                "charts": {
                    "users": {
                        "labels": @json($chartData['labels'] ?? []),
                        "data": @json($chartData['data'] ?? [])
                    },
                    "sales": {
                        "labels": @json($salesChartData['labels'] ?? []),
                        "orders": @json($salesChartData['orders'] ?? []),
                        "revenue": @json($salesChartData['revenue'] ?? [])
                    },
                    "ordersStatus": {
                        "labels": @json($orderStatusChartData['labels'] ?? []),
                        "data": @json($orderStatusChartData['data'] ?? []),
                        "colors": @json($orderStatusChartData['colors'] ?? [])
                    }
                }
            }
        </script>

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
                    <div class="admin-stat-value" id="total-users" data-countup data-target="{{ (int)($stats['totalUsers'] ?? 0) }}">
                        {{ isset($stats['totalUsers']) ? number_format($stats['totalUsers']) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Users') }}</div>
                    <div class="admin-stat-description">{{ __('All registered users in the system') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>+12%</span>
                        <small>{{ __('this month') }}</small>
                    </div>
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
                    <div class="admin-stat-value" id="total-vendors" data-countup data-target="{{ (int)($stats['totalVendors'] ?? 0) }}">
                        {{ isset($stats['totalVendors']) ? number_format($stats['totalVendors']) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Vendors') }}</div>
                    <div class="admin-stat-description">{{ __('Active vendors selling products') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.users.index', ['role' => 'vendor']) }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
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
                    <div class="admin-stat-value" id="pending-users" data-countup data-target="{{ (int)($stats['pendingUsers'] ?? 0) }}">
                        {{ isset($stats['pendingUsers']) ? number_format($stats['pendingUsers']) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Pending Approvals') }}</div>
                    <div class="admin-stat-description">{{ __('Users waiting for approval') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.users.pending') }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-warning">
                        <i class="fas fa-clock"></i>
                        <span>{{ __('Needs attention') }}</span>
                    </div>
                </div>
            </div>

            <!-- Active Today -->
            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="active-today" data-countup data-target="{{ (int)($stats['activeToday'] ?? 0) }}">
                        {{ isset($stats['activeToday']) ? number_format($stats['activeToday']) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Active Today') }}</div>
                    <div class="admin-stat-description">{{ __('Users active today') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.users.index', ['filter' => 'active_today']) }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-info">
                        <i class="fas fa-chart-line"></i>
                        <span>{{ __('Real-time') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Statistics Row -->
        <div class="admin-stats-grid">
            <!-- Total Balance -->
            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="total-balance" data-countup data-decimals="2" data-target="{{ number_format($stats['totalBalance'] ?? 0, 2, '.', '') }}">
                        {{ isset($stats['totalBalance']) ? number_format($stats['totalBalance'], 2) : '0.00' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Balance') }}</div>
                    <div class="admin-stat-description">{{ __('Total balance in the system') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="#" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-success">
                        <i class="fas fa-dollar-sign"></i>
                        <span>{{ $defaultCurrency ? $defaultCurrency->code : '' }}</span>
                    </div>
                </div>
            </div>

            <!-- New Users Today -->
            <div class="admin-stat-card admin-stat-danger">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="new-users-today" data-countup data-target="{{ (int)($stats['newUsersToday'] ?? 0) }}">
                        {{ isset($stats['newUsersToday']) ? number_format($stats['newUsersToday']) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('New Users Today') }}</div>
                    <div class="admin-stat-description">{{ __('Users registered today') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.users.index', ['filter' => 'today']) }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-danger">
                        <i class="fas fa-star"></i>
                        <span>{{ __('Today') }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Admins -->
            <div class="admin-stat-card admin-stat-danger">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="total-admins" data-countup data-target="{{ (int)($stats['totalAdmins'] ?? 0) }}">
                        {{ isset($stats['totalAdmins']) ? number_format($stats['totalAdmins']) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Admins') }}</div>
                    <div class="admin-stat-description">{{ __('Total administrators in the system') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-danger">
                        <i class="fas fa-star"></i>
                        <span>{{ __('Admins') }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="total-customers" data-countup data-target="{{ (int)($stats['totalCustomers'] ?? 0) }}">
                        {{ isset($stats['totalCustomers']) ? number_format($stats['totalCustomers']) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Customers') }}</div>
                    <div class="admin-stat-description">{{ __('Total customers in the system') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.users.index', ['role' => 'customer']) }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-info">
                        <i class="fas fa-star"></i>
                        <span>{{ __('Customers') }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Third Statistics Row -->
        <div class="admin-stats-grid">
            <!-- New Users This Week -->
            <div class="admin-stat-card admin-stat-danger">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="new-users-week" data-countup data-target="{{ (int)($stats['newUsersThisWeek'] ?? 0) }}">
                        {{ isset($stats['newUsersThisWeek']) ? number_format($stats['newUsersThisWeek']) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('New Users This Week') }}</div>
                    <div class="admin-stat-description">{{ __('Users registered this week') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.users.index', ['filter' => 'this_week']) }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-danger">
                        <i class="fas fa-star"></i>
                        <span>{{ __('This Week') }}</span>
                    </div>
                </div>
            </div>

            <!-- New Users This Month -->
            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="new-users-month" data-countup data-target="{{ (int)($stats['newUsersThisMonth'] ?? 0) }}">
                        {{ isset($stats['newUsersThisMonth']) ? number_format($stats['newUsersThisMonth']) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('New Users This Month') }}</div>
                    <div class="admin-stat-description">{{ __('Users registered this month') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.users.index', ['filter' => 'this_month']) }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-success">
                        <i class="fas fa-star"></i>
                        <span>{{ __('This Month') }}</span>
                    </div>
                </div>
            </div>

            <!-- Approved Users -->
            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="approved-users" data-countup data-target="{{ (int)($stats['approvedUsers'] ?? 0) }}">
                        {{ isset($stats['approvedUsers']) ? number_format($stats['approvedUsers']) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Approved Users') }}</div>
                    <div class="admin-stat-description">{{ __('Total approved users') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.users.index', ['status' => 'approved']) }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-info">
                        <i class="fas fa-star"></i>
                        <span>{{ isset($topStats['approval_rate']) ? $topStats['approval_rate'] . '%' : '0%' }}</span>
                        <small>{{ __('approval rate') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fourth Statistics Row -->
        <div class="admin-stats-grid">
            <!-- Total Orders -->
            <div class="admin-stat-card admin-stat-primary">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="total-orders" data-countup data-target="{{ (int)($stats['totalOrders'] ?? 0) }}">
                        {{ isset($stats['totalOrders']) ? number_format($stats['totalOrders']) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Orders') }}</div>
                    <div class="admin-stat-description">{{ __('All time orders') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.orders.index') }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-primary">
                        <i class="fas fa-star"></i>
                        <span>{{ __('All time') }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="total-revenue" data-countup data-decimals="2" data-target="{{ number_format($stats['revenueTotal'] ?? 0, 2, '.', '') }}">
                        {{ isset($stats['revenueTotal']) ? number_format($stats['revenueTotal'], 2) : '0.00' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Revenue') }}</div>
                    <div class="admin-stat-description">{{ __('All time revenue') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.orders.index') }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-success">
                        <i class="fas fa-star"></i>
                        <span>{{ $defaultCurrency->code ?? '' }}</span>
                    </div>
                </div>
            </div>

            <!-- Orders Today -->
            <div class="admin-stat-card admin-stat-warning">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="orders-today" data-countup data-target="{{ (int)($stats['ordersToday'] ?? 0) }}">
                        {{ isset($stats['ordersToday']) ? number_format($stats['ordersToday']) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Orders Today') }}</div>
                    <div class="admin-stat-description">{{ __('Orders placed today') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.orders.index', ['filter' => 'today']) }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-warning">
                        <i class="fas fa-star"></i>
                        <span>{{ __('Today') }}</span>
                    </div>
                </div>
            </div>

            <!-- Revenue Today -->
            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="revenue-today" data-countup data-decimals="2" data-target="{{ number_format($stats['revenueToday'] ?? 0, 2, '.', '') }}">
                        {{ isset($stats['revenueToday']) ? number_format($stats['revenueToday'], 2) : '0.00' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Revenue Today') }}</div>
                    <div class="admin-stat-description">{{ __('Revenue generated today') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.orders.index', ['filter' => 'today']) }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <i class="fas fa-arrow-up"></i>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-info">
                        <i class="fas fa-star"></i>
                        <span>{{ $defaultCurrency->code ?? '' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Content -->
        <div class="row">
            <!-- Chart Section -->
            <div class="col-lg-8 mb-4">
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                            <div>
                                <h3 class="admin-card-title">
                                    <i class="fas fa-chart-line"></i>
                                    {{ __('User Registration Trends') }}
                                </h3>
                                <p class="admin-card-subtitle" id="chart-last-updated">
                                    @if($period === '6m')
                                    {{ __('Last 6 months overview') }}
                                    @elseif($period === '1y')
                                    {{ __('Last 12 months overview') }}
                                    @else
                                    {{ __('All time overview') }}
                                    @endif
                                </p>
                            </div>
                            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.dashboard', ['period' => '6m']) }}"
                                        class="admin-btn admin-btn-outline {{ $period === '6m' ? 'active' : '' }}">6M</a>
                                    <a href="{{ route('admin.dashboard', ['period' => '1y']) }}"
                                        class="admin-btn admin-btn-outline {{ $period === '1y' ? 'active' : '' }}">1Y</a>
                                    <a href="{{ route('admin.dashboard', ['period' => 'all']) }}"
                                        class="admin-btn admin-btn-outline {{ $period === 'all' ? 'active' : '' }}">{{ __('All') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="admin-card-body position-relative">
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
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="admin-card-title">
                                    <i class="fas fa-users"></i>
                                    {{ __('Top Active Users') }}
                                </h3>
                                <p class="admin-card-subtitle">{{ __('Most active users this week') }}</p>
                            </div>
                            <button class="admin-btn admin-btn-outline d-lg-none" type="button" data-bs-toggle="collapse"
                                data-bs-target="#topUsersCollapseNew" aria-expanded="true">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="admin-card-body">
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
                                        <div class="user-activity">{{ __('Last updated') }}:
                                            {{ $user->updated_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="user-badge">
                                        <span class="admin-badge admin-badge-success">
                                            {{ __('Active') }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="admin-empty-state">
                                <div class="admin-notification-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <p class="admin-text-muted">{{ __('No active users data available') }}</p>
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
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="admin-card-title">
                                    <i class="fas fa-chart-line"></i>
                                    {{ __('Sales & Revenue') }}
                                </h3>
                                <p class="admin-card-subtitle">{{ __('Last 30 days') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="admin-card-body h-380 pos-relative">
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
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="admin-card-title">
                                    <i class="fas fa-chart-pie"></i>
                                    {{ __('Order Status Distribution') }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="admin-card-body h-380 pos-relative">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


        @endsection