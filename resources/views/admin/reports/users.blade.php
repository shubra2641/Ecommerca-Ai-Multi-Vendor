@extends('layouts.admin')

@section('title', __('Users Report'))

@section('content')
<div class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="admin-order-title">
                <h1>
                    <i class="fas fa-users"></i>
                    {{ __('Users Report') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Comprehensive analysis of user statistics and activity') }}</p>
            </div>
            <div class="header-actions">
                <button type="button" class="admin-btn admin-btn-secondary js-refresh-page" data-action="refresh">
                    <i class="fas fa-sync-alt"></i>
                    {{ __('Refresh') }}
                </button>
                <div class="dropdown">
                    <button class="admin-btn admin-btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-download"></i>
                        {{ __('Export') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item js-export" href="#" data-export-type="excel" data-report="users">
                                <i class="fas fa-file-excel"></i>
                                {{ __('Excel') }}
                            </a></li>
                        <li><a class="dropdown-item js-export" href="#" data-export-type="pdf" data-report="users">
                                <i class="fas fa-file-pdf"></i>
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
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($usersData['total_users'] ?? 0) }}">{{ isset($usersData['total_users']) ? number_format($usersData['total_users']) : '0' }}</div>
                    <div class="admin-stat-label">{{ __('Total Users') }}</div>
                    <div class="admin-stat-description">{{ __('All registered users') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        {{ __('Growing') }}
                    </span>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($usersData['active_users'] ?? 0) }}">{{ isset($usersData['active_users']) ? number_format($usersData['active_users']) : '0' }}</div>
                    <div class="admin-stat-label">{{ __('Active Users') }}</div>
                    <div class="admin-stat-description">{{ __('Verified and active users') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        +{{ number_format((($usersData['active_users'] ?? 0) / max($usersData['total_users'] ?? 1, 1)) * 100, 1) }}%
                    </span>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-warning">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($usersData['pending_users'] ?? 0) }}">{{ isset($usersData['pending_users']) ? number_format($usersData['pending_users']) : '0' }}</div>
                    <div class="admin-stat-label">{{ __('Pending Users') }}</div>
                    <div class="admin-stat-description">{{ __('Awaiting approval') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-neutral">
                        <i class="fas fa-clock"></i>
                        {{ __('Review needed') }}
                    </span>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-user-friends"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($usersData['new_this_month'] ?? 0) }}">{{ isset($usersData['new_this_month']) ? number_format($usersData['new_this_month']) : '0' }}</div>
                    <div class="admin-stat-label">{{ __('New This Month') }}</div>
                    <div class="admin-stat-description">{{ __('Recent registrations') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
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
                    <i class="fas fa-chart-line"></i>
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
                    <i class="fas fa-shield-alt"></i>
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
                    <i class="fas fa-users"></i>
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
                                        <i class="fas fa-users admin-notification-icon"></i>
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
                    <i class="fas fa-chart-bar"></i>
                    {{ __('User Activity Summary') }}
                </div>
            </div>
            <div class="admin-stats-grid">
                <div class="admin-stat-card admin-stat-primary">
                    <div class="admin-stat-header">
                        <div class="admin-stat-icon-wrapper">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="admin-stat-badge">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                    </div>
                    <div class="admin-stat-content">
                        <div class="admin-stat-value" data-countup="{{ (int)($usersData['activity_summary']['daily_active'] ?? 0) }}">{{ isset($usersData['activity_summary']['daily_active']) ? number_format($usersData['activity_summary']['daily_active']) : '0' }}</div>
                        <div class="admin-stat-label">{{ __('Daily Active Users') }}</div>
                        <div class="admin-stat-description">{{ __('Users active today') }}</div>
                    </div>
                    <div class="admin-stat-footer">
                        <div class="admin-stat-trend admin-stat-trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>{{ __('Today') }}</span>
                        </div>
                    </div>
                </div>

                <div class="admin-stat-card admin-stat-success">
                    <div class="admin-stat-header">
                        <div class="admin-stat-icon-wrapper">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="admin-stat-badge">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                    </div>
                    <div class="admin-stat-content">
                        <div class="admin-stat-value" data-countup="{{ (int)($usersData['activity_summary']['weekly_active'] ?? 0) }}">{{ isset($usersData['activity_summary']['weekly_active']) ? number_format($usersData['activity_summary']['weekly_active']) : '0' }}</div>
                        <div class="admin-stat-label">{{ __('Weekly Active Users') }}</div>
                        <div class="admin-stat-description">{{ __('Users active this week') }}</div>
                    </div>
                    <div class="admin-stat-footer">
                        <div class="admin-stat-trend admin-stat-trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>{{ __('This week') }}</span>
                        </div>
                    </div>
                </div>

                <div class="admin-stat-card admin-stat-info">
                    <div class="admin-stat-header">
                        <div class="admin-stat-icon-wrapper">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="admin-stat-badge">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                    </div>
                    <div class="admin-stat-content">
                        <div class="admin-stat-value" data-countup="{{ (int)($usersData['activity_summary']['monthly_active'] ?? 0) }}">{{ isset($usersData['activity_summary']['monthly_active']) ? number_format($usersData['activity_summary']['monthly_active']) : '0' }}</div>
                        <div class="admin-stat-label">{{ __('Monthly Active Users') }}</div>
                        <div class="admin-stat-description">{{ __('Users active this month') }}</div>
                    </div>
                    <div class="admin-stat-footer">
                        <div class="admin-stat-trend admin-stat-trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>{{ __('This month') }}</span>
                        </div>
                    </div>
                </div>

                <div class="admin-stat-card admin-stat-warning">
                    <div class="admin-stat-header">
                        <div class="admin-stat-icon-wrapper">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="admin-stat-badge">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                    </div>
                    <div class="admin-stat-content">
                        <div class="admin-stat-value">{{ isset($usersData['activity_summary']['avg_session_duration']) ? $usersData['activity_summary']['avg_session_duration'] : '0 min' }}</div>
                        <div class="admin-stat-label">{{ __('Avg Session Duration') }}</div>
                        <div class="admin-stat-description">{{ __('Average session time') }}</div>
                    </div>
                    <div class="admin-stat-footer">
                        <div class="admin-stat-trend admin-stat-trend-neutral">
                            <i class="fas fa-dot-circle"></i>
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