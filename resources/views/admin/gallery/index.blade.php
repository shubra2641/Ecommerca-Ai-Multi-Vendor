@extends('layouts.admin')
@section('title', __('Gallery'))
@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('Gallery') }}</h1>
        <p class="page-description">{{ __('Manage images, SEO data, tags and logo usage') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">
            <i class="fas fa-upload"></i>
            {{ __('Upload') }}
        </a>
        <button type="button" class="btn btn-outline-secondary" id="multiUploadBtn">
            <i class="fas fa-layer-group"></i>
            {{ __('Multi Upload') }}
        </button>
    </div>
</div>

<form method="GET" action="{{ route('admin.gallery.index') }}" class="card card-body mb-3 p-3 shadow-sm">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">@lang('Search')</label>
            <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="@lang('Title / description / tag')">
        </div>
        <div class="col-md-3">
            <label class="form-label">@lang('Tag')</label>
            <select name="tag" class="form-select">
                <option value="">@lang('All')</option>
                @foreach(($distinctTags ?? []) as $t)
                    <option value="{{ $t }}" @selected(($tag ?? '') === $t)>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <div>
                <button class="btn btn-outline-primary me-1"><i class="fas fa-search"></i> @lang('Filter')</button>
                <a href="{{ route('admin.gallery.index') }}" class="btn btn-outline-secondary">@lang('Reset')</a>
            </div>
        </div>
    </div>
</form>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('warning'))<div class="alert alert-warning">{{ session('warning') }}</div>@endif
@if(!$images->count())
    <div class="alert alert-info mb-0">@lang('No images yet.')</div>
@else
<div class="row g-3">
@foreach($images as $img)
    <div class="col-md-3 col-sm-4 col-6">
        <div class="card h-100 shadow-sm">
            <div class="ratio ratio-1x1 bg-light">
                <img src="{{ $img->thumbnail_path ? asset('storage/'.$img->thumbnail_path) : ($img->webp_path ? asset('storage/'.$img->webp_path) : asset('storage/'.$img->original_path)) }}" alt="{{ $img->alt }}" class="img-fluid rounded-top obj-cover">
            </div>
            <div class="card-body p-2">
                <div class="small fw-semibold text-truncate" title="{{ $img->title }}">{{ $img->title ?? __('(No title)') }}</div>
                <div class="text-muted small text-truncate" title="{{ $img->description }}">{{ $img->description ? Str::limit($img->description, 40) : '' }}</div>
                @if($img->tagsList())
                <div class="mt-1 d-flex flex-wrap gap-1">
                    @foreach($img->tagsList() as $tg)
                        <span class="badge bg-light text-secondary border fw-normal small-badge">{{ $tg }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="card-footer d-flex justify-content-between gap-1 p-2">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.gallery.edit', $img) }}" class="btn btn-sm btn-outline-primary" title="@lang('Edit')"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.gallery.use-as-logo', $img) }}" method="POST" class="js-confirm" data-confirm="@lang('Use this as logo?')">
                        @csrf
                        <button class="btn btn-sm btn-outline-success" title="@lang('Use as Logo')"><i class="fas fa-check-circle"></i></button>
                    </form>
                    @if(!($gallerySettingLogo && ($gallerySettingLogo === $img->webp_path || $gallerySettingLogo === $img->original_path)))
                        <form action="{{ route('admin.gallery.destroy', $img) }}" method="POST" class="js-confirm" data-confirm="@lang('Delete image?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="@lang('Delete')"><i class="fas fa-trash"></i></button>
                        </form>
                    @else
                        <form action="{{ route('admin.gallery.destroy', [$img, 'force' => 1]) }}" method="POST" class="js-confirm" data-confirm="@lang('This image is used as logo. Delete anyway?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="@lang('Force Delete Logo Image')"><i class="fas fa-exclamation-triangle"></i></button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
</div>
<div class="mt-3">{{ $images->links() }}</div>
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

@push('scripts')
<script src="{{ asset('admin/js/gallery.js') }}" defer></script>
@endpush
@endsection
