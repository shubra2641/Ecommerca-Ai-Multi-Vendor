@extends('layouts.admin')

@section('title', __('Payments'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="1" y="4" width="22" height="16" rx="2" />
                        <path d="M1 10h22" />
                    </svg>
                    {{ __('Payments') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('View and manage all payment transactions') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.orders.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    {{ __('View Orders') }}
                </a>
            </div>
        </div>

        <!-- Payments List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <h3 class="admin-card-title">{{ __('All Payments') }}</h3>
                <span class="admin-badge-count">{{ $payments->total() }}</span>
            </div>
            <div class="admin-card-body">
                @if($payments->count())
                <div class="admin-items-list">
                    @foreach($payments as $p)
                    <div class="admin-payment-item">
                        <div class="admin-payment-header">
                            <div class="admin-payment-left">
                                <div class="payment-id-label">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                    <span class="payment-id-text">{{ __('Payment') }} #{{ $p->id }}</span>
                                </div>
                                <div class="admin-payment-method">{{ $p->method }}</div>
                                <div class="admin-payment-details">
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $p->user->name ?? ($p->user->email ?? __('Guest')) }}
                                    </span>
                                    <span class="payment-detail-separator">•</span>
                                    <a href="{{ route('admin.orders.show', $p->order_id) }}" class="payment-order-link">
                                        {{ __('Order') }} #{{ $p->order_id }}
                                    </a>
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M12 6v6l4 2" />
                                        </svg>
                                        {{ $p->created_at->format('M d, Y H:i') }}
                                    </span>
                                </div>
                            </div>
                            <div class="admin-payment-right">
                                <div class="payment-amount-display">{{ number_format($p->amount, 2) }} {{ $p->currency ?? '' }}</div>
                                <span class="admin-payment-status status-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="admin-empty-state">
                    <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="admin-notification-icon">
                        <rect x="1" y="4" width="22" height="16" rx="2" />
                        <path d="M1 10h22" />
                    </svg>
                    <p>{{ __('No payments found') }}</p>
                </div>
                @endif
            </div>
            @if($payments->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ __('Showing') }} {{ $payments->firstItem() }} - {{ $payments->lastItem() }} {{ __('of') }} {{ $payments->total() }}
                </div>
                <div class="pagination-links">
                    {{ $payments->links() }}
                </div>
            </div>
            @endif
        </div>

    </div>
</section>
@endsection