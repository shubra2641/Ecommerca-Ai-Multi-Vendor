@extends('layouts.admin')

@section('title', __('Withdrawal #:id', ['id' => $withdrawal->id]))

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
                    {{ __('Withdrawal') }} #{{ $withdrawal->id }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Withdrawal request details') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.vendor.withdrawals.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        <div class="admin-order-grid">
            <!-- Withdrawal Information -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">{{ __('Withdrawal Information') }}</h2>
                </div>
                <div class="admin-card-body">
                    <div class="admin-info-grid">
                        <div class="admin-info-row">
                            <span class="admin-info-label">{{ __('User') }}</span>
                            <span class="admin-info-value">
                                {{ $withdrawal->user?->name ?? __('Unknown') }}
                                @if($withdrawal->user?->email)
                                <br><small class="customer-email">{{ $withdrawal->user->email }}</small>
                                @endif
                            </span>
                        </div>
                        <div class="admin-info-row">
                            <span class="admin-info-label">{{ __('Amount') }}</span>
                            <span class="admin-info-value">{{ $withdrawal->amount }} {{ $withdrawal->currency }}</span>
                        </div>
                        <div class="admin-info-row">
                            <span class="admin-info-label">{{ __('Status') }}</span>
                            <span class="admin-info-value">
                                <span class="admin-status-badge admin-status-badge-{{ $withdrawal->status === 'approved' ? 'completed' : ($withdrawal->status === 'rejected' ? 'cancelled' : 'warning') }}">
                                    {{ ucfirst($withdrawal->status) }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions for Pending Withdrawal -->
            @if($withdrawal->status === 'pending')
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">{{ __('Actions') }}</h2>
                </div>
                <div class="admin-card-body">
                    <!-- Approve Form -->
                    <form method="post" action="{{ route('admin.vendor.withdrawals.approve', $withdrawal) }}" class="admin-form admin-mb-1-5" enctype="multipart/form-data">
                        @csrf
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Admin Note') }}</label>
                            <textarea name="admin_note" class="admin-form-input" rows="3"></textarea>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Proof (optional)') }}</label>
                            <input type="file" name="proof" class="admin-form-input" accept="image/*">
                        </div>
                        <button class="admin-btn admin-btn-success">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Approve') }}
                        </button>
                    </form>

                    <!-- Reject Form -->
                    <form method="post" action="{{ route('admin.vendor.withdrawals.reject', $withdrawal) }}" class="admin-form" enctype="multipart/form-data">
                        @csrf
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Admin Note') }}</label>
                            <textarea name="admin_note" class="admin-form-input" rows="3"></textarea>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Proof (optional)') }}</label>
                            <input type="file" name="proof" class="admin-form-input" accept="image/*">
                        </div>
                        <button class="admin-btn admin-btn-danger">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            {{ __('Reject') }}
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Payout Information -->
        @if($withdrawal->status === 'approved' && $withdrawal->payout)
        <div class="admin-modern-card admin-mt-half">
            <div class="admin-card-header">
                <h2 class="admin-card-title">{{ __('Payout Information') }}</h2>
            </div>
            <div class="admin-card-body">
                <div class="admin-info-grid">
                    <div class="admin-info-row">
                        <span class="admin-info-label">{{ __('Payout ID') }}</span>
                        <span class="admin-info-value">#{{ $withdrawal->payout->id }}</span>
                    </div>
                    <div class="admin-info-row">
                        <span class="admin-info-label">{{ __('Status') }}</span>
                        <span class="admin-info-value">
                            <span class="admin-status-badge admin-status-badge-{{ $withdrawal->payout->status === 'executed' ? 'completed' : 'warning' }}">
                                {{ ucfirst($withdrawal->payout->status) }}
                            </span>
                        </span>
                    </div>
                </div>

                @if($withdrawal->payout->status === 'pending')
                <form method="post" action="{{ route('admin.vendor.withdrawals.payouts.execute', $withdrawal->payout) }}" class="admin-form admin-mt-half" enctype="multipart/form-data">
                    @csrf
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Admin Note') }}</label>
                        <textarea name="admin_note" class="admin-form-input" rows="2"></textarea>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Proof (optional)') }}</label>
                        <input type="file" name="proof" class="admin-form-input" accept="image/*">
                    </div>
                    <button class="admin-btn admin-btn-success">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('Execute Payout') }}
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif

    </div>
</section>
@endsection
