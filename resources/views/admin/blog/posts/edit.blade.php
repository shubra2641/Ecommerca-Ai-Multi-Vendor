@extends('layouts.admin')
@section('page_title', __('Edit Blog Post'))
@section('content')
<section class="admin-order-details-modern">
  <div class="admin-order-wrapper">

    <!-- Header Section -->
    <div class="admin-order-header">
      <div class="header-left">
        <h1 class="admin-order-title">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          {{ __('Edit Blog Post') }}
        </h1>
        <p class="admin-order-subtitle">{{ __('Update blog post information') }}</p>
      </div>
      <div class="header-actions">
        <a href="{{ route('admin.blog.posts.index') }}" class="admin-btn admin-btn-secondary">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          {{ __('Back') }}
        </a>
        @if($post->published)
        <span class="admin-status-badge admin-status-badge-success">{{ __('Published') }}</span>
        @else
        <span class="admin-status-badge admin-status-badge-secondary">{{ __('Draft') }}</span>
        @endif
      </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.blog.posts.update',$post) }}" id="blogPostForm" enctype="multipart/form-data" class="admin-modern-card needs-validation" novalidate>
      @csrf
      @method('PUT')
      <div class="admin-card-body">
        @include('admin.blog.posts._form')
      </div>
      <div class="admin-card-body">
        <button type="submit" class="admin-btn admin-btn-primary">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" />
          </svg>
          {{ __('Update Post') }}
        </button>
        <a href="{{ route('admin.blog.posts.index') }}" class="admin-btn admin-btn-secondary">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg>
          {{ __('Cancel') }}
        </a>
      </div>
    </form>

  </div>
</section>
@endsection