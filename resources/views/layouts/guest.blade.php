<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Easy'))</title>
    @yield('meta')
    <!-- Selected Font Meta -->
    <meta name="selected-font" content="{{ $selectedFont }}">
    <!-- Bootstrap -->
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Local Fonts -->
    <link rel="stylesheet" href="{{ asset('css/local-fonts.css') }}">
    <link href="{{ asset('assets/front/css/front.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/admin.css') }}" rel="stylesheet">

    @php
    $recaptchaService = app(\App\Services\RecaptchaService::class);
    @endphp
    @if($recaptchaService->isEnabled())
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
</head>

<body class="guest-layout" data-font-active="{{ $selectedFont }}">
    <div class="auth-form-panel">
        @hasSection('content')
        @yield('content')
        @elseif(isset($slot))
        {{ $slot }}
        @endif
    </div>
</body>

</html>