@extends('layouts.admin')

@section('title', $link->exists ? __('Edit Social Link') : __('Add Social Link'))

@section('content')
<div class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="admin-order-title">
                <h1>
                    @if($link->exists)
                    {{ __('Edit Social Link') }}
                    @else
                    {{ __('Add Social Link') }}
                    @endif
                </h1>
                <p class="admin-order-subtitle">
                    @if($link->exists)
                    {{ __('Update social media link information') }}
                    @else
                    {{ __('Add a new social media link') }}
                    @endif
                </p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.social.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12,19 5,12 12,5"></polyline>
                    </svg>
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        <form method="post" action="{{ $link->exists ? route('admin.social.update', $link) : route('admin.social.store') }}" novalidate>
            @csrf
            @if($link->exists)
            @method('PUT')
            @endif

            <!-- Social Link Information -->
            <div class="admin-modern-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                        {{ __('Social Link Information') }}
                    </div>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label for="platform" class="admin-form-label required">{{ __('Platform') }}</label>
                            <input type="text" id="platform" name="platform" class="admin-form-input @error('platform') is-invalid @enderror"
                                value="{{ old('platform', $link->platform) }}" required maxlength="50"
                                placeholder="{{ __('e.g., Facebook, Twitter, Instagram') }}">
                            @error('platform')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="label" class="admin-form-label">{{ __('Label') }}</label>
                            <input type="text" id="label" name="label" class="admin-form-input @error('label') is-invalid @enderror"
                                value="{{ old('label', $link->label) }}" maxlength="100"
                                placeholder="{{ __('Optional display text') }}">
                            @error('label')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="url" class="admin-form-label required">{{ __('URL') }}</label>
                            <input type="url" id="url" name="url" class="admin-form-input @error('url') is-invalid @enderror"
                                value="{{ old('url', $link->url) }}" required maxlength="255"
                                placeholder="https://">
                            @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="icon" class="admin-form-label">{{ __('Icon') }}</label>
                            <select id="icon" name="icon" class="admin-form-select @error('icon') is-invalid @enderror">
                                @foreach(($socialIcons ?? []) as $class => $label)
                                <option value="{{ $class }}" @selected(($socialCurrentIcon ?? '' )===$class)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">{{ __('Select an icon to display') }}</div>
                            @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="order" class="admin-form-label">{{ __('Order') }}</label>
                            <input type="number" id="order" name="order" class="admin-form-input @error('order') is-invalid @enderror"
                                value="{{ old('order', $link->order) }}" min="0" max="9999"
                                placeholder="{{ __('Display order') }}">
                            @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                    {{ old('is_active', $link->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    {{ __('Active') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="admin-modern-card">
                <div class="admin-card-body">
                    <div class="admin-flex-end">
                        <button type="submit" class="admin-btn admin-btn-primary">
                            @if($link->exists)
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17,21 17,13 7,13 7,21"></polyline>
                                <polyline points="7,3 7,8 15,8"></polyline>
                            </svg>
                            {{ __('Update') }}
                            @else
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            {{ __('Create') }}
                            @endif
                        </button>
                        <a href="{{ route('admin.social.index') }}" class="admin-btn admin-btn-secondary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection