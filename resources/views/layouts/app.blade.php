<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Easy') }} - @yield('title')</title>
    <!-- Local font-face (Google Fonts removed for CSP) -->
    <!-- Bootstrap (local) -->
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <!-- Unified Admin CSS - All styles consolidated -->
    <link rel="preload" href="{{ asset('assets/admin/css/admin.css') }}" as="style">
    <link href="{{ asset('assets/admin/css/admin.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/local-fonts.css') }}">

    @yield('styles')
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
    <!-- Essential Dependencies -->
    <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('vendor/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('assets/front/js/flash.js') }}"></script>
    <script src="{{ asset('assets/admin/js/admin.js') }}"></script>
    <script src="{{ asset('assets/admin/js/super-simple-charts.js') }}"></script>
    <script src="{{ asset('assets/admin/js/countup.js') }}" defer></script>
</body>

</html>