@extends('layouts.admin')

@section('title', __('Add Attribute'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Add Attribute') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Define a new attribute for product variations') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.product-attributes.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back') }}
                </a>
                <button type="submit" form="attribute-form" class="admin-btn admin-btn-primary">
                    <i class="fas fa-save"></i>
                    {{ __('Save Attribute') }}
                </button>
            </div>
        </div>

        <!-- Attribute Form -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="fas fa-list"></i>
                    {{ __('Attribute Information') }}
                </h3>
                <p class="admin-card-subtitle">{{ __('Fill in the attribute details below') }}</p>
            </div>
            <div class="admin-card-body">
                <form id="attribute-form" method="POST" action="{{ route('admin.product-attributes.store') }}">
                    @csrf
                    @include('admin.products.attributes._form')
                </form>
            </div>
        </div>
    </div>
</section>
@endsection