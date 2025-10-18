@extends('layouts.admin')
@section('title', __('Edit Image'))
@section('content')
<h1 class="h3 mb-3">@lang('Edit Image')</h1>
<div class="card p-3 mb-3">
    <div class="row g-3">
        <div class="col-md-4">
            <div class="ratio ratio-1x1 bg-light">
                <img src="{{ $image->webp_path ? asset('storage/'.$image->webp_path) : asset('storage/'.$image->original_path) }}" class="img-fluid rounded obj-cover" alt="{{ $image->alt }}">
            </div>
        </div>
        <div class="col-md-8">
            <form action="{{ route('admin.gallery.update', $image) }}" method="POST" class="mb-3">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">@lang('SEO Title')</label>
                    <input type="text" name="title" class="form-control" maxlength="150" value="{{ old('title', $image->title) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">@lang('SEO Description')</label>
                    <textarea name="description" class="form-control" rows="3" maxlength="500">{{ old('description', $image->description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">@lang('ALT Text')</label>
                    <input type="text" name="alt" class="form-control" maxlength="150" value="{{ old('alt', $image->alt) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">@lang('Tags')</label>
                    <input type="text" name="tags" class="form-control" maxlength="255" value="{{ old('tags', $image->tags) }}" placeholder="tag1, tag2">
                    <small class="text-muted">@lang('Comma separated')</small>
                </div>
                <button class="btn btn-primary">@lang('Save')</button>
                <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">@lang('Back')</a>
            </form>
            <form action="{{ route('admin.gallery.destroy', $image) }}" method="POST" class="js-confirm" data-confirm="@lang('Delete image?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger">@lang('Delete')</button>
            </form>
        </div>
    </div>
</div>
@endsection
