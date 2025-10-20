@extends('layouts.admin')

@section('title', __('Cities'))

@section('content')
<section class="admin-order-details-modern">
  <div class="admin-order-wrapper">

    <!-- Header Section -->
    <div class="admin-order-header">
      <div class="header-left">
        <h1 class="admin-order-title">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          {{ __('Cities') }}
        </h1>
        <p class="admin-order-subtitle">{{ __('Manage cities and their settings') }}</p>
      </div>
      <div class="header-actions">
        <a href="{{ route('admin.cities.create') }}" class="admin-btn admin-btn-primary">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 4v16m8-8H4" />
          </svg>
          {{ __('Add City') }}
        </a>
      </div>
    </div>

    <!-- Filter -->
    <div class="admin-modern-card admin-mb-1-5">
      <div class="admin-card-header">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
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
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
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
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
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
                  <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                  {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('admin.cities.destroy',$city) }}" class="js-confirm-delete" data-confirm="{{ __('Delete?') }}">
                  @csrf @method('DELETE')
                  <button class="admin-btn-small admin-btn-danger">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
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
          <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="admin-notification-icon">
            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
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