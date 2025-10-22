@extends('layouts.admin')

@section('title', __('Countries'))

@section('content')
<section class="admin-order-details-modern">
  <div class="admin-order-wrapper">

    <!-- Header Section -->
    <div class="admin-order-header">
      <div class="header-left">
        <h1 class="admin-order-title">
          <i class="fas fa-globe"></i>
          {{ __('Countries') }}
        </h1>
        <p class="admin-order-subtitle">{{ __('Manage countries and their settings') }}</p>
      </div>
      <div class="header-actions">
        <a href="{{ route('admin.countries.create') }}" class="admin-btn admin-btn-primary">
          <i class="fas fa-plus"></i>
          {{ __('Add Country') }}
        </a>
      </div>
    </div>

    <!-- Countries List -->
    <div class="admin-modern-card">
      <div class="admin-card-header">
        <i class="fas fa-list"></i>
        <h3 class="admin-card-title">{{ __('All Countries') }}</h3>
        <span class="admin-badge-count">{{ $countries->total() }}</span>
      </div>
      <div class="admin-card-body">
        @if($countries->count())
        <div class="admin-items-list">
          @foreach($countries as $country)
          <div class="admin-item-card">
            <div class="admin-item-main">
              <div class="admin-item-placeholder admin-item-placeholder-primary">
                {{ strtoupper(substr($country->name, 0, 2)) }}
              </div>
              <div class="admin-item-details">
                <div class="admin-item-name">{{ $country->name }}</div>
                <div class="admin-payment-details admin-mt-half">
                  @if($country->iso_code)
                  <span class="payment-detail-item">
                    <i class="fas fa-hashtag" aria-hidden="true"></i>
                    {{ __('ISO') }}: {{ $country->iso_code }}
                  </span>
                  @endif
                </div>
              </div>
            </div>
            <div class="admin-item-meta">
              <span class="admin-status-badge {{ $country->active ? 'status-completed' : 'status-pending' }}">
                {{ $country->active ? __('Active') : __('Inactive') }}
              </span>
              <div class="admin-actions-flex">
                <a href="{{ route('admin.countries.edit',$country) }}" class="admin-btn-small admin-btn-primary">
                  <i class="fas fa-edit" aria-hidden="true"></i>
                  {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('admin.countries.destroy',$country) }}" class="js-confirm-delete" data-confirm="{{ __('Delete?') }}">
                  @csrf @method('DELETE')
                  <button class="admin-btn-small admin-btn-danger">
                    <i class="fas fa-trash" aria-hidden="true"></i>
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
          <i class="fas fa-globe fa-3x" aria-hidden="true"></i>
          <p>{{ __('No countries found') }}</p>
        </div>
        @endif
      </div>
      @if($countries->hasPages())
      <div class="admin-card-footer-pagination">
        <div class="pagination-info">
          {{ __('Showing') }} {{ $countries->firstItem() }} - {{ $countries->lastItem() }} {{ __('of') }} {{ $countries->total() }}
        </div>
        <div class="pagination-links">
          {{ $countries->links() }}
        </div>
      </div>
      @endif
    </div>

  </div>
</section>
@endsection