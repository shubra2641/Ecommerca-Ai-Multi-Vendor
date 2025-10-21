@extends('vendor.layout')

@section('title', __('vendor.withdrawals.title'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('vendor.withdrawals.heading') }}</h1>
                        <p class="admin-order-subtitle">{{ __('vendor.withdrawals.subtitle') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <button type="button" class="admin-btn admin-btn-secondary" data-action="reload" data-tooltip="{{ __('vendor.withdrawals.refresh_tooltip') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 12A9 9 0 0 1 12 3A9 9 0 0 1 21 12A9 9 0 0 1 12 21A9 9 0 0 1 3 12Z" stroke="currentColor" stroke-width="2" />
                        <path d="M12 3V12L16 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('vendor.withdrawals.refresh') }}
                </button>
                <a href="{{ route('vendor.withdrawals.create') }}" class="admin-btn admin-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('vendor.withdrawals.new') }}
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 3V21H21V3H3ZM19 19H5V5H19V19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value">{{ number_format($totalWithdrawn, 2) }} {{ $currency }}</div>
                    <div class="admin-stat-label">{{ __('vendor.withdrawals.total_withdrawn') }}</div>
                    <div class="admin-stat-description">{{ __('Total amount withdrawn') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ __('Total') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value">{{ number_format($pendingAmount, 2) }} {{ $currency }}</div>
                    <div class="admin-stat-label">{{ __('vendor.withdrawals.pending_amount') }}</div>
                    <div class="admin-stat-description">{{ __('Awaiting approval') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-warning">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 9V13M12 17H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ __('Pending') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value">{{ number_format($approvedThisMonth, 2) }} {{ $currency }}</div>
                    <div class="admin-stat-label">{{ __('vendor.withdrawals.approved_this_month') }}</div>
                    <div class="admin-stat-description">{{ __('This month approved') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ __('Approved') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 3H2L10 12.46V19L14 21V12.46L22 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Filters') }}
                </h3>
            </div>
            <form method="GET" action="{{ route('vendor.withdrawals.index') }}" class="admin-card-body" autocomplete="off">
                <div class="admin-filter-grid">
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Search') }}</label>
                        <input type="text" name="search" class="admin-form-input" placeholder="{{ __('vendor.withdrawals.search_placeholder') }}" value="{{ request('search') }}">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Status') }}</label>
                        <select name="status" class="admin-form-input">
                            <option value="">{{ __('vendor.withdrawals.status_all') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('vendor.withdrawals.status_pending') }}</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('vendor.withdrawals.status_approved') }}</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('vendor.withdrawals.status_completed') }}</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('vendor.withdrawals.status_rejected') }}</option>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('From Date') }}</label>
                        <input type="date" name="date_from" class="admin-form-input" value="{{ request('date_from') }}">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Held Only') }}</label>
                        <div class="admin-form-check">
                            <input type="checkbox" name="held" value="1" class="admin-form-check-input" {{ ($heldOnly ?? false) ? 'checked' : '' }}>
                            <label class="admin-form-check-label">{{ __('Show held withdrawals only') }}</label>
                        </div>
                    </div>

                    <div class="admin-filter-actions">
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" />
                                <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ __('vendor.withdrawals.apply_filters') }}
                        </button>
                        <a href="{{ route('vendor.withdrawals.index') }}" class="admin-btn admin-btn-secondary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ __('vendor.withdrawals.clear_all') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Withdrawals Table -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Withdrawals List') }}
                </h3>
                <div class="admin-badge-count">{{ $withdrawals->count() }} {{ __('withdrawals') }}</div>
            </div>
            <div class="admin-card-body">
                @if($withdrawals->count() > 0)
                <div class="admin-table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('vendor.withdrawals.col_amount') }}
                                </th>
                                <th>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 3V21H21V3H3ZM19 19H5V5H19V19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('Gross') }}
                                </th>
                                <th>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('Commission') }}
                                </th>
                                <th>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('vendor.withdrawals.col_status') }}
                                </th>
                                <th>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('vendor.withdrawals.col_request_date') }}
                                </th>
                                <th>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('vendor.withdrawals.col_payment_method') }}
                                </th>
                                <th>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('vendor.withdrawals.col_reference') }}
                                </th>
                                <th>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('vendor.withdrawals.col_actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($withdrawals as $withdrawal)
                            <tr>
                                <td>
                                    <div class="admin-fw-semibold">{{ number_format($withdrawal->amount, 2) }} {{ $withdrawal->currency }}</div>
                                </td>
                                <td>
                                    @if(!is_null($withdrawal->gross_amount))
                                    <span class="admin-text-muted small">{{ number_format($withdrawal->gross_amount,2) }}</span>
                                    @else
                                    <span class="admin-text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!is_null($withdrawal->commission_amount) && $withdrawal->commission_amount > 0)
                                    <span class="admin-text-danger small">-{{ number_format($withdrawal->commission_amount,2) }}</span>
                                    @else
                                    <span class="admin-text-muted">0.00</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="admin-badge admin-badge-{{ $withdrawal->status === 'pending' ? 'warning' : ($withdrawal->status === 'completed' ? 'success' : ($withdrawal->status === 'rejected' ? 'danger' : 'info')) }}">
                                        @if($withdrawal->status === 'pending')
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        @elseif($withdrawal->status === 'approved')
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        @elseif($withdrawal->status === 'completed')
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        @elseif($withdrawal->status === 'rejected')
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        @endif
                                        {{ __('vendor.withdrawals.status_' . $withdrawal->status, [], null) ?: ucfirst($withdrawal->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="admin-fw-semibold">{{ $withdrawal->created_at->format('M d, Y') }}</div>
                                    <div class="admin-text-muted small">{{ $withdrawal->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <div class="admin-text-muted">
                                        @if($withdrawal->payment_method === 'bank_transfer')
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        @else
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        @endif
                                        {{ __('vendor.withdrawals.payment_' . $withdrawal->payment_method, [], null) ?: ucfirst(str_replace('_', ' ', $withdrawal->payment_method)) }}
                                    </div>
                                </td>
                                <td>
                                    @if($withdrawal->reference)
                                    <code class="admin-badge admin-badge-secondary">{{ $withdrawal->reference }}</code>
                                    @else
                                    <span class="admin-text-muted">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="admin-btn-group">
                                        <button class="admin-btn admin-btn-sm admin-btn-outline" data-action="view-withdrawal" data-id="{{ $withdrawal->id }}" data-tooltip="View Details">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                        @if($withdrawal->reference)
                                        <button class="admin-btn admin-btn-sm admin-btn-outline" data-action="copy" data-copy="{{ $withdrawal->reference }}" data-tooltip="Copy Reference">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M5 15H4A2 2 0 0 1 2 13V4A2 2 0 0 1 4 2H13A2 2 0 0 1 15 4V5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                        @endif
                                        @if($withdrawal->status === 'completed')
                                        <a href="{{ route('vendor.withdrawals.receipt', $withdrawal->id) }}" class="admin-btn admin-btn-sm admin-btn-outline-success" data-tooltip="Download Receipt">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M21 15V19A2 2 0 0 1 19 21H5A2 2 0 0 1 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <!-- Empty State -->
                <div class="admin-empty-state">
                    <div class="admin-notification-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h4>{{ __('vendor.withdrawals.empty_title') }}</h4>
                    <p class="admin-text-muted">{{ __('vendor.withdrawals.empty_subtitle') }}</p>
                    <a href="{{ route('vendor.withdrawals.create') }}" class="admin-btn admin-btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('vendor.withdrawals.create_first') }}
                    </a>
                </div>
                @endif
            </div>

            @if($withdrawals->hasPages())
            <div class="admin-card-footer d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div class="admin-text-muted small">{{ __('Showing') }} {{ $withdrawals->firstItem() }} - {{ $withdrawals->lastItem() }} {{ __('of') }} {{ $withdrawals->total() }}</div>
                <div class="admin-pagination-links">{{ $withdrawals->appends(request()->query())->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</section>

@endsection