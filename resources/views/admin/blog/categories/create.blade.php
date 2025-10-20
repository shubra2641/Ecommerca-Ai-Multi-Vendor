@extends('layouts.admin')
@section('title', __('Create Category'))
@section('content')
<section class="admin-order-details-modern">
  <div class="admin-order-wrapper">

    <!-- Header Section -->
    <div class="admin-order-header">
      <div class="header-left">
        <h1 class="admin-order-title">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
          </svg>
          {{ __('Create Category') }}
        </h1>
        <p class="admin-order-subtitle">{{ __('Add a new blog category') }}</p>
      </div>
      <div class="header-actions">
        <a href="{{ route('admin.blog.categories.index') }}" class="admin-btn admin-btn-secondary">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          {{ __('Back') }}
        </a>
      </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.blog.categories.store') }}" class="admin-modern-card">
      @csrf
      <div class="admin-card-body">
        @include('admin.blog.categories._form')
      </div>
      <div class="admin-card-body">
        <a href="{{ route('admin.blog.categories.index') }}" class="admin-btn admin-btn-secondary">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg>
          {{ __('Cancel') }}
        </a>
        <button class="admin-btn admin-btn-primary">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" />
          </svg>
          {{ __('Save Category') }}
        </button>
      </div>
    </form>

  </div>
</section>
@endsection