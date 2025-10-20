@extends('layouts.admin')

@section('title', __('User Balances'))

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
                    {{ __('User Balances') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('View and export user balance information') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.balances.export', ['format' => 'xlsx']) }}" class="admin-btn admin-btn-success">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    {{ __('Export XLSX') }}
                </a>
                <a href="{{ route('admin.balances.export', ['format' => 'pdf']) }}" class="admin-btn admin-btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    {{ __('Export PDF') }}
                </a>
            </div>
        </div>

        <!-- Balances List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">{{ __('User Balances List') }}</h2>
                <span class="admin-badge-count">{{ $users->total() }}</span>
            </div>

            <div class="admin-items-list">
                @forelse($users as $user)
                <div class="admin-item-card">
                    <div class="admin-item-main">
                        <div class="admin-item-placeholder admin-item-placeholder-primary">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="admin-item-details">
                            <h3 class="admin-item-name">{{ $user->name }}</h3>
                            <div class="admin-payment-details admin-mt-half">
                                <span class="payment-detail-item">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ $user->email }}
                                </span>
                                <span class="payment-detail-separator">•</span>
                                <span class="payment-detail-item">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    {{ __('Role') }}: <strong>{{ ucfirst($user->role) }}</strong>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="admin-item-meta">
                        <div class="payment-amount-display">
                            <span class="admin-payment-amount-currency">{{ number_format($user->balance, 2) }}</span>
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
                    <p>{{ __('No users with balances found') }}</p>
                </div>
                @endforelse
            </div>

            @if($users->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ __('Showing :from to :to of :total results', ['from' => $users->firstItem(), 'to' => $users->lastItem(), 'total' => $users->total()]) }}
                </div>
                <div class="pagination-links">
                    {{ $users->links() }}
                </div>
            </div>
            @endif
        </div>

    </div>
</section>
@endsection