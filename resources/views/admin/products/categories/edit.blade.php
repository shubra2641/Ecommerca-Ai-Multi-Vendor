@extends('layouts.admin')

@section('title', __('Edit Category'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Edit Category') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Update category information') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.product-categories.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back') }}
                </a>
                <button type="submit" form="category-form" class="admin-btn admin-btn-primary">
                    <i class="fas fa-save"></i>
                    {{ __('Update Category') }}
                </button>
            </div>
        </div>

        <!-- Category Form -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="fas fa-list"></i>
                    {{ __('Category Information') }}
                </h3>
                <p class="admin-card-subtitle">{{ __('Update the category details below') }}</p>
            </div>
            <div class="admin-card-body">
                <form id="category-form" method="POST" action="{{ route('admin.product-categories.update',$productCategory) }}">@csrf @method('PUT')
                    @include('admin.products.categories._form',['model'=>$productCategory])
                </form>
            </div>
        </div>
    </div>
</section>
@endsection