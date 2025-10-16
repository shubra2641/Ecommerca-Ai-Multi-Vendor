@extends('front.layout')

@section('content')
<div class="container py-5">
    <div class="card">
        <div class="card-body text-center">
            @if($externalRedirectEnabled ?? false)
                <h4>{{ __('Payment Gateway Redirect') }}</h4>
                <p>{{ __('You are being redirected to the external payment gateway for payment id:') }} <strong>{{ $payment->id }}</strong></p>
                <div class="mt-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ __('Loading...') }}</span>
                    </div>
                    <p class="mt-2">{{ __('If you are not redirected automatically, please contact support.') }}</p>
                </div>
                @if(session()->has('driver_html'))
                    {{-- Insert raw driver HTML (may contain a form) and auto-submit if needed --}}
                    <div id="driver-html-container" class="envato-hidden">{{ session('driver_html') }}</div>
                    <script src="{{ asset('front/js/payments-redirect.js') }}" defer></script>
                @endif
            @else
                <h4>{{ __('Simulated Gateway') }}</h4>
                <p>{{ __('You are about to be redirected to the payment gateway for payment id:') }} <strong>{{ $payment->id }}</strong></p>
                <div class="mt-4">
                    <a href="{{ url('/payments/redirect/'.$payment->id.'/complete?result=success') }}" class="btn btn-success">{{ __('Simulate Success') }}</a>
                    <a href="{{ url('/payments/redirect/'.$payment->id.'/complete?result=fail') }}" class="btn btn-danger">{{ __('Simulate Failure') }}</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
