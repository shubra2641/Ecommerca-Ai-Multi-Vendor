@extends('front.layout')
@section('title', __('Payment Failed') . ' - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-times-circle text-danger"></i>
                    </div>

                    <h1 class="h3 text-danger mb-3">{{ __('Payment Failed') }}</h1>

                    <p class="text-muted mb-4">
                        {{ __('We\'re sorry, but your payment could not be processed. Please try again or use a different payment method.') }}
                    </p>

                    @if(isset($order))
                    <div class="alert alert-warning">
                        <strong>{{ __('Order Number:') }}</strong> #{{ $order->id }}<br>
                        <strong>{{ __('Amount:') }}</strong> {{ number_format($order->total, 2) }}
                        {{ $order->currency }}<br>
                        <small
                            class="text-muted">{{ __('Your order is still pending. You can retry payment from your order details.') }}</small>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary">
                            {{ __('Retry Payment') }}
                        </a>
                        <a href="{{ route('checkout.form') }}" class="btn btn-outline-secondary">
                            {{ __('Back to Checkout') }}
                        </a>
                    </div>
                    @else
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('checkout.form') }}" class="btn btn-primary">
                            {{ __('Try Again') }}
                        </a>
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                            {{ __('Back to Cart') }}
                        </a>
                    </div>
                    @endif

                    @if(isset($error_message))
                    <div class="mt-3">
                        <small class="text-muted">{{ __('Error:') }} {{ $error_message }}</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection