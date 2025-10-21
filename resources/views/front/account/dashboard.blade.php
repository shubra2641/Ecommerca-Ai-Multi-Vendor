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
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="title-icon">
                                    <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                    <path d="M8 5a2 2 0 012-2h4a2 2 0 012 2v4H8V5z" />
                                </svg>
                                {{ __('Dashboard') }}
                            </h1>
                            <p class="order-date-modern">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Welcome back, ') }}{{ auth()->user()->name ?? __('User') }}! {{ __('Here\'s your account overview') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="modern-card">
                    <div class="card-header-modern">
                        <h3 class="card-title-modern">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            {{ __('Account Statistics') }}
                        </h3>
                    </div>
                    <div class="dashboard-overview">
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-blue">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
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
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M5 13l4 4L19 7" />
                                    </svg>
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
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
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
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="1" y="4" width="22" height="16" rx="2" />
                                        <path d="M1 10h22" />
                                    </svg>
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
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                                    </svg>
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
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="dash-stats">
                                    <div class="big">{{ number_format($stats['payments_completed'],2) }}</div>
                                    <div class="small muted">{{ __('Spent') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-violet">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
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
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    {{ __('Recent Orders') }}
                                </h3>
                                <a href="{{ route('user.orders') }}" class="info-value method-badge">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 5l7 7-7 7" />
                                    </svg>
                                    {{ __('View All') }}
                                </a>
                            </div>
                            @if(!$recentOrders->count())
                            <div class="empty-state">
                                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="empty-icon">
                                    <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <p class="empty-text">{{ __('No orders yet.') }}</p>
                            </div>
                            @else
                            <div class="items-list-modern">
                                @foreach($recentOrders as $o)
                                <div class="item-card-modern">
                                    <div class="item-img-wrapper">
                                        <div class="item-img-placeholder gradient-blue">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="item-info-modern">
                                        <h4 class="item-name-modern">{{ __('Order') }} #{{ $o->id }}</h4>
                                        <p class="item-variant-modern">
                                            {{ $o->created_at->format('M j, Y') }} · {{ $o->items->count() }} {{ __('items') }}
                                        </p>
                                        <div class="item-meta-modern">
                                            <span class="meta-item">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                                </svg>
                                                {{ number_format($o->total,2) }} {{ $o->currency }}
                                            </span>
                                            <span class="payment-badge-modern status-{{ $o->status }}">
                                                {{ ucfirst($o->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="item-total-modern">
                                        <a href="{{ route('user.orders.show',$o) }}" class="badge-count">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
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
                                                {{ number_format($p->amount,2) }} {{ $p->currency }}
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