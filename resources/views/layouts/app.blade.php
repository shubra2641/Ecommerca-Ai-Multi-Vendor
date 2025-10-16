<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Easy') }} - @yield('title', __('Dashboard'))</title>

    {{-- Keep layout minimal to avoid hitting non-migrated tables during tests --}}
    <link rel="stylesheet" href="{{ asset('front/css/envato-extracted.css') }}" />
</head>
<body class="antialiased">
    <div class="min-h-screen">
        @if(isset($header))
            <header>
                <div class="container">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="content-wrapper">
            @yield('content')
        </main>
    </div>
</body>
</html>
