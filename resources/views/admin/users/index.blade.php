@extends('layouts.admin')

@section('title', __('Users Management'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Users Management') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage all users and their permissions') }}</p>
                        <div class="admin-header-stats">
                            <span class="admin-stat-item-mini">
                                <i class="fas fa-user"></i>
                                {{ $users->total() }} {{ __('Total Users') }}
                            </span>
                            <span class="admin-stat-item-mini">
                                <i class="fas fa-check-circle"></i>
                                {{ $users->where('approved_at', '!=', null)->count() }} {{ __('Approved') }}
                            </span>
                            <span class="admin-stat-item-mini">
                                <i class="fas fa-clock"></i>
                                {{ $users->where('approved_at', null)->count() }} {{ __('Pending') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <div class="admin-action-group">
                    <a href="{{ route('admin.users.create') }}" class="admin-btn admin-btn-primary admin-btn-large">
                        <i class="fas fa-plus"></i>
                        {{ __('Add New User') }}
                    </a>
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
                    <div class="admin-stat-value" data-countup data-target="{{ (int)$users->total() }}">{{ $users->total() }}</div>
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
                    <div class="admin-stat-value" data-countup data-target="{{ (int)$users->where('approved_at', '!=', null)->count() }}">{{ $users->where('approved_at', '!=', null)->count() }}</div>
                    <div class="admin-stat-label">{{ __('Approved') }}</div>
                    <div class="admin-stat-description">{{ __('Verified users') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-up">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                        </svg>
                        +{{ number_format((($users->where('approved_at', '!=', null)->count() / max($users->total(), 1)) * 100), 1) }}%
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
                    <div class="admin-stat-value" data-countup data-target="{{ (int)$users->where('approved_at', null)->count() }}">{{ $users->where('approved_at', null)->count() }}</div>
                    <div class="admin-stat-label">{{ __('Pending') }}</div>
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
                            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 3v18h18M7 12l3-3 3 3 5-5" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)$users->where('role', 'vendor')->count() }}">{{ $users->where('role', 'vendor')->count() }}</div>
                    <div class="admin-stat-label">{{ __('Vendors') }}</div>
                    <div class="admin-stat-description">{{ __('Active vendors') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <span class="admin-stat-trend admin-stat-trend-up">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 3v18h18M7 12l3-3 3 3 5-5" />
                        </svg>
                        {{ __('Active') }}
                    </span>
                </div>
            </div>
        </div>



        <!-- Users Table -->
        <div class="card modern-card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <h3 class="card-title mb-0">{{ __('Users List') }}</h3>
                <div class="card-actions">
                    <div class="bulk-actions d-flex flex-column flex-sm-row gap-2" id="bulkActions">
                        <span class="selected-count text-muted">0</span> <span class="text-muted d-none d-sm-inline">{{ __('selected') }}</span>
                        <button type="button" class="btn btn-sm btn-success" data-action="bulk-approve">
                            <i class="fas fa-check"></i>
                            <span class="d-none d-md-inline">{{ __('Approve') }}</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-action="bulk-delete">
                            <i class="fas fa-trash"></i>
                            <span class="d-none d-md-inline">{{ __('Delete') }}</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="30"><input type="checkbox" id="select-all"></th>
                                <th>{{ __('User') }}</th>
                                <th class="d-none d-md-table-cell">{{ __('Role') }}</th>
                                <th class="d-none d-lg-table-cell">{{ __('Status') }}</th>
                                <th class="d-none d-lg-table-cell">{{ __('Balance') }}</th>
                                <th class="d-none d-xl-table-cell">{{ __('Phone') }}</th>
                                <th class="d-none d-lg-table-cell">{{ __('Joined') }}</th>
                                <th width="150">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $user->id }}">
                                </td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="user-name">{{ $user->name }}</div>
                                            <div class="user-email">{{ $user->email }}</div>
                                            <div class="d-md-none mt-1">
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
                                                @if($user->approved_at)
                                                <span class="badge bg-success ms-1">
                                                    <i class="fas fa-check"></i>
                                                    {{ __('Approved') }}
                                                </span>
                                                @else
                                                <span class="badge bg-warning ms-1">
                                                    <i class="fas fa-clock"></i>
                                                    {{ __('Pending') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
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
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    @if($user->approved_at)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i>
                                        {{ __('Approved') }}
                                    </span>
                                    @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock"></i>
                                        {{ __('Pending') }}
                                    </span>
                                    @endif
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <span class="text-success">
                                        ${{ number_format($user->balance ?? 0, 2) }}
                                    </span>
                                </td>
                                <td class="d-none d-xl-table-cell">{{ $user->phone ?? '-' }}</td>
                                <td class="d-none d-lg-table-cell">{{ $user->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="btn-group d-flex flex-column flex-sm-row">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-secondary mb-1 mb-sm-0" title="{{ __('View') }}">
                                            <i class="fas fa-eye"></i>
                                            <span class="d-sm-none ms-1">{{ __('View') }}</span>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary mb-1 mb-sm-0" title="{{ __('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                            <span class="d-sm-none ms-1">{{ __('Edit') }}</span>
                                        </a>
                                        @if(!$user->approved_at)
                                        <form method="POST" action="{{ route('admin.users.approve', $user) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success mb-1 mb-sm-0" title="{{ __('Approve') }}">
                                                <i class="fas fa-check"></i>
                                                <span class="d-sm-none ms-1">{{ __('Approve') }}</span>
                                            </button>
                                        </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline delete-form">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete this user?') }}" data-confirm="{{ __('Delete this user?') }}">
                                                <i class="fas fa-trash"></i>
                                                <span class="d-sm-none ms-1">{{ __('Delete') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="pagination-info">
                        {{ __('Showing') }} {{ $users->firstItem() }} {{ __('to') }} {{ $users->lastItem() }}
                        {{ __('of') }} {{ $users->total() }} {{ __('results') }}
                    </div>
                    {{ $users->links() }}
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-users fa-3x"></i>
                    <h3>{{ __('No Users Found') }}</h3>
                    <p>{{ __('No users match your current filters. Try adjusting your search criteria.') }}</p>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        {{ __('Add First User') }}
                    </a>
                </div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection