@extends('vendor.layout')

@section('title', __('Notifications'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 8A6 6 0 0 0 6 8C6 7.44772 6.44772 7 7 7H17C17.5523 7 18 7.44772 18 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M13.73 21C13.5542 21.3031 13.3019 21.5547 12.9982 21.7295C12.6946 21.9044 12.3504 21.9965 12 21.9965C11.6496 21.9965 11.3054 21.9044 11.0018 21.7295C10.6982 21.5547 10.4458 21.3031 10.27 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Notifications') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Stay updated with your account activities') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <button type="button" class="admin-btn admin-btn-secondary" data-action="mark-all-read">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Mark All Read') }}
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 8A6 6 0 0 0 6 8C6 7.44772 6.44772 7 7 7H17C17.5523 7 18 7.44772 18 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M13.73 21C13.5542 21.3031 13.3019 21.5547 12.9982 21.7295C12.6946 21.9044 12.3504 21.9965 12 21.9965C11.6496 21.9965 11.3054 21.9044 11.0018 21.7295C10.6982 21.5547 10.4458 21.3031 10.27 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Notifications List') }}
                </h3>
                <div class="admin-badge-count">{{ $notifications->count() }} {{ __('notifications') }}</div>
            </div>
            <div class="admin-card-body">
                @if($notifications->count())
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Notification') }}</th>
                                <th class="d-none d-md-table-cell">{{ __('Date') }}</th>
                                <th class="d-none d-lg-table-cell">{{ __('Status') }}</th>
                                <th width="120">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifications as $n)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $n->data['title'] ?? ucfirst(str_replace('_',' ', $n->data['type'] ?? 'Notification')) }}</div>
                                    <div class="text-muted small">{{ $n->data['message'] ?? $n->data['text'] ?? '' }}</div>
                                    <div class="d-md-none mt-1">
                                        <span class="badge bg-secondary">{{ $n->created_at->format('M d, Y') }}</span>
                                        @if($n->read_at)
                                        <span class="badge bg-success">{{ __('Read') }}</span>
                                        @else
                                        <span class="badge bg-warning text-dark">{{ __('Unread') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <div class="fw-semibold">{{ $n->created_at->format('M d, Y H:i') }}</div>
                                    <div class="text-muted small">{{ $n->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    @if($n->read_at)
                                    <span class="badge bg-success">{{ __('Read') }}</span>
                                    @else
                                    <span class="badge bg-warning text-dark">{{ __('Unread') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if(!$n->read_at)
                                        <form method="POST" action="{{ route('vendor.notifications.read', $n->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary" title="{{ __('Mark read') }}">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-muted small">{{ __('Read') }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <tr>
                    <td colspan="4" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-bell-slash fa-3x mb-3"></i>
                            <h5>{{ __('No notifications found.') }}</h5>
                            <p class="mb-3">{{ __('You have no notifications at the moment.') }}</p>
                        </div>
                    </td>
                </tr>
                @endif
            </div>
            @if($notifications->hasPages())
            <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div class="text-muted small">{{ __('Showing') }} {{ $notifications->firstItem() }} - {{ $notifications->lastItem() }} {{ __('of') }} {{ $notifications->total() }}</div>
                <div class="pagination-links">{{ $notifications->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection