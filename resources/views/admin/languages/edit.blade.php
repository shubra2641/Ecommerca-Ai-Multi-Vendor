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
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Edit Language') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Update language information and settings') }}</p>
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
                <a href="{{ route('admin.languages.translations', $language) }}" class="admin-btn admin-btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                    </svg>
                    {{ __('Manage Translations') }}
                </a>
            </div>
        </div>

        <div class="admin-order-grid-modern">
            <!-- Language Form -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                        </svg>
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
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-2">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
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
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-2">
                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        {{ __('Set as Default Language') }}
                                    </label>
                                </div>
                                <div class="admin-text-muted small">{{ __('This will replace the current default language') }}</div>
                            </div>
                            @else
                            <div class="alert alert-info">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-2">
                                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('This is the default language and cannot be changed here.') }}
                            </div>
                            @endif
                        </div>

                        <div class="admin-flex-end">
                            <button type="submit" class="admin-btn admin-btn-primary">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Update Language') }}
                            </button>
                            <a href="{{ route('admin.languages.index') }}" class="admin-btn admin-btn-secondary">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Language Statistics & Actions -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        {{ __('Language Statistics') }}
                    </h2>
                </div>
                <div class="admin-card-body">
                    <div class="admin-info-grid">
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ __('Total Translations') }}
                            </div>
                            <div class="admin-info-value">{{ $language->translations_count ?? 0 }}</div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ __('Created') }}
                            </div>
                            <div class="admin-info-value">{{ $language->created_at->format('M d, Y') }}</div>
                        </div>
                        <div class="admin-info-item">
                            <div class="admin-info-label">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Last Updated') }}
                            </div>
                            <div class="admin-info-value">{{ $language->updated_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection