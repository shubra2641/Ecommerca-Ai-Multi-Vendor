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
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Create Brand') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Add a new product brand to your store') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.brands.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back to Brands') }}
                </a>
            </div>
        </div>

        <!-- Create Brand Form -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <i class="fas fa-plus"></i>
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
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ __('Active') }}
                                </label>
                            </div>
                            <div class="admin-text-muted small">{{ __('Make this brand visible to customers') }}</div>
                        </div>
                    </div>
                    <div class="admin-flex-end">
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="fas fa-check"></i>
                            {{ __('Create Brand') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection