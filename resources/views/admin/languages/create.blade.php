@extends('layouts.admin')

@section('title', __('Add Language'))

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
                        <h1 class="admin-order-title">{{ __('Add Language') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Create a new language for the system') }}</p>
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

        <!-- Language Form -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <i class="fas fa-language"></i>
                    {{ __('Language Information') }}
                </h2>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.languages.store') }}" method="POST" class="admin-form">
                    @csrf

                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Language Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="admin-form-input @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="{{ __('e.g., English') }}" required>
                            @error('name')
                            <div class="admin-text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Language Code') }} <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="admin-form-input @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="{{ __('e.g., en') }}" maxlength="2" required>
                            <div class="admin-text-muted small">{{ __('2-letter ISO language code') }}</div>
                            @error('code')
                            <div class="admin-text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Flag Emoji') }}</label>
                            <input type="text" name="flag" class="admin-form-input @error('flag') is-invalid @enderror" value="{{ old('flag') }}" placeholder="{{ __('e.g., 🇺🇸') }}" maxlength="10">
                            <div class="admin-text-muted small">{{ __('Optional flag emoji for visual identification') }}</div>
                            @error('flag')
                            <div class="admin-text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ __('Active') }}
                                </label>
                            </div>
                            <div class="admin-text-muted small">{{ __('Whether this language is available for use') }}</div>
                        </div>

                        <div class="admin-form-group">
                            <div class="form-check">
                                <input type="checkbox" name="is_default" id="is_default" class="form-check-input" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default">
                                    <i class="fas fa-star me-2"></i>
                                    {{ __('Set as Default Language') }}
                                </label>
                            </div>
                            <div class="admin-text-muted small">{{ __('This will replace the current default language') }}</div>
                        </div>
                    </div>

                    <div class="admin-flex-end">
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="fas fa-check"></i>
                            {{ __('Create Language') }}
                        </button>
                        <a href="{{ route('admin.languages.index') }}" class="admin-btn admin-btn-secondary">
                            <i class="fas fa-times"></i>
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection