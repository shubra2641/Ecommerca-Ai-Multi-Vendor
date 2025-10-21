@extends('layouts.admin')

@section('title', __('Add Tag'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.59 13.41L13 20.99C12.7 21.3 12.3 21.3 12 20.99L3.41 12.42C3.07 12.08 3.07 11.52 3.41 11.18L11 3.59C11.3 3.29 11.7 3.29 12 3.59L20.59 12.18C20.93 12.52 20.93 13.08 20.59 13.41Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Add Tag') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Create a new product tag') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.product-tags.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ __('Back') }}
                </a>
                <button type="submit" form="tag-form" class="admin-btn admin-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H16L21 8V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="17,21 17,13 7,13 7,21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="7,3 7,8 15,8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ __('Save Tag') }}
                </button>
            </div>
        </div>

        <!-- Content Card -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.59 13.41L13 20.99C12.7 21.3 12.3 21.3 12 20.99L3.41 12.42C3.07 12.08 3.07 11.52 3.41 11.18L11 3.59C11.3 3.29 11.7 3.29 12 3.59L20.59 12.18C20.93 12.52 20.93 13.08 20.59 13.41Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    {{ __('Tag Information') }}
                </h3>
                <div class="admin-card-subtitle">{{ __('Fill in the tag details below') }}</div>
            </div>
            <div class="admin-card-body">
                <form id="tag-form" method="POST" action="{{ route('admin.product-tags.store') }}">
                    @csrf
                    @include('admin.products.tags._form')
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
