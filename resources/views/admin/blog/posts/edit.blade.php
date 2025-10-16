@extends('layouts.admin')
@section('page_title', __('Edit Blog Post'))
@section('content')
<div class="page-header">
  <h1 class="h4 mb-0">{{ __('Edit Blog Post') }}</h1>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('Back') }}</a>
    @if($post->published)
      <span class="badge bg-success align-self-center">{{ __('Published') }}</span>
    @else
      <span class="badge bg-secondary align-self-center">{{ __('Draft') }}</span>
    @endif
  </div>
</div>
<form method="POST" action="{{ route('admin.blog.posts.update',$post) }}" id="blogPostForm" enctype="multipart/form-data" class="needs-validation" novalidate>
  @csrf
  @method('PUT')
  @include('admin.blog.posts._form')
  <div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">{{ __('Update Post') }}</button>
    <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
  </div>
</form>
@push('scripts')
  <script src="{{ asset('admin/js/product-form.js') }}?v={{ asset_modified_time('admin/js/product-form.js') }}" defer></script>
  <script src="{{ asset('admin/js/blog-post-form.js') }}?v={{ asset_modified_time('admin/js/blog-post-form.js') }}" defer></script>
@endpush
@endsection
