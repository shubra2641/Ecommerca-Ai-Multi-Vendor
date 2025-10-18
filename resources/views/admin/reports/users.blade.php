@extends('layouts.admin')

@section('title', __('Users Report'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title mb-1">{{ __('Users Report') }}</h1>
            <p class="text-muted mb-0">{{ __('Comprehensive analysis of user statistics and activity') }}</p>
        </div>
        <div class="page-actions">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary js-refresh-page" data-action="refresh">
                    <i class="fas fa-sync-alt"></i> {{ __('Refresh') }}
                </button>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-download"></i> {{ __('Export') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item js-export" href="#" data-export-type="excel" data-report="users">{{ __('Excel') }}</a>
                        </li>
                        <li><a class="dropdown-item js-export" href="#" data-export-type="pdf" data-report="users">{{ __('PDF') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-primary h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="{{ (int)($usersData['total_users'] ?? 0) }}">
                            {{ isset($usersData['total_users']) ? number_format($usersData['total_users']) : '0' }}
                        </div>
                        <div class="stats-label">{{ __('Total Users') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-users text-primary"></i>
                            <span class="text-primary">{{ __('All registered users') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-success h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="{{ (int)($usersData['active_users'] ?? 0) }}">
                            {{ isset($usersData['active_users']) ? number_format($usersData['active_users']) : '0' }}
                        </div>
                        <div class="stats-label">{{ __('Active Users') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-arrow-up text-success"></i>
                            <span class="text-success">{{ number_format((($usersData['active_users'] / max($usersData['total_users'], 1)) * 100), 1) }}% {{ __('active') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-warning h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="{{ (int)($usersData['pending_users'] ?? 0) }}">
                            {{ isset($usersData['pending_users']) ? number_format($usersData['pending_users']) : '0' }}
                        </div>
                        <div class="stats-label">{{ __('Pending Users') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-clock text-warning"></i>
                            <span class="text-warning">{{ __('Awaiting approval') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-info h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="{{ (int)($usersData['new_this_month'] ?? 0) }}">
                            {{ isset($usersData['new_this_month']) ? number_format($usersData['new_this_month']) : '0' }}
                        </div>
                        <div class="stats-label">{{ __('New This Month') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-user-plus text-info"></i>
                            <span class="text-info">{{ __('Recent registrations') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Registration Chart -->
    @if(isset($usersData['registration_chart']))
    <div class="card modern-card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('User Registration Trends') }}</h6>
        </div>
        <div class="card-body">
            <div class="chart-container h-400">
                <canvas id="registrationChart"></canvas>
            </div>
        </div>
    </div>
    @endif

    <!-- Role Distribution -->
    @if(isset($usersData['role_distribution']))
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card modern-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('User Role Distribution') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container h-380 pt-4 pb-2">
                        <canvas id="roleChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card modern-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Role Statistics') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                @foreach($usersData['role_distribution'] as $role => $count)
                                <tr>
                                    <td>
                                        <span
                                            class="badge badge-{{ $role === 'admin' ? 'danger' : ($role === 'vendor' ? 'warning' : 'primary') }}">
                                            {{ ucfirst($role) }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <strong data-countup data-target="{{ (int)$count }}">{{ number_format($count) }}</strong>
                                    </td>
                                    <td class="text-right text-muted">
                                        <span data-countup data-decimals="1" data-target="{{ isset($usersData['total_users']) && $usersData['total_users'] > 0 ? number_format(($count / $usersData['total_users']) * 100, 1, '.', '') : '0' }}">{{ isset($usersData['total_users']) && $usersData['total_users'] > 0 ? number_format(($count / $usersData['total_users']) * 100, 1) : '0' }}</span>%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Users Table -->
    @if(isset($usersData['recent_users']))
    <div class="card modern-card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Recent Users') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
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
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                            class="rounded-circle" width="32" height="32">
                                        @else
                                        <div class="avatar-initials rounded-circle bg-primary text-white d-flex align-items-center justify-content-center w-32 h-32 fs-14"
                                           >
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ $user->name }}</div>
                                        @if($user->phone)
                                        <div class="text-muted small">{{ $user->phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span
                                    class="badge badge-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'vendor' ? 'warning' : 'primary') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $user->status === 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>
                                @if($user->balance)
                                <span class="text-success font-weight-bold">
                                    {{ number_format($user->balance->amount, 2) }}
                                    {{ $user->balance->currency ?? 'USD' }}
                                </span>
                                @else
                                <span class="text-muted">{{ __('No Balance') }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted">{{ $user->created_at->format('M d, Y') }}</span>
                                <div class="small text-muted">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                @if($user->last_login_at)
                                <span class="text-muted">{{ $user->last_login_at->format('M d, Y H:i') }}</span>
                                <div class="small text-muted">{{ $user->last_login_at->diffForHumans() }}</div>
                                @else
                                <span class="text-muted">{{ __('Never') }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                {{ __('No users found') }}
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
    <div class="card modern-card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('User Activity Summary') }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="text-center">
                        <div class="h4 font-weight-bold text-primary" data-countup data-target="{{ (int)($usersData['activity_summary']['daily_active'] ?? 0) }}">
                            {{ isset($usersData['activity_summary']['daily_active']) ? number_format($usersData['activity_summary']['daily_active']) : '0' }}
                        </div>
                        <div class="text-muted">{{ __('Daily Active Users') }}</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="text-center">
                        <div class="h4 font-weight-bold text-success" data-countup data-target="{{ (int)($usersData['activity_summary']['weekly_active'] ?? 0) }}">
                            {{ isset($usersData['activity_summary']['weekly_active']) ? number_format($usersData['activity_summary']['weekly_active']) : '0' }}
                        </div>
                        <div class="text-muted">{{ __('Weekly Active Users') }}</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="text-center">
                        <div class="h4 font-weight-bold text-info" data-countup data-target="{{ (int)($usersData['activity_summary']['monthly_active'] ?? 0) }}">
                            {{ isset($usersData['activity_summary']['monthly_active']) ? number_format($usersData['activity_summary']['monthly_active']) : '0' }}
                        </div>
                        <div class="text-muted">{{ __('Monthly Active Users') }}</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="text-center">
                        <div class="h4 font-weight-bold text-warning" data-countup data-suffix=" min" data-target="{{ isset($usersData['activity_summary']['avg_session_duration']) ? preg_replace('/[^0-9.]/','',$usersData['activity_summary']['avg_session_duration']) : '0' }}">
                            {{ isset($usersData['activity_summary']['avg_session_duration']) ? $usersData['activity_summary']['avg_session_duration'] : '0 min' }}
                        </div>
                        <div class="text-muted">{{ __('Avg Session Duration') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection