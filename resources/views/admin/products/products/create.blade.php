@extends('layouts.admin')

@section('title', __('Add Product'))

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div class="page-header-content">
        <h1 class="page-title mb-1">{{ __('Add Product') }}</h1>
        <p class="page-description mb-0 text-muted">{{ __('Create a new product in the catalog') }}</p>
    </div>
    <div class="page-actions d-flex flex-wrap gap-2">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span class="d-none d-sm-inline">{{ __('Back to Products') }}</span>
        </a>
        <button type="submit" form="product-form" class="btn btn-primary">
            <i class="fas fa-save"></i>
            {{ __('Save Product') }}
        </button>
    </div>
</div>

<div class="card modern-card content-card">
    <div class="content-card-header card-header">
        <div>
            <h3 class="content-title card-title mb-1">{{ __('Product Information') }}</h3>
            <p class="content-description text-muted mb-0">{{ __('Fill in the product details below') }}</p>
        </div>
    </div>
    <div class="content-card-body card-body">
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 small">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form id="product-form" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data"
            autocomplete="off">
            @csrf
            @include('admin.products.products._form')
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/lang-tabs.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('admin/js/media-picker.js') }}"></script>
<script src="{{ asset('admin/js/product-form.js') }}" data-ai-suggest-url="{{ route('admin.products.ai.suggest') }}"></script>
<script src="{{ asset('admin/js/media-inputs.js') }}"></script>
@endpush