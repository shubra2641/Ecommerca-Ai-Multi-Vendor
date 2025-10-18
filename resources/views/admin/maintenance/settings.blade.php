@extends('layouts.admin')

@section('title', __('Maintenance Settings'))

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">{{ __('Maintenance Settings') }}</h1>
  <form method="POST" action="{{ route('admin.maintenance-settings.update') }}" class="card p-3 shadow-sm">
    @csrf
    @method('PUT')
  <div class="row g-3">
      <div class="col-md-4">
        <label class="form-check">
          <input type="hidden" name="maintenance_enabled" value="0">
          <input type="checkbox" class="form-check-input" name="maintenance_enabled" value="1" @checked(old('maintenance_enabled', $setting->maintenance_enabled ?? false))>
          <span class="form-check-label fw-semibold">{{ __('Enable Maintenance Mode') }}</span>
        </label>
        <small class="text-muted d-block mt-1">{{ __('Front visitors see maintenance page; admins still access panel.') }}</small>
      </div>
      <div class="col-md-4">
        <label class="form-label">{{ __('Reopen At (optional)') }}</label>
        <input type="datetime-local" name="maintenance_reopen_at" value="{{ old('maintenance_reopen_at', isset($setting->maintenance_reopen_at)? $setting->maintenance_reopen_at->format('Y-m-d\TH:i'): '') }}" class="form-control">
        <small class="text-muted">{{ __('Leave blank for indefinite maintenance.') }}</small>
      </div>
      <div class="col-md-4">
        <label class="form-label">{{ __('Maintenance Message (per language)') }}</label>
    @foreach(($activeLanguages ?? collect()) as $lang)
      <input type="text" name="maintenance_message[{{ $lang->code }}]" class="form-control mb-2" placeholder="{{ $lang->name ?? strtoupper($lang->code) }}" value="{{ old('maintenance_message.'.$lang->code, $messages[$lang->code] ?? '') }}" maxlength="255">
        @endforeach
        <small class="text-muted">{{ __('Shown on the maintenance landing page.') }}</small>
      </div>
    </div>
    <div class="mt-4">
      <button class="btn btn-primary">{{ __('Save Maintenance Settings') }}</button>
    <a href="{{ route('admin.maintenance-settings.preview') }}" target="_blank" class="btn btn-outline-info ms-2">{{ __('Preview Page') }}</a>
      <a href="{{ route('admin.footer-settings.edit') }}" class="btn btn-outline-secondary ms-2">{{ __('Back to Footer') }}</a>
    </div>
  </form>
</div>
@endsection
