@extends('layouts.admin')
@section('title', __('Tags'))
@section('content')
<section class="admin-order-details-modern">
  <div class="admin-order-wrapper">
    <!-- Header -->
    <div class="admin-order-header">
      <div class="header-left">
        <div class="admin-header-content">
          <div class="admin-header-icon">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
          </div>
          <div class="admin-header-text">
            <h1 class="admin-order-title">{{ __('Blog Tags') }}</h1>
            <p class="admin-order-subtitle">{{ __('Manage blog tags and create new ones') }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="admin-order-grid-modern">
      <!-- Create Tag Form -->
      <div class="admin-modern-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M12 5v14m7-7H5" />
            </svg>
            {{ __('Create Tag') }}
          </h2>
        </div>
        <div class="admin-card-body">
          <form method="POST" action="{{ route('admin.blog.tags.store') }}">
            @csrf
            <div class="admin-form-group">
              <label class="admin-form-label">{{ __('Name') }}</label>
              <input name="name" class="admin-form-input" required placeholder="{{ __('Enter tag name') }}">
            </div>
            <div class="admin-form-group">
              <label class="admin-form-label">{{ __('Slug') }}</label>
              <input name="slug" class="admin-form-input" placeholder="auto" readonly>
              <div class="admin-text-muted small">{{ __('Slug will be generated from the tag name.') }}</div>
            </div>
            <div class="admin-flex-end">
              <button type="submit" class="admin-btn admin-btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M5 13l4 4L19 7" />
                </svg>
                {{ __('Save') }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Tags List -->
      <div class="admin-modern-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            {{ __('Tags List') }}
          </h2>
          <div class="admin-badge-count">{{ $tags->count() }} {{ __('tags') }}</div>
        </div>
        <div class="admin-card-body">
          @if($tags->count() > 0)
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>{{ __('Name') }}</th>
                  <th>{{ __('Updated') }}</th>
                  <th width="300">{{ __('Actions') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($tags as $tag)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="admin-item-placeholder admin-item-placeholder-primary me-3">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                      </div>
                      <div>
                        <div class="fw-bold">{{ $tag->name }}</div>
                        <small class="admin-text-muted">/{{ $tag->slug }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-1">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span class="admin-text-muted">{{ $tag->updated_at->diffForHumans() }}</span>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex gap-2 align-items-center">
                      <form method="POST" action="{{ route('admin.blog.tags.update', $tag) }}" class="d-flex align-items-center gap-2">
                        @csrf
                        @method('PUT')
                        <input name="name" value="{{ $tag->name }}" class="form-control form-control-sm" style="width: 120px;">
                        <input name="slug" value="{{ $tag->slug }}" class="form-control form-control-sm" style="width: 120px;" readonly>
                        <button type="submit" class="btn btn-sm btn-outline-primary" title="{{ __('Save') }}">
                          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 13l4 4L19 7" />
                          </svg>
                        </button>
                      </form>
                      <form method="POST" action="{{ route('admin.blog.tags.destroy', $tag) }}" class="js-confirm" data-confirm="{{ __('Are you sure you want to delete this tag?') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
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
            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            <h3>{{ __('No Tags Found') }}</h3>
            <p>{{ __('Get started by creating your first blog tag') }}</p>
          </div>
          @endif
        </div>
        @if($tags->hasPages())
        <div class="admin-card-footer-pagination">
          <div class="pagination-info">
            {{ __('Showing') }} {{ $tags->firstItem() }} {{ __('to') }} {{ $tags->lastItem() }} {{ __('of') }} {{ $tags->total() }} {{ __('results') }}
          </div>
          <div class="pagination-links">
            {{ $tags->links() }}
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</section>
@endsection