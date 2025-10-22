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
            <i class="fas fa-tags"></i>
          </div>
          <div class="admin-header-text">
            <h1 class="admin-order-title">{{ __('Blog Categories') }}</h1>
            <p class="admin-order-subtitle">{{ __('Manage blog categories and organize your content') }}</p>
          </div>
        </div>
      </div>
      <div class="header-actions">
        <a href="{{ route('admin.blog.categories.create') }}" class="admin-btn admin-btn-primary">
          <i class="fas fa-plus"></i>
          {{ __('Create Category') }}
        </a>
      </div>
    </div>

    <!-- Categories Table -->
    <div class="admin-modern-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">
          <i class="fas fa-tags"></i>
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
                      <i class="fas fa-tag"></i>
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
                    <i class="fas fa-clock me-1"></i>
                    <span class="admin-text-muted">{{ $cat->updated_at->diffForHumans() }}</span>
                  </div>
                </td>
                <td>
                  <div class="d-flex gap-2">
                    <a href="{{ route('admin.blog.categories.edit', $cat) }}" class="btn btn-sm btn-outline-secondary">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.blog.categories.destroy', $cat) }}" class="js-confirm" data-confirm="{{ __('Are you sure you want to delete this category?') }}">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger">
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
          <i class="fas fa-tags"></i>
          <h3>{{ __('No Categories Found') }}</h3>
          <p>{{ __('Get started by creating your first blog category') }}</p>
          <a href="{{ route('admin.blog.categories.create') }}" class="admin-btn admin-btn-primary">
            <i class="fas fa-plus"></i>
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