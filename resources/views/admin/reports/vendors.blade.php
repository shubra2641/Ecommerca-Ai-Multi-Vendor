@extends('layouts.admin')

@section('title', __('Vendors Report'))

@section('content')
<div class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="admin-order-title">
                <h1>
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    {{ __('Vendors Report') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Comprehensive vendors analysis and statistics') }}</p>
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
                        <li><a class="dropdown-item js-export" href="#" data-export-type="excel" data-report="vendors">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ __('Excel') }}
                            </a></li>
                        <li><a class="dropdown-item js-export" href="#" data-export-type="pdf" data-report="vendors">
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

        <!-- Statistics Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card admin-stat-primary">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ (int)$stats['total'] }}">{{ number_format($stats['total']) }}</div>
                    <div class="admin-stat-label">{{ __('Total Vendors') }}</div>
                    <div class="admin-stat-description">{{ __('All registered vendors') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                            <path d="M17 9l-5 5-3-3-3 3" />
                        </svg>
                        <span>{{ __('All time') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ (int)$stats['active'] }}">{{ number_format($stats['active']) }}</div>
                    <div class="admin-stat-label">{{ __('Active Vendors') }}</div>
                    <div class="admin-stat-description">{{ number_format((($stats['active'] / max($stats['total'], 1)) * 100), 1) }}% {{ __('active') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                            <path d="M17 9l-5 5-3-3-3 3" />
                        </svg>
                        <span>{{ __('Active now') }}</span>
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
                    <div class="admin-stat-value" data-countup="{{ (int)$stats['pending'] }}">{{ number_format($stats['pending']) }}</div>
                    <div class="admin-stat-label">{{ __('Pending Vendors') }}</div>
                    <div class="admin-stat-description">{{ __('Awaiting approval') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>{{ __('Pending review') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 1v6m0 0l3-3m-3 3l-3-3m6 9v6m0 0l3 3m-3-3l3 3M5 12H1m0 0l3 3m-3-3l3-3m18 0h-4m0 0l3 3m-3-3l3-3" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
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