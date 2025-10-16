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
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <defs>
                        <linearGradient id="gradient404" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ef4444"/>
                            <stop offset="100%" stop-color="#dc2626"/>
                        </linearGradient>
                    </defs>
                    <circle cx="100" cy="100" r="80" fill="none" stroke="url(#gradient404)" stroke-width="4" opacity="0.3"/>
                    <path d="M70 70 L130 130 M130 70 L70 130" stroke="url(#gradient404)" stroke-width="6" stroke-linecap="round"/>
                    <circle cx="100" cy="100" r="15" fill="url(#gradient404)" opacity="0.8"/>
                </svg>
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
