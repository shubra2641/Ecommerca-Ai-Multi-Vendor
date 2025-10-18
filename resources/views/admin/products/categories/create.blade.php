@extends('layouts.admin')

@section('title', __('Add Category'))

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title mb-1">{{ __('Add Category') }}</h1>
        <p class="text-muted mb-0">{{ __('Create a new product category') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.product-categories.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>{{ __('Back') }}
        </a>
        <button type="submit" form="category-form" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>{{ __('Save Category') }}
        </button>
    </div>
</div>

<div class="card card-body">
    <form id="category-form" method="POST" action="{{ route('admin.product-categories.store') }}">@csrf
        @include('admin.products.categories._form')
    </form>
</div>
@endsection
