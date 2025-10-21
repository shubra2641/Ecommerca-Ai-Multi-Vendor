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
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" />
                            <path d="M9 22V12H15V22" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Admin Dashboard') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Overview of your system statistics and quick actions') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <button type="button" class="admin-btn admin-btn-secondary" id="refreshDashboardBtn"
                    data-bs-toggle="tooltip" title="{{ __('Refresh dashboard data') }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M23 4v6h-6M1 20v-6h6m15-4a9 9 0 11-18 0 9 9 0 0118 0zM1 10a9 9 0 0118 0" />
                    </svg>
                    {{ __('Refresh') }}
                </button>
                <div class="dropdown d-inline-block">
                    <button class="admin-btn admin-btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 5V19M5 12H19" />
                        </svg>
                        {{ __('Quick Actions') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.users.create') }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M16 7A4 4 0 1 1 8 7A4 4 0 0 1 16 7Z" />
                                    <path d="M12 14A7 7 0 0 0 5 21H19A7 7 0 0 0 12 14Z" />
                                    <path d="M12 5V19M5 12H19" />
                                </svg> {{ __('Add New User') }}
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.currencies.create') }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 6V12L16 14" />
                                </svg> {{ __('Add Currency') }}
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.languages.create') }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12.87 15.07L10.33 12.53L7.5 15.35L4.67 12.53L2.13 15.07" />
                                    <path d="M12.87 8.93L10.33 11.47L7.5 8.65L4.67 11.47L2.13 8.93" />
                                </svg> {{ __('Add Language') }}
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
                    <div class="admin-stat-value" id="total-users" data-countup data-target="{{ (int)($stats['totalUsers'] ?? 0) }}">
                        {{ isset($stats['totalUsers']) ? number_format($stats['totalUsers']) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Users') }}</div>
                    <div class="admin-stat-description">{{ __('All registered users in the system') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
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
                    <div class="admin-stat-value" id="total-vendors" data-countup data-target="{{ (int)($stats['totalVendors'] ?? 0) }}">
                        {{ isset($stats['totalVendors']) ? number_format($stats['totalVendors']) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Vendors') }}</div>
                    <div class="admin-stat-description">{{ __('Active vendors selling products') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('admin.users.index', ['role' => 'vendor']) }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
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
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 6V12L16 14" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8V12L16 14" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-warning">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 6V12L16 14" />
                        </svg>
                        <span>{{ __('Needs attention') }}</span>
                    </div>
                </div>
            </div>

            <!-- Active Today -->
            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 3V21H21" />
                            <path d="M9 9L12 6L16 10L20 6" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-info">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 3V21H21" />
                            <path d="M9 9L12 6L16 10L20 6" />
                        </svg>
                        <span>{{ __('Real-time') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Statistics Row -->
        <div class="admin-stats-grid">
            <!-- Total Balance -->
            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" />
                            <path d="M12 6V12L16 14" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-success">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" />
                        </svg>
                        <span>{{ $defaultCurrency ? $defaultCurrency->code : '' }}</span>
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
                                        <div class="user-activity">{{ __('Last updated') }}:
                                            {{ $user->updated_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="user-badge">
                                        <span class="badge badge-success">
                                            {{ __('Active') }}
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