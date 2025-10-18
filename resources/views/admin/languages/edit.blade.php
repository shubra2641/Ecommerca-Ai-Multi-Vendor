@extends('layouts.admin')

@section('title', __('Edit Language') . ' - ' . $language->name)

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('Edit Language') }}</h1>
        <p class="page-description">{{ __('Update language information and settings') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.languages.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            {{ __('Back to Languages') }}
        </a>
        <a href="{{ route('admin.languages.translations', $language) }}" class="btn btn-primary">
            <i class="fas fa-language"></i>
            {{ __('Manage Translations') }}
        </a>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card modern-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-language text-primary"></i>
                        {{ __('Language Information') }}
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.languages.update', $language) }}" method="POST" class="language-form">
                        @csrf
                        @method('PUT')

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name" class="form-label">{{ __('Language Name') }} <span
                                        class="required">*</span></label>
                                <input type="text" id="name" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $language->name) }}" placeholder="{{ __('e.g., English') }}"
                                    required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="native_name" class="form-label">{{ __('Native Name') }}</label>
                                <input type="text" id="native_name" name="native_name"
                                    class="form-control @error('native_name') is-invalid @enderror"
                                    value="{{ old('native_name', $language->native_name) }}"
                                    placeholder="{{ __('e.g., English') }}">
                                @error('native_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">{{ __('Language name in its native script') }}</small>
                            </div>

                            <div class="form-group">
                                <label for="code" class="form-label">{{ __('Language Code') }} <span
                                        class="required">*</span></label>
                                <input type="text"
                                    class="form-control language-code-input @error('code') is-invalid @enderror"
                                    id="code" name="code" value="{{ old('code', $language->code) }}"
                                    placeholder="{{ __('e.g., en') }}" maxlength="2" required>
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">{{ __('2-letter ISO language code') }}</small>
                            </div>

                            <div class="form-group">
                                <label for="flag" class="form-label">{{ __('Flag Emoji') }}</label>
                                <input type="text" id="flag" name="flag"
                                    class="form-control @error('flag') is-invalid @enderror"
                                    value="{{ old('flag', $language->flag) }}" placeholder="{{ __('e.g., 🇺🇸') }}"
                                    maxlength="10">
                                @error('flag')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small
                                    class="form-text">{{ __('Optional flag emoji for visual identification') }}</small>
                            </div>

                            <div class="form-group">
                                <label for="direction" class="form-label">{{ __('Text Direction') }}</label>
                                <select id="direction" name="direction"
                                    class="form-control @error('direction') is-invalid @enderror">
                                    <option value="ltr"
                                        {{ old('direction', $language->direction) == 'ltr' ? 'selected' : '' }}>
                                        {{ __('Left to Right (LTR)') }}</option>
                                    <option value="rtl"
                                        {{ old('direction', $language->direction) == 'rtl' ? 'selected' : '' }}>
                                        {{ __('Right to Left (RTL)') }}</option>
                                </select>
                                @error('direction')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-options">
                            <div class="form-check">
                                <input type="checkbox" id="is_active" name="is_active" class="form-check-input"
                                    value="1" {{ old('is_active', $language->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label">
                                    {{ __('Active') }}
                                </label>
                                <small class="form-text">{{ __('Whether this language is available for use') }}</small>
                            </div>

                            @if(!$language->is_default)
                            <div class="form-check">
                                <input type="checkbox" id="is_default" name="is_default" class="form-check-input"
                                    value="1" {{ old('is_default', $language->is_default) ? 'checked' : '' }}>
                                <label for="is_default" class="form-check-label">
                                    {{ __('Set as Default Language') }}
                                </label>
                                <small
                                    class="form-text">{{ __('This will replace the current default language') }}</small>
                            </div>
                            @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                {{ __('This is the default language and cannot be changed here.') }}
                            </div>
                            @endif
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Update Language') }}
                            </button>
                            <a href="{{ route('admin.languages.index') }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Language Statistics -->
            <div class="card modern-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar text-primary"></i>
                        {{ __('Language Statistics') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="card modern-card stats-card mb-3">
                        <div class="stats-card-body">
                            <div class="stats-card-content">
                                <div class="stats-label">{{ __('Total Translations') }}</div>
                                <div class="stats-number">{{ $language->translations_count ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card modern-card stats-card mb-3">
                        <div class="stats-card-body">
                            <div class="stats-card-content">
                                <div class="stats-label">{{ __('Created') }}</div>
                                <div class="stats-number">{{ $language->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card modern-card stats-card">
                        <div class="stats-card-body">
                            <div class="stats-card-content">
                                <div class="stats-label">{{ __('Last Updated') }}</div>
                                <div class="stats-number">{{ $language->updated_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card modern-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt text-primary"></i>
                        {{ __('Quick Actions') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="{{ route('admin.languages.translations', $language) }}"
                            class="btn btn-outline-primary btn-block">
                            <i class="fas fa-language"></i> {{ __('Manage Translations') }}
                        </a>

                        @if($language->is_active)
                        <form action="{{ route('admin.languages.deactivate', $language) }}" method="POST" class="mt-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-pause"></i> {{ __('Deactivate') }}
                            </button>
                        </form>
                        @else
                        <form action="{{ route('admin.languages.activate', $language) }}" method="POST" class="mt-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-success btn-block">
                                <i class="fas fa-play"></i> {{ __('Activate') }}
                            </button>
                        </form>
                        @endif

                        @if(!$language->is_default)
                        <form action="{{ route('admin.languages.destroy', $language) }}" method="POST"
                            class="mt-2 delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-block"
                                data-confirm="{{ __('Are you sure you want to delete this language? This action cannot be undone.') }}">
                                <i class="fas fa-trash"></i> {{ __('Delete Language') }}
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection