@extends('layouts.admin')
@section('title', __('Edit Image'))
@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-image"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">@lang('Edit Image')</h1>
                        <p class="admin-order-subtitle">@lang('Update image SEO data and metadata')</p>
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

        <div class="row">
            <!-- Image Preview -->
            <div class="col-md-4">
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">
                            <i class="fas fa-image"></i>
                            @lang('Image Preview')
                        </h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="ratio ratio-1x1 bg-light rounded">
                            <img src="{{ $image->webp_path ? asset('storage/'.$image->webp_path) : asset('storage/'.$image->original_path) }}"
                                class="img-fluid rounded obj-cover"
                                alt="{{ $image->alt }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="col-md-8">
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">
                            <i class="fas fa-edit"></i>
                            @lang('Edit Image Information')
                        </h3>
                    </div>
                    <form action="{{ route('admin.gallery.update', $image) }}" method="POST" class="admin-card-body">
                        @csrf
                        @method('PUT')

                        <div class="admin-form-group">
                            <label class="admin-form-label">@lang('SEO Title')</label>
                            <input type="text" name="title" class="admin-form-input" maxlength="150" value="{{ old('title', $image->title) }}">
                            <div class="admin-text-muted">@lang('Maximum 150 characters')</div>
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">@lang('SEO Description')</label>
                            <textarea name="description" class="admin-form-input" rows="3" maxlength="500">{{ old('description', $image->description) }}</textarea>
                            <div class="admin-text-muted">@lang('Maximum 500 characters')</div>
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">@lang('ALT Text')</label>
                            <input type="text" name="alt" class="admin-form-input" maxlength="150" value="{{ old('alt', $image->alt) }}">
                            <div class="admin-text-muted">@lang('Alternative text for accessibility')</div>
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">@lang('Tags')</label>
                            <input type="text" name="tags" class="admin-form-input" maxlength="255" value="{{ old('tags', $image->tags) }}" placeholder="tag1, tag2">
                            <div class="admin-text-muted">@lang('Comma separated tags')</div>
                        </div>

                        <div class="admin-card-footer">
                            <div class="admin-flex-end">
                                <button type="submit" class="admin-btn admin-btn-primary">
                                    <i class="fas fa-save"></i>
                                    @lang('Save Changes')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Delete Section -->
                <div class="admin-modern-card admin-mt-1-5">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">
                            <i class="fas fa-trash"></i>
                            @lang('Danger Zone')
                        </h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="admin-info-grid">
                            <div class="admin-info-item">
                                <div class="admin-info-label">
                                    <i class="fas fa-info-circle"></i>
                                    @lang('Delete Image')
                                </div>
                                <div class="admin-info-value">
                                    <form action="{{ route('admin.gallery.destroy', $image) }}" method="POST" class="js-confirm" data-confirm="@lang('Delete image?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-btn admin-btn-danger">
                                            <i class="fas fa-trash"></i>
                                            @lang('Delete Image')
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection