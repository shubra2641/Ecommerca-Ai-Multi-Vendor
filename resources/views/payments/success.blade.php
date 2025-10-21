@extends('front.layout')
@section('title', __('Payment Successful') . ' - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>

                    <h1 class="h3 text-success mb-3">{{ __('Payment Successful!') }}</h1>

                    <p class="text-muted mb-4">
                        {{ __('Thank you for your purchase. Your payment has been processed successfully.') }}
                    </p>

                    @if(isset($order))
                    <div class="alert alert-success">
                        <strong>{{ __('Order Number:') }}</strong> #{{ $order->id }}<br>
                        <strong>{{ __('Amount:') }}</strong> {{ number_format($order->total, 2) }}
                        {{ $order->currency }}
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary">
                            {{ __('View Order Details') }}
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            {{ __('Continue Shopping') }}
                        </a>
                    </div>
                    @else
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            {{ __('Back to Home') }}
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            {{ __('Continue Shopping') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection