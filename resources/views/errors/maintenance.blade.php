<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" dir="{{ app()->getLocale()=='ar'?'rtl':'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) - {{ __('Maintenance') }}</title>
    <meta name="app-base" content="{{ url('/') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    @yield('meta')
    <!-- Bootstrap (local) -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <!-- Local Fonts -->
    <link rel="stylesheet" href="{{ asset('css/local-fonts.css') }}">
    <!-- Unified Customer CSS - All styles consolidated -->
    <link href="{{ asset('assets/front/css/front.css') }}" rel="stylesheet">
    @yield('styles')
    <style>
        .maintenance-wrapper {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .maintenance-card {
            width: 100%;
            max-width: 600px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 3rem 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .maintenance-icon {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 1rem;
            text-align: center;
        }

        .maintenance-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 1rem 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .maintenance-message {
            font-size: 1.1rem;
            line-height: 1.6;
            margin: 0 0 2rem 0;
            text-align: center;
        }

        .countdown-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin: 2rem 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .countdown-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0 0 1rem 0;
        }

        .countdown-text {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .countdown-time {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }

        .maintenance-footer {
            margin-top: 2rem;
            text-align: center;
        }

        .maintenance-footer p {
            font-size: 1rem;
            color: #6b7280;
            margin: 0;
        }
    </style>
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