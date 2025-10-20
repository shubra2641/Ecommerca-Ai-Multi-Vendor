@extends('layouts.admin')

@section('title', __('Manage Translations') . ' - ' . $language->name)

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Manage Translations') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Language') }}: <strong>{{ $language->name }}</strong> ({{ strtoupper($language->code) }})</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.languages.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back to Languages') }}
                </a>
            </div>
        </div>

        <div class="admin-order-grid-modern">
            <!-- Add New Translation -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 5v14m7-7H5" />
                        </svg>
                        {{ __('Add New Translation') }}
                    </h2>
                </div>
                <div class="admin-card-body">
                    <form action="{{ route('admin.languages.translations.add', $language) }}" method="POST" class="admin-form">
                        @csrf
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Translation Key') }} <span class="text-danger">*</span></label>
                            <input type="text" name="key" class="admin-form-input @error('key') is-invalid @enderror" value="{{ old('key') }}" placeholder="{{ __('e.g., Welcome Message') }}" required>
                            @error('key')
                            <div class="admin-text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Translation Value') }} <span class="text-danger">*</span></label>
                            <textarea name="value" class="admin-form-input @error('value') is-invalid @enderror" placeholder="{{ __('Enter the translated text...') }}" rows="3" required>{{ old('value') }}</textarea>
                            @error('value')
                            <div class="admin-text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="admin-flex-end">
                            <button type="submit" class="admin-btn admin-btn-primary">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 5v14m7-7H5" />
                                </svg>
                                {{ __('Add Translation') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Existing Translations -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="admin-card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            {{ __('Existing Translations') }} ({{ count($translations) }})
                        </h2>
                        @if(count($translations) > 0)
                        <div class="admin-card-actions">
                            <button type="button" class="admin-btn admin-btn-small admin-btn-outline" id="expandAll">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3" />
                                </svg>
                                {{ __('Expand All') }}
                            </button>
                            <button type="button" class="admin-btn admin-btn-small admin-btn-outline" id="collapseAll">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M15 9h4.5M15 9V4.5M15 9l5.25-5.25M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25" />
                                </svg>
                                {{ __('Collapse All') }}
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="admin-card-body">
                    @if(count($translations) > 0)
                    <!-- Search Box -->
                    <div class="admin-form-group-search mb-3">
                        <div class="admin-input-group">
                            <input type="text" id="translationSearch" class="admin-form-input-search" placeholder="{{ __('Search translations...') }}">
                            <div class="admin-input-icon">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.languages.translations.update', $language) }}" method="POST" class="admin-form">
                        @csrf
                        @method('PUT')

                        <div class="admin-items-list">
                            @foreach($translations as $key => $value)
                            <div class="admin-item-card">
                                <div class="admin-item-main">
                                    <div class="admin-item-details">
                                        <div class="admin-item-name">{{ $key }}</div>
                                        <div class="admin-item-meta">
                                            <div class="admin-meta-item">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                {{ __('Translation Key') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="admin-item-price">
                                        <button type="button" class="admin-btn admin-btn-danger admin-btn-small js-confirm" data-action="delete-translation" data-translation-key="{{ $key }}">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            {{ __('Delete') }}
                                        </button>
                                    </div>
                                </div>
                                <div class="admin-form-group">
                                    <textarea name="translations[{{ $key }}]" class="admin-form-input" rows="2" placeholder="{{ __('Enter translation for: ') . $key }}">{{ $value }}</textarea>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="admin-flex-end">
                            <div class="admin-actions-flex">
                                <button type="button" class="admin-btn admin-btn-secondary" id="resetChanges">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    {{ __('Reset Changes') }}
                                </button>
                                <button type="submit" class="admin-btn admin-btn-success">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ __('Save All Translations') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    @else
                    <div class="admin-empty-state">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="admin-notification-icon">
                            <path d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                        </svg>
                        <h3>{{ __('No translations found') }}</h3>
                        <p>{{ __('Start by adding your first translation using the form on the left') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Delete Translation Form (hidden) -->
<form id="deleteTranslationForm" action="{{ route('admin.languages.translations.delete', $language) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
    <input type="hidden" name="key" id="deleteKey">
</form>
@endsection