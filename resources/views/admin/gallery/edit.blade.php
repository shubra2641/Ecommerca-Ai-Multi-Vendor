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
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" stroke="currentColor" stroke-width="2" />
                            <path d="M21 15L16 10L5 21" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">@lang('Edit Image')</h1>
                        <p class="admin-order-subtitle">@lang('Update image SEO data and metadata')</p>
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

        <div class="row">
            <!-- Image Preview -->
            <div class="col-md-4">
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                                <circle cx="8.5" cy="8.5" r="1.5" stroke="currentColor" stroke-width="2" />
                                <path d="M21 15L16 10L5 21" stroke="currentColor" stroke-width="2" />
                            </svg>
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
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M18.5 2.5C18.8978 2.10218 19.4374 1.87868 20 1.87868C20.5626 1.87868 21.1022 2.10218 21.5 2.5C21.8978 2.89782 22.1213 3.43739 22.1213 4C22.1213 4.56261 21.8978 5.10218 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
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
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H16L21 8V19C21 20.1046 20.1046 21 19 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M17 21V13H7V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M7 3V8H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
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
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <polyline points="3,6 5,6 21,6" stroke="currentColor" stroke-width="2" />
                                <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6" stroke="currentColor" stroke-width="2" />
                                <line x1="10" y1="11" x2="10" y2="17" stroke="currentColor" stroke-width="2" />
                                <line x1="14" y1="11" x2="14" y2="17" stroke="currentColor" stroke-width="2" />
                            </svg>
                            @lang('Danger Zone')
                        </h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="admin-info-grid">
                            <div class="admin-info-item">
                                <div class="admin-info-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                                        <line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="2" />
                                        <line x1="12" y1="16" x2="12.01" y2="16" stroke="currentColor" stroke-width="2" />
                                    </svg>
                                    @lang('Delete Image')
                                </div>
                                <div class="admin-info-value">
                                    <form action="{{ route('admin.gallery.destroy', $image) }}" method="POST" class="js-confirm" data-confirm="@lang('Delete image?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-btn admin-btn-danger">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <polyline points="3,6 5,6 21,6" stroke="currentColor" stroke-width="2" />
                                                <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6" stroke="currentColor" stroke-width="2" />
                                                <line x1="10" y1="11" x2="10" y2="17" stroke="currentColor" stroke-width="2" />
                                                <line x1="14" y1="11" x2="14" y2="17" stroke="currentColor" stroke-width="2" />
                                            </svg>
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