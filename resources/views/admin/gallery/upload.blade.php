@extends('layouts.admin')
@section('title', __('Upload Image'))
@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">@lang('Upload Images')</h1>
                        <p class="admin-order-subtitle">@lang('Upload and manage your gallery images with SEO optimization')</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.gallery.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    @lang('Back to Gallery')
                </a>
            </div>
        </div>

        @if($errors->any())
        <div class="admin-modern-card admin-mb-1-5">
            <div class="admin-card-body">
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Upload Form -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    @lang('Upload Images')
                </h3>
            </div>
            <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="admin-card-body">
                @csrf

                <div class="admin-form-group">
                    <label class="admin-form-label">@lang('Images') <span class="text-danger">*</span></label>
                    <input type="file" name="images[]" class="admin-form-input" accept="image/*" multiple required>
                    <div class="admin-text-muted">@lang('You can select multiple images (max 15, each up to 4MB).')</div>
                </div>

                <div class="admin-form-grid">
                    <div class="admin-form-group">
                        <label class="admin-form-label">@lang('SEO Title')</label>
                        <input type="text" name="title" class="admin-form-input" maxlength="150" value="{{ old('title') }}">
                        <div class="admin-text-muted">@lang('Maximum 150 characters')</div>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">@lang('ALT Text')</label>
                        <input type="text" name="alt" class="admin-form-input" maxlength="150" value="{{ old('alt') }}">
                        <div class="admin-text-muted">@lang('Alternative text for accessibility')</div>
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label">@lang('SEO Description')</label>
                    <textarea name="description" class="admin-form-input" rows="3" maxlength="500">{{ old('description') }}</textarea>
                    <div class="admin-text-muted">@lang('Maximum 500 characters')</div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label">@lang('Tags')</label>
                    <input type="text" name="tags" class="admin-form-input" maxlength="255" value="{{ old('tags') }}" placeholder="tag1, tag2, tag3">
                    <div class="admin-text-muted">@lang('Comma separated tags')</div>
                </div>

                <div class="admin-card-footer">
                    <div class="admin-flex-end">
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            @lang('Upload Images')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection