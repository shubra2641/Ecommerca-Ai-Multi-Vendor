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
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Gallery') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage images, SEO data, tags and logo usage') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.gallery.create') }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-upload"></i>
                    {{ __('Upload') }}
                </a>
                <button type="button" class="admin-btn admin-btn-secondary" id="multiUploadBtn">
                    <i class="fas fa-layer-group"></i>
                    {{ __('Multi Upload') }}
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="admin-modern-card admin-mb-1-5">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="fas fa-filter"></i>
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
                                <i class="fas fa-search"></i>
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
                <i class="fas fa-image" style="font-size: 48px;"></i>
            </div>
            <h3>@lang('No images yet.')</h3>
            <p>@lang('Upload your first image to get started.')</p>
            <a href="{{ route('admin.gallery.create') }}" class="admin-btn admin-btn-primary">
                <i class="fas fa-upload"></i>
                @lang('Upload Images')
            </a>
        </div>
        @else
        <!-- Images Grid -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="fas fa-image"></i>
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
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.gallery.use-as-logo', $img) }}" method="POST" class="js-confirm" data-confirm="@lang('Use this as logo?')">
                                            @csrf
                                            <button class="admin-btn admin-btn-small admin-btn-success" title="@lang('Use as Logo')">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                        @if(!($gallerySettingLogo && ($gallerySettingLogo === $img->webp_path || $gallerySettingLogo === $img->original_path)))
                                        <form action="{{ route('admin.gallery.destroy', $img) }}" method="POST" class="js-confirm" data-confirm="@lang('Delete image?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="admin-btn admin-btn-small admin-btn-danger" title="@lang('Delete')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('admin.gallery.destroy', [$img, 'force' => 1]) }}" method="POST" class="js-confirm" data-confirm="@lang('This image is used as logo. Delete anyway?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="admin-btn admin-btn-small admin-btn-danger" title="@lang('Force Delete Logo Image')">
                                                <i class="fas fa-ban"></i>
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