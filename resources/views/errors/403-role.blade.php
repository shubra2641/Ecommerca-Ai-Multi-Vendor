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
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <defs>
                        <linearGradient id="gradient403" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#f59e0b"/>
                            <stop offset="100%" stop-color="#d97706"/>
                        </linearGradient>
                    </defs>
                    <circle cx="100" cy="100" r="80" fill="none" stroke="url(#gradient403)" stroke-width="4" opacity="0.3"/>
                    <rect x="70" y="85" width="60" height="40" rx="5" fill="none" stroke="url(#gradient403)" stroke-width="4"/>
                    <circle cx="100" cy="105" r="3" fill="url(#gradient403)"/>
                    <path d="M85 85 L85 75 Q85 65 95 65 L105 65 Q115 65 115 75 L115 85" fill="none" stroke="url(#gradient403)" stroke-width="4" stroke-linecap="round"/>
                </svg>
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
