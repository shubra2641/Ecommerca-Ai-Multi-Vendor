@extends('front.layout')
@section('title', __('Contact Us'))
@push('meta')<meta name="robots" content="index,follow">@endpush
@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <nav class="breadcrumb">
                <a href="{{ route('home') }}" class="breadcrumb-item">{{ __('Home') }}</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-item active">{{ __('Contact Us') }}</span>
            </nav>
            <h1 class="page-title">{{ __('Contact Us') }}</h1>
            <p class="page-description">{{ __('Get in touch with us. We\'d love to hear from you.') }}</p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <div class="contact-layout">
            <!-- Contact Main Content -->
            <div class="contact-main">
                <div class="contact-blocks">
                    @foreach($blocks as $block)
                        @php($locale = app()->getLocale())
                        <div class="contact-block contact-block-{{ $block->type }}">
                            @if($block->title($locale))
                                <h2 class="block-title">{{ $block->title($locale) }}</h2>
                            @endif
                            @includeIf('front.contact.types.'.$block->type, ['block'=>$block,'setting'=>$setting,'social'=>$social])
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Contact Sidebar -->
            <aside class="contact-sidebar">
                @if($setting?->contact_email || $setting?->contact_phone)
                    <div class="contact-info-card">
                        <h3 class="card-title">
                            <svg class="card-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M17,18H15.5L13.5,16H10.5L8.5,18H7A2,2 0 0,1 5,16V8A2,2 0 0,1 7,6H17A2,2 0 0,1 19,8V16A2,2 0 0,1 17,18Z"/>
                            </svg>
                            {{ __('Contact Information') }}
                        </h3>
                        <div class="contact-info-list">
                            @if($setting?->contact_phone)
                                <div class="contact-info-item">
                                    <div class="info-icon">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/>
                                        </svg>
                                    </div>
                                    <div class="info-content">
                                        <span class="info-label">{{ __('Phone') }}</span>
                                        <a href="tel:{{ $setting->contact_phone }}" class="info-value">{{ $setting->contact_phone }}</a>
                                    </div>
                                </div>
                            @endif
                            @if($setting?->contact_email)
                                <div class="contact-info-item">
                                    <div class="info-icon">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/>
                                        </svg>
                                    </div>
                                    <div class="info-content">
                                        <span class="info-label">{{ __('Email') }}</span>
                                        <a href="mailto:{{ $setting->contact_email }}" class="info-value">{{ $setting->contact_email }}</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                
                @if($social->count())
                    <div class="social-media-card">
                        <h3 class="card-title">
                            <svg class="card-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18,16.08C17.24,16.08 16.56,16.38 16.04,16.85L8.91,12.7C8.96,12.47 9,12.24 9,12C9,11.76 8.96,11.53 8.91,11.3L15.96,7.19C16.5,7.69 17.21,8 18,8A3,3 0 0,0 21,5A3,3 0 0,0 18,2A3,3 0 0,0 15,5C15,5.24 15.04,5.47 15.09,5.7L8.04,9.81C7.5,9.31 6.79,9 6,9A3,3 0 0,0 3,12A3,3 0 0,0 6,15C6.79,15 7.5,14.69 8.04,14.19L15.16,18.34C15.11,18.55 15.08,18.77 15.08,19C15.08,20.61 16.39,21.91 18,21.91C19.61,21.91 20.92,20.61 20.92,19A2.92,2.92 0 0,0 18,16.08Z"/>
                            </svg>
                            {{ __('Follow Us') }}
                        </h3>
                        <div class="social-links">
                            @foreach($social as $s)
                                <a href="{{ $s->url }}" target="_blank" rel="noopener" class="social-link" title="{{ $s->platform }}">
                                    <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                                        @if($s->icon === 'facebook')
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        @elseif($s->icon === 'twitter')
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        @elseif($s->icon === 'instagram')
                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                        @else
                                            <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                                        @endif
                                    </svg>
                                    <span class="social-label">{{ $s->platform }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</section>
@endsection
