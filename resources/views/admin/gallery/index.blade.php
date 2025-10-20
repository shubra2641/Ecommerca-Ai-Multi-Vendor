@extends('layouts.admin')
@section('title', __('Gallery'))
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
                        <h1 class="admin-order-title">{{ __('Gallery') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage images, SEO data, tags and logo usage') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.gallery.create') }}" class="admin-btn admin-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M16 13H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M16 17H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M10 9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Upload') }}
                </a>
                <button type="button" class="admin-btn admin-btn-secondary" id="multiUploadBtn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Multi Upload') }}
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="admin-modern-card admin-mb-1-5">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46 22,3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Search & Filter') }}
                </h3>
            </div>
            <form method="GET" action="{{ route('admin.gallery.index') }}" class="admin-card-body">
                <div class="admin-form-grid">
                    <div class="admin-form-group">
                        <label class="admin-form-label">@lang('Search')</label>
                        <input type="text" name="q" value="{{ $q ?? '' }}" class="admin-form-input" placeholder="@lang('Title / description / tag')">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">@lang('Tag')</label>
                        <select name="tag" class="admin-form-input">
                            <option value="">@lang('All')</option>
                            @foreach(($distinctTags ?? []) as $t)
                            <option value="{{ $t }}" @selected(($tag ?? '' )===$t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">&nbsp;</label>
                        <div class="admin-actions-flex">
                            <button class="admin-btn admin-btn-primary">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" />
                                    <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" />
                                </svg>
                                @lang('Filter')
                            </button>
                            <a href="{{ route('admin.gallery.index') }}" class="admin-btn admin-btn-secondary">@lang('Reset')</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        @if(!$images->count())
        <div class="admin-empty-state">
            <div class="admin-notification-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                    <circle cx="8.5" cy="8.5" r="1.5" stroke="currentColor" stroke-width="2" />
                    <path d="M21 15L16 10L5 21" stroke="currentColor" stroke-width="2" />
                </svg>
            </div>
            <h3>@lang('No images yet.')</h3>
            <p>@lang('Upload your first image to get started.')</p>
            <a href="{{ route('admin.gallery.create') }}" class="admin-btn admin-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                @lang('Upload Images')
            </a>
        </div>
        @else
        <!-- Images Grid -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" stroke="currentColor" stroke-width="2" />
                        <path d="M21 15L16 10L5 21" stroke="currentColor" stroke-width="2" />
                    </svg>
                    {{ __('Gallery Images') }}
                </h3>
                <div class="admin-badge-count">{{ $images->count() }} {{ __('images') }}</div>
            </div>
            <div class="admin-card-body">
                <div class="row g-3">
                    @foreach($images as $img)
                    <div class="col-md-3 col-sm-4 col-6">
                        <div class="admin-item-card">
                            <div class="admin-item-main">
                                <div class="ratio ratio-1x1 bg-light rounded">
                                    <img src="{{ $img->thumbnail_path ? asset('storage/'.$img->thumbnail_path) : ($img->webp_path ? asset('storage/'.$img->webp_path) : asset('storage/'.$img->original_path)) }}"
                                        alt="{{ $img->alt }}"
                                        class="img-fluid rounded obj-cover">
                                </div>
                                <div class="admin-item-details">
                                    <div class="admin-item-name" title="{{ $img->title }}">
                                        {{ $img->title ?? __('(No title)') }}
                                    </div>
                                    <div class="admin-item-meta" title="{{ $img->description }}">
                                        {{ $img->description ? Str::limit($img->description, 40) : '' }}
                                    </div>
                                    @if($img->tagsList())
                                    <div class="admin-item-badges">
                                        @foreach($img->tagsList() as $tg)
                                        <span class="admin-badge admin-badge-secondary">{{ $tg }}</span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                <div class="admin-item-price">
                                    <div class="admin-actions-flex">
                                        <a href="{{ route('admin.gallery.edit', $img) }}"
                                            class="admin-btn admin-btn-small admin-btn-primary"
                                            title="@lang('Edit')">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M18.5 2.5C18.8978 2.10218 19.4374 1.87868 20 1.87868C20.5626 1.87868 21.1022 2.10218 21.5 2.5C21.8978 2.89782 22.1213 3.43739 22.1213 4C22.1213 4.56261 21.8978 5.10218 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.gallery.use-as-logo', $img) }}" method="POST" class="js-confirm" data-confirm="@lang('Use this as logo?')">
                                            @csrf
                                            <button class="admin-btn admin-btn-small admin-btn-success" title="@lang('Use as Logo')">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.7649 14.1003 1.98232 16.07 2.85999" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <polyline points="22,4 12,14.01 9,11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </form>
                                        @if(!($gallerySettingLogo && ($gallerySettingLogo === $img->webp_path || $gallerySettingLogo === $img->original_path)))
                                        <form action="{{ route('admin.gallery.destroy', $img) }}" method="POST" class="js-confirm" data-confirm="@lang('Delete image?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="admin-btn admin-btn-small admin-btn-danger" title="@lang('Delete')">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <polyline points="3,6 5,6 21,6" stroke="currentColor" stroke-width="2" />
                                                    <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6" stroke="currentColor" stroke-width="2" />
                                                    <line x1="10" y1="11" x2="10" y2="17" stroke="currentColor" stroke-width="2" />
                                                    <line x1="14" y1="11" x2="14" y2="17" stroke="currentColor" stroke-width="2" />
                                                </svg>
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('admin.gallery.destroy', [$img, 'force' => 1]) }}" method="POST" class="js-confirm" data-confirm="@lang('This image is used as logo. Delete anyway?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="admin-btn admin-btn-small admin-btn-danger" title="@lang('Force Delete Logo Image')">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.29 3.86L1.82 18C1.64547 18.3024 1.5729 18.6453 1.61233 18.9823C1.65176 19.3193 1.80126 19.6345 2.04 19.88C2.27 20.12 2.56 20.29 2.88 20.37L5.1 20.9C5.65 21.04 6.24 20.97 6.74 20.7L21.5 12.5C21.8 12.33 22.02 12.05 22.12 11.72C22.22 11.39 22.19 11.03 22.04 10.72C21.89 10.41 21.64 10.16 21.33 10.01L10.29 3.86Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M22 2L2 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ $images->links() }}
                </div>
            </div>
        </div>
        @endif
        <!-- Multi Upload Modal -->
        <div class="modal fade" id="multiUploadModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-layer-group me-2"></i>@lang('Multi Upload')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" id="multiUploadForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">@lang('Images')</label>
                                <div id="dropzone" class="border border-2 border-dashed rounded p-4 text-center bg-light cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                                    <p class="mb-1 fw-semibold">@lang('Drag & Drop or Click to Select')</p>
                                    <p class="text-muted small mb-0">@lang('Up to 15 images, each max 4MB')</p>
                                    <input type="file" name="images[]" id="imagesInput" multiple accept="image/*" class="d-none">
                                </div>
                                <div id="previewList" class="row g-2 mt-3"></div>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">@lang('SEO Title (applied to all)')</label>
                                    <input type="text" name="title" class="form-control" maxlength="150">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Tags')</label>
                                    <input type="text" name="tags" class="form-control" maxlength="255" placeholder="tag1, tag2">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('ALT')</label>
                                    <input type="text" name="alt" class="form-control" maxlength="150">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">@lang('SEO Description (applied to all)')</label>
                                <textarea name="description" class="form-control" rows="2" maxlength="500"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>@lang('Upload')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endsection