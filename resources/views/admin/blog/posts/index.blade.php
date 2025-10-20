@extends('layouts.admin')
@section('title', __('Posts'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Blog Posts') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage blog posts and content') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.blog.posts.create') }}" class="admin-btn admin-btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 5v14m7-7H5" />
                    </svg>
                    {{ __('Create Post') }}
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                    </svg>
                    {{ __('Filters') }}
                </h2>
            </div>
            <div class="admin-card-body">
                <form method="GET" class="admin-filter-grid">
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Search') }}</label>
                        <input type="text" name="q" value="{{ $q }}" class="admin-form-input" placeholder="{{ __('Search posts...') }}">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Category') }}</label>
                        <select name="category_id" class="admin-form-select">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}" @if($categoryId==$c->id) selected @endif>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Published') }}</label>
                        <select name="published" class="admin-form-select">
                            <option value="">{{ __('All') }}</option>
                            <option value="1" @if($published==='1' ) selected @endif>{{ __('Published') }}</option>
                            <option value="0" @if($published==='0' ) selected @endif>{{ __('Draft') }}</option>
                        </select>
                    </div>
                    <div class="admin-filter-actions">
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('admin.blog.posts.index') }}" class="admin-btn admin-btn-secondary">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ __('Reset') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Posts Table -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('Posts List') }}
                </h2>
                <div class="admin-badge-count">{{ $posts->count() }} {{ __('posts') }}</div>
            </div>
            <div class="admin-card-body">
                @if($posts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Published') }}</th>
                                <th>{{ __('Updated') }}</th>
                                <th width="150">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($posts as $post)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="admin-item-placeholder admin-item-placeholder-primary me-3">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $post->title }}</div>
                                            <small class="admin-text-muted">/{{ $post->slug }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($post->category)
                                    <span class="badge bg-secondary">{{ $post->category->name }}</span>
                                    @else
                                    <span class="admin-text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($post->published)
                                    <span class="badge bg-success">{{ __('Published') }}</span>
                                    @else
                                    <span class="badge bg-secondary">{{ __('Draft') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-1">
                                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="admin-text-muted">{{ $post->updated_at->diffForHumans() }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.blog.posts.edit', $post) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.blog.posts.destroy', $post) }}" class="js-confirm" data-confirm="{{ __('Are you sure you want to delete this post?') }}">
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
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3>{{ __('No Posts Found') }}</h3>
                    <p>{{ __('No posts match your current filters. Try adjusting your search criteria.') }}</p>
                    <a href="{{ route('admin.blog.posts.create') }}" class="admin-btn admin-btn-primary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 5v14m7-7H5" />
                        </svg>
                        {{ __('Create First Post') }}
                    </a>
                </div>
                @endif
            </div>
            @if($posts->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ __('Showing') }} {{ $posts->firstItem() }} {{ __('to') }} {{ $posts->lastItem() }} {{ __('of') }} {{ $posts->total() }} {{ __('results') }}
                </div>
                <div class="pagination-links">
                    {{ $posts->withQueryString()->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection