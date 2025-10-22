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
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Blog Posts') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage blog posts and content') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.blog.posts.create') }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('Create Post') }}
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <i class="fas fa-filter"></i>
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
                            <i class="fas fa-search"></i>
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('admin.blog.posts.index') }}" class="admin-btn admin-btn-secondary">
                            <i class="fas fa-undo"></i>
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
                    <i class="fas fa-newspaper"></i>
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
                                            <i class="fas fa-newspaper"></i>
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
                                        <i class="fas fa-clock me-1"></i>
                                        <span class="admin-text-muted">{{ $post->updated_at->diffForHumans() }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.blog.posts.edit', $post) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.blog.posts.destroy', $post) }}" class="js-confirm" data-confirm="{{ __('Are you sure you want to delete this post?') }}">
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
                    <i class="fas fa-newspaper" style="font-size: 48px;"></i>
                    <h3>{{ __('No Posts Found') }}</h3>
                    <p>{{ __('No posts match your current filters. Try adjusting your search criteria.') }}</p>
                    <a href="{{ route('admin.blog.posts.create') }}" class="admin-btn admin-btn-primary">
                        <i class="fas fa-plus"></i>
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