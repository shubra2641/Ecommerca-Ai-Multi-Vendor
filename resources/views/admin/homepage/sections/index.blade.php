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
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
              <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
              <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
            </svg>
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
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
            <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2" />
            <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
          </svg>
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
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                      </svg>
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
                    <input type="text" class="admin-form-input" name="sections[{{ $loop->parent->index }}][title][{{ $lang->code }}]" value="{{ $section->title_i18n[$lang->code] ?? '' }}" placeholder="{{ $lang->code }}">
                  </div>
                  @endforeach
                </td>
                <td>
                  @foreach($activeLanguages as $lang)
                  <div class="admin-mb-half d-flex align-items-center gap-1">
                    <span class="admin-badge admin-badge-secondary">{{ strtoupper($lang->code) }}</span>
                    <input type="text" class="admin-form-input" name="sections[{{ $loop->parent->index }}][subtitle][{{ $lang->code }}]" value="{{ $section->subtitle_i18n[$lang->code] ?? '' }}" placeholder="{{ $lang->code }}">
                  </div>
                  @endforeach
                </td>
                <td>
                  @foreach($activeLanguages as $lang)
                  <div class="admin-mb-half d-flex align-items-center gap-1">
                    <span class="admin-badge admin-badge-info">{{ strtoupper($lang->code) }}</span>
                    <input type="text" class="admin-form-input" name="sections[{{ $loop->parent->index }}][cta_label][{{ $lang->code }}]" value="{{ $section->cta_label_i18n[$lang->code] ?? '' }}" placeholder="{{ __('CTA') }} {{ $lang->code }}">
                  </div>
                  @endforeach
                </td>
                <td>
                  <div class="form-check form-switch admin-mb-half">
                    <input class="form-check-input" type="checkbox" name="sections[{{ $loop->index }}][cta_enabled]" value="1" @checked($section->cta_enabled)>
                    <label class="form-check-label small">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                      </svg>
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
          <div class="admin-flex-end">
            <button type="submit" class="admin-btn admin-btn-primary">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H16L21 8V19C21 20.1046 20.1046 21 19 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M17 21V13H7V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M7 3V8H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              {{ __('Save Changes') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection