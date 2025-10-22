<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Payment</title>
    
</head>
<body>
    @if(request('redirect'))
    <iframe id="provider-iframe" src="{{ request('redirect') }}" data-fallback="{{ request('fallback', '') }}" allow="payment *; clipboard-read; clipboard-write; camera; microphone;" allowtransparency="true"></iframe>
    @if(request('fallback'))
    <script>
        // Provide fallback URL to the payments-iframe script
        window.__PAYMENT_FALLBACK_URL__ = {!! json_encode(request('fallback')) !!};
    </script>
    <script src="{{ asset('front/js/payments-iframe.js') }}" defer></script>
    @endif
    @else
    @if(request('fallback'))
        <script>window.location.replace({{ json_encode(request('fallback')) }});</script>
        <noscript><div class="center">JavaScript is required to continue to the payment page. <a href="{{ request('fallback') }}">Open payment</a></div></noscript>
    @else
        <div class="center">Payment URL not provided.</div>
    @endif
@endif
</body>
</html>
