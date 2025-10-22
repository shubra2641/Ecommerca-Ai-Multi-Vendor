<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Access Denied') }}</title>
    <meta name="description" content="{{ __('You do not have permission to access this area.') }}">
    <link rel="stylesheet" href="{{ asset('front/css/error-pages.css') }}">
</head>

<body class="error-403">
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-ban error-icon-large"></i>
            </div>

            <div class="error-code" aria-label="{{ __('Error code 403') }}">403</div>

            <h1 class="error-title">{{ __('You do not have permission to access this area.') }}</h1>

            <p class="error-description">
                {{ __('If you believe this is an error, contact your site administrator or request access. For security reasons, this area is restricted.') }}
            </p>

            <div class="error-actions">
                <a href="{{ url('/') }}" class="error-btn error-btn-primary">
                    {{ __('Back to Home') }}
                </a>
                @auth

                @else
                <a href="{{ route('login') }}" class="error-btn error-btn-secondary">
                    {{ __('Sign in') }}
                </a>
                @endauth
            </div>
        </div>
    </div>
</body>

</html>