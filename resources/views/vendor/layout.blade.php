<!DOCTYPE html>
<html     <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Unified Merchant CSS - All styles consolidated -->
    <link href="{{ asset('assets/merchant/css/merchant.css') }}" rel="stylesheet">place('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Easy') }} - @yield('title')</title>

    <!-- Modern Fonts -->
    {{-- Variables provided by SiteBrandingComposer: $setting, $selectedFont, $siteName, $logoPath --}}

    <!-- Selected Font Meta -->
    <meta name="selected-font" content="{{ $selectedFont }}">

    <!-- Local font-face (Google Fonts removed for CSP) -->
    <link rel="stylesheet" href="{{ asset('css/local-fonts.css') }}">

    <!-- Bootstrap (local) -->
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('front/css/envato-fixes.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Unified Merchant CSS - All styles consolidated -->
    <link href="{{ asset('assets/merchant/css/merchant.css') }}" rel="stylesheet">




    @stack('styles')
</head>

<body class="modern-admin-layout" data-font-active="{{ $selectedFont }}" @if(session()->pull('refresh_admin_notifications')) data-refresh-admin-notifications="1" @endif>

    <!-- Sidebar -->
    @include('vendor.partials.sidebar')


    <!-- Main Content -->
    @include('vendor.partials.vendor-top')
    <!-- Overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Scripts -->
    @stack('scripts')
    @yield('scripts')

    <!-- Essential Dependencies -->
    <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('vendor/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}" defer></script>
    
    <!-- Unified Merchant JS - All functionality consolidated -->
    <script src="{{ asset('assets/merchant/js/merchant.js') }}"></script>

    <!-- Font Loader Script -->
    <script>
        // Load selected font
        document.addEventListener('DOMContentLoaded', function() {
            const fontName = document.querySelector('meta[name="selected-font"]').getAttribute('content');
            if (fontName && fontName !== 'Inter') {
                document.body.style.fontFamily = fontName + ', sans-serif';
            }
        });
    </script>
</body>

</html>