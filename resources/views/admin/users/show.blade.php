@extends('layouts.admin')

@section('title', __('User Details'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ __('User Details') }}: {{ $user->name }}
                </h1>
                <p class="admin-order-subtitle">{{ __('View and manage user information') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.users.edit', $user) }}" class="admin-btn admin-btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Edit User') }}
                </a>
                <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back to Users') }}
                </a>
            </div>
        </div>

        <!-- Quick Actions Grid -->
        <div class="admin-modern-card admin-mb-1-5">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    {{ __('Quick Actions') }}
                </h2>
            </div>
            <div class="admin-card-body">
                <div class="admin-actions-grid">
                    <a href="{{ route('admin.users.edit', $user) }}" class="admin-btn admin-btn-primary">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>{{ __('Edit Profile') }}</span>
                    </a>
                    <a href="{{ route('admin.users.balance', $user) }}" class="admin-btn admin-btn-primary">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>{{ __('Manage Balance') }}</span>
                    </a>
                    <span class="admin-btn admin-btn-secondary admin-btn admin-btn-small admin-btn-primary-disabled">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ __('View Activity') }}</span>
                    </span>
                    @if($user->role === 'vendor')
                    <span class="admin-btn admin-btn-small admin-btn-primary admin-btn admin-btn-small admin-btn-primary-disabled">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>{{ __('Vendor Details') }}</span>
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- User Overview Grid -->
        <div class="admin-order-grid-modern">
            <!-- User Profile Card -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ __('User Profile') }}
                    </h2>
                </div>
                <div class="admin-card-body">
                    <div class="admin-user-profile">
                        <div class="admin-user-avatar">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="admin-user-info">
                            <h3 class="admin-user-name">{{ $user->name }}</h3>
                            <p class="admin-user-email">{{ $user->email }}</p>
                            <div class="admin-user-meta">
                                <div class="admin-user-status">
                                    @if($user->approved_at)
                                    <span class="admin-status-badge admin-status-badge-completed">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('Approved') }}
                                    </span>
                                    @else
                                    <span class="admin-status-badge admin-status-badge-warning">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('Pending Approval') }}
                                    </span>
                                    @endif
                                </div>
                                <div class="admin-user-role">
                                    @switch($user->role)
                                    @case('admin')
                                    <span class="admin-status-badge admin-status-badge-danger">{{ __('Admin') }}</span>
                                    @break
                                    @case('vendor')
                                    <span class="admin-status-badge admin-status-badge-warning">{{ __('Vendor') }}</span>
                                    @break
                                    @default
                                    <span class="admin-status-badge admin-status-badge-secondary">{{ __('Customer') }}</span>
                                    @endswitch
                                </div>
                            </div>
                            @if(!$user->approved_at)
                            <div class="admin-user-actions">
                                <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    <button type="submit" class="admin-btn admin-btn-success admin-btn-small" data-confirm="approve-user"
                                        data-confirm-message="{{ __('Are you sure you want to approve this user?') }}">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('Approve User') }}
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Statistics -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        {{ __('Account Statistics') }}
                    </h2>
                </div>
                <div class="admin-card-body">
                    <div class="admin-stats-grid">
                        <div class="admin-stat-item">
                            <div class="admin-stat-icon">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">${{ number_format($user->balance ?? 0, 2) }}</div>
                                <div class="admin-stat-label">{{ __('Account Balance') }}</div>
                            </div>
                        </div>
                        <div class="admin-stat-item">
                            <div class="admin-stat-icon">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="admin-stat-content">
                                <div class="admin-stat-value">{{ $user->created_at->diffInDays(now()) }}</div>
                                <div class="admin-stat-label">{{ __('Days Active') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Detailed Information Grid -->
        <div class="admin-order-grid-modern">
            <!-- Personal Information -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        {{ __('Personal Information') }}
                    </h2>
                </div>
                <div class="admin-card-body">
                    <div class="admin-info-grid">
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('Full Name') }}
                            </div>
                            <div class="admin-info-value">{{ $user->name }}</div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ __('Email Address') }}
                            </div>
                            <div class="admin-info-value">{{ $user->email }}</div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" />
                                </svg>
                                {{ __('Phone Number') }}
                            </div>
                            <div class="admin-info-value">{{ $user->phone ?? __('Not provided') }}</div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ __('Member Since') }}
                            </div>
                            <div class="admin-info-value">{{ $user->created_at->format('F j, Y') }}</div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                {{ __('Role') }}
                            </div>
                            <div class="admin-info-value">
                                @switch($user->role)
                                @case('admin')
                                <span class="admin-status-badge admin-status-badge-danger">{{ __('Admin') }}</span>
                                @break
                                @case('vendor')
                                <span class="admin-status-badge admin-status-badge-warning">{{ __('Vendor') }}</span>
                                @break
                                @default
                                <span class="admin-status-badge admin-status-badge-secondary">{{ __('Customer') }}</span>
                                @endswitch
                            </div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Last Login') }}
                            </div>
                            <div class="admin-info-value">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : __('Never') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Status & Additional Info -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        {{ __('Account Status & Information') }}
                    </h2>
                </div>
                <div class="admin-card-body">
                    <!-- Status Overview -->
                    <div class="admin-info-grid">
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Approval Status') }}
                            </div>
                            <div class="admin-info-value">
                                @if($user->approved_at)
                                <span class="admin-status-badge admin-status-badge-completed">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('Approved') }}
                                </span>
                                @else
                                <span class="admin-status-badge admin-status-badge-warning">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('Pending') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                {{ __('Email Verification') }}
                            </div>
                            <div class="admin-info-value">
                                @if($user->email_verified_at)
                                <span class="admin-status-badge admin-status-badge-completed">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('Verified') }}
                                </span>
                                @else
                                <span class="admin-status-badge admin-status-badge-danger">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                    {{ __('Not Verified') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                {{ __('Account Balance') }}
                            </div>
                            <div class="admin-info-value admin-info-value-success">
                                ${{ number_format($user->balance ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                {{ __('Account Status') }}
                            </div>
                            <div class="admin-info-value">
                                @if($user->approved_at)
                                <span class="admin-status-badge admin-status-badge-completed">{{ __('Active') }}</span>
                                @else
                                <span class="admin-status-badge admin-status-badge-warning">{{ __('Pending') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Status Information -->
                    <div class="admin-mt-1-5">
                        <div class="admin-info-grid">
                            <div class="admin-info-item">
                                <div class="admin-info-label">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ __('Approval Date') }}
                                </div>
                                <div class="admin-info-value">
                                    @if($user->approved_at)
                                    {{ $user->approved_at->format('Y-m-d H:i') }}
                                    @else
                                    <span class="admin-text-muted">{{ __('Not approved yet') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="admin-info-item">
                                <div class="admin-info-label">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ __('Email Verified Date') }}
                                </div>
                                <div class="admin-info-value">
                                    @if($user->email_verified_at)
                                    {{ $user->email_verified_at->format('Y-m-d H:i') }}
                                    @else
                                    <span class="admin-text-muted">{{ __('Not verified') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Recent Activity') }}
                </h2>
                <span class="admin-btn admin-btn-small admin-btn-secondary disabled">
                    {{ __('View All') }}
                </span>
            </div>
            <div class="admin-card-body">
                @if(isset($recentActivity) && count($recentActivity) > 0)
                <div class="admin-activity-timeline">
                    @foreach($recentActivity as $activity)
                    <div class="admin-activity-item">
                        <div class="admin-activity-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                        </div>
                        <div class="admin-activity-content">
                            <h6 class="admin-activity-title">{{ $activity['title'] }}</h6>
                            <p class="admin-activity-description">{{ $activity['description'] }}</p>
                            <small class="admin-activity-time">{{ $activity['created_at']->diffForHumans() }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="admin-empty-state">
                    <div class="admin-notification-icon">
                        <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p>{{ __('This user has no recent activity to display.') }}</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection