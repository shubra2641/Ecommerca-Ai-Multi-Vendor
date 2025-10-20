<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" dir="{{ app()->getLocale()=='ar'?'rtl':'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(config('services.webpush.vapid_public_key'))
    <meta name="vapid-public-key" content="{{ config('services.webpush.vapid_public_key') }}">
    @endif
    <title>@yield('title', config('app.name'))</title>
    <meta name="theme-color" content="#ffffff">
    @if(app()->environment('production'))
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    @endif
    <meta name="app-base" content="{{ url('/') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    @yield('meta')
    <meta name="selected-font" content="{{ $selectedFont }}">
    {{-- Set to '1' to allow loading external Google Fonts; keep '0' for strict CSP environments --}}
    <meta name="allow-google-fonts" content="0">
    <!-- Bootstrap (local) -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <!-- Unified Customer CSS - All styles consolidated -->
    <link href="{{ asset('assets/front/css/front.css') }}" rel="stylesheet">
    <!-- Critical CSS is now in external file -->
    @yield('styles')
</head>

<body class="@if(request()->routeIs('user.*')) account-body @endif">
    <div id="app-loader" class="app-loader" aria-hidden="false">
        <div class="loader-core">
            <div class="spinner"></div>
            <div class="loader-brand">{{ config('app.name') }}</div>
        </div>
    </div>
    @include('front.partials.header')
    @include('front.partials.flash')
    <main class="site-main">
        @yield('content')
    </main>
    @includeWhen(View::exists('front.partials.footer_extended'),'front.partials.footer_extended')
    @yield('modals')
    @if(request()->routeIs('products.index') || request()->routeIs('products.category') ||
    request()->routeIs('products.tag'))
    @include('front.partials.notify-modal')
    @endif
    <!-- Removed local toast test button now that unified notification system is stable -->
    <!-- Essential Dependencies -->
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}" defer></script>

    <!-- Unified Customer JS - All functionality consolidated -->
    <script src="{{ asset('assets/front/js/front.js') }}"></script>
    <script src="{{ asset('assets/front/js/pwa.js') }}"></script>
    <script src="{{ asset('assets/front/js/flash.js') }}"></script>
    @yield('scripts')
</body>

</html>