@extends('layouts.admin')

@section('title', __('Users Management'))

@section('content')
@include('admin.partials.page-header', [
    'title' => __('Users Management'),
    'subtitle' => __('Manage all users and their permissions'),
    'actions' => '<a href="'.route('admin.users.create').'" class="btn btn-primary"><i class="fas fa-plus"></i> '.e(__('Add New User')).'</a> <a href="'.route('admin.users.export').'" class="btn btn-secondary"><i class="fas fa-download"></i> '.e(__('Export')).'</a>'
])

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card modern-card stats-card stats-card-primary h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="{{ (int)$users->total() }}">{{ $users->total() }}</div>
                    <div class="stats-label">{{ __('Total Users') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-arrow-up text-success"></i>
                        <span class="text-success">{{ __('from last month') }}</span>
                    </div>
                </div>
                <div class="stats-icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card modern-card stats-card stats-card-success h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="{{ (int)$users->where('approved_at', '!=', null)->count() }}">{{ $users->where('approved_at', '!=', null)->count() }}</div>
                    <div class="stats-label">{{ __('Approved') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-arrow-up text-success"></i>
                        <span class="text-success">+{{ number_format((($users->where('approved_at', '!=', null)->count() / max($users->total(), 1)) * 100), 1) }}%</span>
                    </div>
                </div>
                <div class="stats-icon"><i class="fas fa-user-check"></i></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card modern-card stats-card stats-card-warning h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="{{ (int)$users->where('approved_at', null)->count() }}">{{ $users->where('approved_at', null)->count() }}</div>
                    <div class="stats-label">{{ __('Pending') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-clock text-muted"></i>
                        <span class="text-muted">{{ __('Awaiting approval') }}</span>
                    </div>
                </div>
                <div class="stats-icon"><i class="fas fa-user-clock"></i></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card modern-card stats-card stats-card-info h-100">
            <div class="stats-card-body">
                <div class="stats-card-content">
                    <div class="stats-number" data-countup data-target="{{ (int)$users->where('role', 'vendor')->count() }}">{{ $users->where('role', 'vendor')->count() }}</div>
                    <div class="stats-label">{{ __('Vendors') }}</div>
                    <div class="stats-trend">
                        <i class="fas fa-chart-line text-success"></i>
                        <span class="text-success">{{ __('Active vendors') }}</span>
                    </div>
                </div>
                <div class="stats-icon"><i class="fas fa-store"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card modern-card">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="filters-form">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="form-group mb-3">
                        <label for="search" class="form-label">{{ __('Search') }}</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" 
                               class="form-control" placeholder="{{ __('Search by name, email...') }}">
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="form-group mb-3">
                        <label for="role" class="form-label">{{ __('Role') }}</label>
                        <select id="role" name="role" class="form-select">
                            <option value="">{{ __('All Roles') }}</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                            <option value="vendor" {{ request('role') === 'vendor' ? 'selected' : '' }}>{{ __('Vendor') }}</option>
                            <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>{{ __('Customer') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="form-group mb-3">
                        <label for="status" class="form-label">{{ __('Status') }}</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="form-group mb-3">
                        <label for="per_page" class="form-label">{{ __('Per Page') }}</label>
                        <select id="per_page" name="per_page" class="form-select">
                            <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 col-md-9 col-lg-3">
                    <div class="form-group mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <button type="submit" class="btn btn-primary w-100 w-sm-auto">
                                <i class="fas fa-search"></i>
                                <span class="d-none d-sm-inline">{{ __('Filter') }}</span>
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100 w-sm-auto">
                                <i class="fas fa-times"></i>
                                <span class="d-none d-sm-inline">{{ __('Clear') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
@endsection