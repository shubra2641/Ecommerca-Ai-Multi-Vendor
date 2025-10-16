@extends('layouts.admin')

   @section('title', __('Currencies'))

   @section('content')
   @include('admin.partials.page-header', ['title'=>__('Currencies'),'subtitle'=>__('Manage system currencies and exchange rates'),'actions'=>'<a href="'.route('admin.currencies.create').'" class="btn btn-primary"><i class="fas fa-plus"></i> '.__('Add Currency').'</a>'])

   <!-- Statistics Cards -->
   <div class="row mb-4 mb-4">
    <div class="stats-card stats-card-danger">
           <div class="stats-card-body">
               <div class="stats-icon">
                   <i class="fas fa-coins"></i>
               </div>
               <div class="stats-card-content">
                   <div class="stats-label">{{ __('Total Currencies') }}</div>
                   <div class="stats-number">{{ $currencies->count() }}</div>
               </div>
           </div>
       </div>

    <div class="stats-card stats-card-success">
           <div class="stats-card-body">
               <div class="stats-icon">
                   <i class="fas fa-check-circle"></i>
               </div>
               <div class="stats-card-content">
                   <div class="stats-label">{{ __('Active Currencies') }}</div>
                   <div class="stats-number">{{ $currencies->where('is_active', true)->count() }}</div>
               </div>
           </div>
       </div>

    <div class="stats-card stats-card-primary">
           <div class="stats-card-body">
               <div class="stats-icon">
                   <i class="fas fa-star"></i>
               </div>
               <div class="stats-card-content">
                   <div class="stats-label">{{ __('Default Currency') }}</div>
                   <div class="stats-number">{{ $currencies->where('is_default', true)->first()->code ?? __('N/A') }}</div>
               </div>
           </div>
       </div>

       <div class="col-xl-3 col-md-6 mb-4">
           <div class="stats-card-body">
               <div class="stats-icon">
                   <i class="fas fa-clock"></i>
               </div>
               <div class="stats-card-content">
                   <div class="stats-label">{{ __('Last Updated') }}</div>
                   <div class="stats-number">{{ $currencies->max('updated_at')?->diffForHumans() ?? __('N/A') }}</div>
               </div>
           </div>
       </div>
   </div>

   <!-- Currencies Table -->
    <div class="card modern-card">
       <div class="card-header">
           <h3 class="card-title">
               <i class="fas fa-list text-primary"></i>
               {{ __('Currencies List') }}
           </h3>
       </div>
       <div class="card-body">
           @if($currencies->count() > 0)
           <div class="table-responsive">
               <table class="table table-striped">
                   <thead>
                       <tr>
                           <th>{{ __('Currency') }}</th>
                           <th>{{ __('Code') }}</th>
                           <th>{{ __('Symbol') }}</th>
                           <th>{{ __('Exchange Rate') }}</th>
                           <th>{{ __('Status') }}</th>
                           <th>{{ __('Default') }}</th>
                           <th>{{ __('Created') }}</th>
                           <th width="250">{{ __('Actions') }}</th>
                       </tr>
                   </thead>
                   <tbody>
                       @foreach($currencies as $currency)
                       <tr class="border-bottom">
                           <td class="py-3">
                               <div class="d-flex align-items-center">
                                   <div class="currency-avatar me-3">
                                       <div class="avatar-circle bg-primary text-white">
                                           {{ strtoupper(substr($currency->code, 0, 2)) }}
                                       </div>
                                   </div>
                                   <div class="currency-info">
                                       <div class="fw-semibold">{{ $currency->name }}</div>
                                       <small class="text-muted">{{ $currency->full_name ?? $currency->name }}</small>
                                   </div>
                               </div>
                           </td>
                           <td class="py-3">
                               <span class="badge bg-secondary">{{ strtoupper($currency->code) }}</span>
                           </td>
                           <td class="py-3">
                               <span class="fw-bold text-primary fs-5">{{ $currency->symbol }}</span>
                           </td>
                           <td class="py-3">
                               <div class="exchange-rate">
                                   <div class="fw-semibold">{{ number_format($currency->exchange_rate, 4) }}</div>
                                   <small class="text-muted">{{ __('to USD') }}</small>
                               </div>
                           </td>
                           <td class="py-3">
                               @if($currency->is_active)
                               <span class="badge bg-success">
                                   <i class="fas fa-check me-1"></i>
                                   {{ __('Active') }}
                               </span>
                               @else
                               <span class="badge bg-danger">
                                   <i class="fas fa-times me-1"></i>
                                   {{ __('Inactive') }}
                               </span>
                               @endif
                           </td>
                           <td class="py-3">
                               @if($currency->is_default)
                               <span class="badge bg-warning">
                                   <i class="fas fa-star me-1"></i>
                                   {{ __('Default') }}
                               </span>
                               @else
                               <span class="text-muted">-</span>
                               @endif
                           </td>
                           <td class="py-3">
                               <div class="fw-semibold">{{ $currency->created_at->format('M d, Y') }}</div>
                               <small class="text-muted">{{ $currency->created_at->format('H:i') }}</small>
                           </td>
                           <td class="py-3">
                               <div class="btn-group" role="group">
                                   <a href="{{ route('admin.currencies.show', $currency) }}"
                                       class="btn btn-sm btn-outline-secondary" title="{{ __('View') }}">
                                       <i class="fas fa-eye"></i>
                                   </a>
                                   <a href="{{ route('admin.currencies.edit', $currency) }}"
                                       class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                                       <i class="fas fa-edit"></i>
                                   </a>
                                   @if(!$currency->is_default)
                                   <form action="{{ route('admin.currencies.toggle-status', $currency) }}" method="POST"
                                       class="d-inline">
                                       @csrf
                                       <button type="submit"
                                           class="btn btn-sm btn-outline-{{ $currency->is_active ? 'warning' : 'success' }}"
                                           title="{{ $currency->is_active ? __('Deactivate') : __('Activate') }}">
                                           <i class="fas fa-{{ $currency->is_active ? 'pause' : 'play' }}"></i>
                                       </button>
                                   </form>
                                   @endif
                                   @if(!$currency->is_default)
                                   <form action="{{ route('admin.currencies.set-default', $currency) }}" method="POST"
                                       class="d-inline set-default-form">
                                       @csrf
                                       <button type="submit" class="btn btn-sm btn-outline-warning"
                                           title="{{ __('Set as Default') }}"
                                           data-confirm="{{ __('Are you sure you want to set this as default currency?') }}">
                                           <i class="fas fa-star"></i>
                                       </button>
                                   </form>
                                   @endif
                                   @if(!$currency->is_default && !$currency->is_active)
                                   <form action="{{ route('admin.currencies.destroy', $currency) }}" method="POST"
                                       class="d-inline delete-form">
                                       @csrf
                                       @method('DELETE')
                                       <button type="submit" class="btn btn-sm btn-outline-danger"
                                           title="{{ __('Delete') }}"
                                           data-confirm="{{ __('Are you sure you want to delete this currency?') }}">
                                           <i class="fas fa-trash"></i>
                                       </button>
                                   </form>
                                   @endif
                               </div>
                           </td>
                       </tr>
                       @endforeach
                   </tbody>
               </table>
           </div>
           @else
           <div class="empty-state text-center py-5">
               <div class="empty-icon mb-4">
                   <i class="fas fa-coins fa-4x text-muted"></i>
               </div>
               <h4 class="fw-semibold mb-2">{{ __('No Currencies Found') }}</h4>
               <p class="text-muted mb-4">{{ __('Start by adding your first currency to the system.') }}</p>
               <a href="{{ route('admin.currencies.create') }}" class="btn btn-primary">
                   <i class="fas fa-plus me-1"></i>
                   {{ __('Add First Currency') }}
               </a>
           </div>
           @endif
       </div>
   </div>

   <!-- Exchange Rate Info -->
   <div class="modern-card mt-4">
       <div class="card-header">
           <h5 class="card-title mb-0">{{ __('Exchange Rate Information') }}</h5>
       </div>
       <div class="card-body">
           <div class="alert alert-info border-0">
               <i class="fas fa-info-circle me-2"></i>
               <strong>{{ __('Note:') }}</strong>
               {{ __('Exchange rates are relative to USD. The default currency serves as the base for all transactions.') }}
           </div>

           <div class="row">
               <div class="col-md-6">
                   <h6 class="fw-semibold mb-3">{{ __('Currency Conversion Examples') }}</h6>
                   @if($currencies->count() >= 2)
                   <div class="conversion-list">
                       @foreach($currencies->take(3) as $currency)
                       <div
                           class="conversion-item d-flex justify-content-between align-items-center py-2 border-bottom">
                           <span class="fw-semibold">1 USD</span>
                           <span class="text-primary fw-bold">{{ number_format($currency->exchange_rate, 2) }}
                               {{ $currency->code }}</span>
                       </div>
                       @endforeach
                   </div>
                   @endif
               </div>
               <div class="col-md-6">
                   <h6 class="fw-semibold mb-3">{{ __('Last Update') }}</h6>
                   <div class="update-info mb-3">
                       <p class="text-muted mb-2">
                           {{ __('Rates last updated:') }}
                       </p>
                       <div class="fw-semibold text-dark">
                           {{ $currencies->max('updated_at')?->diffForHumans() ?? __('Never') }}</div>
                   </div>
                   <button type="button" class="btn btn-sm btn-primary" data-action="update-rates">
                       <i class="fas fa-sync me-1"></i>
                       {{ __('Update All Rates') }}
                   </button>
               </div>
           </div>
       </div>
   </div>
   @endsection

   @push('styles')
   <link rel="stylesheet" href="{{ asset('admin/css/currencies.css') }}">
   @endpush

   @push('scripts')
   <script src="{{ asset('admin/js/currencies.js') }}"></script>
   <script src="{{ asset('admin/js/dropdown-debug.js') }}"></script>
   @endpush