
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
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <defs>
                        <linearGradient id="gradient500" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ef4444"/>
                            <stop offset="100%" stop-color="#b91c1c"/>
                        </linearGradient>
                    </defs>
                    <circle cx="100" cy="100" r="80" fill="none" stroke="url(#gradient500)" stroke-width="4" opacity="0.3"/>
                    <rect x="60" y="60" width="80" height="80" rx="8" fill="none" stroke="url(#gradient500)" stroke-width="4"/>
                    <path d="M80 80 L120 120 M120 80 L80 120" stroke="url(#gradient500)" stroke-width="4" stroke-linecap="round"/>
                    <circle cx="100" cy="50" r="6" fill="url(#gradient500)" opacity="0.8"/>
                    <path d="M100 35 L100 45" stroke="url(#gradient500)" stroke-width="3" stroke-linecap="round"/>
                </svg>
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

