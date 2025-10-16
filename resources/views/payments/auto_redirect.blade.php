<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Redirecting...') }}</title>
    <meta name="robots" content="noindex,nofollow">
</head>
<body class="envato-redirect-body">
    <h3>{{ __('Redirecting to payment gateway') }}</h3>
    <p>{{ __('Please wait...') }}</p>
    <form id="gateway_redirect_form" method="POST" action="{{ $action }}" data-auto-submit="true">
        @foreach($fields as $k=>$v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}" />
        @endforeach
        <noscript>
            <p>{{ __('JavaScript disabled - click continue to proceed.') }}</p>
            <button type="submit">{{ __('Continue') }}</button>
        </noscript>
    </form>
</body>
</html>
