@extends('layouts.admin')
@section('title', __('Categories'))
@section('content')
<section class="admin-order-details-modern">
  <div class="admin-order-wrapper">
    <!-- Header -->
    <div class="admin-order-header">
      <div class="header-left">
        <div class="admin-header-content">
          <div class="admin-header-icon">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
          <div class="admin-header-text">
            <h1 class="admin-order-title">{{ __('Blog Categories') }}</h1>
            <p class="admin-order-subtitle">{{ __('Manage blog categories and organize your content') }}</p>
          </div>
        </div>
      </div>
      <div class="header-actions">
        <a href="{{ route('admin.blog.categories.create') }}" class="admin-btn admin-btn-primary">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 5v14m7-7H5" />
          </svg>
          {{ __('Create Category') }}
        </a>
      </div>
    </div>

    <!-- Categories Table -->
    <div class="admin-modern-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          {{ __('Categories') }}
        </h2>
        <div class="admin-badge-count">{{ $categories->count() }} {{ __('categories') }}</div>
      </div>
      <div class="admin-card-body">
        @if($categories->count() > 0)
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Parent') }}</th>
                <th>{{ __('Updated') }}</th>
                <th width="120">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($categories as $cat)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="admin-item-placeholder admin-item-placeholder-primary me-3">
                      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                      </svg>
                    </div>
                    <div>
                      <div class="fw-bold">{{ $cat->name }}</div>
                      <small class="admin-text-muted">/{{ $cat->slug }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  @if($cat->parent)
                  <span class="badge bg-secondary">{{ $cat->parent->name }}</span>
                  @else
                  <span class="admin-text-muted">-</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-1">
                      <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="admin-text-muted">{{ $cat->updated_at->diffForHumans() }}</span>
                  </div>
                </td>
                <td>
                  <div class="d-flex gap-2">
                    <a href="{{ route('admin.blog.categories.edit', $cat) }}" class="btn btn-sm btn-outline-secondary">
                      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </a>
                    <form method="POST" action="{{ route('admin.blog.categories.destroy', $cat) }}" class="js-confirm" data-confirm="{{ __('Are you sure you want to delete this category?') }}">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger">
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
            <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          <h3>{{ __('No Categories Found') }}</h3>
          <p>{{ __('Get started by creating your first blog category') }}</p>
          <a href="{{ route('admin.blog.categories.create') }}" class="admin-btn admin-btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M12 5v14m7-7H5" />
            </svg>
            {{ __('Create First Category') }}
          </a>
        </div>
        @endif
      </div>
      @if($categories->hasPages())
      <div class="admin-card-footer-pagination">
        <div class="pagination-info">
          {{ __('Showing') }} {{ $categories->firstItem() }} {{ __('to') }} {{ $categories->lastItem() }} {{ __('of') }} {{ $categories->total() }} {{ __('results') }}
        </div>
        <div class="pagination-links">
          {{ $categories->links() }}
        </div>
      </div>
      @endif
    </div>
  </div>
</section>
@endsection