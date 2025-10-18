@extends('layouts.admin')

@section('title', __('Vendors Report'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title mb-1">{{ __('Vendors Report') }}</h1>
            <p class="text-muted mb-0">{{ __('Comprehensive vendors analysis and statistics') }}</p>
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
                        <li><a class="dropdown-item js-export" href="#" data-export-type="excel" data-report="vendors">{{ __('Excel') }}</a>
                        </li>
                        <li><a class="dropdown-item js-export" href="#" data-export-type="pdf" data-report="vendors">{{ __('PDF') }}</a>
                        </li>
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
                        <div class="stats-number" data-countup data-target="{{ (int)$stats['total'] }}">{{ number_format($stats['total']) }}</div>
                        <div class="stats-label">{{ __('Total Vendors') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-store text-primary"></i>
                            <span class="text-primary">{{ __('All registered vendors') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-store"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-success h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="{{ (int)$stats['active'] }}">{{ number_format($stats['active']) }}</div>
                        <div class="stats-label">{{ __('Active Vendors') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-arrow-up text-success"></i>
                            <span class="text-success">{{ number_format((($stats['active'] / max($stats['total'], 1)) * 100), 1) }}% {{ __('active') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-warning h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="{{ (int)$stats['pending'] }}">{{ number_format($stats['pending']) }}</div>
                        <div class="stats-label">{{ __('Pending Vendors') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-clock text-warning"></i>
                            <span class="text-warning">{{ __('Awaiting approval') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-info h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number">${{ number_format($stats['totalBalance'], 2) }}</div>
                        <div class="stats-label">{{ __('Total Balance') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-dollar-sign text-info"></i>
                            <span class="text-info">{{ __('Vendor earnings') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-dollar-sign"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendors Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Vendors List') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Balance') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Joined Date') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                        <tr>
                            <td>{{ $vendor->id }}</td>
                            <td>{{ $vendor->name }}</td>
                            <td>{{ $vendor->email }}</td>
                            <td>${{ number_format($vendor->balance, 2) }}</td>
                            <td>
                                @if($vendor->approved_at)
                                <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                <span class="badge bg-warning">{{ __('Pending') }}</span>
                                @endif
                            </td>
                            <td>{{ $vendor->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.users.show', $vendor->id) }}"
                                        class="btn btn-sm btn-outline-secondary" title="{{ __('View') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>{{ __('No vendors found') }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($vendors->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $vendors->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
