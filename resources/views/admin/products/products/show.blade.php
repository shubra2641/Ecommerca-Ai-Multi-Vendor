@extends('layouts.admin')

@section('title', __('Product Details'))

@section('content')
@include('admin.partials.page-header', [
    'title'=>__('Product Details'),
    'subtitle'=>$psSubtitle ?? ($product->name ?? ''),
    'actions'=>$psActionsHtml ?? ''
])

<div class="modern-card">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <h5>{{ __('Basic') }}</h5>
                <dl class="row">
                    <dt class="col-4">{{ __('Name') }}</dt><dd class="col-8">{{ $product->name }}</dd>
                    <dt class="col-4">{{ __('SKU') }}</dt><dd class="col-8">{{ $product->sku ?? '-' }}</dd>
                    <dt class="col-4">{{ __('Type') }}</dt><dd class="col-8">{{ $product->type }}</dd>
                    <dt class="col-4">{{ __('Category') }}</dt><dd class="col-8">{{ $product->category->name ?? '-' }}</dd>
                </dl>
            </div>
            <div class="col-md-6">
                <h5>{{ __('Pricing') }}</h5>
                <div>{{ __('Effective Price') }}: {{ number_format($product->effectivePrice(),2) }}</div>
                @if($product->isOnSale())<div class="text-success small">{{ __('On Sale') }}: {{ number_format($product->sale_price,2) }}</div>@endif
            </div>
        </div>

        <hr>
        <h5>{{ __('Gallery') }}</h5>
    @if(!empty($psGallery))
            <div class="d-flex flex-wrap gap-2 mb-3">
        @foreach($psGallery as $img)
                    <div class="w-60 h-60 pos-relative">
                        <img src="{{ $img }}" class="obj-cover w-60 h-60 border-ddd rounded-4" alt="img">
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-muted small mb-3">{{ __('No gallery images.') }}</div>
        @endif

        <hr>
        <h5>{{ __('Variations') }}</h5>
        @if($product->variations->count())
        <div class="table-responsive">
            <table class="table table-sm">
                <thead><tr><th>{{ __('SKU') }}</th><th>{{ __('Attributes') }}</th><th>{{ __('Price') }}</th><th>{{ __('Stock') }}</th></tr></thead>
                <tbody>
                    @foreach($product->variations as $v)
                    <tr>
                        <td>{{ $v->sku ?? '-' }}</td>
                        <td>
                            @foreach($v->attribute_data ?? [] as $k=>$val)
                                <div><strong>{{ $k }}:</strong> {{ $val }}</div>
                            @endforeach
                        </td>
                        <td>{{ number_format($v->effectivePrice(),2) }}</td>
                        <td>{{ $v->manage_stock ? $v->availableStock ?? $v->stock_qty : __('N/A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-muted small">{{ __('No variations.') }}</div>
        @endif
    </div>
</div>
@endsection
