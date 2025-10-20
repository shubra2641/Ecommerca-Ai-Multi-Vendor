@extends('layouts.admin')

@section('title', __('Notifications'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    {{ __('Notifications') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Manage and send system notifications') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.notifications.send.create') }}" class="admin-btn admin-btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Send notification') }}
                </a>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <h3 class="admin-card-title">{{ __('All Notifications') }}</h3>
                <span class="admin-badge-count">{{ $notifications->total() }}</span>
            </div>
            <div class="admin-card-body">
                @if($notifications->count())
                <div class="admin-items-list">
                    @foreach($notifications as $n)
                    <div class="admin-item-card">
                        <div class="admin-item-main">
                            <div class="admin-item-placeholder {{ $n->read_at ? 'admin-item-placeholder-gray' : 'admin-item-placeholder-cyan' }}">
                                <svg width="32" height="32" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                    @if($n->read_at)
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    @else
                                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    @endif
                                </svg>
                            </div>
                            <div class="admin-item-details">
                                <div class="admin-item-name">
                                    {{ $n->data['title'] ?? ($n->data['type'] ?? __('Notification')) }}
                                </div>
                                @if($n->data['message'] ?? false)
                                <div class="admin-item-variant admin-mt-half">
                                    {{ $n->data['message'] }}
                                </div>
                                @endif
                                <div class="admin-payment-details admin-mt-half">
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M12 6v6l4 2" />
                                        </svg>
                                        {{ $n->created_at->diffForHumans() }}
                                    </span>
                                    @if($n->read_at)
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item admin-success-text">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('Read') }} {{ $n->read_at->diffForHumans() }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="admin-item-meta">
                            @if(!$n->read_at)
                            <span class="admin-status-badge status-pending admin-status-unread">
                                {{ __('Unread') }}
                            </span>
                            <form method="POST" action="{{ route('admin.notifications.read', $n->id) }}" class="admin-form admin-mt-half">
                                @csrf
                                <button class="admin-btn-small admin-btn-success">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ __('Mark read') }}
                                </button>
                            </form>
                            @else
                            <span class="admin-status-badge status-completed">
                                {{ __('Read') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="admin-empty-state">
                    <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="admin-notification-icon">
                        <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p>{{ __('No notifications') }}</p>
                </div>
                @endif
            </div>
            @if($notifications->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ __('Showing') }} {{ $notifications->firstItem() }} - {{ $notifications->lastItem() }} {{ __('of') }} {{ $notifications->total() }}
                </div>
                <div class="pagination-links">
                    {{ $notifications->links() }}
                </div>
            </div>
            @endif
        </div>

    </div>
</section>
@endsection