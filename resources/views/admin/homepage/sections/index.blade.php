@extends('layouts.admin')
@section('title', __('Homepage Sections'))
@section('content')
<section class="admin-order-details-modern">
  <div class="admin-order-wrapper">
    <!-- Header -->
    <div class="admin-order-header">
      <div class="header-left">
        <div class="admin-header-content">
          <div class="admin-header-icon">
            <i class="fas fa-th-large"></i>
          </div>
          <div class="admin-header-text">
            <h1 class="admin-order-title">{{ __('Homepage Sections') }}</h1>
            <p class="admin-order-subtitle">{{ __('Configure and manage homepage content sections') }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Sections Configuration Form -->
    <div class="admin-modern-card">
      <div class="admin-card-header">
        <h3 class="admin-card-title">
          <i class="fas fa-th-large"></i>
          {{ __('Sections Configuration') }}
        </h3>
        <div class="admin-badge-count">{{ $sections->count() }} {{ __('sections') }}</div>
      </div>
      <form method="POST" action="{{ route('admin.homepage.sections.update') }}" class="admin-card-body">
        @csrf
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ __('Key') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Order') }}</th>
                <th>{{ __('Limit') }}</th>
                <th class="table-head-wide">{{ __('Titles') }}</th>
                <th class="table-head-wide">{{ __('Subtitles') }}</th>
                <th class="table-head-wide">{{ __('CTA Labels') }}</th>
                <th class="table-head-medium">{{ __('CTA Settings') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($sections as $section)
              <tr>
                <td>
                  <span class="admin-badge">{{ $loop->iteration }}</span>
                  <input type="hidden" name="sections[{{ $loop->index }}][id]" value="{{ $section->id }}">
                </td>
                <td>
                  <code class="admin-code">{{ $section->key }}</code>
                </td>
                <td>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="sections[{{ $loop->index }}][enabled]" value="1" @checked($section->enabled)>
                    <label class="form-check-label">
                      <i class="fas fa-check-circle"></i>
                    </label>
                  </div>
                </td>
                <td>
                  <input type="number" class="admin-form-input" name="sections[{{ $loop->index }}][sort_order]" value="{{ $section->sort_order }}">
                </td>
                <td>
                  <input type="number" class="admin-form-input" name="sections[{{ $loop->index }}][item_limit]" value="{{ $section->item_limit }}" min="1" max="100">
                </td>
                <td>
                  @foreach($activeLanguages as $lang)
                  <div class="admin-mb-half d-flex align-items-center gap-1">
                    <span class="admin-badge admin-badge-secondary">{{ strtoupper($lang->code) }}</span>
                    <input type="text" class="admin-form-input" name="sections[{{ $loop->parent->index }}][title][{{ $lang->code }}]" value="{{ old('sections.'.$loop->parent->index.'.title.'.$lang->code, $section->title_i18n[$lang->code] ?? '') }}" placeholder="{{ $lang->code }}">
                  </div>
                  @endforeach
                </td>
                <td>
                  @foreach($activeLanguages as $lang)
                  <div class="admin-mb-half d-flex align-items-center gap-1">
                    <span class="admin-badge admin-badge-secondary">{{ strtoupper($lang->code) }}</span>
                    <input type="text" class="admin-form-input" name="sections[{{ $loop->parent->index }}][subtitle][{{ $lang->code }}]" value="{{ old('sections.'.$loop->parent->index.'.subtitle.'.$lang->code, $section->subtitle_i18n[$lang->code] ?? '') }}" placeholder="{{ $lang->code }}">
                  </div>
                  @endforeach
                </td>
                <td>
                  @foreach($activeLanguages as $lang)
                  <div class="admin-mb-half d-flex align-items-center gap-1">
                    <span class="admin-badge admin-badge-info">{{ strtoupper($lang->code) }}</span>
                    <input type="text" class="admin-form-input" name="sections[{{ $loop->parent->index }}][cta_label][{{ $lang->code }}]" value="{{ old('sections.'.$loop->parent->index.'.cta_label.'.$lang->code, $section->cta_label_i18n[$lang->code] ?? '') }}" placeholder="{{ __('CTA') }} {{ $lang->code }}">
                  </div>
                  @endforeach
                </td>
                <td>
                  <div class="form-check form-switch admin-mb-half">
                    <input class="form-check-input" type="checkbox" name="sections[{{ $loop->index }}][cta_enabled]" value="1" @checked($section->cta_enabled)>
                    <label class="form-check-label small">
                      <i class="fas fa-check-circle"></i>
                      {{ __('Enabled') }}
                    </label>
                  </div>
                  <input type="text" class="admin-form-input" name="sections[{{ $loop->index }}][cta_url]" value="{{ $section->cta_url }}" placeholder="/products">
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="admin-card-footer">
          <div class="admin-flex-between">
            <div class="admin-flex-start gap-2">
              <button type="submit" formaction="{{ route('admin.homepage.sections.ai-suggest') }}" class="admin-btn admin-btn-secondary">
                <i class="fas fa-magic"></i>
                {{ __('AI Generate All') }}
              </button>
              <button type="submit" class="admin-btn admin-btn-primary">
                <i class="fas fa-save"></i>
                {{ __('Save Changes') }}
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection