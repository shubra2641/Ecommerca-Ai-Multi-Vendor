@extends('layouts.admin')

@section('title', __('Governorates'))

@section('content')
<section class="admin-order-details-modern">
  <div class="admin-order-wrapper">

    <!-- Header Section -->
    <div class="admin-order-header">
      <div class="header-left">
        <h1 class="admin-order-title">
          <i class="fas fa-map-marked-alt"></i>
          {{ __('Governorates') }}
        </h1>
        <p class="admin-order-subtitle">{{ __('Manage governorates and their settings') }}</p>
      </div>
      <div class="header-actions">
        <a href="{{ route('admin.governorates.create') }}" class="admin-btn admin-btn-primary">
          <i class="fas fa-plus"></i>
          {{ __('Add Governorate') }}
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
            <label class="admin-form-label">{{ __('Country') }}</label>
            <select name="country" class="admin-form-select">
              <option value="">-- {{ __('All Countries') }} --</option>
              @foreach($countries as $c)
              <option value="{{ $c->id }}" {{ $countryId==$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
              @endforeach
            </select>
          </div>
        </form>
      </div>
    </div>

    <!-- Governorates List -->
    <div class="admin-modern-card">
      <div class="admin-card-header">
        <i class="fas fa-clipboard-list"></i>
        <h3 class="admin-card-title">{{ __('All Governorates') }}</h3>
        <span class="admin-badge-count">{{ $governorates->total() }}</span>
      </div>
      <div class="admin-card-body">
        @if($governorates->count())
        <div class="admin-items-list">
          @foreach($governorates as $gov)
          <div class="admin-item-card">
            <div class="admin-item-main">
              <div class="admin-item-placeholder admin-item-placeholder-cyan">
                {{ strtoupper(substr($gov->name, 0, 2)) }}
              </div>
              <div class="admin-item-details">
                <div class="admin-item-name">{{ $gov->name }}</div>
                <div class="admin-payment-details admin-mt-half">
                  <span class="payment-detail-item">
                    <i class="fas fa-globe"></i>
                    {{ $gov->country ? $gov->country->name : '-' }}
                  </span>
                </div>
              </div>
            </div>
            <div class="admin-item-meta">
              <span class="admin-status-badge {{ $gov->active ? 'status-completed' : 'status-pending' }}">
                {{ $gov->active ? __('Active') : __('Inactive') }}
              </span>
              <div class="admin-actions-flex">
                <a href="{{ route('admin.governorates.edit',$gov) }}" class="admin-btn-small admin-btn-primary">
                  <i class="fas fa-edit"></i>
                  {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('admin.governorates.destroy',$gov) }}" class="js-confirm-delete" data-confirm="{{ __('Delete?') }}">
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
          <i class="fas fa-map-marked-alt" style="font-size: 64px;"></i>
          <p>{{ __('No governorates found') }}</p>
        </div>
        @endif
      </div>
      @if($governorates->hasPages())
      <div class="admin-card-footer-pagination">
        <div class="pagination-info">
          {{ __('Showing') }} {{ $governorates->firstItem() }} - {{ $governorates->lastItem() }} {{ __('of') }} {{ $governorates->total() }}
        </div>
        <div class="pagination-links">
          {{ $governorates->links() }}
        </div>
      </div>
      @endif
    </div>

  </div>
</section>
@endsection