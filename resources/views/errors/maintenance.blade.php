<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Maintenance') }}</title>
    <link href="{{ asset('assets/front/css/maintenance.css') }}" rel="stylesheet">
</head>
<body>
    <main id="maintenance" role="main" aria-labelledby="maintenance-title" @if($reopen_at) data-reopen="{{ $reopen_at }}"@endif>
        <div class="card">
            <h1 id="maintenance-title">{{ __('We&rsquo;ll be back soon') }}</h1>
            <p class="msg">{{ e($message) }}</p>

            @if($reopen_at)
                <div class="count" id="countdown" aria-live="polite">--:--:--</div>
                <p class="reopen" hidden id="reopen-server">
                    {{ __('Estimated reopen time:') }}
                    <time datetime="{{ $reopen_at }}">{{ \Carbon\Carbon::parse($reopen_at)->toDayDateTimeString() }}</time>
                </p>
                <noscript>
                    <p class="reopen">{{ __('Estimated reopen time:') }} <time datetime="{{ $reopen_at }}">{{ \Carbon\Carbon::parse($reopen_at)->toDayDateTimeString() }}</time></p>
                </noscript>
            @endif
        </div>
    </main>
    <script src="{{ asset('assets/front/js/maintenance-countdown.js') }}"></script>
    </body>
</html>
