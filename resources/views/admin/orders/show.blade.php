@extends('layouts.admin')

@section('title', __('Order :id', ['id' => $order->id]))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <i class="fas fa-shopping-bag"></i>
                    {{ __('Order') }} <span class="order-id-badge">#{{ $order->id }}</span>
                </h1>
                <p class="admin-order-subtitle">{{ __('Order details, payments and management') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.orders.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back to Orders') }}
                </a>
                <form method="POST" action="{{ route('admin.orders.retry-assign', $order->id) }}" class="d-inline admin-form">
                    @csrf
                    <button class="admin-btn admin-btn-primary">
                        <i class="fas fa-redo"></i>
                        {{ __('Retry Serials') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="admin-order-grid">

            <!-- Sidebar -->
            <div class="admin-order-sidebar">

                <!-- Summary Card -->
                <div class="admin-modern-card admin-summary-card">
                    <div class="admin-card-header">
                        <i class="fas fa-clipboard-check"></i>
                        <h3 class="admin-card-title">{{ __('Summary') }}</h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="admin-info-grid">
                            <div class="admin-info-row">
                                <span class="admin-info-label">{{ __('Order ID') }}</span>
                                <span class="admin-info-value">#{{ $order->id }}</span>
                            </div>
                            <div class="admin-info-row">
                                <span class="admin-info-label">{{ __('Placed') }}</span>
                                <span class="admin-info-value">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="admin-info-row">
                                <span class="admin-info-label">{{ __('Customer') }}</span>
                                <span class="admin-info-value">
                                    @if($order->user)
                                    <div class="customer-name">{{ $order->user->name ?? __('(No Name)') }}</div>
                                    <div class="customer-email">{{ $order->user->email }}</div>
                                    @if($order->user->phone ?? false)
                                    <div class="customer-phone">{{ $order->user->phone }}</div>
                                    @endif
                                    @else
                                    {{ __('Guest') }}
                                    @endif
                                </span>
                            </div>
                            <div class="admin-info-row">
                                <span class="admin-info-label">{{ __('Status') }}</span>
                                <span class="admin-info-value">
                                    <span class="admin-status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                                    @if(!empty($order->has_backorder))
                                    <span class="admin-status-badge status-warning">{{ __('Backorder') }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="admin-info-row">
                                <span class="admin-info-label">{{ __('Payment Status') }}</span>
                                <span class="admin-info-value">
                                    <span class="admin-payment-badge">{{ $order->payment_status ?? __('Pending') }}</span>
                                </span>
                            </div>
                            <div class="admin-info-row">
                                <span class="admin-info-label">{{ __('Shipping') }}</span>
                                <span class="admin-info-value">
                                    {{ $order->shipping_price ? number_format($order->shipping_price, 2) . ' ' . ($order->currency ?? '') : __('N/A') }}
                                    @if($order->shipping_zone)
                                    <br><small>{{ $order->shipping_zone->company_name ?? __('N/A') }}</small>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="admin-total-section">
                            <div class="admin-total-breakdown">
                                <div class="admin-total-row">
                                    <span>{{ __('Subtotal') }}</span>
                                    <span>{{ number_format($order->items_subtotal ?? 0, 2) }} {{ $order->currency ?? '' }}</span>
                                </div>
                                @if($order->shipping_price > 0)
                                <div class="admin-total-row">
                                    <span>{{ __('Shipping') }}</span>
                                    <span>{{ number_format($order->shipping_price, 2) }} {{ $order->currency ?? '' }}</span>
                                </div>
                                @endif
                            </div>
                            <div class="admin-total-label">{{ __('Total') }}</div>
                            <div class="admin-total-amount">
                                <span class="stats-number" data-countup data-target="{{ $order->total }}">{{ number_format($order->total,2) }}</span>
                                <span class="admin-currency">{{ $order->currency ?? '' }}</span>
                            </div>
                        </div>
                        <div class="admin-card-actions">
                            <a href="{{ route('admin.orders.show', $order) }}" class="admin-btn-block admin-btn-outline">
                                <i class="fas fa-sync-alt"></i>
                                {{ __('Refresh') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Status History Card -->
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <i class="fas fa-clock"></i>
                        <h3 class="admin-card-title">{{ __('Status History') }}</h3>
                    </div>
                    <div class="admin-card-body">
                        @if($order->statusHistory->count())
                        <div class="admin-timeline">
                            @foreach($order->statusHistory as $hist)
                            <div class="admin-timeline-item">
                                <div class="admin-timeline-marker"></div>
                                <div class="admin-timeline-content">
                                    <div class="admin-timeline-status">{{ ucfirst($hist->status) }}</div>
                                    <div class="admin-timeline-date">{{ $hist->created_at->format('Y-m-d H:i') }}</div>
                                    @if($hist->note)
                                    <div class="admin-timeline-note">{{ $hist->note }}</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="admin-empty-state">{{ __('No status changes yet.') }}</div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Main Content -->
            <div class="admin-order-main">

                <!-- Order Items -->
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <i class="fas fa-shopping-bag"></i>
                        <h3 class="admin-card-title">{{ __('Items') }}</h3>
                        <span class="admin-badge-count">{{ $order->items->count() }}</span>
                    </div>
                    <div class="admin-card-body admin-table-wrapper">
                        <div class="admin-items-list">
                            @foreach($order->items as $item)
                            <div class="admin-item-card">
                                <div class="admin-item-main">
                                    <div class="admin-item-placeholder">
                                        {{ strtoupper(substr($item->name,0,2)) }}
                                    </div>
                                    <div class="admin-item-details">
                                        <div class="admin-item-name">{{ $item->name }}</div>
                                        @if($item->meta && isset($item->meta['variant']) && !empty($item->meta['variant']['name']))
                                        <div class="admin-item-variant">{{ $item->meta['variant']['name'] }}</div>
                                        @endif
                                        @if(!empty($item->is_backorder))
                                        <div class="admin-item-badges">
                                            <span class="admin-badge admin-badge-warning">{{ __('Backorder') }}</span>
                                            <form method="POST" action="{{ route('admin.orders.cancelBackorderItem', ['order' => $order->id, 'item' => $item->id]) }}" class="d-inline admin-form">
                                                @csrf
                                                <button class="admin-btn-tiny admin-btn-danger" type="submit">{{ __('Cancel Backorder') }}</button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="admin-item-meta">
                                    <div class="admin-item-qty">
                                        <span class="qty-label">{{ __('Qty') }}:</span>
                                        <span class="qty-value">{{ $item->qty }}</span>
                                        <div class="admin-item-status">
                                            <span class="admin-badge admin-badge-{{ $item->committed? 'success':'secondary' }}">
                                                {{ $item->committed? __('Committed'):__('Not Committed') }}
                                            </span>
                                            @if($item->restocked)
                                            <span class="admin-badge admin-badge-info">{{ __('Restocked') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="admin-item-price">{{ number_format($item->price,2) }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Customer & Shipping -->
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3 class="admin-card-title">{{ __('Customer & Shipping') }}</h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="admin-two-cols">
                            <div class="admin-col">
                                <h5 class="admin-section-title">{{ __('Customer Details') }}</h5>
                                <div class="admin-customer-info">
                                    @if($order->user)
                                    <div class="customer-name-bold">{{ $order->user->name ?? __('(No Name)') }}</div>
                                    <div class="customer-email-text">{{ $order->user->email }}</div>
                                    @if($order->user->phone ?? false)
                                    <div class="customer-phone-text">{{ $order->user->phone }}</div>
                                    @endif
                                    @else
                                    <div>{{ __('Guest') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="admin-col">
                                <h5 class="admin-section-title">{{ __('Shipping Address') }}</h5>
                                <div class="admin-address-text">{{ $aovAddressText ?: __('N/A') }}</div>
                            </div>
                        </div>
                        @if(!empty($order->notes) || !empty($aovFirstPaymentNote))
                        <div class="admin-notes-section">
                            <strong class="admin-notes-title">{{ __('Notes') }}</strong>
                            <div class="admin-notes-text">{{ $order->notes ?? $aovFirstPaymentNote }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Payments -->
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <i class="fas fa-credit-card"></i>
                        <h3 class="admin-card-title">{{ __('Payments') }}</h3>
                    </div>
                    <div class="admin-card-body">
                        @foreach($order->payments as $payment)
                        <div class="admin-payment-item">
                            <div class="admin-payment-header">
                                <div class="admin-payment-left">
                                    <div class="admin-payment-method">{{ $payment->method }}</div>
                                    <div class="admin-payment-amount">{{ number_format($payment->amount,2) }} {{ $payment->currency ?? '' }}</div>
                                </div>
                                <div class="admin-payment-right">
                                    <span class="admin-payment-status status-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span>
                                </div>
                            </div>
                            @if($payment->attachments->count())
                            <div class="admin-payment-attachments">
                                <strong>{{ __('Attachments:') }}</strong>
                                @foreach($payment->attachments as $att)
                                <div class="admin-attachment-item">
                                    @if(str_starts_with($att->mime, 'image/'))
                                    <img src="{{ \App\Helpers\GlobalHelper::storageImageUrl($att->path) }}" alt="Payment proof">
                                    @else
                                    <a href="{{ \App\Helpers\GlobalHelper::storageImageUrl($att->path) }}" target="_blank" class="admin-attachment-link">
                                        <i class="fas fa-paperclip"></i>
                                        {{ basename($att->path) }}
                                    </a>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif
                            @if($payment->status !== 'completed' && !empty($aovOfflinePayments[$payment->id]))
                            <div class="admin-payment-actions">
                                <form method="POST" action="{{ route('admin.orders.payments.accept', $payment->id) }}" class="d-inline admin-form">
                                    @csrf
                                    <button class="admin-btn-small admin-btn-success">
                                        <i class="fas fa-check"></i>
                                        {{ __('Accept') }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.orders.payments.reject', $payment->id) }}" class="d-inline admin-form">
                                    @csrf
                                    <button class="admin-btn-small admin-btn-warning">
                                        <i class="fas fa-times"></i>
                                        {{ __('Reject') }}
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Manage Order -->
                <div class="admin-modern-card admin-manage-card">
                    <div class="admin-card-header">
                        <i class="fas fa-cog"></i>
                        <h3 class="admin-card-title">{{ __('Manage') }}</h3>
                    </div>
                    <div class="admin-card-body">
                        <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="admin-form admin-manage-form">
                            @csrf
                            <div class="admin-form-grid">
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Change Status') }}</label>
                                    <select name="status" class="admin-form-select">
                                        @foreach(['pending','processing','completed','cancelled','on-hold','refunded'] as $s)
                                        <option value="{{ $s }}" {{ $order->status === $s? 'selected':'' }}>{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="admin-form-group admin-form-group-wide">
                                    <label class="admin-form-label">{{ __('Note (optional)') }}</label>
                                    <input type="text" name="note" class="admin-form-input" placeholder="{{ __('Provide optional note') }}">
                                </div>
                                <div class="admin-form-actions">
                                    <button class="admin-btn admin-btn-primary admin-btn-large">
                                        <i class="fas fa-check"></i>
                                        {{ __('Update') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>
@endsection