@extends('layouts.admin')

@section('title', __('User Activity'))

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('User Activity') }}: {{ $user->name }}</h1>
        <p class="page-description">{{ __('View user activity history and logs') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            {{ __('Back to User') }}
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-clock text-info"></i>
                <h3 class="card-title mb-0">{{ __('Activity Timeline') }}</h3>
            </div>
            <div class="card-body">
                @if($activities->isEmpty())
                <div class="empty-state text-center py-6">
                    <div class="empty-state-icon mb-3">
                        <i class="fas fa-history fa-2x text-muted"></i>
                    </div>
                    <h3 class="empty-state-title">{{ __('No Activity Found') }}</h3>
                    <p class="empty-state-description text-muted">{{ __('This user has no recorded activity yet.') }}
                    </p>
                </div>
                @else
                <div class="timeline">
                    @foreach($activities as $activity)
                    <div class="timeline-item d-flex mb-4">
                        <div class="timeline-marker me-3 w-36 flex-fixed-36">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center w-28 h-28 text-white">
                                <i class="fas fa-clock fs-12"></i>
                            </div>
                        </div>
                        <div class="timeline-content flex-fill">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ ucfirst(str_replace('.', ' ', $activity->type)) }}</strong>
                                    <div class="small text-muted">{{ $activity->description }}</div>
                                </div>
                                <div class="text-end small text-muted">
                                    {{ $activity->created_at->format('Y-m-d H:i') }}<br>
                                    <span class="d-block">{{ $activity->ip_address }}</span>
                                </div>
                            </div>
                            @if(!empty($activity->data_html))
                            <div class="mt-2 small">@clean($activity->data_html)</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-3">{{ $activities->links() }}</div>
                @endif
            </div>
        </div>
    </div>

            <div class="col-md-4">
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-chart-bar text-success"></i>
                <h3 class="card-title mb-0">{{ __('Activity Summary') }}</h3>
            </div>
            <div class="card-body">
                <div class="card modern-card stats-card mb-3 stats-card-primary">
                    <div class="stats-card-body">
                        <div class="stats-card-content">
                            <div class="stats-label">{{ __('Total Activities') }}</div>
                            <div class="stats-number">{{ count($activities) }}</div>
                        </div>
                    </div>
                </div>
                <div class="card modern-card stats-card mb-3 stats-card-info">
                    <div class="stats-card-body">
                        <div class="stats-card-content">
                            <div class="stats-label">{{ __('Last Activity') }}</div>
                            <div class="stats-number">
                                @if(!empty($activities))
                                {{ $activities[0]['time'] ?? __('No recent activity') }}
                                @else
                                {{ __('No activity') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card modern-card stats-card stats-card-warning">
                    <div class="stats-card-body">
                        <div class="stats-card-content">
                            <div class="stats-label">{{ __('User Since') }}</div>
                            <div class="stats-number">{{ $user->created_at->format('M Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-user text-primary"></i>
                <h3 class="card-title mb-0">{{ __('User Info') }}</h3>
            </div>
            <div class="card-body">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-details">
                        <h4>{{ $user->name }}</h4>
                        <p class="text-muted">{{ $user->email }}</p>
                        <span
                            class="badge badge-{{ $user->role === 'admin' ? 'primary' : ($user->role === 'vendor' ? 'success' : 'secondary') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection