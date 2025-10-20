@extends('layouts.admin')

@section('title', __('Orders'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    {{ __('Orders') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('View and manage all customer orders') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.orders.payments') }}" class="admin-btn admin-btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="1" y="4" width="22" height="16" rx="2" />
                        <path d="M1 10h22" />
                    </svg>
                    {{ __('Payments') }}
                </a>
            </div>
        </div>

        <!-- Orders List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h3 class="admin-card-title">{{ __('All Orders') }}</h3>
                <span class="admin-badge-count">{{ $orders->total() }}</span>
            </div>
            <div class="admin-card-body">
                @if($orders->count())
                <div class="admin-items-list">
                    @foreach($orders as $order)
                    <div class="admin-item-card">
                        <div class="admin-item-main">
                            <div class="admin-item-placeholder admin-item-placeholder-primary">
                                #{{ $order->id }}
                            </div>
                            <div class="admin-item-details">
                                <div class="admin-item-name">
                                    @if(($ordersPrepared[$order->id]['firstItem'] ?? null))
                                    {{ $ordersPrepared[$order->id]['firstItem']->name }}
                                    @if($ordersPrepared[$order->id]['variantLabel'])
                                    <span class="admin-item-name-secondary"> - {{ $ordersPrepared[$order->id]['variantLabel'] }}</span>
                                    @endif
                                    @if($order->items->count()>1)
                                    <span class="admin-item-name-secondary"> + {{ $order->items->count()-1 }} {{ __('more') }}</span>
                                    @endif
                                    @else
                                    {{ __('Order') }} #{{ $order->id }}
                                    @endif
                                </div>
                                <div class="admin-payment-details admin-mt-half">
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $order->user->name ?? __('Guest') }}
                                        @if($order->user && $order->user->email)
                                        <span class="admin-item-name-secondary">({{ $order->user->email }})</span>
                                        @endif
                                    </span>
                                    @if($ordersPrepared[$order->id]['shipText'] ?? false)
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                        </svg>
                                        {{ $ordersPrepared[$order->id]['shipText'] }}
                                    </span>
                                    @endif
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M12 6v6l4 2" />
                                        </svg>
                                        {{ $order->created_at->format('M d, Y H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="admin-item-meta">
                            <div class="payment-amount-display">{{ number_format($order->total,2) }} <span class="admin-payment-amount-currency">{{ $order->currency }}</span></div>
                            <div class="admin-status-flex">
                                <span class="admin-status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                                <span class="admin-payment-status status-{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span>
                            </div>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="admin-btn-small admin-btn-primary admin-mt-half">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                {{ __('View') }}
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="admin-empty-state">
                    <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="admin-notification-icon">
                        <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <p>{{ __('No orders found') }}</p>
                </div>
                @endif
            </div>
            @if($orders->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ __('Showing') }} {{ $orders->firstItem() }} - {{ $orders->lastItem() }} {{ __('of') }} {{ $orders->total() }}
                </div>
                <div class="pagination-links">
                    {{ $orders->links() }}
                </div>
            </div>
            @endif
        </div>

    </div>
</section>
@endsection