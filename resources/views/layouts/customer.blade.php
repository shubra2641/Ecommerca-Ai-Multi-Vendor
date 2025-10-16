<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Easy') }} - @yield('title', __('Shop'))</title>

    <!-- Modern Fonts -->
    <meta name="selected-font" content="{{ $selectedFont ?? 'Inter' }}">

    <!-- Local font-face -->
    <link rel="stylesheet" href="{{ asset('css/local-fonts.css') }}">

    <!-- Bootstrap -->
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('front/css/envato-fixes.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Unified Customer CSS - All styles consolidated -->
    <link href="{{ asset('assets/customer/css/customer.css') }}" rel="stylesheet">

    @stack('styles')
    <script src="{{ asset('admin/js/loading-fallback.js') }}" defer></script>
</head>

<body class="customer-layout" data-font-active="{{ $selectedFont ?? 'Inter' }}" @if(session()->pull('refresh_customer_notifications')) data-refresh-customer-notifications="1" @endif>
    @include('components.noscript-warning')
    
    <!-- Loading Screen -->
    <div id="loading-screen" class="loading-screen">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ __('Loading') }}...</span>
            </div>
        </div>
    </div>

    <!-- Customer Header -->
    <header class="customer-header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <a href="{{ route('home') }}" class="logo">
                        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="logo-img">
                    </a>
                </div>
                
                <div class="search-section">
                    <form action="{{ route('products.search') }}" method="GET" class="search-form">
                        <input type="text" name="q" placeholder="{{ __('Search products...') }}" value="{{ request('q') }}" class="search-input">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <div class="user-section">
                    @auth
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i>
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.orders') }}">{{ __('My Orders') }}</a></li>
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
                    @else
                        <a href="{{ route('login') }}" class="login-btn">{{ __('Login') }}</a>
                        <a href="{{ route('register') }}" class="register-btn">{{ __('Register') }}</a>
                    @endauth

                    <a href="{{ route('cart.show') }}" class="cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">{{ $cartCount ?? 0 }}</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Customer Navigation -->
    <nav class="customer-nav">
        <div class="container">
            <ul class="nav-menu">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">{{ __('Home') }}</a></li>
                <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">{{ __('Products') }}</a></li>
                <li><a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">{{ __('Categories') }}</a></li>
                <li><a href="{{ route('deals') }}" class="{{ request()->routeIs('deals') ? 'active' : '' }}">{{ __('Deals') }}</a></li>
                <li><a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">{{ __('Blog') }}</a></li>
                <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">{{ __('Contact') }}</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="customer-main">
        @yield('content')
    </main>

    <!-- Customer Footer -->
    <footer class="customer-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>{{ __('Company') }}</h3>
                    <ul>
                        <li><a href="{{ route('about') }}">{{ __('About Us') }}</a></li>
                        <li><a href="{{ route('careers') }}">{{ __('Careers') }}</a></li>
                        <li><a href="{{ route('press') }}">{{ __('Press') }}</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>{{ __('Customer Service') }}</h3>
                    <ul>
                        <li><a href="{{ route('help') }}">{{ __('Help Center') }}</a></li>
                        <li><a href="{{ route('shipping') }}">{{ __('Shipping Info') }}</a></li>
                        <li><a href="{{ route('returns') }}">{{ __('Returns') }}</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>{{ __('Legal') }}</h3>
                    <ul>
                        <li><a href="{{ route('terms') }}">{{ __('Terms of Service') }}</a></li>
                        <li><a href="{{ route('privacy') }}">{{ __('Privacy Policy') }}</a></li>
                        <li><a href="{{ route('cookies') }}">{{ __('Cookie Policy') }}</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>{{ __('Follow Us') }}</h3>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p>&copy; {{ date('Y') }} {{ config('app.name', 'Easy') }}. {{ __('All rights reserved.') }}</p>
                    <div class="payment-methods">
                        <i class="fab fa-cc-visa"></i>
                        <i class="fab fa-cc-mastercard"></i>
                        <i class="fab fa-cc-paypal"></i>
                        <i class="fab fa-cc-stripe"></i>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Notification Container -->
    <div class="notification-container"></div>

    <!-- Scripts -->
    @stack('scripts')
    @yield('scripts')

    <!-- Essential Dependencies -->
    <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}" defer></script>
    
    <!-- Unified Customer JS - All functionality consolidated -->
    <script src="{{ asset('assets/customer/js/customer.js') }}"></script>

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