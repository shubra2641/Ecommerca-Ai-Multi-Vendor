@extends('layouts.admin')
@section('title', __('Create Brand'))
@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4zM9 6v10h6V6H9z" />
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Create Brand') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Add a new product brand to your store') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.brands.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back to Brands') }}
                </a>
            </div>
        </div>

        <!-- Create Brand Form -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 5v14m7-7H5" />
                    </svg>
                    {{ __('Brand Information') }}
                </h2>
            </div>
            <div class="admin-card-body">
                <form method="POST" action="{{ route('admin.brands.store') }}" class="admin-form">
                    @csrf
                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Brand Name') }}</label>
                            <input type="text" name="name" class="admin-form-input" required placeholder="{{ __('Enter brand name') }}">
                            @error('name')
                            <div class="admin-text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">{{ __('Slug') }}</label>
                            <input type="text" name="slug" class="admin-form-input" placeholder="{{ __('Enter brand slug') }}">
                            <div class="admin-text-muted small">{{ __('URL-friendly version of the name') }}</div>
                            @error('slug')
                            <div class="admin-text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="admin-form-group">
                            <div class="form-check">
                                <input type="checkbox" name="active" id="active" class="form-check-input" value="1" checked>
                                <label class="form-check-label" for="active">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="me-2">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('Active') }}
                                </label>
                            </div>
                            <div class="admin-text-muted small">{{ __('Make this brand visible to customers') }}</div>
                        </div>
                    </div>
                    <div class="admin-flex-end">
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Create Brand') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection