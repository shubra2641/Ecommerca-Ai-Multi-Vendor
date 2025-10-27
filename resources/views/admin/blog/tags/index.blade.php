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
            <i class="fas fa-tags"></i>
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
            <i class="fas fa-plus"></i>
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
                <i class="fas fa-check"></i>
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
            <i class="fas fa-tags"></i>
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
                        <i class="fas fa-tag"></i>
                      </div>
                      <div>
                        <div class="fw-bold">{{ $tag->name }}</div>
                        <small class="admin-text-muted">/{{ $tag->slug }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <i class="fas fa-clock me-1"></i>
                      <span class="admin-text-muted">{{ $tag->updated_at->diffForHumans() }}</span>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex gap-2 align-items-center">
                      <form method="POST" action="{{ route('admin.blog.tags.update', $tag) }}" class="d-flex align-items-center gap-2">
                        @csrf
                        @method('PUT')
                        <input name="name" value="{{ $tag->name }}" class="form-control form-control-sm">
                        <input name="slug" value="{{ $tag->slug }}" class="form-control form-control-sm" readonly>
                        <button type="submit" class="btn btn-sm btn-outline-primary" title="{{ __('Save') }}">
                          <i class="fas fa-check"></i>
                        </button>
                      </form>
                      <form method="POST" action="{{ route('admin.blog.tags.destroy', $tag) }}" class="js-confirm" data-confirm="{{ __('Are you sure you want to delete this tag?') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                          <i class="fas fa-trash"></i>
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
            <i class="fas fa-tags icon-large"></i>
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