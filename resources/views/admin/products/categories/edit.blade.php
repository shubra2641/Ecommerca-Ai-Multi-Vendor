@extends('layouts.admin')

@section('title', __('Edit Category'))

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title mb-1">{{ __('Edit Category') }}</h1>
        <p class="text-muted mb-0">{{ __('Update category information') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.product-categories.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>{{ __('Back') }}
        </a>
        <button type="submit" form="category-form" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>{{ __('Update Category') }}
        </button>
    </div>
</div>

<div class="card card-body">
    <form id="category-form" method="POST" action="{{ route('admin.product-categories.update',$productCategory) }}">@csrf @method('PUT')
        @include('admin.products.categories._form',['model'=>$productCategory])
    </form>
</div>
@endsection
@push('scripts')
<script src="{{ asset('admin/js/media-picker.js') }}" defer></script>
<script src="{{ asset('admin/js/media-picker-init.js') }}" defer></script>
<script src="{{ asset('admin/js/product-form.js') }}" defer></script>
@endpush