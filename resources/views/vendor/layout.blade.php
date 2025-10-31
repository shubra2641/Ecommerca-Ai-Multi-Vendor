<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Easy') }} - @yield('title')</title>
    <!-- Selected Font Meta -->
    <meta name="selected-font" content="{{ $selectedFont }}">
    <!-- Local font-face (Google Fonts removed for CSP) -->
    <!-- Bootstrap (local) -->
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <!-- Unified Admin CSS - All styles consolidated -->
    <link rel="preload" href="{{ asset('assets/admin/css/admin.css') }}" as="style">
    <link href="{{ asset('assets/admin/css/admin.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/local-fonts.css') }}">

    @yield('styles')
</head>

<body class="body" data-font-active="{{ $selectedFont }}" data-admin-base="{{ url('') }}" data-storage-base="{{ asset('storage') }}" @if(session()->pull('refresh_admin_notifications')) data-refresh-admin-notifications="1" @endif>

    <!-- Sidebar -->
    @include('vendor.partials.sidebar')
    <!-- Main Content -->
    @include('vendor.partials.vendor-top')

    <!-- Overlay for mobile -->
    <main class="main-content">
        <div class="page-content">
            @include('front.partials.flash')
            @yield('content')
        </div>
        <!-- Overlay for mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

    </main>
    @yield('scripts')
    <div class="modal fade" id="mediaUploadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="mediaUploadForm" action="{{ route('vendor.gallery.quick-upload') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" data-media-title>{{ __('Upload Media') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="mediaUploadInput">{{ __('Select file(s)') }}</label>
                            <input class="form-control" id="mediaUploadInput" type="file" accept="image/*">
                            <div class="form-text" data-media-hint>{{ __('Accepted formats: JPG, PNG, WEBP. Max size 4 MB each.') }}</div>
                        </div>
                        <div class="alert alert-danger py-2 px-3 d-none" role="alert" data-media-error></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary" data-media-submit>{{ __('Upload') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Essential Dependencies -->
    <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('vendor/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('assets/front/js/flash.js') }}"></script>
    <script src="{{ asset('assets/admin/js/admin.js') }}"></script>
    <script src="{{ asset('assets/admin/js/super-simple-charts.js') }}"></script>
    <script src="{{ asset('assets/admin/js/countup.js') }}" defer></script>
</body>

</html>