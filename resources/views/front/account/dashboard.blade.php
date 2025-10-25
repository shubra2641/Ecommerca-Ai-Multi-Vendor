@extends('front.layout')
@section('title', __('My Account').' - '.config('app.name'))
@section('content')

<section class="account-section">
    <div class="container account-grid">
        @include('front.account._sidebar')
        <main class="account-main">
            <div class="dashboard-page">

                <!-- Header -->
                <div class="order-title-card">
                    <div class="title-row">
                        <div class="title-content">
                            <h1 class="modern-order-title">
                                <i class="fas fa-tachometer-alt title-icon"></i>
                                {{ __('Dashboard') }}
                            </h1>
                            <p class="order-date-modern">
                                <i class="fas fa-check-circle"></i>
                                {{ __('Welcome back, ') }}{{ auth()->user()->name ?? __('User') }}! {{ __('Here\'s your account overview') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="modern-card">
                    <div class="card-header-modern">
                        <h3 class="card-title-modern">
                            <i class="fas fa-chart-bar"></i>
                            {{ __('Account Statistics') }}
                        </h3>
                    </div>
                    <div class="dashboard-overview">
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-blue">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <div class="dash-stats">
                                    <div class="big">{{ $stats['orders_total'] }}</div>
                                    <div class="small muted">{{ __('Orders') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-green">
                                    <i class="fas fa-check" aria-hidden="true"></i>
                                </div>
                                <div class="dash-stats">
                                    <div class="big">{{ $stats['orders_completed'] }}</div>
                                    <div class="small muted">{{ __('Completed') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-yellow">
                                    <i class="fas fa-clock" aria-hidden="true"></i>
                                </div>
                                <div class="dash-stats">
                                    <div class="big">{{ $stats['orders_pending'] }}</div>
                                    <div class="small muted">{{ __('In Progress') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-blue">
                                    <i class="far fa-credit-card" aria-hidden="true"></i>
                                </div>
                                <div class="dash-stats">
                                    <div class="big">{{ number_format($stats['payments_total']) }}</div>
                                    <div class="small muted">{{ __('Payments') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-violet">
                                    <i class="fas fa-wallet" aria-hidden="true"></i>
                                </div>
                                <div class="dash-stats">
                                    <div class="big">{{ auth()->user()->formatted_balance ?? auth()->user()->formatted_balance }}</div>
                                    <div class="small muted">{{ __('Balance') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-red">
                                    <i class="fas fa-wallet" aria-hidden="true"></i>
                                </div>
                                <div class="dash-stats">
                                    <div class="big">{{ number_format($stats['payments_completed'],2) }}</div>
                                    <div class="small muted">{{ __('Spent') }} ({{ $currencySymbol }})</div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-violet">
                                    <i class="fas fa-user-circle" aria-hidden="true"></i>
                                </div>
                                <div class="dash-stats">
                                    <div class="big">{{ $stats['profile_completion'] }}%</div>
                                    <div class="small muted">{{ __('Profile') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="order-grid-modern">
                    <!-- Recent Orders -->
                    <div class="order-main-col">
                        <div class="modern-card">
                            <div class="card-header-modern">
                                <h3 class="card-title-modern">
                                    <i class="fas fa-shopping-bag" aria-hidden="true"></i>
                                    {{ __('Recent Orders') }}
                                </h3>
                                <a href="{{ route('user.orders') }}" class="info-value method-badge">
                                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                    {{ __('View All') }}
                                </a>
                            </div>
                            @if(!$recentOrders->count())
                            <div class="empty-state">
                                <i class="fas fa-box-open empty-icon" style="font-size:48px;" aria-hidden="true"></i>
                                <p class="empty-text">{{ __('No orders yet.') }}</p>
                            </div>
                            @else
                            <div class="items-list-modern">
                                @foreach($recentOrders as $o)
                                <div class="item-card-modern">
                                    <div class="item-img-wrapper">
                                        <div class="item-img-placeholder gradient-blue">
                                            <i class="fas fa-shopping-bag" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                    <div class="item-info-modern">
                                        <h4 class="item-name-modern">{{ __('Order') }} #{{ $o->id }}</h4>
                                        <p class="item-variant-modern">
                                            {{ $o->created_at->format('M j, Y') }} · {{ $o->items->count() }} {{ __('items') }}
                                        </p>
                                        <div class="item-meta-modern">
                                            <span class="meta-item">
                                                <i class="fas fa-dollar-sign" aria-hidden="true"></i>
                                                {{ number_format($o->display_total,2) }} {{ $currencySymbol }}
                                            </span>
                                            <span class="payment-badge-modern status-{{ $o->status }}">
                                                {{ ucfirst($o->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="item-total-modern">
                                        <a href="{{ route('user.orders.show',$o) }}" class="badge-count">
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                            {{ __('View') }}
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Payments -->
                    <div class="order-sidebar-col">
                        <div class="modern-card">
                            <div class="card-header-modern">
                                <h3 class="card-title-modern">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="1" y="4" width="22" height="16" rx="2" />
                                        <path d="M1 10h22" />
                                    </svg>
                                    {{ __('Recent Payments') }}
                                </h3>
                                <a href="{{ route('user.invoices') }}" class="info-value method-badge">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 5l7 7-7 7" />
                                    </svg>
                                    {{ __('View All') }}
                                </a>
                            </div>
                            @if(!$recentPayments->count())
                            <div class="empty-state">
                                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="empty-icon">
                                    <rect x="1" y="4" width="22" height="16" rx="2" />
                                    <path d="M1 10h22" />
                                </svg>
                                <p class="empty-text">{{ __('No payments yet.') }}</p>
                            </div>
                            @else
                            <div class="items-list-modern">
                                @foreach($recentPayments as $p)
                                <div class="item-card-modern">
                                    <div class="item-img-wrapper">
                                        <div class="item-img-placeholder gradient-green">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <rect x="1" y="4" width="22" height="16" rx="2" />
                                                <path d="M1 10h22" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="item-info-modern">
                                        <h4 class="item-name-modern">{{ __('Payment') }} #{{ $p->id }}</h4>
                                        <p class="item-variant-modern">{{ $p->created_at->format('M j, Y') }}</p>
                                        <div class="item-meta-modern">
                                            <span class="meta-item">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                                </svg>
                                                {{ number_format($p->display_amount,2) }} {{ $currencySymbol }}
                                            </span>
                                            <span class="payment-badge-modern status-{{ $p->status }}">
                                                {{ ucfirst($p->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

    </div>
    </main>
    </div>
</section>
@endsection