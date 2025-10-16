@extends('vendor.layout')

@section('title', __('Dashboard'))

@section('content')
<div class="vendor-dashboard">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <h1 class="page-title">{{ __('Vendor Dashboard') }}</h1>
            <p class="page-subtitle">{{ __('Welcome back! Here\'s what\'s happening with your store.') }}</p>
        </div>
        <div class="header-actions">
            <a class="btn btn-primary" href="{{ route('vendor.products.create') }}">
                <i class="fas fa-plus"></i>
                {{ __('Add Product') }}
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
    <div class="stats-card stats-card-danger">
            <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stats-card-content">
                    <h3 class="stats-label">{{ __('Total Sales') }}</h3>
                    <p class="stats-number" data-countup data-prefix="$" data-decimals="2" data-target="{{ number_format($totalSales ?? 0, 2, '.', '') }}">{{ number_format($totalSales ?? 0, 2) }}</p>
                    <span class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        {{ __('This month') }}
                    </span>
                </div>
            </div>
        </div>

    <div class="card modern-card stats-card stats-card-secondary">
            <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stats-card-content">
                    <h3 class="stats-label">{{ __('Total Orders') }}</h3>
                    <p class="stats-number" data-countup data-target="{{ (int)($ordersCount ?? 0) }}">{{ number_format($ordersCount ?? 0) }}</p>
                    <span class="stat-change">
                        <i class="fas fa-chart-line"></i>
                        {{ __('All time') }}
                    </span>
                </div>
            </div>
        </div>

    <div class="stats-card stats-card-primary">
            <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stats-card-content">
                    <h3 class="stats-label">{{ __('Pending Withdrawals') }}</h3>
                    <p class="stats-number" data-countup data-prefix="$" data-decimals="2" data-target="{{ number_format($pendingWithdrawals ?? 0, 2, '.', '') }}">{{ number_format($pendingWithdrawals ?? 0, 2) }}</p>
                    <span class="stat-change pending">
                        <i class="fas fa-clock"></i>
                        {{ __('Awaiting approval') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card">
                <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="stats-card-content">
                    <h3 class="stats-label">{{ __('Current Balance') }}</h3>
                    <p class="stats-number" data-countup data-prefix="$" data-decimals="2" data-target="{{ number_format(auth()->user()->balance ?? 0, 2, '.', '') }}">{{ number_format(auth()->user()->balance ?? 0, 2) }}</p>
                    <span class="stat-change available">
                        <i class="fas fa-wallet"></i>
                        {{ __('Available for withdrawal') }}
                    </span>
                </div>
            </div>
        </div>

    <div class="card modern-card stats-card stats-card-neutral">
            <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stats-card-content">
                    <h3 class="stats-label">{{ __('Total Products') }}</h3>
                    <p class="stats-number" data-countup data-target="{{ (int)($productsCount ?? 0) }}">{{ number_format($productsCount ?? 0) }}</p>
                    <span class="stat-change">
                        <i class="fas fa-cubes"></i>
                        {{ __('In catalog') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2 class="section-title">{{ __('Quick Actions') }}</h2>
        <div class="actions-grid">
            <a href="{{ route('vendor.products.index') }}" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="action-content">
                    <h3>{{ __('Manage Products') }}</h3>
                    <p>{{ __('Add, edit, or remove products from your catalog') }}</p>
                </div>
                <i class="fas fa-arrow-right action-arrow"></i>
            </a>

            <a href="{{ route('vendor.orders.index') }}" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="action-content">
                    <h3>{{ __('View Orders') }}</h3>
                    <p>{{ __('Track and manage your customer orders') }}</p>
                </div>
                <i class="fas fa-arrow-right action-arrow"></i>
            </a>

            <a href="{{ route('vendor.withdrawals.index') }}" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="action-content">
                    <h3>{{ __('Withdrawals') }}</h3>
                    <p>{{ __('Request withdrawals and view payment history') }}</p>
                </div>
                <i class="fas fa-arrow-right action-arrow"></i>
            </a>

            <a href="#" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-user-cog"></i>
                </div>
                <div class="action-content">
                    <h3>{{ __('Store Settings') }}</h3>
                    <p>{{ __('Update your store information and preferences') }}</p>
                </div>
                <i class="fas fa-arrow-right action-arrow"></i>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="recent-activity">
        <h2 class="section-title">{{ __('Recent Activity') }}</h2>
    <div class="card modern-card activity-card">
            @if(isset($recentOrders) && $recentOrders->count() > 0)
                <div class="activity-list">
                    @foreach($recentOrders->take(5) as $order)
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="activity-content">
                                <p class="activity-title">{{ __('New order') }} #{{ $order->id }}</p>
                                <p class="activity-meta">${{ number_format($order->total, 2) }} • {{ $order->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="activity-status">
                                <span class="badge badge-{{ $order->status === 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-chart-line"></i>
                    <h3>{{ __('No recent activity') }}</h3>
                    <p>{{ __('Your recent orders and activities will appear here') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
