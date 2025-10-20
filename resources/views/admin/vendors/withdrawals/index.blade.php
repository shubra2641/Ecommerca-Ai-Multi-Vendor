@extends('layouts.admin')

@section('title', __('Vendor Withdrawals'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    {{ __('Vendor Withdrawals') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Manage vendor withdrawal requests') }}</p>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="admin-modern-card admin-mb-1-5">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    {{ __('Filter') }}
                </h2>
            </div>
            <div class="admin-card-body">
                <form method="GET" class="admin-form-filter">
                    <div class="admin-form-group">
                        <label class="form-check">
                            <input type="checkbox" name="held" value="1" class="form-check-input" {{ ($heldOnly ?? false) ? 'checked' : '' }}>
                            <span class="form-check-label">{{ __('Held Only') }}</span>
                        </label>
                    </div>
                    <div class="admin-actions-flex">
                        <button class="admin-btn admin-btn-small admin-btn-primary" type="submit">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            {{ __('Filter') }}
                        </button>
                        @if(($heldOnly ?? false))
                        <a href="{{ route('admin.vendor.withdrawals.index') }}" class="admin-btn admin-btn-small admin-btn-secondary">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            {{ __('Clear') }}
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Withdrawals List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">{{ __('Withdrawals List') }}</h2>
                <span class="admin-badge-count">{{ $withdrawals->total() }}</span>
            </div>

            <div class="admin-items-list">
                @forelse($withdrawals as $w)
                <div class="admin-item-card">
                    <div class="admin-item-main">
                        <div class="admin-item-placeholder admin-item-placeholder-warning">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="admin-item-details">
                            <h3 class="admin-item-name">{{ $w->user?->name ?? __('Unknown User') }}</h3>
                            <div class="admin-payment-details admin-mt-half">
                                <span class="payment-detail-item">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('Amount') }}: <strong>{{ number_format($w->amount, 2) }} {{ $w->currency }}</strong>
                                </span>
                                @if($w->commission_amount_exact)
                                <span class="payment-detail-separator">•</span>
                                <span class="payment-detail-item">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    {{ __('Commission') }}: <strong>{{ number_format($w->commission_amount_exact, 4) }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="admin-item-meta admin-mt-half">
                                @if($w->held_at)
                                <span class="payment-detail-item">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('Held') }}: {{ $w->held_at->format('Y-m-d H:i') }}
                                </span>
                                @endif
                                <span class="payment-detail-separator">•</span>
                                <span class="payment-detail-item">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ __('Requested') }}: {{ $w->created_at->format('Y-m-d H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="admin-item-meta">
                        <span class="admin-status-badge admin-status-badge-{{ $w->status === 'approved' ? 'completed' : ($w->status === 'rejected' ? 'cancelled' : 'warning') }}">
                            {{ ucfirst($w->status) }}
                        </span>
                        <div class="admin-actions-flex">
                            <a href="{{ route('admin.vendor.withdrawals.show', $w) }}" class="admin-btn admin-btn-small admin-btn-secondary">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                {{ __('View') }}
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="admin-empty-state">
                    <div class="admin-notification-icon">
                        <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <p>{{ __('No withdrawals found') }}</p>
                </div>
                @endforelse
            </div>

            @if($withdrawals->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ __('Showing :from to :to of :total results', ['from' => $withdrawals->firstItem(), 'to' => $withdrawals->lastItem(), 'total' => $withdrawals->total()]) }}
                </div>
                <div class="pagination-links">
                    {{ $withdrawals->links() }}
                </div>
            </div>
            @endif
        </div>

    </div>
</section>
@endsection