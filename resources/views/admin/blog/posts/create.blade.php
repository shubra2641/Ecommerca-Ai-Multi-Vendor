@extends('layouts.admin')
@section('page_title', __('Create Blog Post'))
@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <i class="fas fa-edit"></i>
                    {{ __('Create Blog Post') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Add a new blog post') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.blog.posts.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.blog.posts.store') }}" id="blogPostForm" enctype="multipart/form-data" class="admin-modern-card needs-validation" novalidate>
            @csrf
            <div class="admin-card-body">
                @include('admin.blog.posts._form')
            </div>
            <div class="admin-card-body">
                <button type="submit" class="admin-btn admin-btn-primary">
                    <i class="fas fa-save"></i>
                    {{ __('Save Post') }}
                </button>
                <a href="{{ route('admin.blog.posts.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-times"></i>
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>

    </div>
</section>
@endsection