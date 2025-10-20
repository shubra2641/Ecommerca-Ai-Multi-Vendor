@extends('layouts.admin')

@section('title', __('Balance History'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Balance History') }} - {{ $user->name }}
                </h1>
                <p class="admin-order-subtitle">{{ __('View all balance transactions for this user') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.users.balance', $user) }}" class="admin-btn admin-btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back to Balance') }}
                </a>
                <a href="{{ route('admin.users.show', $user) }}" class="admin-btn admin-btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ __('View User') }}
                </a>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Transaction History') }}
                </h2>
                <span class="admin-badge-count">{{ $balanceHistories->count() }}</span>
            </div>
            <div class="admin-card-body">
                @if($balanceHistories->count() > 0)
                <div class="admin-items-list">
                    @foreach($balanceHistories as $transaction)
                    <div class="admin-item-card">
                        <div class="admin-item-main">
                            <div class="admin-item-placeholder admin-item-placeholder-{{ $transaction->type === 'credit' ? 'success' : 'warning' }}">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    @if($transaction->type === 'credit')
                                    <path d="M12 4v16m8-8H4" />
                                    @else
                                    <path d="M5 12h14" />
                                    @endif
                                </svg>
                            </div>
                            <div class="admin-item-details">
                                <h3 class="admin-item-name">
                                    {{ $transaction->type === 'credit' ? __('Credit') : __('Debit') }} - {{ number_format($transaction->amount, 2) }}
                                </h3>
                                <div class="admin-payment-details admin-mt-half">
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $transaction->created_at->format('M d, Y H:i') }}
                                    </span>
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        {{ __('Previous') }}: {{ number_format($transaction->previous_balance, 2) }}
                                    </span>
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        {{ __('New') }}: {{ number_format($transaction->new_balance, 2) }}
                                    </span>
                                </div>
                                @if($transaction->note)
                                <div class="admin-item-meta admin-mt-half">
                                    <span class="payment-detail-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        {{ $transaction->note }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="admin-item-meta">
                            <span class="admin-status-badge admin-status-badge-{{ $transaction->type === 'credit' ? 'completed' : 'warning' }}">
                                {{ $transaction->type === 'credit' ? __('Credit') : __('Debit') }}
                            </span>
                            <div class="admin-actions-flex">
                                <div class="admin-transaction-amount {{ $transaction->type === 'credit' ? 'admin-amount-credit' : 'admin-amount-debit' }}">
                                    {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="admin-empty-state">
                    <div class="admin-notification-icon">
                        <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p>{{ __('This user has no balance transactions yet.') }}</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection