@extends('layouts.admin')

@section('title', __('Manage Translations') . ' - ' . $language->name)

@section('content')
@include('admin.partials.page-header', ['title'=>__('Manage Translations'),'subtitle'=>__('Language').': <strong>'.$language->name.'</strong> ('.strtoupper($language->code).')','actions'=>'<a href="'.route('admin.languages.index').'" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> '.__('Back to Languages').'</a>'])

<div class="container-fluid">
    <div class="row">
        <!-- Add New Translation -->
        <div class="col-md-4">
            <div class="card modern-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus-circle text-primary"></i>
                        {{ __('Add New Translation') }}
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.languages.translations.add', $language) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="key" class="form-label">{{ __('Translation Key') }} <span
                                    class="required">*</span></label>
                            <input type="text" id="key" name="key"
                                class="form-control @error('key') is-invalid @enderror" value="{{ old('key') }}"
                                placeholder="{{ __('e.g., Welcome Message') }}" required>
                            @error('key')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="value" class="form-label">{{ __('Translation Value') }} <span
                                    class="required">*</span></label>
                            <textarea id="value" name="value" class="form-control @error('value') is-invalid @enderror"
                                placeholder="{{ __('Enter the translated text...') }}" rows="3"
                                required>{{ old('value') }}</textarea>
                            @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('Add Translation') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Existing Translations -->
        <div class="col-md-8">
            <div class="card modern-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-list text-primary"></i>
                            {{ __('Existing Translations') }} ({{ count($translations) }})
                        </h3>
                        @if(count($translations) > 0)
                        <div class="header-actions">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="expandAll">
                                <i class="fas fa-expand-alt"></i> {{ __('Expand All') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="collapseAll">
                                <i class="fas fa-compress-alt"></i> {{ __('Collapse All') }}
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(count($translations) > 0)
                    <!-- Search Box -->
                    <div class="search-section mb-3">
                        <div class="input-group">
                            <input type="text" id="translationSearch" class="form-control"
                                placeholder="{{ __('Search translations...') }}">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.languages.translations.update', $language) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="translations-list">
                            @foreach($translations as $key => $value)
                            <div class="translation-item">
                                <div class="translation-header">
                                    <label class="translation-key">{{ $key }}</label>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                        data-action="delete-translation" data-translation-key="{{ $key }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="translation-value">
                                    <textarea name="translations[{{ $key }}]" class="form-control translation-textarea"
                                        rows="2"
                                        placeholder="{{ __('Enter translation for: ') . $key }}">{{ $value }}</textarea>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="form-actions d-flex justify-content-between align-items-center">
                            <div class="bulk-actions">
                                <button type="button" class="btn btn-outline-warning btn-sm" id="resetChanges">
                                    <i class="fas fa-undo"></i> {{ __('Reset Changes') }}
                                </button>
                            </div>
                            <div class="save-actions">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> {{ __('Save All Translations') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    @else
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-language fa-3x text-muted mb-3"></i>
                        <h4>{{ __('No translations found') }}</h4>
                        <p class="text-muted">
                            {{ __('Start by adding your first translation using the form on the left') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Translation Form (hidden) -->
<form id="deleteTranslationForm" action="{{ route('admin.languages.translations.delete', $language) }}" method="POST"
    class="d-none">
    @csrf
    @method('DELETE')
    <input type="hidden" name="key" id="deleteKey">
</form>
@endsection