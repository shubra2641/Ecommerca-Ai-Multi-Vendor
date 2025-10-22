@extends('layouts.admin')
@section('title', __('Edit Category'))
@section('content')
<section class="admin-order-details-modern">
  <div class="admin-order-wrapper">

    <!-- Header Section -->
    <div class="admin-order-header">
      <div class="header-left">
        <h1 class="admin-order-title">
          <i class="fas fa-tag"></i>
          {{ __('Edit Category') }}
        </h1>
        <p class="admin-order-subtitle">{{ __('Update blog category information') }}</p>
      </div>
      <div class="header-actions">
        <a href="{{ route('admin.blog.categories.index') }}" class="admin-btn admin-btn-secondary">
          <i class="fas fa-arrow-left"></i>
          {{ __('Back') }}
        </a>
      </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.blog.categories.update',$category) }}" class="admin-modern-card">
      @csrf @method('PUT')
      <div class="admin-card-body">
        @include('admin.blog.categories._form',['category'=>$category])
      </div>
      <div class="admin-card-body">
        <a href="{{ route('admin.blog.categories.index') }}" class="admin-btn admin-btn-secondary">
          <i class="fas fa-times"></i>
          {{ __('Cancel') }}
        </a>
        <button class="admin-btn admin-btn-primary">
          <i class="fas fa-save"></i>
          {{ __('Update Category') }}
        </button>
      </div>
    </form>

  </div>
</section>
@endsection