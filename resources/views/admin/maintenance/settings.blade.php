@extends('layouts.admin')

@section('title', __('Maintenance Settings'))

@section('content')
<section class="admin-order-details-modern">
  <div class="admin-order-wrapper">
    <!-- Header -->
    <div class="admin-order-header">
      <div class="header-left">
        <div class="admin-header-content">
          <div class="admin-header-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
              <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
              <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
            </svg>
          </div>
          <div class="admin-header-text">
            <h1 class="admin-order-title">{{ __('Maintenance Settings') }}</h1>
            <p class="admin-order-subtitle">{{ __('Configure system maintenance mode and settings') }}</p>
          </div>
        </div>
      </div>
    </div>

    <form method="POST" action="{{ route('admin.maintenance-settings.update') }}" class="admin-modern-card">
      @csrf
      @method('PUT')

      <div class="admin-card-header">
        <h3 class="admin-card-title">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
            <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
            <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
          </svg>
          {{ __('Maintenance Configuration') }}
        </h3>
      </div>

      <div class="admin-card-body">
        <div class="admin-form-grid">
          <!-- Maintenance Mode Toggle -->
          <div class="admin-form-group">
            <label class="admin-form-label">{{ __('Maintenance Mode') }}</label>
            <div class="form-check">
              <input type="hidden" name="maintenance_enabled" value="0">
              <input type="checkbox" class="form-check-input" name="maintenance_enabled" value="1" @checked(old('maintenance_enabled', $setting->maintenance_enabled ?? false))>
              <label class="form-check-label">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                </svg>
                {{ __('Enable Maintenance Mode') }}
              </label>
            </div>
            <div class="admin-text-muted">{{ __('Front visitors see maintenance page; admins still access panel.') }}</div>
          </div>

          <!-- Reopen At -->
          <div class="admin-form-group">
            <label class="admin-form-label">{{ __('Reopen At (optional)') }}</label>
            <input type="datetime-local" name="maintenance_reopen_at" value="{{ old('maintenance_reopen_at', isset($setting->maintenance_reopen_at)? $setting->maintenance_reopen_at->format('Y-m-d\TH:i'): '') }}" class="admin-form-input">
            <div class="admin-text-muted">{{ __('Leave blank for indefinite maintenance.') }}</div>
          </div>
        </div>

        <!-- Maintenance Messages -->
        <div class="admin-form-group">
          <label class="admin-form-label">{{ __('Maintenance Message (per language)') }}</label>
          <div class="admin-form-grid">
            @foreach(($activeLanguages ?? collect()) as $lang)
            <div class="admin-form-group">
              <label class="admin-form-label">{{ strtoupper($lang->code) }}</label>
              <input type="text" name="maintenance_message[{{ $lang->code }}]" class="admin-form-input" placeholder="{{ $lang->name ?? strtoupper($lang->code) }}" value="{{ old('maintenance_message.'.$lang->code, $messages[$lang->code] ?? '') }}" maxlength="255">
            </div>
            @endforeach
          </div>
          <div class="admin-text-muted">{{ __('Shown on the maintenance landing page.') }}</div>
        </div>
      </div>

      <div class="admin-card-footer">
        <div class="admin-flex-end">
          <button type="submit" class="admin-btn admin-btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H16L21 8V19C21 20.1046 20.1046 21 19 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M17 21V13H7V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M7 3V8H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            {{ __('Save Maintenance Settings') }}
          </button>
          <a href="{{ route('admin.maintenance-settings.preview') }}" target="_blank" class="admin-btn admin-btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M1 12S5 4 12 4S23 12 23 12S19 20 12 20S1 12 1 12Z" stroke="currentColor" stroke-width="2" />
              <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" />
            </svg>
            {{ __('Preview Page') }}
          </a>
          <a href="{{ route('admin.footer-settings.edit') }}" class="admin-btn admin-btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            {{ __('Back to Footer') }}
          </a>
        </div>
      </div>
    </form>
  </div>
</section>
@endsection