@extends('layouts.admin')

@section('title', __('User Details'))

@section('content')
<div class="container-fluid">
    @include('admin.partials.page-header', [
        'title' => __('User Details') . ': ' . $user->name,
        'subtitle' => __('View and manage user information'),
        'actions' => '<a href="'.route('admin.users.edit', $user).'" class="btn btn-primary"><i class="fas fa-edit me-1"></i> '.e(__('Edit User')).'</a> <a href="'.route('admin.users.index').'" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> '.e(__('Back to Users')).'</a>'
    ])

    <div class="row">
        <div class="col-md-4">
            <div class="card modern-card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-user-circle text-primary"></i>
                    <h5 class="card-title mb-0">{{ __('User Profile') }}</h5>
                </div>
                <div class="card-body text-center">
                    <div class="user-avatar-large mb-3">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <h4 class="mb-2">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>

                    <div class="user-status mb-3">
                        @if($user->approved_at)
                        <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>
                            {{ __('Approved') }}
                        </span>
                        @else
                        <span class="badge bg-warning">
                            <i class="fas fa-clock me-1"></i>
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
                                <i class="fas fa-check me-1"></i>
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
                    <h5 class="card-title mb-0">{{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-1"></i>
                            {{ __('Edit Profile') }}
                        </a>
                        <a href="{{ route('admin.users.balance', $user) }}" class="btn btn-outline-success">
                            <i class="fas fa-wallet me-1"></i>
                            {{ __('Manage Balance') }}
                        </a>
                        <span class="btn btn-outline-info disabled">
                            <i class="fas fa-history me-1"></i>
                            {{ __('View Activity') }}
                        </span>
                        @if($user->role === 'vendor')
                        <span class="btn btn-outline-warning disabled">
                            <i class="fas fa-store me-1"></i>
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
                    <h5 class="card-title mb-0">{{ __('Personal Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="form-label fw-bold">{{ __('Full Name') }}</label>
                                <div class="info-value">{{ $user->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="form-label fw-bold">{{ __('Email Address') }}</label>
                                <div class="info-value">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="form-label fw-bold">{{ __('Phone Number') }}</label>
                                <div class="info-value">{{ $user->phone ?? __('Not provided') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="form-label fw-bold">{{ __('Role') }}</label>
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
                                <label class="form-label fw-bold">{{ __('Account Balance') }}</label>
                                <div class="info-value text-success">
                                    ${{ number_format($user->balance ?? 0, 2) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="form-label fw-bold">{{ __('Member Since') }}</label>
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
                    <h5 class="card-title mb-0">{{ __('Account Status') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="status-item d-flex align-items-start gap-3">
                                <div class="status-icon {{ $user->approved_at ? 'text-success' : 'text-warning' }}">
                                    <i class="fas {{ $user->approved_at ? 'fa-check-circle' : 'fa-clock' }} fa-2x"></i>
                                </div>
                                <div class="status-content">
                                    <h6 class="mb-1">{{ __('Approval Status') }}</h6>
                                    <p class="mb-0 text-muted">
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
                            <div class="status-item d-flex align-items-start gap-3">
                                <div class="status-icon {{ $user->email_verified_at ? 'text-success' : 'text-danger' }}">
                                    <i class="fas {{ $user->email_verified_at ? 'fa-shield-check' : 'fa-shield-exclamation' }} fa-2x"></i>
                                </div>
                                <div class="status-content">
                                    <h6 class="mb-1">{{ __('Email Verification') }}</h6>
                                    <p class="mb-0 text-muted">
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
                <div class="card-header d-flex align-items-center gap-2 justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-history text-secondary"></i>
                        <h5 class="card-title mb-0">{{ __('Recent Activity') }}</h5>
                    </div>
                    <span class="btn btn-sm btn-outline-primary disabled">
                        {{ __('View All') }}
                    </span>
                </div>
                <div class="card-body">
                    @if(isset($recentActivity) && count($recentActivity) > 0)
                    <div class="activity-timeline">
                        @foreach($recentActivity as $activity)
                        <div class="activity-item d-flex align-items-start gap-3 mb-3">
                            <div class="activity-icon">
                                <i class="fas {{ $activity['icon'] ?? 'fa-circle' }} text-primary"></i>
                            </div>
                            <div class="activity-content">
                                <h6 class="mb-1">{{ $activity['title'] }}</h6>
                                <p class="mb-1 text-muted">{{ $activity['description'] }}</p>
                                <small class="text-muted">{{ $activity['created_at']->diffForHumans() }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state text-center py-4">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('No Recent Activity') }}</h5>
                        <p class="text-muted">{{ __('This user has no recent activity to display.') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
