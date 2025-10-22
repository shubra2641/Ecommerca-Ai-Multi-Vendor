@extends('layouts.admin')

@section('title', __('Product Notifications'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <i class="fas fa-bell"></i>
                    {{ __('Product Notifications') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Manage customer interest notifications and alerts') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.notify.topProducts') }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-trophy"></i>
                    {{ __('Top Products') }}
                </a>
            </div>
        </div>

        <!-- Summary -->
        @isset($breakdown)
        @include('admin.notify.summary',['breakdown'=>$breakdown])
        @endisset

        <!-- Filters -->
        <div class="admin-modern-card admin-mb-1-5">
            <div class="admin-card-header">
                <i class="fas fa-filter"></i>
                <h3 class="admin-card-title">{{ __('Filters') }}</h3>
            </div>
            <div class="admin-card-body">
                <form method="get" class="admin-form-grid-auto">
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Email') }}</label>
                        <input name="email" value="{{ request('email') }}" class="admin-form-input" placeholder="{{ __('Email') }}">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Product ID') }}</label>
                        <input name="product" value="{{ request('product') }}" class="admin-form-input" placeholder="{{ __('Product ID') }}">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Status') }}</label>
                        <select name="status" class="admin-form-select">
                            <option value="">{{ __('All Statuses') }}</option>
                            @foreach(['pending','notified','cancelled'] as $s)
                            <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Type') }}</label>
                        <select name="type" class="admin-form-select">
                            <option value="">{{ __('All Types') }}</option>
                            @foreach(['stock','back_in_stock','price_drop'] as $t)
                            <option value="{{ $t }}" @selected(request('type')===$t)>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-form-group admin-flex-end">
                        <button class="admin-btn admin-btn-primary admin-btn-block">
                            <i class="fas fa-search"></i>
                            {{ __('Filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <i class="fas fa-list"></i>
                <h3 class="admin-card-title">{{ __('All Notifications') }}</h3>
                <span class="admin-badge-count">{{ $interests->total() }}</span>
            </div>
            <div class="admin-card-body">
                @if($interests->count())
                <div class="admin-items-list">
                    @foreach($interests as $interest)
                    <div class="admin-item-card">
                        <div class="admin-item-main">
                            <div class="admin-item-placeholder admin-item-placeholder-cyan">
                                #{{ $interest->id }}
                            </div>
                            <div class="admin-item-details">
                                <div class="admin-item-name">
                                    <a href="{{ route('admin.products.show',$interest->product_id) }}" class="admin-product-link">
                                        {{ $interest->product?->name ?? __('Product') . ' #' . $interest->product_id }}
                                    </a>
                                </div>
                                <div class="admin-payment-details admin-mt-half">
                                    @if($interest->email)
                                    <span class="payment-detail-item">
                                        <i class="fas fa-envelope"></i>
                                        {{ $interest->email }}
                                    </span>
                                    <span class="payment-detail-separator">•</span>
                                    @endif
                                    @if($interest->phone)
                                    <span class="payment-detail-item">
                                        <i class="fas fa-phone"></i>
                                        {{ $interest->phone }}
                                    </span>
                                    <span class="payment-detail-separator">•</span>
                                    @endif
                                    <span class="payment-detail-item">
                                        <i class="fas fa-tag"></i>
                                        {{ ucfirst(str_replace('_', ' ', $interest->type)) }}
                                    </span>
                                    <span class="payment-detail-separator">•</span>
                                    <span class="payment-detail-item">
                                        <i class="fas fa-clock"></i>
                                        {{ $interest->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="admin-item-meta">
                            <span class="admin-status-badge status-{{ $interest->status }}">
                                {{ ucfirst($interest->status) }}
                            </span>
                            <div class="admin-actions-flex">
                                @if($interest->status==='pending')
                                <form method="post" action="{{ route('admin.notify.mark',$interest) }}">
                                    @csrf @method('put')
                                    <button class="admin-btn-small admin-btn-success">
                                        <i class="fas fa-check"></i>
                                        {{ __('Mark') }}
                                    </button>
                                </form>
                                @endif
                                <form method="post" action="{{ route('admin.notify.delete',$interest) }}" class="js-confirm" data-confirm="{{ __('Delete?') }}">
                                    @csrf @method('delete')
                                    <button class="admin-btn-small admin-btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="admin-empty-state">
                    <i class="fas fa-bell" style="font-size:64px"></i>
                    <p>{{ __('No interests found') }}</p>
                </div>
                @endif
            </div>
            @if($interests->hasPages())
            <div class="admin-card-footer-pagination">
                <div class="pagination-info">
                    {{ __('Showing') }} {{ $interests->firstItem() }} - {{ $interests->lastItem() }} {{ __('of') }} {{ $interests->total() }}
                </div>
                <div class="pagination-links">
                    {{ $interests->links() }}
                </div>
            </div>
            @endif
        </div>

    </div>
</section>
@endsection