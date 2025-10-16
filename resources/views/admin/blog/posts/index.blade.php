@extends('layouts.admin')
@section('title', __('Posts'))
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item active">{{ __('Posts') }}</li>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('Posts') }}</h1>
        <p class="page-description">{{ __('Manage blog posts and content') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            {{ __('Create Post') }}
        </a>
    </div>
</div>

<!-- Filters -->
<form class="card card-body mb-3 p-3 shadow-sm" method="GET">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label">{{ __('Search') }}</label>
            <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="{{ __('Search') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('Category') }}</label>
            <select name="category_id" class="form-select">
                <option value="">{{ __('All') }}</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @if($categoryId==$c->id) selected @endif>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('Published') }}</label>
            <select name="published" class="form-select">
                <option value="">{{ __('All') }}</option>
                <option value="1" @if($published==='1') selected @endif>{{ __('Yes') }}</option>
                <option value="0" @if($published==='0') selected @endif>{{ __('No') }}</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <div>
                <button class="btn btn-outline-primary me-1">
                    <i class="fas fa-search"></i> {{ __('Filter') }}
                </button>
                <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a>
            </div>
        </div>
    </div>
</form>

<!-- Posts Table -->
<div class="card modern-card">
    <div class="card-header">
        <h3 class="card-title">{{ __('Posts List') }}</h3>
    </div>
    <div class="card-body">
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
                                <div class="fw-semibold">{{ $post->title }}</div>
                                <div class="text-muted small">/{{ $post->slug }}</div>
                            </td>
                            <td>{{ $post->category->name ?? '-' }}</td>
                            <td>
                                @if($post->published)
                                    <span class="badge bg-success">{{ __('Yes') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('No') }}</span>
                                @endif
                            </td>
                            <td>{{ $post->updated_at->diffForHumans() }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.blog.posts.edit',$post) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.blog.posts.destroy',$post) }}" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}" data-confirm="{{ __('Delete?') }}">
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
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="pagination-info">
                    {{ __('Showing') }} {{ $posts->firstItem() }} {{ __('to') }} {{ $posts->lastItem() }} 
                    {{ __('of') }} {{ $posts->total() }} {{ __('results') }}
                </div>
                {{ $posts->withQueryString()->links() }}
            </div>
        @else
            <div class="empty-state text-center py-5">
                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                <h3>{{ __('No Posts Found') }}</h3>
                <p class="text-muted">{{ __('No posts match your current filters. Try adjusting your search criteria.') }}</p>
                <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('Add First Post') }}
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
