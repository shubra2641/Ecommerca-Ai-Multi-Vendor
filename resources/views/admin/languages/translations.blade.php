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
                        <i class="fas fa-language"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Manage Translations') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Language') }}: <strong>{{ $language->name }}</strong> ({{ strtoupper($language->code) }})</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.languages.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back to Languages') }}
                </a>
            </div>
        </div>

        <div class="admin-order-grid-modern">
            <!-- Add New Translation -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <i class="fas fa-plus"></i>
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
                                <i class="fas fa-plus" aria-hidden="true"></i>
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
                            <i class="fas fa-list"></i>
                            {{ __('Existing Translations') }} ({{ count($translations) }})
                        </h2>
                        @if(count($translations) > 0)
                        <div class="admin-card-actions">
                            <button type="button" class="admin-btn admin-btn-small admin-btn" id="expandAll">
                                <i class="fas fa-expand-arrows-alt"></i>
                                {{ __('Expand All') }}
                            </button>
                            <button type="button" class="admin-btn admin-btn-small admin-btn" id="collapseAll">
                                <i class="fas fa-compress-arrows-alt"></i>
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
                                <i class="fas fa-search"></i>
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
                                                <i class="fas fa-file-alt"></i>
                                                {{ __('Translation Key') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="admin-item-price">
                                        <button type="button" class="admin-btn admin-btn-danger admin-btn-small js-confirm" data-action="delete-translation" data-translation-key="{{ $key }}">
                                            <i class="fas fa-trash"></i>
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
                                    <i class="fas fa-sync-alt"></i>
                                    {{ __('Reset Changes') }}
                                </button>
                                <button type="submit" class="admin-btn admin-btn-success">
                                    <i class="fas fa-check"></i>
                                    {{ __('Save All Translations') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    @else
                    <div class="admin-empty-state">
                        <i class="fas fa-language fa-3x" aria-hidden="true"></i>
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