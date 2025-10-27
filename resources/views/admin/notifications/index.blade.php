@extends('layouts.admin')

@section('title', __('Notifications'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <i class="fas fa-bell"></i>
                    {{ __('Notifications') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Manage and send system notifications') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.notifications.send.create') }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('Send notification') }}
                </a>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <i class="fas fa-bell"></i>
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
                                @if($n->read_at)
                                <i class="fas fa-check icon-large"></i>
                                @else
                                <i class="fas fa-bell"></i>
                                @endif
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
                                        <i class="fas fa-clock"></i>
                                        {{ $n->created_at->diffForHumans() }}
                                    </span>
                                    @if($n->read_at)
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item admin-success-text">
                                        <i class="fas fa-check-circle"></i>
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
                                    <i class="fas fa-check icon-medium"></i>
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
                    <i class="fas fa-bell admin-notification-icon"></i>
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