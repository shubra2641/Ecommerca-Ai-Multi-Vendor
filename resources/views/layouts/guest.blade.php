<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Easy'))</title>
    @stack('meta')

    <!-- Modern Fonts -->
    {{-- Provided by SiteBrandingComposer: $setting, $selectedFont, $siteName, $logoPath --}}

    <!-- Selected Font Meta -->
    <meta name="selected-font" content="{{ $selectedFont }}">

    <!-- Local fonts instead of Google Fonts -->
    <link rel="stylesheet" href="{{ asset('css/local-fonts.css') }}">

    <!-- Bootstrap -->
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('front/css/envato-fixes.css') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Unified Customer CSS - All styles consolidated -->
    <link href="{{ asset('assets/customer/css/customer.css') }}" rel="stylesheet">

    <!-- Dynamic font variable script moved to external CSS (local-fonts.css). Optional inline removal for CSP strictness. -->
</head>

<body class="guest-layout">
    <header class="auth-topbar">
        <div class="topbar-inner">
            <div class="topbar-left">
                <a href="{{ url('/') }}" class="topbar-logo" aria-label="{{ $siteName }}">
                    @if($logoPath && file_exists(public_path('storage/'.$logoPath)))
                    <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $siteName }}" />
                    @else
                    <span class="logo-text">{{ $siteName }}</span>
                    @endif
                </a>
            </div>
            <div class="topbar-right">
                <div class="topbar-lang">
                    <details class="profile-menu" role="list">
                        <summary class="profile-trigger" aria-haspopup="menu">
                            <i class="fas fa-globe" aria-hidden="true"></i>
                            <span class="d-none d-sm-inline">{{ strtoupper(app()->getLocale()) }}</span>
                            <i class="chevron" aria-hidden="true">▾</i>
                        </summary>
                        <ul class="profile-dropdown" role="menu">
                            {{-- Languages reused from language-switcher composer if available; fallback query removed for inline elimination. Expect partial including languages variable. --}}
                            @foreach(($languages ?? collect()) as $lang)
                            <li>
                                <form method="POST" action="{{ route('language.switch') }}">
                                    @csrf
                                    <input type="hidden" name="language" value="{{ $lang->code }}">
                                    <button type="submit" @disabled(app()->getLocale()===$lang->code)>{{ $lang->name }}</button>
                                </form>
                            </li>
                            @endforeach
                        </ul>
                    </details>
                </div>
                <div class="topbar-profile">
                    @auth
                    <details class="profile-menu" role="list">
                        <summary class="profile-trigger" aria-haspopup="menu">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff"
                                alt="{{ auth()->user()->name }}" class="avatar" />
                            <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                            <i class="chevron" aria-hidden="true">▾</i>
                        </summary>
                        <ul class="profile-dropdown" role="menu">
                            @if(Route::has('profile.edit'))
                            <li><a href="{{ route('profile.edit') }}">{{ __('Edit Profile') }}</a></li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit">{{ __('Logout') }}</button>
                                </form>
                            </li>
                        </ul>
                    </details>
                    @else
                    <details class="profile-menu" role="list">
                        <summary class="profile-trigger" aria-haspopup="menu">
                            <i class="fas fa-user-circle guest-icon" aria-hidden="true"></i>
                            <span class="d-none d-sm-inline">{{ __('Account') }}</span>
                            <i class="chevron" aria-hidden="true">▾</i>
                        </summary>
                        <ul class="profile-dropdown" role="menu">
                            @if(Route::has('login'))
                            <li><a href="{{ route('login') }}">{{ __('Login') }}</a></li>
                            @endif
                            @if(Route::has('register'))
                            <li><a href="{{ route('register') }}">{{ __('Register') }}</a></li>
                            @endif
                        </ul>
                    </details>
                    @endauth
                </div>
            </div>
        </div>
    </header>
    <div class="auth-container">
        <div class="auth-branding-panel">
            <h1>{{ $siteName }}</h1>
            <p>{{ __('A new experience in online shopping.') }}</p>
        </div>
        <div class="auth-form-panel">
            @hasSection('content')
            @yield('content')
            @elseif(isset($slot))
            {{ $slot }}
            @endif
        </div>
    </div>

    <!-- Essential Dependencies -->
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