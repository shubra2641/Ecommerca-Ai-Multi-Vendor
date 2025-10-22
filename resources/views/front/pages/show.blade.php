@extends('front.layout')
@section('title', $page->seoTitle(app()->getLocale()) ?? $page->title(app()->getLocale()))
@push('meta')
@php($locale = app()->getLocale())
@if($desc = $page->seo_description($locale))
<meta name="description" content="{{ strip_tags($desc) }}">@endif
@php($tagsRaw = $page->seo_tags($locale))
@if(!empty($tagsRaw))
@php($tagsContent = is_array($tagsRaw) ? implode(',', $tagsRaw) : $tagsRaw)
<meta name="keywords" content="{{ e($tagsContent) }}">
@endif
@endpush
@section('content')
@php($locale = app()->getLocale())
<!-- Page Content -->
<section class="page-content">
    <div class="container">
        <div class="page-wrapper">
            <article class="page-body content-style">
                <h1 class="page-title">{{ $page->title($locale) }}</h1>
                @if($desc = $page->seoDescription($locale))
                <p class="page-description">{{ $desc }}</p>
                @endif
                @clean($page->body($locale))
            </article>
            @if($page->slug === 'contact-us')
            <div class="contact-section">
                <div class="contact-info">
                    <h2 class="section-title">{{ __('Contact Information') }}</h2>
                    <div class="contact-details">
                        @if($setting?->contact_phone)
                        <div class="contact-item">
                            <i class="fas fa-phone contact-icon"></i>
                            <span>{{ $setting->contact_phone }}</span>
                        </div>
                        @endif
                        @if($setting?->contact_email)
                        <div class="contact-item">
                            <i class="fas fa-envelope contact-icon"></i>
                            <span>{{ $setting->contact_email }}</span>
                        </div>
                        @endif
                    </div>
                    @if($social->count())
                    <div class="social-links">
                        @foreach($social as $s)
                        <a href="{{ $s->url }}" class="social-link" target="_blank" rel="noopener">
                            <i class="fab fa-{{ $s->icon }}"></i>
                            <span>{{ $s->title ?? $s->platform }}</span>
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="contact-form-section">
                    <h3 class="form-title">{{ __('Send us a message') }}</h3>
                    <form method="POST" action="{{ route('contact.submit') }}" class="contact-form">
                        @csrf
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">{{ __('Name') }} *</label>
                                <input type="text" name="name" class="form-input" required maxlength="150"
                                    value="{{ old('name') }}">
                                @error('name')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Email') }} *</label>
                                <input type="email" name="email" class="form-input" required maxlength="190"
                                    value="{{ old('email') }}">
                                @error('email')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group form-group-full">
                                <label class="form-label">{{ __('Subject') }}</label>
                                <input type="text" name="subject" class="form-input" maxlength="190"
                                    value="{{ old('subject') }}">
                                @error('subject')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group form-group-full">
                                <label class="form-label">{{ __('Message') }} *</label>
                                <textarea name="message" rows="5" class="form-input form-textarea" required
                                    maxlength="5000">{{ old('message') }}</textarea>
                                @error('message')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group form-group-full">
                                <button type="submit" class="btn btn-primary">
                                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    {{ __('Send') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection