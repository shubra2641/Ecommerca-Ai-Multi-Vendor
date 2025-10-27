@extends('layouts.admin')

@section('title', __('Payments'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <i class="fas fa-credit-card"></i>
                    {{ __('Payments') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('View and manage all payment transactions') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.orders.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-list"></i>
                    {{ __('View Orders') }}
                </a>
            </div>
        </div>

        <!-- Payments List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <i class="fas fa-credit-card"></i>
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
                                    <i class="fas fa-receipt"></i>
                                    <span class="payment-id-text">{{ __('Payment') }} #{{ $p->id }}</span>
                                </div>
                                <div class="admin-payment-method">{{ $p->method }}</div>
                                <div class="admin-payment-details">
                                    <span class="payment-detail-item">
                                        <i class="fas fa-user"></i>
                                        {{ $p->user->name ?? ($p->user->email ?? __('Guest')) }}
                                    </span>
                                    <span class="payment-detail-separator">•</span>
                                    <a href="{{ route('admin.orders.show', $p->order_id) }}" class="payment-order-link">
                                        {{ __('Order') }} #{{ $p->order_id }}
                                    </a>
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item">
                                        <i class="fas fa-clock"></i>
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
                    <i class="fas fa-file-invoice icon-xlarge"></i>
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