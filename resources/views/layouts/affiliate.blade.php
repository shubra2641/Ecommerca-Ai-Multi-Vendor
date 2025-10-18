<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Easy') }} - @yield('title', __('Affiliate Dashboard'))</title>

    <!-- Modern Fonts -->
    <meta name="selected-font" content="{{ $selectedFont ?? 'Inter' }}">

    <!-- Local font-face -->
    <link rel="stylesheet" href="{{ asset('css/local-fonts.css') }}">

    <!-- Bootstrap -->
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('front/css/envato-fixes.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Unified Affiliate CSS - All styles consolidated -->
    <link href="{{ asset('assets/affiliate/css/affiliate.css') }}" rel="stylesheet">

    @yield('styles')
    <script src="{{ asset('admin/js/loading-fallback.js') }}" defer></script>
</head>

<body class="affiliate-layout" data-font-active="{{ $selectedFont ?? 'Inter' }}" @if(session()->pull('refresh_affiliate_notifications')) data-refresh-affiliate-notifications="1" @endif>
    @include('components.noscript-warning')
    
    <!-- Loading Screen -->
    <div id="loading-screen" class="loading-screen">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ __('Loading') }}...</span>
            </div>
        </div>
    </div>

    <!-- Affiliate Header -->
    <header class="affiliate-header">
        <h1>{{ __('Affiliate Program') }}</h1>
        <p>{{ __('Earn money by promoting our products') }}</p>
    </header>

    <!-- Affiliate Navigation -->
    <nav class="affiliate-nav">
        <ul>
            <li><a href="{{ route('affiliate.dashboard') }}" class="{{ request()->routeIs('affiliate.dashboard') ? 'active' : '' }}">{{ __('Dashboard') }}</a></li>
            <li><a href="{{ route('affiliate.links') }}" class="{{ request()->routeIs('affiliate.links') ? 'active' : '' }}">{{ __('Links') }}</a></li>
            <li><a href="{{ route('affiliate.commissions') }}" class="{{ request()->routeIs('affiliate.commissions') ? 'active' : '' }}">{{ __('Commissions') }}</a></li>
            <li><a href="{{ route('affiliate.referrals') }}" class="{{ request()->routeIs('affiliate.referrals') ? 'active' : '' }}">{{ __('Referrals') }}</a></li>
            <li><a href="{{ route('affiliate.reports') }}" class="{{ request()->routeIs('affiliate.reports') ? 'active' : '' }}">{{ __('Reports') }}</a></li>
            <li>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                        {{ Auth::user()->name }}
                    </a>
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
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="affiliate-dashboard">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="affiliate-footer">
        <div class="affiliate-footer-content">
            <div class="affiliate-footer-section">
                <h3>{{ __('Support') }}</h3>
                <ul>
                    <li><a href="{{ route('help') }}">{{ __('Help Center') }}</a></li>
                    <li><a href="{{ route('contact') }}">{{ __('Contact Us') }}</a></li>
                </ul>
            </div>
            <div class="affiliate-footer-section">
                <h3>{{ __('Legal') }}</h3>
                <ul>
                    <li><a href="{{ route('terms') }}">{{ __('Terms of Service') }}</a></li>
                    <li><a href="{{ route('privacy') }}">{{ __('Privacy Policy') }}</a></li>
                </ul>
            </div>
        </div>
        <div class="affiliate-footer-bottom">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Easy') }}. {{ __('All rights reserved.') }}</p>
        </div>
    </footer>

    <!-- Notification Container -->
    <div class="notification-container"></div>

    <!-- Scripts -->
    @yield('scripts')
    @yield('scripts')

    <!-- Essential Dependencies -->
    <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('vendor/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}" defer></script>
    
    <!-- Unified Affiliate JS - All functionality consolidated -->
    <script src="{{ asset('assets/affiliate/js/affiliate.js') }}"></script>
</body>
</html>