<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Easy') }} - @yield('title', __('Merchant Dashboard'))</title>

    <!-- Modern Fonts -->
    <meta name="selected-font" content="{{ $selectedFont ?? 'Inter' }}">

    <!-- Local font-face -->
    <link rel="stylesheet" href="{{ asset('css/local-fonts.css') }}">

    <!-- Bootstrap -->
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('front/css/envato-fixes.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Unified Merchant CSS - All styles consolidated -->
    <link href="{{ asset('assets/merchant/css/merchant.css') }}" rel="stylesheet">

    @stack('styles')
    <script src="{{ asset('admin/js/loading-fallback.js') }}" defer></script>
</head>

<body class="merchant-layout" data-font-active="{{ $selectedFont ?? 'Inter' }}" @if(session()->pull('refresh_merchant_notifications')) data-refresh-merchant-notifications="1" @endif>
    @include('components.noscript-warning')
    
    <!-- Loading Screen -->
    <div id="loading-screen" class="loading-screen">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ __('Loading') }}...</span>
            </div>
        </div>
    </div>

    <!-- Merchant Header -->
    <header class="merchant-header">
        <div class="merchant-header-main">
            <nav class="merchant-nav">
                <a href="{{ route('vendor.dashboard') }}" class="merchant-logo">
                    {{ config('app.name', 'Easy') }} - {{ __('Merchant') }}
                </a>
                
                <ul class="merchant-menu">
                    <li><a href="{{ route('vendor.dashboard') }}" class="{{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">{{ __('Dashboard') }}</a></li>
                    <li><a href="{{ route('vendor.products.index') }}" class="{{ request()->routeIs('vendor.products.*') ? 'active' : '' }}">{{ __('Products') }}</a></li>
                    <li><a href="{{ route('vendor.orders.index') }}" class="{{ request()->routeIs('vendor.orders.*') ? 'active' : '' }}">{{ __('Orders') }}</a></li>
                    <li><a href="{{ route('vendor.withdrawals.index') }}" class="{{ request()->routeIs('vendor.withdrawals.*') ? 'active' : '' }}">{{ __('Withdrawals') }}</a></li>
                </ul>

                <div class="merchant-actions">
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">{{ __('Log Out') }}</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="merchant-dashboard">
        @yield('content')
    </main>

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