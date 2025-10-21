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

        <!-- Second Statistics Row -->
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
            <div class="admin-stat-card admin-stat-danger">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M16 7A4 4 0 1 1 8 7A4 4 0 0 1 16 7Z" />
                            <path d="M12 14A7 7 0 0 0 5 21H19A7 7 0 0 0 12 14Z" />
                            <path d="M12 5V19M5 12H19" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-danger">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
                        <span>{{ __('Today') }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Admins -->
            <div class="admin-stat-card admin-stat-danger">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 22S8 18 8 12V5L12 3L16 5V12C16 18 12 22 12 22Z" />
                            <path d="M9 12L11 14L15 10" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-danger">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
                        <span>{{ __('Admins') }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M16 7A4 4 0 1 1 8 7A4 4 0 0 1 16 7Z" />
                            <path d="M12 14A7 7 0 0 0 5 21H19A7 7 0 0 0 12 14Z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-info">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
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
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-danger">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
                        <span>{{ __('This Week') }}</span>
                    </div>
                </div>
            </div>

            <!-- New Users This Month -->
            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-success">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
                        <span>{{ __('This Month') }}</span>
                    </div>
                </div>
            </div>

            <!-- Approved Users -->
            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-info">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
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
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M16 11V7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7V11M5 9H19L18 21H6L5 9Z" />
                            <path d="M12 15V15.01" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-primary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
                        <span>{{ __('All time') }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-success">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
                        <span>{{ $defaultCurrency->code ?? '' }}</span>
                    </div>
                </div>
            </div>

            <!-- Orders Today -->
            <div class="admin-stat-card admin-stat-warning">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-warning">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
                        <span>{{ __('Today') }}</span>
                    </div>
                </div>
            </div>

            <!-- Revenue Today -->
            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 3V21H21" />
                            <path d="M9 9L12 6L16 10L20 6" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-info">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
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
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 3V21H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M9 9L12 6L16 10L20 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
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
                                <a href="{{ route('admin.dashboard', ['refresh' => '1']) }}" class="admin-btn admin-btn-outline" data-bs-toggle="tooltip"
                                    title="{{ __('Refresh dashboard data') }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M23 4v6h-6M1 20v-6h6m15-4a9 9 0 11-18 0 9 9 0 0118 0zM1 10a9 9 0 0118 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span class="d-none d-md-inline ms-1">{{ __('Refresh') }}</span>
                                </a>
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
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 7A4 4 0 1 1 8 7A4 4 0 0 1 16 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M12 14A7 7 0 0 0 5 21H19A7 7 0 0 0 12 14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('Top Active Users') }}
                                </h3>
                                <p class="admin-card-subtitle">{{ __('Most active users this week') }}</p>
                            </div>
                            <button class="admin-btn admin-btn-outline d-lg-none" type="button" data-bs-toggle="collapse"
                                data-bs-target="#topUsersCollapseNew" aria-expanded="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
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
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 7A4 4 0 1 1 8 7A4 4 0 0 1 16 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M12 14A7 7 0 0 0 5 21H19A7 7 0 0 0 12 14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
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
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 3V21H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M9 9L12 6L16 10L20 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
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
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21.21 15.89A10 10 0 1 1 8 2.83" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M22 12A10 10 0 0 0 12 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
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