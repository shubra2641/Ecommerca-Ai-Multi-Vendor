@extends('layouts.admin')

@section('title', __('User Balances'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <i class="fas fa-wallet"></i>
                    {{ __('User Balances') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('View and export user balance information') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.balances.export', ['format' => 'xlsx']) }}" class="admin-btn admin-btn-success">
                    <i class="fas fa-file-excel"></i>
                    {{ __('Export XLSX') }}
                </a>
                <a href="{{ route('admin.balances.export', ['format' => 'pdf']) }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-file-pdf"></i>
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
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="admin-item-details">
                            <h3 class="admin-item-name">{{ $user->name }}</h3>
                            <div class="admin-payment-details admin-mt-half">
                                <span class="payment-detail-item">
                                    <i class="fas fa-envelope"></i>
                                    {{ $user->email }}
                                </span>
                                <span class="payment-detail-separator">•</span>
                                <span class="payment-detail-item">
                                    <i class="fas fa-shield-alt"></i>
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
                        <i class="fas fa-wallet"></i>
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