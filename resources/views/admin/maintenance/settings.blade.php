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
            <i class="fas fa-cog"></i>
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
          <i class="fas fa-cog"></i>
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
                <i class="fas fa-check"></i>
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
            <i class="fas fa-save"></i>
            {{ __('Save Maintenance Settings') }}
          </button>
          <a href="{{ route('admin.maintenance-settings.preview') }}" target="_blank" class="admin-btn admin-btn-secondary">
            <i class="fas fa-eye"></i>
            {{ __('Preview Page') }}
          </a>
          <a href="{{ route('admin.footer-settings.edit') }}" class="admin-btn admin-btn-outline">
            <i class="fas fa-arrow-left"></i>
            {{ __('Back to Footer') }}
          </a>
        </div>
      </div>
    </form>
  </div>
</section>
@endsection