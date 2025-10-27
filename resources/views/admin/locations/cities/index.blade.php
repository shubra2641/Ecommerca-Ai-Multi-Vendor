@extends('layouts.admin')

@section('title', __('Cities'))

@section('content')
<section class="admin-order-details-modern">
  <div class="admin-order-wrapper">

    <!-- Header Section -->
    <div class="admin-order-header">
      <div class="header-left">
        <h1 class="admin-order-title">
          <i class="fas fa-city"></i>
          {{ __('Cities') }}
        </h1>
        <p class="admin-order-subtitle">{{ __('Manage cities and their settings') }}</p>
      </div>
      <div class="header-actions">
        <a href="{{ route('admin.cities.create') }}" class="admin-btn admin-btn-primary">
          <i class="fas fa-plus"></i>
          {{ __('Add City') }}
        </a>
      </div>
    </div>

    <!-- Filter -->
    <div class="admin-modern-card admin-mb-1-5">
      <div class="admin-card-header">
        <i class="fas fa-filter"></i>
        <h3 class="admin-card-title">{{ __('Filter') }}</h3>
      </div>
      <div class="admin-card-body">
        <form method="GET" class="admin-form-grid-auto js-auto-submit">
          <div class="admin-form-group">
            <label class="admin-form-label">{{ __('Governorate') }}</label>
            <select name="governorate" class="admin-form-select">
              <option value="">-- {{ __('All Governorates') }} --</option>
              @foreach($governorates as $g)
              <option value="{{ $g->id }}" {{ $govId==$g->id ? 'selected' : '' }}>{{ $g->name }}</option>
              @endforeach
            </select>
          </div>
        </form>
      </div>
    </div>

    <!-- Cities List -->
    <div class="admin-modern-card">
      <div class="admin-card-header">
        <i class="fas fa-clipboard-list"></i>
        <h3 class="admin-card-title">{{ __('All Cities') }}</h3>
        <span class="admin-badge-count">{{ $cities->total() }}</span>
      </div>
      <div class="admin-card-body">
        @if($cities->count())
        <div class="admin-items-list">
          @foreach($cities as $city)
          <div class="admin-item-card">
            <div class="admin-item-main">
              <div class="admin-item-placeholder admin-item-placeholder-warning">
                {{ strtoupper(substr($city->name, 0, 2)) }}
              </div>
              <div class="admin-item-details">
                <div class="admin-item-name">{{ $city->name }}</div>
                <div class="admin-payment-details admin-mt-half">
                  <span class="payment-detail-item">
                    <i class="fas fa-city"></i>
                    {{ $city->governorate ? $city->governorate->name : '-' }}
                  </span>
                </div>
              </div>
            </div>
            <div class="admin-item-meta">
              <span class="admin-status-badge {{ $city->active ? 'status-completed' : 'status-pending' }}">
                {{ $city->active ? __('Active') : __('Inactive') }}
              </span>
              <div class="admin-actions-flex">
                <a href="{{ route('admin.cities.edit',$city) }}" class="admin-btn-small admin-btn-primary">
                  <i class="fas fa-edit"></i>
                  {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('admin.cities.destroy',$city) }}" class="js-confirm-delete" data-confirm="{{ __('Delete?') }}">
                  @csrf @method('DELETE')
                  <button class="admin-btn-small admin-btn-danger">
                    <i class="fas fa-trash"></i>
                    {{ __('Delete') }}
                  </button>
                </form>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        @else
        <div class="admin-empty-state">
          <i class="fas fa-city icon-xlarge"></i>
          <p>{{ __('No cities found') }}</p>
        </div>
        @endif
      </div>
      @if($cities->hasPages())
      <div class="admin-card-footer-pagination">
        <div class="pagination-info">
          {{ __('Showing') }} {{ $cities->firstItem() }} - {{ $cities->lastItem() }} {{ __('of') }} {{ $cities->total() }}
        </div>
        <div class="pagination-links">
          {{ $cities->links() }}
        </div>
      </div>
      @endif
    </div>

  </div>
</section>
@endsection