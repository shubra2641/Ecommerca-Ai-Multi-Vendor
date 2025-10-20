@extends('layouts.admin')

@section('title', __('User Balance'))
@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    {{ __('User Balance Management') }} - {{ $user->name }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Manage balance for') }} {{ $user->name }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.users.show', $user) }}" class="admin-btn admin-btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back to User') }}
                </a>
                <button type="button" class="admin-btn admin-btn-primary btn-view-history">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('View History') }}
                </button>
            </div>
        </div>

        <!-- Current Balance Card -->
        <div class="admin-modern-card admin-mb-1-5" data-user-id="{{ $user->id }}">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    {{ __('Current Balance') }}
                </h2>
                <div class="admin-balance-actions">
                    <a href="{{ route('admin.users.balance', $user) }}" class="admin-btn admin-btn-small admin-btn-secondary">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ __('Refresh') }}
                    </a>
                    <a href="{{ route('admin.users.balance.history', $user) }}" class="admin-btn admin-btn-small admin-btn-primary">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('View History') }}
                    </a>
                </div>
            </div>
            <div class="admin-card-body">
                <div class="admin-balance-display">
                    <div class="admin-balance-amount">
                        <span class="admin-balance-value" data-countup data-target="{{ $user->balance ?? 0 }}">
                            {{ number_format($user->balance ?? 0, 2) }}
                        </span>
                        <span class="admin-balance-currency">{{ $defaultCurrency ? $defaultCurrency->symbol : 'USD' }}</span>
                    </div>
                    <div class="admin-balance-info">
                        <span class="admin-balance-date">{{ __('As of') }} {{ $user->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="admin-card-footer">
                <div class="admin-stats-grid">
                    <div class="admin-stat-item admin-stat-primary">
                        <div class="admin-stat-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="admin-stat-content">
                            <div class="admin-stat-value" data-countup data-target="{{ $balanceStats['total_added'] ?? 0 }}">
                                {{ number_format($balanceStats['total_added'] ?? 0, 2) }} {{ $defaultCurrency ? $defaultCurrency->symbol : 'USD' }}
                            </div>
                            <div class="admin-stat-label">{{ __('Total Added') }}</div>
                        </div>
                    </div>
                    <div class="admin-stat-item admin-stat-info">
                        <div class="admin-stat-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M5 12h14" />
                            </svg>
                        </div>
                        <div class="admin-stat-content">
                            <div class="admin-stat-value" data-countup data-target="{{ $balanceStats['total_deducted'] ?? 0 }}">
                                {{ number_format($balanceStats['total_deducted'] ?? 0, 2) }} {{ $defaultCurrency ? $defaultCurrency->symbol : 'USD' }}
                            </div>
                            <div class="admin-stat-label">{{ __('Total Deducted') }}</div>
                        </div>
                    </div>
                    <div class="admin-stat-item admin-stat-success">
                        <div class="admin-stat-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M3 3v18h18M7 12l3-3 3 3 5-5" />
                            </svg>
                        </div>
                        <div class="admin-stat-content">
                            <div class="admin-stat-value" data-countup data-target="{{ $balanceStats['net_balance_change'] ?? 0 }}">
                                {{ number_format($balanceStats['net_balance_change'] ?? 0, 2) }} {{ $defaultCurrency ? $defaultCurrency->symbol : 'USD' }}
                            </div>
                            <div class="admin-stat-label">{{ __('Net Change') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions Preview -->
        <div class="admin-modern-card admin-mb-1-5">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Recent Transactions') }}
                </h2>
                <button type="button" class="admin-btn admin-btn-small admin-btn-primary btn-view-history">
                    {{ __('View All') }}
                </button>
            </div>
            <div class="admin-card-body">
                <div class="admin-empty-state">
                    <div class="admin-notification-icon">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p>{{ __('No transactions yet.') }}</p>
                </div>
            </div>
        </div>

        <!-- User Summary -->
        <div class="admin-modern-card admin-mb-1-5">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ __('User Summary') }}
                </h2>
            </div>
            <div class="admin-card-body">
                <div class="admin-user-profile">
                    <div class="admin-user-avatar">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="admin-user-info">
                        <h3 class="admin-user-name">{{ $user->name }}</h3>
                        <p class="admin-user-email">{{ $user->email }}</p>
                        <span class="admin-status-badge admin-status-badge-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'vendor' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                </div>
                <div class="admin-user-details">
                    <div class="admin-info-item">
                        <div class="admin-info-label">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" />
                            </svg>
                            {{ __('Phone') }}
                        </div>
                        <div class="admin-info-value">{{ $user->phone ?? __('Not provided') }}</div>
                    </div>
                    <div class="admin-info-item">
                        <div class="admin-info-label">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ __('Registered') }}
                        </div>
                        <div class="admin-info-value">{{ $user->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="admin-info-item">
                        <div class="admin-info-label">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('Last Updated') }}
                        </div>
                        <div class="admin-info-value">{{ $user->updated_at->diffForHumans() }}</div>
                    </div>
                    <div class="admin-info-item">
                        <div class="admin-info-label">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            {{ __('Status') }}
                        </div>
                        <div class="admin-info-value">
                            @if($user->approved_at)
                            <span class="admin-status-badge admin-status-badge-completed">{{ __('Approved') }}</span>
                            @else
                            <span class="admin-status-badge admin-status-badge-warning">{{ __('Pending') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    {{ __('Quick Actions') }}
                </h2>
            </div>
            <div class="admin-card-body">
                <div class="admin-actions-grid">
                    <a href="{{ route('admin.users.edit', $user) }}" class="admin-action-btn">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>{{ __('Edit User') }}</span>
                    </a>
                    <a href="{{ route('admin.users.balance.history', $user) }}" class="admin-action-btn">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ __('View Balance History') }}</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="admin-action-btn">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span>{{ __('All Users') }}</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Add Balance Modal -->
<div class="modal fade" id="addBalanceModal" tabindex="-1" aria-labelledby="addBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBalanceModalLabel">
                    <i class="fas fa-plus-circle text-success me-2"></i>
                    {{ __('Add Balance') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBalanceForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addAmount">{{ __('Amount') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" max="999999" class="form-control" id="addAmount"
                                name="amount" required placeholder="0.00">
                            <div class="input-group-append">
                                <span
                                    class="input-group-text">{{ $defaultCurrency ? $defaultCurrency->symbol : 'USD' }}</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">{{ __('Enter the amount to add to user balance') }}</small>
                    </div>
                    <div class="form-group">
                        <label for="addReason">{{ __('Reason') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="addReason" name="note" rows="3" required
                            placeholder="{{ __('Enter reason for adding balance...') }}"></textarea>
                        <small class="form-text text-muted">{{ __('Minimum 3 characters required') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>
                        {{ __('Add Balance') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Deduct Balance Modal -->
<div class="modal fade" id="deductBalanceModal" tabindex="-1" aria-labelledby="deductBalanceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deductBalanceModalLabel">
                    <i class="fas fa-minus-circle text-warning me-2"></i>
                    {{ __('Deduct Balance') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deductBalanceForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group alert alert-warning mb-3">
                        {{ __('Note: Deducting balance cannot be undone') }}
                    </div>
                    <div class="form-group">
                        <label for="deductAmount">{{ __('Amount') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" max="{{ $user->balance ?? 0 }}"
                                id="deductAmount" name="amount" class="form-control" required placeholder="0.00">
                            <div class="input-group-append">
                                <span
                                    class="input-group-text">{{ $defaultCurrency ? $defaultCurrency->symbol : 'USD' }}</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">{{ __('Maximum amount you can deduct') }}:
                            {{ number_format($user->balance ?? 0, 2) }}
                            {{ $defaultCurrency ? $defaultCurrency->symbol : 'USD' }}</small>
                    </div>
                    <div class="form-group">
                        <label for="deductReason">{{ __('Reason') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="deductReason" name="note" rows="3" required
                            placeholder="{{ __('Enter reason for deducting balance...') }}"></textarea>
                        <small class="form-text text-muted">{{ __('Minimum 3 characters required') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-minus me-1"></i>
                        {{ __('Deduct Balance') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Balance History Modal -->
<div class="modal fade" id="balanceHistoryModal" tabindex="-1" aria-labelledby="balanceHistoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="balanceHistoryModalLabel">
                    <i class="fas fa-history text-info me-2"></i>
                    {{ __('Balance History') }} - {{ $user->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="balanceHistoryContainer">
                    <div class="text-center p-4">
                        <div class="loading-spinner mx-auto"></div>
                        <p class="mt-2">{{ __('Loading history...') }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

<template id="user-balance-config">{!! json_encode([
    'urls' => [
    'add' => route('admin.users.add-balance', $user),
    'deduct' => route('admin.users.deduct-balance', $user),
    'stats' => route('admin.users.balance.stats', $user),
    'history' => route('admin.users.balance.history', $user),
    'refresh' => route('admin.users.balance.refresh', $user),
    ],
    'currency' => [
    'code' => $defaultCurrency?->code ?? 'USD',
    'symbol' => $defaultCurrency?->symbol ?? '$'
    ],
    'i18n' => [
    'credit' => __('Credit'),
    'debit' => __('Debit'),
    'balance_added' => __('Balance added successfully'),
    'balance_deducted' => __('Balance deducted successfully'),
    'balance_invalid_add' => __('Please enter a valid amount and a reason'),
    'balance_invalid_deduct' => __('Please enter a valid amount and a reason'),
    'balance_exceeds' => __('Amount exceeds current balance'),
    'balance_refreshed' => __('Data refreshed successfully'),
    'loading_refresh' => __('Refreshing data...'),
    'loading_history' => __('Loading history...'),
    'error' => __('Error'),
    'error_add' => __('Error while adding balance'),
    'error_deduct' => __('Error while deducting balance'),
    'error_refresh' => __('Error while refreshing data'),
    'error_history' => __('Failed to load balance history'),
    'error_server' => __('Server communication error'),
    'no_history' => __('No history'),
    'no_history_desc' => __('No previous transactions found'),
    'not_specified' => __('Not specified'),
    'processing' => __('Processing...'),
    ]
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</template>