@extends('layouts.admin')

@section('title', __('User Details'))

@section('content')
    <div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('User Details') }}: {{ $user->name }}</h1>
        <p class="page-description">{{ __('View and manage user information') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i>
            {{ __('Edit User') }}
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            {{ __('Back to Users') }}
        </a>
    </div>
</div>

<!-- User Info Cards -->
<div class="row">
    <div class="col-md-4">
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-user-circle text-primary"></i>
                <h3 class="card-title mb-0">{{ __('User Profile') }}</h3>
            </div>
            <div class="card-body text-center">
                <div class="user-avatar-large">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <h4 class="mt-3">{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>

                <div class="user-status">
                    @if($user->approved_at)
                    <span class="badge bg-success">
                        <i class="fas fa-check"></i>
                        {{ __('Approved') }}
                    </span>
                    @else
                    <span class="badge bg-warning">
                        <i class="fas fa-clock"></i>
                        {{ __('Pending Approval') }}
                    </span>
                    @endif
                </div>

                @if(!$user->approved_at)
                <div class="mt-3">
                    <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm" data-confirm="approve-user"
                            data-confirm-message="{{ __('Are you sure you want to approve this user?') }}">
                            <i class="fas fa-check"></i>
                            {{ __('Approve User') }}
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-bolt text-warning"></i>
                <h3 class="card-title mb-0">{{ __('Quick Actions') }}</h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit"></i>
                        {{ __('Edit Profile') }}
                    </a>
                    <a href="{{ route('admin.users.balance', $user) }}" class="btn btn-outline-success">
                        <i class="fas fa-wallet"></i>
                        {{ __('Manage Balance') }}
                    </a>
                    <a href="{{ route('admin.users.activity', $user) }}" class="btn btn-outline-info">
                        <i class="fas fa-history"></i>
                        {{ __('View Activity') }}
                    </a>
                    @if($user->role === 'vendor')
                    <span class="btn btn-outline-warning disabled">
                        <i class="fas fa-store"></i>
                        {{ __('Vendor Details') }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

        <div class="col-md-8">
        <!-- User Details -->
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-id-card text-info"></i>
                <h3 class="card-title mb-0">{{ __('Personal Information') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>{{ __('Full Name') }}</label>
                            <div class="info-value">{{ $user->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>{{ __('Email Address') }}</label>
                            <div class="info-value">{{ $user->email }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>{{ __('Phone Number') }}</label>
                            <div class="info-value">{{ $user->phone ?? __('Not provided') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>{{ __('Role') }}</label>
                            <div class="info-value">
                                @switch($user->role)
                                @case('admin')
                                <span class="badge bg-danger">{{ __('Admin') }}</span>
                                @break
                                @case('vendor')
                                <span class="badge bg-warning">{{ __('Vendor') }}</span>
                                @break
                                @default
                                <span class="badge bg-secondary">{{ __('Customer') }}</span>
                                @endswitch
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>{{ __('Account Balance') }}</label>
                            <div class="info-value text-success">
                                ${{ number_format($user->balance ?? 0, 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label>{{ __('Member Since') }}</label>
                            <div class="info-value">{{ $user->created_at->format('F j, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Status -->
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-shield-check text-success"></i>
                <h3 class="card-title mb-0">{{ __('Account Status') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="status-item">
                            <div class="status-icon {{ $user->approved_at ? 'text-success' : 'text-warning' }}">
                                <i class="fas {{ $user->approved_at ? 'fa-check-circle' : 'fa-clock' }}"></i>
                            </div>
                            <div class="status-content">
                                <h5>{{ __('Approval Status') }}</h5>
                                <p>
                                    @if($user->approved_at)
                                    {{ __('Approved on') }} {{ $user->approved_at->format('Y-m-d H:i') }}
                                    @else
                                    {{ __('Pending approval since') }} {{ $user->created_at->format('Y-m-d H:i') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="status-item">
                            <div class="status-icon {{ $user->email_verified_at ? 'text-success' : 'text-danger' }}">
                                <i
                                    class="fas {{ $user->email_verified_at ? 'fa-shield-check' : 'fa-shield-exclamation' }}"></i>
                            </div>
                            <div class="status-content">
                                <h5>{{ __('Email Verification') }}</h5>
                                <p>
                                    @if($user->email_verified_at)
                                    {{ __('Verified on') }} {{ $user->email_verified_at->format('Y-m-d H:i') }}
                                    @else
                                    {{ __('Email not verified') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-history text-secondary"></i>
                <h3 class="card-title mb-0">{{ __('Recent Activity') }}</h3>
                <div class="card-actions ms-auto">
                    <a href="{{ route('admin.users.activity', $user) }}" class="btn btn-sm btn-outline-primary">
                        {{ __('View All') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(isset($recentActivity) && count($recentActivity) > 0)
                <div class="activity-timeline">
                    @foreach($recentActivity as $activity)
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas {{ $activity['icon'] ?? 'fa-circle' }}"></i>
                        </div>
                        <div class="activity-content">
                            <h4>{{ $activity['title'] }}</h4>
                            <p>{{ $activity['description'] }}</p>
                            <small>{{ $activity['created_at']->diffForHumans() }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-history fa-2x"></i>
                    <h4>{{ __('No Recent Activity') }}</h4>
                    <p>{{ __('This user has no recent activity to display.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
