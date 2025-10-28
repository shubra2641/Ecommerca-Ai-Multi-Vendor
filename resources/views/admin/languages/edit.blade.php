@extends('layouts.admin')

@section('title', __('Edit Language') . ' - ' . $language->name)

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
                        <h1 class="admin-order-title">{{ __('Edit Language') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Update language information and settings') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.languages.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back to Languages') }}
                </a>
                <a href="{{ route('admin.languages.translations', $language) }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-language"></i>
                    {{ __('Manage Translations') }}
                </a>
            </div>
        </div>

        <div class="admin-order-grid-modern">
            <!-- Language Form -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <i class="fas fa-language"></i>
                        {{ __('Language Information') }}
                    </h2>
                </div>
                <div class="admin-card-body">
                    <form action="{{ route('admin.languages.update', $language) }}" method="POST" class="admin-form">
                        @csrf
                        @method('PUT')

                        <div class="admin-form-grid">
                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Language Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="admin-form-input @error('name') is-invalid @enderror" value="{{ old('name', $language->name) }}" placeholder="{{ __('e.g., English') }}" required>
                                @error('name')
                                <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Native Name') }}</label>
                                <input type="text" name="native_name" class="admin-form-input @error('native_name') is-invalid @enderror" value="{{ old('native_name', $language->native_name) }}" placeholder="{{ __('e.g., English') }}">
                                <div class="admin-text-muted small">{{ __('Language name in its native script') }}</div>
                                @error('native_name')
                                <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Language Code') }} <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="admin-form-input @error('code') is-invalid @enderror" value="{{ old('code', $language->code) }}" placeholder="{{ __('e.g., en') }}" maxlength="2" required>
                                <div class="admin-text-muted small">{{ __('2-letter ISO language code') }}</div>
                                @error('code')
                                <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Flag Emoji') }}</label>
                                <input type="text" name="flag" class="admin-form-input @error('flag') is-invalid @enderror" value="{{ old('flag', $language->flag) }}" placeholder="{{ __('e.g., 🇺🇸') }}" maxlength="10">
                                <div class="admin-text-muted small">{{ __('Optional flag emoji for visual identification') }}</div>
                                @error('flag')
                                <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Text Direction') }}</label>
                                <select name="direction" class="admin-form-input @error('direction') is-invalid @enderror">
                                    <option value="ltr" {{ old('direction', $language->direction) == 'ltr' ? 'selected' : '' }}>{{ __('Left to Right (LTR)') }}</option>
                                    <option value="rtl" {{ old('direction', $language->direction) == 'rtl' ? 'selected' : '' }}>{{ __('Right to Left (RTL)') }}</option>
                                </select>
                                @error('direction')
                                <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="admin-form-grid">
                            <div class="admin-form-group">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $language->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <i class="fas fa-check-circle me-2"></i>
                                        {{ __('Active') }}
                                    </label>
                                </div>
                                <div class="admin-text-muted small">{{ __('Whether this language is available for use') }}</div>
                            </div>

                            @if(!$language->is_default)
                            <div class="admin-form-group">
                                <div class="form-check">
                                    <input type="checkbox" name="is_default" id="is_default" class="form-check-input" value="1" {{ old('is_default', $language->is_default) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        <i class="fas fa-star me-2"></i>
                                        {{ __('Set as Default Language') }}
                                    </label>
                                </div>
                                <div class="admin-text-muted small">{{ __('This will replace the current default language') }}</div>
                            </div>
                            @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('This is the default language and cannot be changed here.') }}
                            </div>
                            @endif
                        </div>

                        <div class="admin-flex-end">
                            <button type="submit" class="admin-btn admin-btn-primary">
                                <i class="fas fa-check"></i>
                                {{ __('Update Language') }}
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
    </div>
</section>
@endsection