@extends('vendor.layout')

@section('title', __('vendor.withdrawals.create_title'))

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
                        <h1 class="admin-order-title">{{ __('vendor.withdrawals.create_heading') }}</h1>
                        <p class="admin-order-subtitle">{{ __('vendor.withdrawals.create_subtitle') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('vendor.withdrawals.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('vendor.withdrawals.back_to_withdrawals') }}
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ __('vendor.withdrawals.create_form_title') }}
                        </h3>
                        <p class="admin-card-subtitle">{{ __('vendor.withdrawals.create_form_subtitle') }}</p>
                    </div>
                    <div class="admin-card-body">
                        @if(!empty($commissionEnabled) && $commissionEnabled && ($commissionRate ?? 0) > 0)
                        <div class="admin-alert admin-alert-info">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 9V13M12 17H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ __('A commission of :rate% will be deducted from the requested amount.', ['rate' => number_format($commissionRate,2)]) }}
                        </div>
                        @endif

                        <form method="POST" action="{{ route('vendor.withdrawals.store') }}" id="withdrawalForm" autocomplete="off"
                            data-minimum="{{ $minimumAmount ?? 0 }}" data-available="{{ $availableBalance ?? 0 }}" @if(!empty($commissionEnabled) && $commissionEnabled && ($commissionRate ?? 0)> 0) data-commission-rate="{{ (float)$commissionRate }}" @endif>
                            @csrf
                            <input type="hidden" name="currency" value="{{ $currency ?? 'USD' }}">

                            <div class="admin-form-group">
                                <label for="amount" class="admin-form-label">{{ __('vendor.withdrawals.field_amount') }}</label>
                                <input type="number" step="0.01" min="0" max="{{ $availableBalance ?? 0 }}" name="amount" id="amount" class="admin-form-input" value="{{ old('amount') }}" required>
                                @error('amount')<div class="admin-text-danger small mt-1">{{ $message }}</div>@enderror
                                <div class="admin-form-text">{{ __('vendor.withdrawals.available_balance_text') }} {{ number_format($availableBalance ?? 0, 2) }} {{ $currency ?? '' }}</div>
                                @if(!empty($commissionEnabled) && $commissionEnabled && ($commissionRate ?? 0) > 0)
                                <div class="admin-text-muted small mt-1" id="netAmountHint" data-net-label="{{ __('Net after commission:') }}" data-fee-label="{{ __('Fee:') }}"></div>
                                @endif
                            </div>

                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('vendor.withdrawals.field_method') }}</label>
                                @if(!empty($gateways) && is_array($gateways))
                                @foreach($gateways as $slug => $gw)
                                <div class="admin-form-check">
                                    <input class="admin-form-check-input payment-radio" type="radio" name="payment_method" id="gw_{{ $slug }}" value="{{ $slug }}" {{ old('payment_method', $loop->first ? $slug : '') === $slug ? 'checked' : '' }}>
                                    <label class="admin-form-check-label" for="gw_{{ $slug }}">
                                        {{ $gw['label'] ?? ucfirst(str_replace('-',' ',$slug)) }}
                                        @if(!empty($gw['description'])) <div class="admin-text-muted small">{{ $gw['description'] }}</div> @endif
                                    </label>
                                </div>
                                @endforeach
                                @error('payment_method')<div class="admin-text-danger small mt-1">{{ $message }}</div>@enderror
                                @else
                                <div class="admin-alert admin-alert-warning">{{ __('vendor.withdrawals.no_methods') }}</div>
                                @endif
                            </div>

                            <div class="admin-form-group admin-form-check">
                                <input type="checkbox" class="admin-form-check-input" id="terms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
                                <label for="terms" class="admin-form-check-label">{{ __('vendor.withdrawals.terms_agree') }}</label>
                                @error('terms')<div class="admin-text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="admin-form-actions">
                                <button type="submit" class="admin-btn admin-btn-primary" id="submitBtn">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ __('vendor.withdrawals.submit') }}
                                </button>
                                <a href="{{ route('vendor.withdrawals.index') }}" class="admin-btn admin-btn-secondary">{{ __('vendor.withdrawals.cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2V22M17 5H9.5A3.5 3.5 0 0 0 9.5 12H14.5A3.5 3.5 0 0 1 14.5 19H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ __('vendor.withdrawals.pending_label') }}
                        </h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="admin-stat-value">{{ number_format($pendingAmount ?? 0, 2) }} {{ $currency ?? '' }}</div>
                        <div class="admin-text-muted small">{{ __('vendor.withdrawals.pending_note') }}</div>

                        <hr class="admin-divider">

                        <h5 class="admin-card-subtitle">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 9V13M12 17H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ __('vendor.withdrawals.guidelines_title') }}
                        </h5>
                        <ul class="admin-list admin-text-muted small">
                            <li>{{ __('vendor.withdrawals.guideline_processing') }}</li>
                            <li>{{ __('vendor.withdrawals.guideline_minimum') }}</li>
                            <li>{{ __('vendor.withdrawals.guideline_verification') }}</li>
                        </ul>

                        <div class="admin-form-actions">
                            <a href="{{ route('vendor.withdrawals.index') }}" class="admin-btn admin-btn-outline w-100">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                {{ __('vendor.withdrawals.view_history') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terms Modal -->
        <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ __('vendor.withdrawals.terms_title') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6>{{ __('vendor.withdrawals.term_processing') }}</h6>
                        <p>{{ __('vendor.withdrawals.term_processing_text') }}</p>
                        <p>{{ __('vendor.withdrawals.term_minimum_text') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="admin-btn admin-btn-secondary" data-bs-dismiss="modal">{{ __('vendor.withdrawals.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection