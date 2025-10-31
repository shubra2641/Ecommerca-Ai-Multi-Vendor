@extends('vendor.layout')

@section('title', __('Edit Product'))

@section('content')
<div class="vendor-product-form-container">
    <!-- Page Header -->
    <div class="form-header">
        <div class="header-actions">


    @include('admin.partials.page-header', [
        'title' => __('Edit Product'),
        'subtitle' => __('Update product information'),
        'actions' => '<a href="'.route('vendor.products.index').'" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> <span class="d-none d-sm-inline">'.e(__('Back to Products')).'</span></a> <button type="submit" form="product-form" class="btn btn-primary"><i class="fas fa-save"></i> '.e(__('Update')).'</button>'
    ])

    
            @if($product->status === 'pending')
                <span class="badge badge-warning">
                    <i class="fas fa-clock"></i>
                    {{ __('Pending Review') }}
                </span>
            @elseif($product->status === 'approved')
                <span class="badge badge-success">
                    <i class="fas fa-check-circle"></i>
                    {{ __('Approved') }}
                </span>
            @elseif($product->status === 'rejected')
                <span class="badge badge-danger">
                    <i class="fas fa-times-circle"></i>
                    {{ __('Rejected') }}
                </span>
            @endif
        </div>

    <div class="card modern-card content-card">
        <div class="content-card-header card-header">
            <div>
                <h3 class="content-title card-title mb-1">{{ __('Product Information') }}</h3>
                <p class="content-description text-muted mb-0">{{ __('Update the product details below') }}</p>
            </div>
        </div>
        <div class="content-card-body card-body">
            <form id="product-form" method="POST" action="{{ route('vendor.products.update', $product) }}" enctype="multipart/form-data" autocomplete="off">
                @csrf
                @method('PUT')
                @include('admin.products.products._form',['model'=>$product, 'categories' => $categories ?? \App\Models\ProductCategory::all(), 'tags' => $tags ?? \App\Models\Tag::all(), 'attributes' => $attributes ?? \App\Models\ProductAttribute::with('values')->get(), 'isVendorForm' => true])
            </form>
        </div>
    </div>

    @endsection

@section('scripts')
    @include('admin.products.products._script')
@endsection


    <!-- Product Form -->
