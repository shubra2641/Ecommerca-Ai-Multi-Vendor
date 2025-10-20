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
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
              <path d="M8 12L12 8L16 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M12 8V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
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
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                <path d="M8 12L12 8L16 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M12 8V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
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
                      <div class="admin-item-placeholder" style="width: 70px; height: 50px;">
                        <img src="{{ asset('storage/'.$slide->image) }}"
                          class="img-fluid rounded"
                          alt="slide"
                          style="width: 100%; height: 100%; object-fit: cover;">
                      </div>
                      @else
                      <div class="admin-item-placeholder admin-item-placeholder-gray" style="width: 70px; height: 50px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                          <circle cx="8.5" cy="8.5" r="1.5" stroke="currentColor" stroke-width="2" />
                          <path d="M21 15L16 10L5 21" stroke="currentColor" stroke-width="2" />
                        </svg>
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
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              @if($slide->enabled)
                              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                              <path d="M8 12L12 8L16 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                              @else
                              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                              <path d="M8 12L12 8L16 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                              @endif
                            </svg>
                            {{ $slide->enabled ? __('Disable') : __('Enable') }}
                          </button>
                        </form>
                        <form method="POST" action="{{ route('admin.homepage.slides.destroy',$slide) }}"
                          class="d-inline js-confirm" data-confirm="{{ __('Delete slide?') }}">
                          @csrf @method('DELETE')
                          <button class="admin-btn admin-btn-small admin-btn-danger">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <polyline points="3,6 5,6 21,6" stroke="currentColor" stroke-width="2" />
                              <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6" stroke="currentColor" stroke-width="2" />
                              <line x1="10" y1="11" x2="10" y2="17" stroke="currentColor" stroke-width="2" />
                              <line x1="14" y1="11" x2="14" y2="17" stroke="currentColor" stroke-width="2" />
                            </svg>
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
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                  <path d="M8 12L12 8L16 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M12 8V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
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
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
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
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" />
                  </svg>
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
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                        <line x1="2" y1="12" x2="22" y2="12" stroke="currentColor" stroke-width="2" />
                        <path d="M12 2C14.5013 4.73835 15.9228 8.29203 16 12C15.9228 15.708 14.5013 19.2616 12 22C9.49872 19.2616 8.07725 15.708 8 12C8.07725 8.29203 9.49872 4.73835 12 2Z" stroke="currentColor" stroke-width="2" />
                      </svg>
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
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
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