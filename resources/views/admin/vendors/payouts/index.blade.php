@extends('layouts.admin')

@section('title', __('Payouts'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <i class="fas fa-money-bill-wave"></i>
                    {{ __('Payouts') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Manage vendor payouts and executions') }}</p>
            </div>
        </div>

        <!-- Payouts List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">{{ __('Payouts List') }}</h2>
                <span class="admin-badge-count">{{ $payouts->total() }}</span>
            </div>

            <div class="admin-items-list">
                @forelse($payouts as $p)
                <div class="admin-item-card">
                    <div class="admin-item-main">
                        <div class="admin-item-placeholder admin-item-placeholder-primary">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="admin-item-details">
                            <h3 class="admin-item-name">{{ $p->user?->name ?? __('Unknown User') }}</h3>
                            <div class="admin-payment-details admin-mt-half">
                                <span class="payment-detail-item">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    {{ __('ID') }}: <strong>#{{ $p->id }}</strong>
                                </span>
                                <span class="payment-detail-separator">•</span>
                                <span class="payment-detail-item">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('Amount') }}: <strong>{{ $p->amount }} {{ $p->currency }}</strong>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="admin-item-meta">
                        <span class="admin-status-badge admin-status-badge-{{ $p->status === 'executed' ? 'completed' : 'warning' }}">
                            {{ ucfirst($p->status) }}
                        </span>
                        <div class="admin-actions-flex">
                            @if($p->status === 'pending')
                            <form method="post" action="{{ route('admin.vendor.withdrawals.payouts.execute', $p) }}" class="d-inline-block">
                                @csrf
                                <button class="admin-btn admin-btn-small admin-btn-success">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ __('Execute') }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="admin-empty-state">
                    <div class="admin-notification-icon">
                        <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <p>{{ __('No payouts found') }}</p>
                </div>
                @endforelse
            </div>

            @if($payouts->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ __('Showing :from to :to of :total results', ['from' => $payouts->firstItem(), 'to' => $payouts->lastItem(), 'total' => $payouts->total()]) }}
                </div>
                <div class="pagination-links">
                    {{ $payouts->links() }}
                </div>
            </div>
            @endif
        </div>

    </div>
</section>
@endsection