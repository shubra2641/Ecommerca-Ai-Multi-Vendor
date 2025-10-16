@extends('vendor.layout')

@section('title', __('vendor.withdrawals.create_title'))

@section('content')
<div class="vendor-withdrawals-container">
    <div class="withdrawals-header animate-fade-in">
        <div class="header-content">
            <div class="header-left">
                    <h1 class="page-title">
                    <i class="fas fa-paper-plane"></i>
                    {{ __('vendor.withdrawals.create_heading') }}
                </h1>
                <p class="page-subtitle">{{ __('vendor.withdrawals.create_subtitle') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('vendor.withdrawals.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('vendor.withdrawals.back_to_withdrawals') }}
                </a>
            </div>
        </div>
    </div>

    <div class="withdrawal-create-grid animate-slide-in layout-grid-2cols mt-1">
        <div>
            <div class="card modern-card">
                <div class="card-body">
                    <h3 class="card-title"><i class="fas fa-paper-plane"></i> {{ __('vendor.withdrawals.create_form_title') }}</h3>
                    <p class="card-subtitle">{{ __('vendor.withdrawals.create_form_subtitle') }}</p>
                    @if(!empty($commissionEnabled) && $commissionEnabled && ($commissionRate ?? 0) > 0)
                        <div class="alert alert-info py-2 mt-2 mb-3">
                            {{ __('A commission of :rate% will be deducted from the requested amount.', ['rate' => number_format($commissionRate,2)]) }}
                        </div>
                    @endif

                                        <form method="POST" action="{{ route('vendor.withdrawals.store') }}" id="withdrawalForm" autocomplete="off"
                                                    data-minimum="{{ $minimumAmount ?? 0 }}" data-available="{{ $availableBalance ?? 0 }}" @if(!empty($commissionEnabled) && $commissionEnabled && ($commissionRate ?? 0) > 0) data-commission-rate="{{ (float)$commissionRate }}" @endif>
                        @csrf
                                                <input type="hidden" name="currency" value="{{ $currency ?? 'USD' }}">

                        <div class="mb-3">
                            <label for="amount" class="form-label">{{ __('vendor.withdrawals.field_amount') }}</label>
                            <input type="number" step="0.01" min="0" max="{{ $availableBalance ?? 0 }}" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" required>
                            @error('amount')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            <div class="form-text">{{ __('vendor.withdrawals.available_balance_text') }} {{ number_format($availableBalance ?? 0, 2) }} {{ $currency ?? '' }}</div>
                            @if(!empty($commissionEnabled) && $commissionEnabled && ($commissionRate ?? 0) > 0)
                                <div class="small text-muted mt-1" id="netAmountHint" data-net-label="{{ __('Net after commission:') }}" data-fee-label="{{ __('Fee:') }}"></div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('vendor.withdrawals.field_method') }}</label>
                            @if(!empty($gateways) && is_array($gateways))
                                @foreach($gateways as $slug => $gw)
                                <div class="form-check">
                                    <input class="form-check-input payment-radio" type="radio" name="payment_method" id="gw_{{ $slug }}" value="{{ $slug }}" {{ old('payment_method', $loop->first ? $slug : '') === $slug ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gw_{{ $slug }}">
                                        {{ $gw['label'] ?? ucfirst(str_replace('-',' ',$slug)) }}
                                        @if(!empty($gw['description'])) <div class="small text-muted">{{ $gw['description'] }}</div> @endif
                                    </label>
                                </div>
                                @endforeach
                                @error('payment_method')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            @else
                                <div class="alert alert-warning">{{ __('vendor.withdrawals.no_methods') }}</div>
                            @endif
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
                            <label for="terms" class="form-check-label">{{ __('vendor.withdrawals.terms_agree') }}</label>
                            @error('terms')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn">{{ __('vendor.withdrawals.submit') }}</button>
                            <a href="{{ route('vendor.withdrawals.index') }}" class="btn btn-outline-secondary">{{ __('vendor.withdrawals.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <aside>
            <div class="card modern-card">
                <div class="card-body">
                    <div class="balance-label">{{ __('vendor.withdrawals.pending_label') }}</div>
                    <div class="mt-2">
                        <strong>{{ number_format($pendingAmount ?? 0, 2) }} {{ $currency ?? '' }}</strong>
                        <div class="small text-muted">{{ __('vendor.withdrawals.pending_note') }}</div>
                    </div>
                    <hr>
                    <h5 class="guidelines-title"><i class="fas fa-info-circle"></i> {{ __('vendor.withdrawals.guidelines_title') }}</h5>
                    <ul class="small mt-2">
                        <li>{{ __('vendor.withdrawals.guideline_processing') }}</li>
                        <li>{{ __('vendor.withdrawals.guideline_minimum') }}</li>
                        <li>{{ __('vendor.withdrawals.guideline_verification') }}</li>
                    </ul>
                    <div class="mt-3">
                        <a href="{{ route('vendor.withdrawals.index') }}" class="btn btn-outline-primary w-100">{{ __('vendor.withdrawals.view_history') }}</a>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-contract"></i> {{ __('vendor.withdrawals.terms_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>{{ __('vendor.withdrawals.term_processing') }}</h6>
                    <p>{{ __('vendor.withdrawals.term_processing_text') }}</p>
                    <p>{{ __('vendor.withdrawals.term_minimum_text') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('vendor.withdrawals.close') }}</button>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection


