@extends('layouts.admin')

@section('title', __('Payout #:id', ['id' => $payout->id]))

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
                    {{ __('Payout') }} #{{ $payout->id }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Payout details and management') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.vendor.withdrawals.payouts.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        <div class="admin-order-grid">
            <!-- Payout Information -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">{{ __('Payout Information') }}</h2>
                </div>
                <div class="admin-card-body">
                    <div class="admin-info-grid">
                        <div class="admin-info-row">
                            <span class="admin-info-label">{{ __('User') }}</span>
                            <span class="admin-info-value">
                                {{ $payout->user?->name ?? __('Unknown') }}
                                @if($payout->user?->email)
                                <br><small class="customer-email">{{ $payout->user->email }}</small>
                                @endif
                            </span>
                        </div>
                        <div class="admin-info-row">
                            <span class="admin-info-label">{{ __('Amount') }}</span>
                            <span class="admin-info-value">{{ $payout->amount }} {{ $payout->currency }}</span>
                        </div>
                        <div class="admin-info-row">
                            <span class="admin-info-label">{{ __('Status') }}</span>
                            <span class="admin-info-value">
                                <span class="admin-status-badge admin-status-badge-{{ $payout->status === 'executed' ? 'completed' : 'warning' }}">
                                    {{ ucfirst($payout->status) }}
                                </span>
                            </span>
                        </div>
                        @if($payout->admin_note)
                        <div class="admin-info-row">
                            <span class="admin-info-label">{{ __('Admin Note') }}</span>
                            <span class="admin-info-value">{{ $payout->admin_note }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Execute Payout Form -->
            @if($payout->status === 'pending')
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">{{ __('Execute Payout') }}</h2>
                </div>
                <div class="admin-card-body">
                    <form method="post" action="{{ route('admin.vendor.withdrawals.payouts.execute', $payout) }}" class="admin-form" enctype="multipart/form-data">
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
                            {{ __('Execute Payout') }}
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Proof Image -->
        @if(!empty($payout->proof_path))
        <div class="admin-modern-card admin-mt-half">
            <div class="admin-card-header">
                <h2 class="admin-card-title">{{ __('Proof Image') }}</h2>
            </div>
            <div class="admin-card-body">
                <div class="text-center">
                    <img src="{{ asset('storage/'.$payout->proof_path) }}" alt="proof" class="img-fluid" style="max-width: 600px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                </div>
            </div>
        </div>
        @endif

    </div>
</section>
@endsection
