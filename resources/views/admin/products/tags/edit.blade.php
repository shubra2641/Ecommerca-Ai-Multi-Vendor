@extends('layouts.admin')

@section('title', __('Edit Tag'))

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
                        <h1 class="admin-order-title">{{ __('Edit Tag') }}</h1>
                        <p class="admin-order-subtitle">{{ $productTag->name }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.product-tags.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back') }}
                </a>
                <button type="submit" form="tag-form" class="admin-btn admin-btn-primary">
                    <i class="fas fa-save"></i>
                    {{ __('Update Tag') }}
                </button>
            </div>
        </div>

        <!-- Content Card -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="fas fa-tags"></i>
                    {{ __('Tag Information') }}
                </h3>
                <div class="admin-card-subtitle">{{ __('Update the tag details below') }}</div>
            </div>
            <div class="admin-card-body">
                <form id="tag-form" method="POST" action="{{ route('admin.product-tags.update', $productTag) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.products.tags._form', ['model' => $productTag])
                </form>
            </div>
        </div>
    </div>
</section>
@endsection