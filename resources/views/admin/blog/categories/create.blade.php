@extends('layouts.admin')
@section('title', __('Create Category'))
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.blog.categories.index') }}">{{ __('Categories') }}</a></li>
<li class="breadcrumb-item active">{{ __('Create') }}</li>
@endsection
@section('content')
<form method="POST" action="{{ route('admin.blog.categories.store') }}" class="category-form-enhanced">
  @csrf
  @include('admin.blog.categories._form')
  <div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i>{{ __('Cancel') }}</a>
    <button class="btn btn-primary"><i class="fas fa-save me-1"></i>{{ __('Save Category') }}</button>
  </div>
</form>
@endsection
