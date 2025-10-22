@extends('layouts.admin')

@section('title', __('Return Requests'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <i class="fas fa-undo"></i>
                    {{ __('Return Requests') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Manage product return requests and refunds') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.orders.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-list"></i>
                    {{ __('View Orders') }}
                </a>
            </div>
        </div>

        <!-- Returns List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <i class="fas fa-shopping-bag"></i>
                <h3 class="admin-card-title">{{ __('All Return Requests') }}</h3>
                <span class="admin-badge-count">{{ $items->total() }}</span>
            </div>
            <div class="admin-card-body">
                @if($items->count())
                <div class="admin-items-list">
                    @foreach($items as $item)
                    <div class="admin-item-card">
                        <div class="admin-item-main">
                            <div class="admin-item-placeholder admin-item-placeholder-warning">
                                {{ strtoupper(substr($item->name, 0, 2)) }}
                            </div>
                            <div class="admin-item-details">
                                <div class="admin-item-name">{{ $item->name }}</div>
                                <div class="admin-payment-details admin-mt-half">
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                        </svg>
                                        {{ __('Return') }} #{{ $item->id }}
                                    </span>
                                    <span class="payment-detail-separator">•</span>
                                    <a href="{{ route('admin.orders.show', $item->order) }}" class="payment-order-link">
                                        {{ __('Order') }} #{{ $item->order_id }}
                                    </a>
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $item->order->user?->name ?? __('User') . ' #' . $item->order->user_id }}
                                    </span>
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M12 6v6l4 2" />
                                        </svg>
                                        {{ $item->updated_at->format('M d, Y H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="admin-item-meta">
                            <span class="admin-status-badge status-{{ strtolower(str_replace(' ', '-', $item->return_status)) }}">
                                {{ $item->return_status }}
                            </span>
                            <a href="{{ route('admin.returns.show', $item) }}" class="admin-btn-small admin-btn-primary admin-mt-half">
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
                        <path d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                    </svg>
                    <p>{{ __('No return requests found') }}</p>
                </div>
                @endif
            </div>
            @if($items->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ __('Showing') }} {{ $items->firstItem() }} - {{ $items->lastItem() }} {{ __('of') }} {{ $items->total() }}
                </div>
                <div class="pagination-links">
                    {{ $items->links() }}
                </div>
            </div>
            @endif
        </div>

    </div>
</section>
@endsection