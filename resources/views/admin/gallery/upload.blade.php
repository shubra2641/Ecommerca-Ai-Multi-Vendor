@extends('layouts.admin')
@section('title', __('Upload Image'))
@section('content')
<h1 class="h3 mb-3">@lang('Upload Images')</h1>
@if($errors->any())
<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif
<form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="card p-3">
    @csrf
    <div class="mb-3">
        <label class="form-label">@lang('Images') <span class="text-danger">*</span></label>
        <input type="file" name="images[]" class="form-control" accept="image/*" multiple required>
        <small class="text-muted">@lang('You can select multiple images (max 15, each up to 4MB).')</small>
    </div>
    <div class="mb-3">
        <label class="form-label">@lang('SEO Title')</label>
        <input type="text" name="title" class="form-control" maxlength="150" value="{{ old('title') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">@lang('SEO Description')</label>
        <textarea name="description" class="form-control" rows="3" maxlength="500">{{ old('description') }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">@lang('ALT Text')</label>
        <input type="text" name="alt" class="form-control" maxlength="150" value="{{ old('alt') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">@lang('Tags')</label>
        <input type="text" name="tags" class="form-control" maxlength="255" value="{{ old('tags') }}" placeholder="tag1, tag2, tag3">
        <small class="text-muted">@lang('Comma separated')</small>
    </div>
    <button class="btn btn-primary">@lang('Upload')</button>
    <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">@lang('Back')</a>
</form>
@endsection
