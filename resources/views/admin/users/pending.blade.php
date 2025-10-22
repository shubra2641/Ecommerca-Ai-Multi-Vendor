@extends('layouts.admin')

@section('title', __('Pending Approval Users'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Users Pending Approval') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Review and approve pending user registrations') }}</p>
            </div>
        </div>

        <!-- Pending Users List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">{{ __('Pending Users') }}</h2>
                <span class="admin-badge-count">{{ $users->total() }}</span>
            </div>
            <div class="admin-card-body">
                @if($users->count() > 0)
                <div class="admin-items-list">
                    @foreach($users as $user)
                    <div class="admin-item-card">
                        <div class="admin-item-main">
                            <div class="admin-item-placeholder admin-item-placeholder-warning">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="admin-item-details">
                                <h3 class="admin-item-name">{{ $user->name }}</h3>
                                <div class="admin-payment-details admin-mt-half">
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        {{ $user->email }}
                                    </span>
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        {{ __('Role') }}: <strong>{{ ucfirst($user->role) }}</strong>
                                    </span>
                                    @if($user->phone_number)
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" />
                                        </svg>
                                        {{ $user->phone_number }}
                                    </span>
                                    @endif
                                </div>
                                <div class="admin-item-meta admin-mt-half">
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ __('Registered') }}: {{ $user->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="admin-item-meta">
                            <span class="admin-status-badge admin-status-badge-warning">
                                {{ __('Pending Approval') }}
                            </span>
                            <div class="admin-actions-flex">
                                <a href="{{ route('admin.users.edit', $user) }}" class="admin-btn admin-btn-small admin-btn-primary">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('Review & Approve') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($users->hasPages())
                <div class="admin-card-footer-pagination">
                    <div class="pagination-info">
                        {{ __('Showing :from to :to of :total results', ['from' => $users->firstItem(), 'to' => $users->lastItem(), 'total' => $users->total()]) }}
                    </div>
                    <div class="pagination-links">
                        {{ $users->links() }}
                    </div>
                </div>
                @endif
                @else
                <div class="admin-empty-state">
                    <div class="admin-notification-icon">
                        <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p>{{ __('All users have been approved!') }}</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection