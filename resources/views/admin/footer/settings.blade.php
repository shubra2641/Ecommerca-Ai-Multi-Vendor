@extends('layouts.admin')

@section('title', __('Footer Settings'))

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">{{ $footerSettingsTitle ?? __('Footer Settings') }}</h1>
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <strong>{{ __('Please fix the following errors:') }}</strong>
      <ul class="mb-0 small">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  <form method="POST" action="{{ route('admin.footer-settings.update') }}" enctype="multipart/form-data" class="card p-3 shadow-sm">
    @csrf
    @method('PUT')

    <h5 class="mt-2">{{ __('Sections Visibility') }}</h5>
    <div class="row g-2 mb-3">
      @foreach(['support_bar'=>'Support Bar','apps'=>'Apps / Downloads','social'=>'Social Links','pages'=>'Pages','payments'=>'Payments'] as $k=>$lbl)
        <div class="col-6 col-md-3">
          <label class="form-check">
            <input type="checkbox" class="form-check-input" name="sections[{{ $k }}]" value="1" @checked($sections[$k])>
            <span class="form-check-label">{{ __($lbl) }}</span>
          </label>
        </div>
      @endforeach
    </div>

    <hr>
    <h5>{{ __('Support Text (multilingual)') }}</h5>
    <div class="accordion" id="supportTexts">
      @foreach($activeLanguages as $lang)
        <div class="accordion-item mb-2">
          <h2 class="accordion-header" id="heading-{{ $lang->code }}">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $lang->code }}">{{ strtoupper($lang->code) }}</button>
          </h2>
          <div id="collapse-{{ $lang->code }}" class="accordion-collapse collapse" data-bs-parent="#supportTexts">
            <div class="accordion-body">
              <div class="mb-2">
                <label class="form-label">{{ __('Support Heading') }}</label>
                <input name="footer_support_heading[{{ $lang->code }}]" value="{{ old('footer_support_heading.'.$lang->code, $setting->footer_support_heading[$lang->code] ?? '') }}" class="form-control" maxlength="120">
              </div>
              <div class="mb-2">
                <label class="form-label">{{ __('Support Subheading') }}</label>
                <input name="footer_support_subheading[{{ $lang->code }}]" value="{{ old('footer_support_subheading.'.$lang->code, $setting->footer_support_subheading[$lang->code] ?? '') }}" class="form-control" maxlength="180">
              </div>
              <div class="mb-2">
                <label class="form-label">{{ __('Rights Line') }}</label>
                <input name="rights_i18n[{{ $lang->code }}]" value="{{ old('rights_i18n.'.$lang->code, $setting->rights_i18n[$lang->code] ?? ($lang->is_default ? $setting->rights : '')) }}" class="form-control" maxlength="255">
              </div>
              <hr>
              <div class="row g-2">
                <div class="col-md-6 mb-2">
                  <label class="form-label">{{ __('Help Center Label') }}</label>
                  <input name="footer_labels[help_center][{{ $lang->code }}]" value="{{ old('footer_labels.help_center.'.$lang->code, $setting->footer_labels['help_center'][$lang->code] ?? '') }}" class="form-control" maxlength="120">
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">{{ __('Email Support Label') }}</label>
                  <input name="footer_labels[email_support][{{ $lang->code }}]" value="{{ old('footer_labels.email_support.'.$lang->code, $setting->footer_labels['email_support'][$lang->code] ?? '') }}" class="form-control" maxlength="120">
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">{{ __('Phone Support Label') }}</label>
                  <input name="footer_labels[phone_support][{{ $lang->code }}]" value="{{ old('footer_labels.phone_support.'.$lang->code, $setting->footer_labels['phone_support'][$lang->code] ?? '') }}" class="form-control" maxlength="120">
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">{{ __('Apps Section Heading') }}</label>
                  <input name="footer_labels[apps_heading][{{ $lang->code }}]" value="{{ old('footer_labels.apps_heading.'.$lang->code, $setting->footer_labels['apps_heading'][$lang->code] ?? '') }}" class="form-control" maxlength="120">
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">{{ __('Social Section Heading') }}</label>
                  <input name="footer_labels[social_heading][{{ $lang->code }}]" value="{{ old('footer_labels.social_heading.'.$lang->code, $setting->footer_labels['social_heading'][$lang->code] ?? '') }}" class="form-control" maxlength="120">
                </div>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <hr>
    <h5>{{ __('App Download Badges') }}</h5>
    <div class="row">
      @foreach($appLinks as $platform=>$link)
        <div class="col-md-4 mb-3">
          <div class="border rounded p-2 h-100">
            <h6 class="mb-2">{{ ucfirst($platform) }}</h6>
            <label class="form-check mb-2">
              <input type="checkbox" class="form-check-input" name="app_links[{{ $platform }}][enabled]" value="1" @checked($link['enabled'])>
              <span class="form-check-label">{{ __('Enabled') }}</span>
            </label>
            <div class="mb-2">
              <label class="form-label">URL</label>
              <input type="url" class="form-control" name="app_links[{{ $platform }}][url]" value="{{ $link['url'] }}" placeholder="https://...">
            </div>
            <div class="mb-2">
              <label class="form-label">{{ __('Order') }}</label>
              <input type="number" class="form-control" name="app_links[{{ $platform }}][order]" value="{{ $link['order'] }}" min="0" max="50">
            </div>
            <div class="mb-2">
              <label class="form-label">{{ __('Badge Image (max 180x54 suggested)') }}</label>
              @if(!empty($link['image']))
                <div class="mb-1"><img src="{{ asset('storage/'.$link['image']) }}" alt="badge" class="img-badge-thumb"></div>
                <input type="hidden" name="app_links[{{ $platform }}][existing_image]" value="{{ $link['image'] }}">
              @endif
              <input type="file" class="form-control" name="app_links[{{ $platform }}][image]" accept="image/*">
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <hr>
    <h5>{{ __('Footer Pages') }}</h5>
    <p class="text-muted small">{{ __('Select pages to display (max 8). Order will follow selection order.') }}</p>
    @if($pages->count() > 0)
      <select name="footer_pages[]" class="form-select" multiple size="8">
        @foreach($pages as $p)
          <option value="{{ $p->id }}" @selected(in_array($p->id, $setting->footer_pages ?? []))>{{ $footerPageTitles[$p->id] ?? ('#'.$p->id) }} @if(isset($p->identifier) && $p->identifier) ({{ $p->identifier }}) @endif</option>
        @endforeach
      </select>
    @else
      <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> {{ __('No pages available. Pages will be available once the pages system is set up.') }}
      </div>
    @endif

    <hr>
    <h5>{{ __('Payment Methods (one per line, max 6 shown)') }}</h5>
    <textarea name="footer_payment_methods" class="form-control" rows="3" placeholder="VISA\nMC\nPAYPAL">{{ implode("\n", $setting->footer_payment_methods ?? []) }}</textarea>

    <div class="mt-4">
      <button class="btn btn-primary">{{ __('Save Footer Settings') }}</button>
    </div>
  </form>
</div>
@endsection
