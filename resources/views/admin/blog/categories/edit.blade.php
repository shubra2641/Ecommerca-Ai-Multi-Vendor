@extends('layouts.admin')
@section('title', __('Edit Category'))
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.blog.categories.index') }}">{{ __('Categories') }}</a></li>
<li class="breadcrumb-item active">{{ __('Edit') }}</li>
@endsection
@push('scripts')
<script src="{{ asset('admin/js/slug-auto.js') }}" defer></script>
<script src="{{ asset('admin/js/product-form.js') }}" defer></script>
<script src="{{ asset('admin/js/blog-category-form.js') }}" defer></script>
@endpush
@section('content')
<form method="POST" action="{{ route('admin.blog.categories.update',$category) }}" class="category-form-enhanced">
  @csrf @method('PUT')
  @include('admin.blog.categories._form',['category'=>$category])
  <div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>{{ __('Back') }}</a>
    <button class="btn btn-primary"><i class="fas fa-save me-1"></i>{{ __('Update Category') }}</button>
  </div>
</form>
@endsection
