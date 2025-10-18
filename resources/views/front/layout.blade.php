<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" dir="{{ app()->getLocale()=='ar'?'rtl':'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(config('services.webpush.vapid_public_key'))
    <meta name="vapid-public-key" content="{{ config('services.webpush.vapid_public_key') }}">
    @endif
    <title>@yield('title', config('app.name'))</title>
    <meta name="theme-color" content="#ffffff">
    @if(app()->environment('production'))
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    @endif
    <meta name="app-base" content="{{ url('/') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    @yield('meta')
    <meta name="selected-font" content="{{ $selectedFont }}">
    {{-- Set to '1' to allow loading external Google Fonts; keep '0' for strict CSP environments --}}
    <meta name="allow-google-fonts" content="0">
    <link rel="stylesheet" href="{{ asset('css/local-fonts.css') }}">
    <!-- Inline font CSS removed for CSP compliance; default font rules moved to envato-fixes.css; JS font-loader applies selected font at runtime -->
    <!-- Bootstrap (local) -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/envato-fixes.css') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Unified Customer CSS - All styles consolidated -->
    <link href="{{ asset('assets/customer/css/customer.css') }}" rel="stylesheet">
    <!-- Critical CSS is now in external file -->
    @yield('styles')
    {{-- Load pattern sanitizer early for checkout pages to avoid invalid RegExp compile-time errors --}}
    @if(request()->is('checkout*') || request()->routeIs('checkout.*'))
    <script src="{{ asset('front/js/checkout-pattern-sanitizer.js') }}"></script>
    @endif
</head>

<body class="@if(request()->routeIs('user.*')) account-body @endif">
    <div id="app-loader" class="app-loader hidden" aria-hidden="true">
        <div class="loader-core">
            <div class="spinner"></div>
            <div class="loader-brand">{{ config('app.name') }}</div>
        </div>
    </div>
    @include('front.partials.header')
    <div id="flash-messages-root" class="position-fixed flash-root"
        data-flash-success='{{ e(json_encode(session('success'), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}'
        data-flash-error='{{ e(json_encode(session('error'), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}'
        data-flash-warning='{{ e(json_encode(session('warning'), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}'
        data-flash-info='{{ e(json_encode(session('info'), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}'></div>
    <?php // backup: layout.blade.php original l10n-data block saved as layout.blade.php.bak ?>
    <template id="l10n-data">{!! json_encode([
        'added_to_cart' => __('Added to cart'),
        'failed_add_to_cart' => __('Failed to add to cart'),
        'select_options_first' => __('Please select product options first.'),
        'subscription_saved' => __('Subscription saved'),
        'network_error' => __('Network error'),
        'removed_from_cart' => __('Removed from cart'),
        'moved_to_wishlist' => __('Moved to wishlist'),
        'coupon_applied' => __('Coupon applied'),
        'failed_apply_coupon' => __('Failed to apply coupon'),
        'please_select_required_options' => __('Please select required options first'),
        'sku_copied' => __('SKU copied'),
        'failed_copy' => __('Failed to copy'),
        ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</template>
    <main class="site-main">@yield('content')</main>
    @includeWhen(View::exists('front.partials.footer_extended'),'front.partials.footer_extended')
    @yield('modals')
    @if(request()->routeIs('products.index') || request()->routeIs('products.category') ||
    request()->routeIs('products.tag'))
    @include('front.partials.notify-modal')
    @endif
    <!-- Removed local toast test button now that unified notification system is stable -->
    <!-- Essential Dependencies -->
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}" defer></script>
    
    <!-- Unified Customer JS - All functionality consolidated -->
    <script src="{{ asset('assets/customer/js/customer.js') }}"></script>

    @yield('scripts')

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