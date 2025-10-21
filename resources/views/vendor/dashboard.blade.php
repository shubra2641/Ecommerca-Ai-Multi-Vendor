@extends('vendor.layout')

@section('title', __('Dashboard'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Vendor Dashboard') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Welcome back! Here\'s what\'s happening with your store.') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('vendor.products.create') }}" class="admin-btn admin-btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 5V19M5 12H19" />
                    </svg>
                    {{ __('Add Product') }}
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="admin-stats-grid">
            <!-- Total Sales -->
            <div class="admin-stat-card admin-stat-danger">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="total-sales" data-countup data-decimals="2" data-target="{{ number_format($totalSales ?? 0, 2, '.', '') }}">
                        {{ isset($totalSales) ? number_format($totalSales, 2) : '0.00' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Sales') }}</div>
                    <div class="admin-stat-description">{{ __('All time sales revenue') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('vendor.orders.index') }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-danger">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                        <span>{{ __('This month') }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="admin-stat-card admin-stat-secondary">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M16 11V7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7V11M5 9H19L18 21H6L5 9Z" />
                            <path d="M12 15V15.01" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="total-orders" data-countup data-target="{{ (int)($ordersCount ?? 0) }}">
                        {{ isset($ordersCount) ? number_format($ordersCount) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Orders') }}</div>
                    <div class="admin-stat-description">{{ __('All time orders received') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('vendor.orders.index') }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-secondary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                        <span>{{ __('All time') }}</span>
                    </div>
                </div>
            </div>

            <!-- Pending Withdrawals -->
            <div class="admin-stat-card admin-stat-primary">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8V12L16 14" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="pending-withdrawals" data-countup data-decimals="2" data-target="{{ number_format($pendingWithdrawals ?? 0, 2, '.', '') }}">
                        {{ isset($pendingWithdrawals) ? number_format($pendingWithdrawals, 2) : '0.00' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Pending Withdrawals') }}</div>
                    <div class="admin-stat-description">{{ __('Awaiting approval') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('vendor.withdrawals.index') }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-warning">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8V12L16 14" />
                        </svg>
                        <span>{{ __('Awaiting approval') }}</span>
                    </div>
                </div>
            </div>

            <!-- Current Balance -->
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
                    <div class="admin-stat-value" id="current-balance" data-countup data-decimals="2" data-target="{{ number_format(auth()->user()->balance ?? 0, 2, '.', '') }}">
                        {{ number_format(auth()->user()->balance ?? 0, 2) }}
                    </div>
                    <div class="admin-stat-label">{{ __('Current Balance') }}</div>
                    <div class="admin-stat-description">{{ __('Available for withdrawal') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('vendor.withdrawals.index') }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-success">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" />
                        </svg>
                        <span>{{ __('Available') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Statistics Row -->
        <div class="admin-stats-grid">
            <!-- Total Products -->
            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 7L12 3L4 7M20 7L12 11M20 7V17L12 21L4 17V7L12 3" />
                            <path d="M12 11V21" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" id="total-products" data-countup data-target="{{ (int)($productsCount ?? 0) }}">
                        {{ isset($productsCount) ? number_format($productsCount) : '0' }}
                    </div>
                    <div class="admin-stat-label">{{ __('Total Products') }}</div>
                    <div class="admin-stat-description">{{ __('Products in your catalog') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <a href="{{ route('vendor.products.index') }}" class="admin-btn admin-btn-secondary">
                        {{ __('View Details') }}
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </a>
                    <div class="admin-stat-trend admin-stat-trend-info">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
                        <span>{{ __('In catalog') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="admin-card-title">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('Quick Actions') }}
                                </h3>
                                <p class="admin-card-subtitle">{{ __('Manage your store efficiently') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="admin-card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('vendor.products.index') }}" class="admin-btn admin-btn-outline w-100 d-flex align-items-center justify-content-center">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-2">
                                        <path d="M20 7L12 3L4 7M20 7L12 11M20 7V17L12 21L4 17V7L12 3" />
                                        <path d="M12 11V21" />
                                    </svg>
                                    {{ __('Manage Products') }}
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('vendor.orders.index') }}" class="admin-btn admin-btn-outline w-100 d-flex align-items-center justify-content-center">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-2">
                                        <path d="M16 11V7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7V11M5 9H19L18 21H6L5 9Z" />
                                        <path d="M12 15V15.01" />
                                    </svg>
                                    {{ __('View Orders') }}
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('vendor.withdrawals.index') }}" class="admin-btn admin-btn-outline w-100 d-flex align-items-center justify-content-center">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-2">
                                        <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" />
                                    </svg>
                                    {{ __('Withdrawals') }}
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="#" class="admin-btn admin-btn-outline w-100 d-flex align-items-center justify-content-center">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-2">
                                        <path d="M12 22S8 18 8 12V5L12 3L16 5V12C16 18 12 22 12 22Z" />
                                        <path d="M9 12L11 14L15 10" />
                                    </svg>
                                    {{ __('Store Settings') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Section -->
        <div class="row">
            <div class="col-12">
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="admin-card-title">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 11V7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7V11M5 9H19L18 21H6L5 9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M12 15V15.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('Recent Orders') }}
                                </h3>
                                <p class="admin-card-subtitle">{{ __('Latest customer orders') }}</p>
                            </div>
                            <a href="{{ route('vendor.orders.index') }}" class="admin-btn admin-btn-outline">
                                {{ __('View All') }}
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M7 14l3-3 3 3 5-5" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="admin-card-body">
                        @if(isset($recentOrders) && $recentOrders->count() > 0)
                        <div class="admin-table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Order ID') }}</th>
                                        <th>{{ __('Customer') }}</th>
                                        <th>{{ __('Total') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders->take(5) as $order)
                                    <tr>
                                        <td>
                                            <span class="admin-badge admin-badge-outline">#{{ $order->id }}</span>
                                        </td>
                                        <td>{{ $order->user->name ?? __('Guest') }}</td>
                                        <td>{{ number_format($order->total, 2) }}</td>
                                        <td>
                                            <span class="admin-badge admin-badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->diffForHumans() }}</td>
                                        <td>
                                            <a href="{{ route('vendor.orders.show', $order->id) }}" class="admin-btn admin-btn-sm admin-btn-outline">
                                                {{ __('View') }}
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="admin-empty-state">
                            <div class="admin-notification-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16 11V7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7V11M5 9H19L18 21H6L5 9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M12 15V15.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <h4>{{ __('No recent orders') }}</h4>
                            <p class="admin-text-muted">{{ __('Your recent orders will appear here') }}</p>
                            <a href="{{ route('vendor.products.create') }}" class="admin-btn admin-btn-primary">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 5V19M5 12H19" />
                                </svg>
                                {{ __('Add Your First Product') }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection