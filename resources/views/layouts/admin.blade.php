<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Easy') }} - @yield('title')</title>
    <!-- Selected Font Meta -->
    <meta name="selected-font" content="{{ $selectedFont }}">
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

<body class="body" data-font-active="{{ $selectedFont }}" data-admin-base="{{ url('') }}" @if(session()->pull('refresh_admin_notifications')) data-refresh-admin-notifications="1" @endif>
    @include('components.noscript-warning')
    <!-- Sidebar -->
    @include('layouts.navigation')
    <!-- Main Content -->

    <main class="main-content">
        @include('admin.top-header')
        <div class="page-content">
            @include('front.partials.flash')
            @yield('content')
        </div>
        </div>
        <!-- Overlay for mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

    </main>
    @yield('scripts')
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