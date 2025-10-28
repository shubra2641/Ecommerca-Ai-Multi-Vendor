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
                        <i class="fas fa-upload"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">@lang('Upload Images')</h1>
                        <p class="admin-order-subtitle">@lang('Upload and manage your gallery images with SEO optimization')</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.gallery.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    @lang('Back to Gallery')
                </a>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="fas fa-upload"></i>
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
                            <i class="fas fa-upload"></i>
                            @lang('Upload Images')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection