@extends('layouts.admin')

@section('title', __('Vendors Report'))

@section('content')
<div class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="admin-order-title">
                <h1>
                    <i class="fas fa-store"></i>
                    {{ __('Vendors Report') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Comprehensive vendors analysis and statistics') }}</p>
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
                        <li><a class="dropdown-item js-export" href="#" data-export-type="excel" data-report="vendors">
                                <i class="fas fa-file-excel"></i>
                                {{ __('Excel') }}
                            </a></li>
                        <li><a class="dropdown-item js-export" href="#" data-export-type="pdf" data-report="vendors">
                                <i class="fas fa-file-pdf"></i>
                                {{ __('PDF') }}
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card admin-stat-primary">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ (int)$stats['total'] }}">{{ number_format($stats['total']) }}</div>
                    <div class="admin-stat-label">{{ __('Total Vendors') }}</div>
                    <div class="admin-stat-description">{{ __('All registered vendors') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('All time') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ (int)$stats['active'] }}">{{ number_format($stats['active']) }}</div>
                    <div class="admin-stat-label">{{ __('Active Vendors') }}</div>
                    <div class="admin-stat-description">{{ number_format((($stats['active'] / max($stats['total'], 1)) * 100), 1) }}% {{ __('active') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('Active now') }}</span>
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
                    <div class="admin-stat-value" data-countup="{{ (int)$stats['pending'] }}">{{ number_format($stats['pending']) }}</div>
                    <div class="admin-stat-label">{{ __('Pending Vendors') }}</div>
                    <div class="admin-stat-description">{{ __('Awaiting approval') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <i class="fas fa-dot-circle"></i>
                        <span>{{ __('Pending review') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ number_format($stats['totalBalance'], 2) }}">${{ number_format($stats['totalBalance'], 2) }}</div>
                    <div class="admin-stat-label">{{ __('Total Balance') }}</div>
                    <div class="admin-stat-description">{{ __('Vendor earnings') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                            <path d="M17 9l-5 5-3-3-3 3" />
                        </svg>
                        <span>{{ __('Total revenue') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendors Table -->
        <div class="card modern-card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <h3 class="card-title mb-0">{{ __('Vendors List') }}</h3>
                <div class="card-actions">
                    <div class="bulk-actions d-flex flex-column flex-sm-row gap-2" id="bulkActions">
                        <span class="selected-count text-muted">0</span> <span class="text-muted d-none d-sm-inline">{{ __('selected') }}</span>
                        <button type="button" class="btn btn-sm btn-success" data-action="bulk-approve">
                            <i class="fas fa-check"></i>
                            <span class="d-none d-md-inline">{{ __('Approve') }}</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-action="bulk-export">
                            <i class="fas fa-download"></i>
                            <span class="d-none d-md-inline">{{ __('Export') }}</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="30"><input type="checkbox" id="select-all"></th>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th class="d-none d-md-table-cell">{{ __('Email') }}</th>
                                <th class="d-none d-lg-table-cell">{{ __('Balance') }}</th>
                                <th class="d-none d-lg-table-cell">{{ __('Status') }}</th>
                                <th class="d-none d-xl-table-cell">{{ __('Joined Date') }}</th>
                                <th width="150">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendors as $vendor)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $vendor->id }}">
                                </td>
                                <td><span class="admin-badge">{{ $vendor->id }}</span></td>
                                <td>
                                    <div class="admin-item-name">{{ $vendor->name }}</div>
                                    <div class="d-md-none mt-1">
                                        <div class="admin-text-muted">{{ $vendor->email }}</div>
                                        @if($vendor->approved_at)
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                        @else
                                        <span class="badge bg-warning">{{ __('Pending') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <div class="admin-text-muted">{{ $vendor->email }}</div>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <div class="admin-stock-value">${{ number_format($vendor->balance, 2) }}</div>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    @if($vendor->approved_at)
                                    <span class="admin-status-badge admin-status-badge-completed">{{ __('Active') }}</span>
                                    @else
                                    <span class="admin-status-badge admin-status-badge-warning">{{ __('Pending') }}</span>
                                    @endif
                                </td>
                                <td class="d-none d-xl-table-cell">
                                    <div class="admin-text-muted">{{ $vendor->created_at->format('Y-m-d') }}</div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.show', $vendor->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                        <span class="d-none d-md-inline">{{ __('View') }}</span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="admin-empty-state">
                                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" class="admin-notification-icon">
                                            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <h3>{{ __('No vendors found') }}</h3>
                                        <p>{{ __('No vendors match your current filters') }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($vendors->hasPages())
                <div class="admin-card-footer-pagination">
                    <div class="pagination-info">
                        {{ __('Showing') }} {{ $vendors->firstItem() }} {{ __('to') }} {{ $vendors->lastItem() }} {{ __('of') }} {{ $vendors->total() }} {{ __('results') }}
                    </div>
                    <div class="pagination-links">
                        {{ $vendors->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endsection