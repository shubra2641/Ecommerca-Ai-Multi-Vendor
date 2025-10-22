<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Server Error') }}</title>
    <meta name="description" content="{{ __('An internal server error occurred.') }}">
    <link rel="stylesheet" href="{{ asset('front/css/error-pages.css') }}">
</head>

<body class="error-500">
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>

            <div class="error-code" aria-label="{{ __('Error code 500') }}">500</div>

            <h1 class="error-title">{{ __('Server Error') }}</h1>

            <p class="error-description">
                {{ __('An internal server error occurred. The issue has been logged and our technical team has been notified.') }}
            </p>

            <div class="error-actions">
                <a href="{{ url('/') }}" class="error-btn error-btn-primary">
                    {{ __('Back to Home') }}
                </a>
                <a href="{{ url()->previous() }}" class="error-btn error-btn-secondary">
                    {{ __('Go Back') }}
                </a>
            </div>

            <p class="error-note">
                {{ __('If the problem persists, please contact technical support or check the application logs.') }}
            </p>
        </div>
    </div>
</body>

</html>