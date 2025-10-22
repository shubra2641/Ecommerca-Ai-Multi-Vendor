@extends('front.layout')
@section('title', __('Contact Us'))
@push('meta')
<meta name="robots" content="index,follow">@endpush
@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <x-breadcrumb :items="[
                ['title' => __('Home'), 'url' => route('home'), 'icon' => 'fas fa-home'],
                ['title' => __('Contact Us'), 'url' => '#']
            ]" />
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
                        <i class="fas fa-info-circle card-icon"></i>
                        {{ __('Contact Information') }}
                    </h3>
                    <div class="contact-info-list">
                        @if($setting?->contact_phone)
                        <div class="contact-info-item">
                            <div class="info-icon">
                                <i class="fas fa-phone" aria-hidden="true"></i>
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
                                <i class="fas fa-envelope" aria-hidden="true"></i>
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
                        <i class="fas fa-share-alt card-icon" aria-hidden="true"></i>
                        {{ __('Follow Us') }}
                    </h3>
                    <div class="social-links">
                        @foreach($social as $s)
                        <a href="{{ $s->url }}" target="_blank" rel="noopener" class="social-link" title="{{ $s->platform }}">
                            @if($s->icon === 'facebook')
                            <i class="fab fa-facebook social-icon" aria-hidden="true"></i>
                            @elseif($s->icon === 'twitter')
                            <i class="fab fa-twitter social-icon" aria-hidden="true"></i>
                            @elseif($s->icon === 'instagram')
                            <i class="fab fa-instagram social-icon" aria-hidden="true"></i>
                            @elseif($s->icon === 'linkedin')
                            <i class="fab fa-linkedin social-icon" aria-hidden="true"></i>
                            @elseif($s->icon === 'youtube')
                            <i class="fab fa-youtube social-icon" aria-hidden="true"></i>
                            @else
                            <i class="fas fa-globe social-icon" aria-hidden="true"></i>
                            @endif
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