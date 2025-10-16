@extends('layouts.app')

@section('title', __('Payment Processing'))

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h1 class="h4 mb-3">{{ __('Payment Processing') }}</h1>
                        <p class="text-muted mb-4">{{ __('Your payment is still being confirmed by the payment gateway. This can take a few seconds.') }}</p>
                        @if($payment)
                            <div class="mb-3">
                                <strong>{{ __('Reference') }}:</strong> {{ $payment->id }}<br>
                                <strong>{{ __('Amount') }}:</strong> {{ $payment->amount }} {{ $payment->currency }}
                            </div>
                        @endif
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                        </div>
                        <p class="small text-secondary mb-4">{{ __('This page will not auto-refresh. You can refresh manually in a moment to update the status.') }}</p>
                        @if($order)
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-secondary">{{ __('Back to Order') }}</a>
                        @else
                            <a href="{{ url('/') }}" class="btn btn-outline-secondary">{{ __('Back Home') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('front.layout')

@section('title', __('Payment Processing'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-5">
                    <!-- Processing Icon -->
                    <div class="mb-4">
                        <div class="spinner-border text-warning spinner-large" role="status">
                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                        </div>
                    </div>
                    
                    <!-- Processing Message -->
                    <h2 class="text-warning mb-3">
                        <i class="fas fa-clock me-2"></i>
                        {{ __('Payment Processing') }}
                    </h2>
                    
                    <p class="text-muted mb-4">
                        {{ __('Your payment is currently being processed. Please wait while we verify your transaction.') }}
                    </p>
                    
                    @if($order)
                        <!-- Order Details -->
                        <div class="bg-light rounded p-4 mb-4">
                            <h5 class="mb-3">{{ __('Order Details') }}</h5>
                            <div class="row text-start">
                                <div class="col-sm-6">
                                    <strong>{{ __('Order Number') }}:</strong><br>
                                    <span class="text-muted">#{{ $order->id }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>{{ __('Total Amount') }}:</strong><br>
                                    <span class="text-muted">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Auto Refresh Notice -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ __('This page will automatically refresh every 10 seconds to check for updates.') }}
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('orders.show', $order ?? $payment->order_id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-receipt me-2"></i>
                            {{ __('View Order') }}
                        </a>
                        
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>
                            {{ __('Back to Home') }}
                        </a>
                        
                        <button data-action="reload" class="btn btn-warning">
                            <i class="fas fa-sync-alt me-2"></i>
                            {{ __('Refresh Status') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- styles moved to front/css/envato-fixes.css and auto-refresh handled via delegated JS -->
@endsection