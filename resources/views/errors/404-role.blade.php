{{-- Variables $role and $dashboard provided by ErrorRoleComposer --}}

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Not Found') }}</title>
    <meta name="description" content="{{ __('The page you are looking for could not be found.') }}">
    <link rel="stylesheet" href="{{ asset('front/css/error-pages.css') }}">
</head>

<body class="error-404">
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle error-icon-large"></i>
            </div>

            <div class="error-code" aria-label="{{ __('Error code 404') }}">404</div>

            <h1 class="error-title">{{ __('Page not found or not accessible') }}</h1>

            <p class="error-description">
                {{ __('You do not have permission to access this area. If you believe this is an error, contact support.') }}
            </p>

            <div class="error-actions">
                <a href="{{ $dashboard }}" class="error-btn error-btn-primary">
                    {{ __('Go to your dashboard') }}
                </a>
                <a href="{{ url()->previous() }}" class="error-btn error-btn-secondary">
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </div>
</body>

</html>