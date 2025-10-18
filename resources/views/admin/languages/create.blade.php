@extends('layouts.admin')

@section('title', __('Add Language'))

@section('content')
@include('admin.partials.page-header', ['title'=>__('Add Language'),'subtitle'=>__('Create a new language for the system'),'actions'=>'<a href="'.route('admin.languages.index').'" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> '.__('Back to Languages').'</a>'])

<div class="container-fluid">

    <div class="card modern-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-language text-primary"></i>
                {{ __('Language Information') }}
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.languages.store') }}" method="POST" class="language-form">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="form-label">{{ __('Language Name') }} <span
                                class="required">*</span></label>
                        <input type="text" id="name" name="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                            placeholder="{{ __('e.g., English') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="code" class="form-label">{{ __('Language Code') }} <span
                                class="required">*</span></label>
                        <input type="text" class="form-control language-code-input @error('code') is-invalid @enderror"
                            id="code" name="code" value="{{ old('code') }}" placeholder="{{ __('e.g., en') }}"
                            maxlength="2" required>
                        @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">{{ __('2-letter ISO language code') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="flag" class="form-label">{{ __('Flag Emoji') }}</label>
                        <input type="text" id="flag" name="flag"
                            class="form-control @error('flag') is-invalid @enderror" value="{{ old('flag') }}"
                            placeholder="{{ __('e.g., 🇺🇸') }}" maxlength="10">
                        @error('flag')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text">{{ __('Optional flag emoji for visual identification') }}</small>
                    </div>
                </div>

                <div class="form-options">
                    <div class="form-check">
                        <input type="checkbox" id="is_active" name="is_active" class="form-check-input" value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">
                            {{ __('Active') }}
                        </label>
                        <small class="form-text">{{ __('Whether this language is available for use') }}</small>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="is_default" name="is_default" class="form-check-input" value="1"
                            {{ old('is_default') ? 'checked' : '' }}>
                        <label for="is_default" class="form-check-label">
                            {{ __('Set as Default Language') }}
                        </label>
                        <small class="form-text">{{ __('This will replace the current default language') }}</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('Create Language') }}
                    </button>
                    <a href="{{ route('admin.languages.index') }}" class="btn btn-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection