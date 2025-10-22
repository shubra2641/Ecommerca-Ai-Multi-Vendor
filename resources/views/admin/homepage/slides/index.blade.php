@extends('layouts.admin')
@section('title', __('Homepage Slides'))
@section('content')
<section class="admin-order-details-modern">
  <div class="admin-order-wrapper">
    <!-- Header -->
    <div class="admin-order-header">
      <div class="header-left">
        <div class="admin-header-content">
          <div class="admin-header-icon">
            <i class="fas fa-images"></i>
          </div>
          <div class="admin-header-text">
            <h1 class="admin-order-title">{{ __('Homepage Slides') }}</h1>
            <p class="admin-order-subtitle">{{ __('Manage homepage slider content and promotional slides') }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Slides List -->
      <div class="col-lg-8 order-2 order-lg-1">
        <div class="admin-modern-card">
          <div class="admin-card-header">
            <h3 class="admin-card-title">
              <i class="fas fa-images"></i>
              {{ __('Slides List') }}
            </h3>
            <div class="admin-badge-count">{{ $slides->count() }} {{ __('slides') }}</div>
          </div>
          <div class="admin-card-body">
            @if($slides->count() > 0)
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>{{ __('Image') }}</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Order') }}</th>
                    <th>{{ __('Actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($slides as $slide)
                  <tr>
                    <td>
                      <span class="admin-badge">{{ $loop->iteration }}</span>
                    </td>
                    <td>
                      @if($slide->image)
                      <div class="admin-item-placeholder">
                        <img src="{{ asset('storage/'.$slide->image) }}"
                          class="img-fluid rounded"
                          alt="slide">
                      </div>
                      @else
                      <div class="admin-item-placeholder admin-item-placeholder-gray">
                        <i class="fas fa-image"></i>
                      </div>
                      @endif
                    </td>
                    <td>
                      <div class="admin-item-name">{{ $slide->title ?: '-' }}</div>
                    </td>
                    <td>
                      @if($slide->enabled)
                      <span class="admin-status-badge admin-status-badge-completed">{{ __('On') }}</span>
                      @else
                      <span class="admin-status-badge admin-status-badge-warning">{{ __('Off') }}</span>
                      @endif
                    </td>
                    <td>
                      <span class="admin-stock-value">{{ $slide->sort_order }}</span>
                    </td>
                    <td>
                      <div class="admin-actions-flex">
                        <form method="POST" action="{{ route('admin.homepage.slides.update',$slide) }}" class="d-inline">
                          @csrf @method('PUT')
                          <input type="hidden" name="sort_order" value="{{ $slide->sort_order }}">
                          <input type="hidden" name="enabled" value="{{ $slide->enabled?0:1 }}">
                          <button class="admin-btn admin-btn-small {{ $slide->enabled ? 'admin-btn-warning' : 'admin-btn-success' }}">
                            <i class="fas fa-{{ $slide->enabled ? 'toggle-on' : 'toggle-off' }}"></i>
                            {{ $slide->enabled ? __('Disable') : __('Enable') }}
                          </button>
                        </form>
                        <form method="POST" action="{{ route('admin.homepage.slides.destroy',$slide) }}"
                          class="d-inline js-confirm" data-confirm="{{ __('Delete slide?') }}">
                          @csrf @method('DELETE')
                          <button class="admin-btn admin-btn-small admin-btn-danger">
                            <i class="fas fa-trash"></i>
                            {{ __('Delete') }}
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @else
            <div class="admin-empty-state">
              <div class="admin-notification-icon">
                <i class="fas fa-images" style="font-size: 48px;"></i>
              </div>
              <h3>{{ __('No slides yet.') }}</h3>
              <p>{{ __('Create your first slide to get started.') }}</p>
            </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Add Slide Form -->
      <div class="col-lg-4 order-1 order-lg-2">
        <div class="admin-modern-card">
          <div class="admin-card-header">
            <h3 class="admin-card-title">
              <i class="fas fa-plus"></i>
              {{ __('Add Slide') }}
            </h3>
          </div>
          <form method="POST" action="{{ route('admin.homepage.slides.store') }}" enctype="multipart/form-data" class="admin-card-body">
            @csrf

            <div class="admin-form-group">
              <label class="admin-form-label">{{ __('Image') }} <span class="text-danger">*</span></label>
              <input name="image" type="file" accept="image/*" required class="admin-form-input">
              <div class="admin-text-muted">{{ __('Upload slide image') }}</div>
            </div>

            <div class="admin-form-group">
              <label class="admin-form-label">{{ __('Link URL') }}</label>
              <input name="link_url" type="url" class="admin-form-input" placeholder="https://example.com">
              <div class="admin-text-muted">{{ __('Optional link when slide is clicked') }}</div>
            </div>

            <div class="admin-form-group">
              <label class="admin-form-label">{{ __('Sort Order') }}</label>
              <input name="sort_order" type="number" value="100" class="admin-form-input">
              <div class="admin-text-muted">{{ __('Lower numbers appear first') }}</div>
            </div>

            <div class="admin-form-group">
              <div class="form-check">
                <input type="checkbox" class="form-check-input" name="enabled" value="1" checked id="slide_enabled">
                <label for="slide_enabled" class="form-check-label">
                  <i class="fas fa-check-circle"></i>
                  {{ __('Enabled') }}
                </label>
              </div>
            </div>

            <!-- Language Accordion -->
            <div class="admin-form-group">
              <label class="admin-form-label">{{ __('Content (Multi-language)') }}</label>
              <div class="accordion" id="slideLangAcc">
                @foreach($activeLanguages as $lang)
                <div class="accordion-item">
                  <h2 class="accordion-header" id="sl-head-{{ $lang->code }}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sl-body-{{ $lang->code }}">
                      <i class="fas fa-globe"></i>
                      {{ strtoupper($lang->code) }}
                    </button>
                  </h2>
                  <div id="sl-body-{{ $lang->code }}" class="accordion-collapse collapse" data-bs-parent="#slideLangAcc">
                    <div class="accordion-body">
                      <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Title') }}</label>
                        <input name="title_i18n[{{ $lang->code }}]" class="admin-form-input" maxlength="120" placeholder="{{ __('Enter title for') }} {{ $lang->name }}">
                      </div>
                      <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Subtitle') }}</label>
                        <input name="subtitle_i18n[{{ $lang->code }}]" class="admin-form-input" maxlength="180" placeholder="{{ __('Enter subtitle for') }} {{ $lang->name }}">
                      </div>
                      <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Button Text') }}</label>
                        <input name="button_text_i18n[{{ $lang->code }}]" class="admin-form-input" maxlength="40" placeholder="{{ __('Enter button text for') }} {{ $lang->name }}">
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>

            <div class="admin-card-footer">
              <div class="admin-flex-end">
                <button type="submit" class="admin-btn admin-btn-primary">
                  <i class="fas fa-plus"></i>
                  {{ __('Create Slide') }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection