@extends('layouts.admin')

@section('title', __('Users Report'))

@section('content')
<div class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="admin-order-title">
                <h1>
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ __('Users Report') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Comprehensive analysis of user statistics and activity') }}</p>
            </div>
            <div class="header-actions">
                <button type="button" class="admin-btn admin-btn-secondary js-refresh-page" data-action="refresh">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M23 4v6h-6M1 20v-6h6m15-4a9 9 0 11-18 0 9 9 0 0118 0zM1 10a9 9 0 0118 0" />
                    </svg>
                    {{ __('Refresh') }}
                </button>
                <div class="dropdown">
                    <button class="admin-btn admin-btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{ __('Export') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item js-export" href="#" data-export-type="excel" data-report="users">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ __('Excel') }}
                            </a></li>
                        <li><a class="dropdown-item js-export" href="#" data-export-type="pdf" data-report="users">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14,2 14,8 20,8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10,9 9,9 8,9" />
                                </svg>
                                {{ __('PDF') }}
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Enhanced Stats Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card admin-stat-primary">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($usersData['total_users'] ?? 0) }}">{{ isset($usersData['total_users']) ? number_format($usersData['total_users']) : '0' }}</div>
                    <div class="admin-stat-label">{{ __('Total Users') }}</div>
                    <div class="admin-stat-description">{{ __('All registered users') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-up">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                        {{ __('Growing') }}
                    </span>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($usersData['active_users'] ?? 0) }}">{{ isset($usersData['active_users']) ? number_format($usersData['active_users']) : '0' }}</div>
                    <div class="admin-stat-label">{{ __('Active Users') }}</div>
                    <div class="admin-stat-description">{{ __('Verified and active users') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-up">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                        +{{ number_format((($usersData['active_users'] ?? 0) / max($usersData['total_users'] ?? 1, 1)) * 100, 1) }}%
                    </span>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-warning">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($usersData['pending_users'] ?? 0) }}">{{ isset($usersData['pending_users']) ? number_format($usersData['pending_users']) : '0' }}</div>
                    <div class="admin-stat-label">{{ __('Pending Users') }}</div>
                    <div class="admin-stat-description">{{ __('Awaiting approval') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-neutral">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('Review needed') }}
                    </span>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($usersData['new_this_month'] ?? 0) }}">{{ isset($usersData['new_this_month']) ? number_format($usersData['new_this_month']) : '0' }}</div>
                    <div class="admin-stat-label">{{ __('New This Month') }}</div>
                    <div class="admin-stat-description">{{ __('Recent registrations') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-up">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                        {{ __('Growing') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- User Registration Chart -->
        @if(isset($usersData['registration_chart']))
        <div class="admin-modern-card admin-mb-1-5">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 3v18h18M7 12l3-3 3 3 5-5" />
                    </svg>
                    {{ __('User Registration Trends') }}
                </h2>
            </div>
            <div class="admin-card-body">
                <div class="chart-container h-400">
                    <canvas id="registrationChart"></canvas>
                </div>
            </div>
        </div>
        @endif

        <!-- Role Distribution -->
        @if(isset($usersData['role_distribution']))
        <div class="admin-modern-card admin-mb-1-5">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    {{ __('User Role Distribution') }}
                </h2>
            </div>
            <div class="admin-card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="chart-container h-380 pt-4 pb-2">
                            <canvas id="roleChart"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="admin-role-stats">
                            <h6 class="admin-section-title">{{ __('Role Statistics') }}</h6>
                            <div class="admin-role-list">
                                @foreach($usersData['role_distribution'] as $role => $count)
                                <div class="admin-role-item">
                                    <div class="admin-role-info">
                                        <span class="admin-status-badge admin-status-badge-{{ $role === 'admin' ? 'danger' : ($role === 'vendor' ? 'warning' : 'primary') }}">
                                            {{ ucfirst($role) }}
                                        </span>
                                        <div class="admin-role-count">
                                            <strong data-countup data-target="{{ (int)$count }}">{{ number_format($count) }}</strong>
                                            <span class="admin-role-percentage">
                                                <span data-countup data-decimals="1" data-target="{{ isset($usersData['total_users']) && $usersData['total_users'] > 0 ? number_format(($count / $usersData['total_users']) * 100, 1, '.', '') : '0' }}">{{ isset($usersData['total_users']) && $usersData['total_users'] > 0 ? number_format(($count / $usersData['total_users']) * 100, 1) : '0' }}</span>%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="admin-role-progress">
                                        <div class="admin-progress-bar">
                                            @php
                                            $percentage = isset($usersData['total_users']) && $usersData['total_users'] > 0 ? round(($count / $usersData['total_users']) * 100, 1) : 0;
                                            @endphp
                                            <div class="admin-progress-fill"></div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Users Table -->
        @if(isset($usersData['recent_users']))
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                    </svg>
                    {{ __('Recent Users') }}
                </div>
                <div class="admin-badge-count">{{ count($usersData['recent_users']) }} {{ __('users') }}</div>
            </div>
            <div class="admin-card-body">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Role') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Balance') }}</th>
                                <th>{{ __('Registered') }}</th>
                                <th>{{ __('Last Login') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usersData['recent_users'] as $user)
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            @if($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                                class="rounded-circle" width="32" height="32">
                                            @else
                                            <div class="avatar-initials rounded-circle bg-primary text-white d-flex align-items-center justify-content-center w-32 h-32 fs-14">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="user-name">{{ $user->name }}</div>
                                            @if($user->phone)
                                            <div class="user-email">{{ $user->phone }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="admin-text-muted">{{ $user->email }}</div>
                                </td>
                                <td>
                                    <span class="admin-status-badge admin-status-badge-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'vendor' ? 'warning' : 'primary') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="admin-status-badge admin-status-badge-{{ $user->status === 'active' ? 'completed' : 'warning' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->balance)
                                    <div class="admin-stock-value">
                                        {{ number_format($user->balance->amount, 2) }}
                                        {{ $user->balance->currency ?? 'USD' }}
                                    </div>
                                    @else
                                    <div class="admin-text-muted">{{ __('No Balance') }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="admin-text-muted">{{ $user->created_at->format('M d, Y') }}</div>
                                    <div class="admin-text-muted small">{{ $user->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    @if($user->last_login_at)
                                    <div class="admin-text-muted">{{ $user->last_login_at->format('M d, Y H:i') }}</div>
                                    <div class="admin-text-muted small">{{ $user->last_login_at->diffForHumans() }}</div>
                                    @else
                                    <div class="admin-text-muted">{{ __('Never') }}</div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="admin-empty-state">
                                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" class="admin-notification-icon">
                                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                                        </svg>
                                        <h3>{{ __('No users found') }}</h3>
                                        <p>{{ __('No users match your current filters') }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- User Activity Summary -->
        @if(isset($usersData['activity_summary']))
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    {{ __('User Activity Summary') }}
                </div>
            </div>
            <div class="admin-stats-grid">
                <div class="admin-stat-card admin-stat-primary">
                    <div class="admin-stat-header">
                        <div class="admin-stat-icon-wrapper">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="admin-stat-badge">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="admin-stat-content">
                        <div class="admin-stat-value" data-countup="{{ (int)($usersData['activity_summary']['daily_active'] ?? 0) }}">{{ isset($usersData['activity_summary']['daily_active']) ? number_format($usersData['activity_summary']['daily_active']) : '0' }}</div>
                        <div class="admin-stat-label">{{ __('Daily Active Users') }}</div>
                        <div class="admin-stat-description">{{ __('Users active today') }}</div>
                    </div>
                    <div class="admin-stat-footer">
                        <div class="admin-stat-trend admin-stat-trend-up">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M7 14l3-3 3 3 5-5" />
                                <path d="M17 9l-5 5-3-3-3 3" />
                            </svg>
                            <span>{{ __('Today') }}</span>
                        </div>
                    </div>
                </div>

                <div class="admin-stat-card admin-stat-success">
                    <div class="admin-stat-header">
                        <div class="admin-stat-icon-wrapper">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="admin-stat-badge">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="admin-stat-content">
                        <div class="admin-stat-value" data-countup="{{ (int)($usersData['activity_summary']['weekly_active'] ?? 0) }}">{{ isset($usersData['activity_summary']['weekly_active']) ? number_format($usersData['activity_summary']['weekly_active']) : '0' }}</div>
                        <div class="admin-stat-label">{{ __('Weekly Active Users') }}</div>
                        <div class="admin-stat-description">{{ __('Users active this week') }}</div>
                    </div>
                    <div class="admin-stat-footer">
                        <div class="admin-stat-trend admin-stat-trend-up">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M7 14l3-3 3 3 5-5" />
                                <path d="M17 9l-5 5-3-3-3 3" />
                            </svg>
                            <span>{{ __('This week') }}</span>
                        </div>
                    </div>
                </div>

                <div class="admin-stat-card admin-stat-info">
                    <div class="admin-stat-header">
                        <div class="admin-stat-icon-wrapper">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="admin-stat-badge">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="admin-stat-content">
                        <div class="admin-stat-value" data-countup="{{ (int)($usersData['activity_summary']['monthly_active'] ?? 0) }}">{{ isset($usersData['activity_summary']['monthly_active']) ? number_format($usersData['activity_summary']['monthly_active']) : '0' }}</div>
                        <div class="admin-stat-label">{{ __('Monthly Active Users') }}</div>
                        <div class="admin-stat-description">{{ __('Users active this month') }}</div>
                    </div>
                    <div class="admin-stat-footer">
                        <div class="admin-stat-trend admin-stat-trend-up">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M7 14l3-3 3 3 5-5" />
                                <path d="M17 9l-5 5-3-3-3 3" />
                            </svg>
                            <span>{{ __('This month') }}</span>
                        </div>
                    </div>
                </div>

                <div class="admin-stat-card admin-stat-warning">
                    <div class="admin-stat-header">
                        <div class="admin-stat-icon-wrapper">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="admin-stat-badge">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="admin-stat-content">
                        <div class="admin-stat-value">{{ isset($usersData['activity_summary']['avg_session_duration']) ? $usersData['activity_summary']['avg_session_duration'] : '0 min' }}</div>
                        <div class="admin-stat-label">{{ __('Avg Session Duration') }}</div>
                        <div class="admin-stat-description">{{ __('Average session time') }}</div>
                    </div>
                    <div class="admin-stat-footer">
                        <div class="admin-stat-trend admin-stat-trend-neutral">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>{{ __('Average') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection