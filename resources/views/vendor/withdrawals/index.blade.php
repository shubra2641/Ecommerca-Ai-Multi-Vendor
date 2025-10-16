@extends('vendor.layout')

@section('title', __('vendor.withdrawals.title'))

@section('content')
<div class="vendor-withdrawals-container">
    <!-- Page Header -->
    <div class="withdrawals-header animate-fade-in">
        <div class="header-content">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-wallet"></i>
                    {{ __('vendor.withdrawals.heading') }}
                </h1>
                <p class="page-subtitle">{{ __('vendor.withdrawals.subtitle') }}</p>
            </div>
            <div class="header-actions">
                <button type="button" class="btn btn-outline-secondary" data-action="reload" data-tooltip="{{ __('vendor.withdrawals.refresh_tooltip') }}">
                    <i class="fas fa-sync-alt"></i> {{ __('vendor.withdrawals.refresh') }}
                </button>
                <a href="{{ route('vendor.withdrawals.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('vendor.withdrawals.new') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Balance Summary Cards -->
    <div class="balance-summary animate-slide-in">
    <div class="card modern-card balance-card" data-stat="total">
            <div class="balance-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="balance-info">
                <div class="balance-label">{{ __('vendor.withdrawals.total_withdrawn') }}</div>
                <div class="balance-amount">
                    {{ number_format($totalWithdrawn, 2) }}
                    <span class="balance-currency">{{ $currency }}</span>
                </div>
            </div>
        </div>
        
    <div class="card modern-card balance-card" data-stat="pending">
            <div class="balance-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="balance-info">
                <div class="balance-label">{{ __('vendor.withdrawals.pending_amount') }}</div>
                <div class="balance-amount">
                    {{ number_format($pendingAmount, 2) }}
                    <span class="balance-currency">{{ $currency }}</span>
                </div>
            </div>
        </div>
        
    <div class="card modern-card balance-card" data-stat="approved">
            <div class="balance-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="balance-info">
                <div class="balance-label">{{ __('vendor.withdrawals.approved_this_month') }}</div>
                <div class="balance-amount">
                    {{ number_format($approvedThisMonth, 2) }}
                    <span class="balance-currency">{{ $currency }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="withdrawals-filters animate-scale-in">
        <form method="GET" action="{{ route('vendor.withdrawals.index') }}" class="filters-form">
            <div class="filters-row">
                <div class="search-group">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
               <input type="text" 
                   name="search" 
                   class="search-input" 
                   placeholder="{{ __('vendor.withdrawals.search_placeholder') }}" 
                   value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="filter-group">
                    <select name="status" class="form-select">
                        <option value="">{{ __('vendor.withdrawals.status_all') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('vendor.withdrawals.status_pending') }}</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('vendor.withdrawals.status_approved') }}</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('vendor.withdrawals.status_completed') }}</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('vendor.withdrawals.status_rejected') }}</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <input type="date" 
                           name="date_from" 
                           class="form-control" 
                           placeholder="From Date" 
                           value="{{ request('date_from') }}">
                </div>
            </div>
            
                <div class="filter-group d-flex align-items-center gap-2 mt-2">
                    <label class="d-flex align-items-center gap-1">
                        <input type="checkbox" name="held" value="1" {{ ($heldOnly ?? false) ? 'checked' : '' }}>
                        <span>{{ __('Held Only') }}</span>
                    </label>
                </div>
                <div class="filter-actions mt-1 d-flex gap-1 justify-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> {{ __('vendor.withdrawals.apply_filters') }}
                </button>
                <a href="{{ route('vendor.withdrawals.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> {{ __('vendor.withdrawals.clear_all') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Withdrawals Table -->
    <div class="withdrawals-table-container animate-fade-in">
        @if($withdrawals->count() > 0)
            <div class="table-responsive">
                <table class="table withdrawals-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-money-bill-wave"></i> {{ __('vendor.withdrawals.col_amount') }}</th>
                            <th><i class="fas fa-layer-group"></i> {{ __('Gross') }}</th>
                            <th><i class="fas fa-percentage"></i> {{ __('Commission') }}</th>
                            <th><i class="fas fa-calculator"></i> {{ __('Commission (Exact)') }}</th>
                            <th><i class="fas fa-info-circle"></i> {{ __('vendor.withdrawals.col_status') }}</th>
                            <th><i class="fas fa-lock"></i> {{ __('Held At') }}</th>
                            <th><i class="fas fa-calendar"></i> {{ __('vendor.withdrawals.col_request_date') }}</th>
                            <th><i class="fas fa-credit-card"></i> {{ __('vendor.withdrawals.col_payment_method') }}</th>
                            <th><i class="fas fa-hashtag"></i> {{ __('vendor.withdrawals.col_reference') }}</th>
                            <th><i class="fas fa-cogs"></i> {{ __('vendor.withdrawals.col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($withdrawals as $withdrawal)
                        <tr>
                            <td>
                                <div class="amount-display">
                                    <span class="amount-value">{{ number_format($withdrawal->amount, 2) }}</span>
                                    <span class="amount-currency">{{ $withdrawal->currency }}</span>
                                </div>
                            </td>
                            <td>
                                @if(!is_null($withdrawal->gross_amount))
                                    <span class="text-muted small">{{ number_format($withdrawal->gross_amount,2) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if(!is_null($withdrawal->commission_amount) && $withdrawal->commission_amount > 0)
                                    <span class="text-danger small">-{{ number_format($withdrawal->commission_amount,2) }}</span>
                                @else
                                    <span class="text-muted">0.00</span>
                                @endif
                            </td>
                            <td>
                                @if(!is_null($withdrawal->commission_amount_exact) && $withdrawal->commission_amount_exact > 0)
                                    <span class="text-danger small">-{{ number_format($withdrawal->commission_amount_exact,4) }}</span>
                                @else
                                    <span class="text-muted">0.0000</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-{{ $withdrawal->status }}">
                                    @if($withdrawal->status === 'pending')
                                        <i class="fas fa-clock"></i>
                                    @elseif($withdrawal->status === 'approved')
                                        <i class="fas fa-check"></i>
                                    @elseif($withdrawal->status === 'completed')
                                        <i class="fas fa-check-circle"></i>
                                    @elseif($withdrawal->status === 'rejected')
                                        <i class="fas fa-times"></i>
                                    @endif
                                    {{ __('vendor.withdrawals.status_' . $withdrawal->status, [], null) ?: ucfirst($withdrawal->status) }}
                                </span>
                            </td>
                            <td>
                                {{ $withdrawal->held_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>
                            <td>
                                <div class="date-display">
                                    <span class="date-primary">{{ $withdrawal->created_at->format('M d, Y') }}</span>
                                    <span class="date-secondary">{{ $withdrawal->created_at->diffForHumans() }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="pmethod-cell">
                                    @if($withdrawal->payment_method === 'bank_transfer')
                                        <i class="fas fa-university pmethod-icon bank"></i>
                                    @else
                                        <i class="fas fa-credit-card pmethod-icon generic"></i>
                                    @endif
                                    {{ __('vendor.withdrawals.payment_' . $withdrawal->payment_method, [], null) ?: ucfirst(str_replace('_', ' ', $withdrawal->payment_method)) }}
                                </div>
                            </td>
                            <td>
                                @if($withdrawal->reference)
                                    <code class="reference-code ref-badge">{{ $withdrawal->reference }}</code>
                                @else
                                    <span class="ref-pending">Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-outline-primary" data-action="view-withdrawal" data-id="{{ $withdrawal->id }}" data-tooltip="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($withdrawal->reference)
                                    <button class="btn btn-outline-secondary" data-action="copy" data-copy="{{ $withdrawal->reference }}" data-tooltip="Copy Reference">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    @endif
                                    @if($withdrawal->status === 'completed')
                                    <a href="{{ route('vendor.withdrawals.receipt', $withdrawal->id) }}" class="btn btn-outline-success" data-tooltip="Download Receipt">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <h4>{{ __('vendor.withdrawals.empty_title') }}</h4>
                <p>{{ __('vendor.withdrawals.empty_subtitle') }}</p>
                <a href="{{ route('vendor.withdrawals.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('vendor.withdrawals.create_first') }}
                </a>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($withdrawals->hasPages())
    <div class="withdrawals-pagination">
        {{ $withdrawals->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@endsection
