<!doctype html>
<html lang="{{ app()->getLocale() }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ __('Maintenance') }}</title>

    <link href="{{ asset('assets/front/css/maintenance.css') }}" rel="stylesheet">

</head>
<body>
    <div class="card">
        @if(!empty($isPreview))<div class="preview-badge">PREVIEW</div>@endif
        <h1>{{ __('We\'ll be back soon') }}</h1>
        <p class="lead">{{ $message ?? __('We\'re performing scheduled maintenance. Please check back shortly.') }}</p>
        @if(!empty($reopenAt))
            <div id="countdown" class="countdown" data-target="{{ $reopenAt }}"></div>
        @endif
        @if(!empty($isPreview))
            <a href="{{ url()->previous() }}" class="btn">{{ __('Close Preview') }}</a>
        @endif
        <footer>&copy; {{ date('Y') }} {{ config('app.name') }}</footer>
    </div>
    @if(!empty($reopenAt))
        <script src="{{ asset('assets/front/js/maintenance-countdown.js') }}" defer></script>
    @endif
</body>
</html>