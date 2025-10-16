@extends('layouts.admin')
@section('page_title', __('Create Blog Post'))
@section('content')
<div class="page-header">
    <h1 class="h4 mb-0">{{ __('Create Blog Post') }}</h1>
    <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('Back') }}</a>
</div>
<form method="POST" action="{{ route('admin.blog.posts.store') }}" id="blogPostForm" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    @include('admin.blog.posts._form')
    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary">{{ __('Save Post') }}</button>
        <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
    </div>
</form>
@push('scripts')
    <script src="{{ asset('admin/js/product-form.js') }}?v={{ asset_modified_time('admin/js/product-form.js') }}" defer></script>
    <script src="{{ asset('admin/js/blog-post-form.js') }}?v={{ asset_modified_time('admin/js/blog-post-form.js') }}" defer></script>
@endpush
@endsection