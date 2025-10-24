<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" dir="{{ app()->getLocale()=='ar'?'rtl':'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) - {{ __('Maintenance') }}</title>
    <meta name="app-base" content="{{ url('/') }}">
    @yield('meta')
    <!-- Bootstrap (local) -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <!-- Local Fonts -->
    <link rel="stylesheet" href="{{ asset('css/local-fonts.css') }}">
    <!-- Unified Customer CSS - All styles consolidated -->
    <link href="{{ asset('assets/front/css/front.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/front/css/maintenance.css') }}" rel="stylesheet">
    @yield('styles')

</head>

<body class="@if(request()->routeIs('user.*')) account-body @endif">
    <div id="app-loader" class="app-loader" aria-hidden="false">
        <div class="loader-core">
            <div class="spinner"></div>
            <div class="loader-brand">{{ config('app.name') }}</div>
        </div>
    </div>
    <div class="maintenance-wrapper">
        <div class="maintenance-card text-center animate-fade-in-up">
            <div>
                <i class="fas fa-tools maintenance-icon"></i>
                <h1 class="maintenance-title">{{ __('We\'ll be back soon') }}</h1>
                <p class="maintenance-message text-muted">{{ e($message) }}</p>
            </div>

            @if($reopen_at)
            @php
            $now = now();
            $reopenTime = \Carbon\Carbon::parse($reopen_at);
            $remaining = $now->diff($reopenTime);

            if ($reopenTime->isPast()) {
            $countdownText = __('Service is back online!');
            } elseif ($remaining->days > 0) {
            $countdownText = __('Reopens in: :days days, :hours hours', [
            'days' => $remaining->days,
            'hours' => $remaining->h
            ]);
            } elseif ($remaining->h > 0) {
            $countdownText = __('Reopens in: :hours hours, :minutes minutes', [
            'hours' => $remaining->h,
            'minutes' => $remaining->i
            ]);
            } elseif ($remaining->i > 0) {
            $countdownText = __('Reopens in: :minutes minutes, :seconds seconds', [
            'minutes' => $remaining->i,
            'seconds' => $remaining->s
            ]);
            } else {
            $countdownText = __('Reopens in: :seconds seconds', ['seconds' => $remaining->s]);
            }
            @endphp

            <div class="countdown-card">
                <h2 class="countdown-title">{{ __('Estimated Return Time') }}</h2>
                <div class="countdown-text">{{ $countdownText }}</div>
                <p class="countdown-time">{{ __('Estimated reopen time:') }} <time datetime="{{ $reopen_at }}">{{ $reopenTime->toDayDateTimeString() }}</time></p>
            </div>
            @endif

            <div class="maintenance-footer">
                <p>{{ __('We apologize for the inconvenience. Our team is working hard to bring the site back online.') }}</p>
            </div>
        </div>
    </div>
    <!-- Removed local toast test button now that unified notification system is stable -->
    <!-- Essential Dependencies -->
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}" defer></script>
    <!-- Unified Customer JS - All functionality consolidated -->
    <script src="{{ asset('assets/front/js/front.js') }}"></script>
    @yield('scripts')
</body>

</html>