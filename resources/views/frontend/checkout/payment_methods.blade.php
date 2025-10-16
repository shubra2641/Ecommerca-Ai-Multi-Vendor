@extends('layouts.app')

@section('title', __('Choose Payment Method'))

@section('content')
<div class="container py-5">

                                    <br>
                                    <small class="text-muted">{{ __('Redirect directly to the payment gateway\'s official website for secure payment processing') }}</small>
                                </label>
                            </div>
                        </div>

                        <div class="payment-methods">
                            @forelse($paymentGateways as $gateway)
                            <div class="payment-method" data-gateway="{{ $gateway->slug }}">
                                <input type="radio" name="payment_gateway" value="{{ $gateway->slug }}" id="gateway_{{ $gateway->id }}" class="payment-radio">
                                <label for="gateway_{{ $gateway->id }}" class="payment-label">
                                    <div class="payment-option">
                                        <div class="payment-header">
                                            <div class="payment-logo">
                                                @if($gateway->logo)
                                                <img src="{{ asset('storage/' . $gateway->logo) }}" alt="{{ $gateway->name }}" class="gateway-logo">
                                                @else
                                                <div class="gateway-placeholder">
                                                    <i class="fas fa-credit-card"></i>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="payment-info">
                                                <h6 class="payment-name">{{ $gateway->name }}</h6>
                                                <p class="payment-description">{{ $gateway->description }}</p>
                                            </div>
                                            <div class="payment-status">
                                                @if($gateway->is_available)
                                                <span class="badge bg-success">{{ __('Available') }}</span>
                                                @else
                                                <span class="badge bg-warning">{{ __('Unavailable') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($gateway->fees && ($gateway->fees['fixed'] > 0 || $gateway->fees['percentage'] > 0))
                                        <div class="payment-fees">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                {{ __('Processing fee') }}: 
                                                @if($gateway->fees['fixed'] > 0)
                                                    {{ number_format($gateway->fees['fixed'], 2) }} {{ $order->currency }}
                                                @endif
                                                @if($gateway->fees['percentage'] > 0)
                                                    @if($gateway->fees['fixed'] > 0) + @endif
                                                    {{ $gateway->fees['percentage'] }}%
                                                @endif
                                            </small>
                                        </div>
                                        @endif
                                        
                                        <div class="payment-details envato-hidden">
                                            @if($gateway->slug === 'bank_transfer')
                                            <div class="bank-details mt-3 p-3 bg-light rounded">
                                                <h6>{{ __('Bank Transfer Details') }}</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>{{ __('Bank Name') }}:</strong><br>
                                                        {{ $gateway->config['bank_name'] ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>{{ __('Account Number') }}:</strong><br>
                                                        <code>{{ $gateway->config['account_number'] ?? 'N/A' }}</code>
                                                    </div>
                                                    <div class="col-md-6 mt-2">
                                                        <strong>{{ __('Account Name') }}:</strong><br>
                                                        {{ $gateway->config['account_name'] ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mt-2">
                                                        <strong>{{ __('IBAN') }}:</strong><br>
                                                        <code>{{ $gateway->config['iban'] ?? 'N/A' }}</code>
                                                    </div>
                                                </div>
                                                <div class="alert alert-info mt-3 mb-0">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    {{ __('Please include your order number in the transfer reference') }}: <strong>#{{ $order->order_number }}</strong>
                                                </div>
                                            </div>
                                            @elseif($gateway->slug === 'cash_on_delivery')
                                            <div class="cod-details mt-3 p-3 bg-light rounded">
                                                <div class="alert alert-warning mb-0">
                                                    <i class="fas fa-hand-holding-usd me-2"></i>
                                                    {{ __('You will pay cash when your order is delivered. Please have the exact amount ready.') }}
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h5>{{ __('No Payment Methods Available') }}</h5>
                                <p class="text-muted">{{ __('Please contact support for assistance.') }}</p>
                            </div>
                            @endforelse
                        </div>
                        
                        @if($paymentGateways->count() > 0)
                        <div class="card-footer bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="security-badges">
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt text-success me-1"></i>
                                            {{ __('Secure SSL Encryption') }}
                                        </small>
                                        <small class="text-muted ms-3">
                                            <i class="fas fa-lock text-success me-1"></i>
                                            {{ __('PCI DSS Compliant') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <a href="{{ route('checkout.shipping') }}" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        {{ __('Back') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg" id="proceedBtn" disabled>
                                        <span class="btn-text">
                                            <i class="fas fa-credit-card me-2"></i>
                                            {{ __('Proceed to Payment') }}
                                        </span>
                                        <span class="btn-loading">
                                            <i class="fas fa-spinner fa-spin me-2"></i>
                                            {{ __('Processing...') }}
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Security Information -->
            <div class="row mt-4">
                <div class="col-md-4 text-center">
                    <div class="security-feature">
                        <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                        <h6>{{ __('Secure Payments') }}</h6>
                        <small class="text-muted">{{ __('Your payment information is encrypted and secure') }}</small>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="security-feature">
                        <i class="fas fa-undo fa-2x text-info mb-2"></i>
                        <h6>{{ __('Easy Returns') }}</h6>
                        <small class="text-muted">{{ __('30-day return policy on all items') }}</small>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="security-feature">
                        <i class="fas fa-headset fa-2x text-primary mb-2"></i>
                        <h6>{{ __('24/7 Support') }}</h6>
                        <small class="text-muted">{{ __('Get help whenever you need it') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Processing Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                    <div class="payment-processing">
                    <div class="spinner-border text-primary mb-3 spinner-large" role="status">
                        <span class="visually-hidden">{{ __('Loading...') }}</span>
                    </div>
                    <h5>{{ __('Processing Payment') }}</h5>
                    <p class="text-muted">{{ __('Please wait while we redirect you to the payment gateway...') }}</p>
                    <div class="progress mt-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" data-progress="0" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection