@extends('layouts.admin')

@section('title', __('Order :id', ['id' => $order->id]))

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('Order') }} #{{ $order->id }}</h1>
        <p class="page-description">{{ __('Order details, payments and management') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            {{ __('Back to Orders') }}
        </a>
        <form method="POST" action="{{ route('admin.orders.retry-assign', $order->id) }}" class="d-inline admin-form">
            @csrf
            <button class="btn btn-outline-primary">
                <i class="fas fa-sync-alt"></i>
                {{ __('Retry Serials') }}
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-receipt text-primary"></i>
                <h3 class="card-title mb-0">{{ __('Summary') }}</h3>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted">{{ __('Order ID') }}</dt>
                    <dd class="col-7">#{{ $order->id }}</dd>

                    <dt class="col-5 text-muted">{{ __('Placed') }}</dt>
                    <dd class="col-7">{{ $order->created_at->format('Y-m-d H:i') }}</dd>

                    <dt class="col-5 text-muted">{{ __('Customer') }}</dt>
                    <dd class="col-7">
                        @if($order->user)
                        <div><strong>{{ $order->user->name ?? __('(No Name)') }}</strong></div>
                        <div class="small text-muted">{{ $order->user->email }}</div>
                        @if($order->user->phone ?? false)
                        <div class="small">{{ $order->user->phone }}</div>
                        @endif
                        @else
                        {{ __('Guest') }}
                        @endif
                    </dd>

                    <dt class="col-5 text-muted">{{ __('Status') }}</dt>
                    <dd class="col-7">
                        <span class="badge bg-info">{{ ucfirst($order->status) }}</span>
                        @if(!empty($order->has_backorder))
                        <span class="badge bg-warning text-dark ms-1">{{ __('Backorder') }}</span>
                        @endif
                    </dd>

                    <dt class="col-5 text-muted">{{ __('Payment Status') }}</dt>
                    <dd class="col-7">{{ $order->payment_status ?? __('Pending') }}</dd>

                    <dt class="col-5 text-muted">{{ __('Shipping') }}</dt>
                    <dd class="col-7">{{ $order->shipping_method ?? __('N/A') }}</dd>

                    <dt class="col-5 text-muted">{{ __('Total') }}</dt>
                    <dd class="col-7"><span class="stats-number" data-countup data-target="{{ $order->total }}">{{ number_format($order->total,2) }}</span>
                        <span class="small text-muted">{{ $order->currency ?? '' }}</span>
                    </dd>
                </dl>

                <hr>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-secondary">{{ __('Refresh') }}</a>
                </div>
            </div>
        </div>

    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-history text-secondary"></i>
                <h3 class="card-title mb-0">{{ __('Status History') }}</h3>
            </div>
            <div class="card-body">
                @if($order->statusHistory->count())
                <ul class="list-unstyled">
                    @foreach($order->statusHistory as $hist)
                    <li>
                        <strong>{{ ucfirst($hist->status) }}</strong>
                        <div class="small text-muted">{{ $hist->created_at->format('Y-m-d H:i') }}</div>
                        @if($hist->note)<div class="mt-1">{{ $hist->note }}</div>@endif
                        <hr>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="small text-muted">{{ __('No status changes yet.') }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
    <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2 justify-content-between">
                <div class="d-flex align-items-center gap-2"><i class="fas fa-box-open text-info"></i>
                <h3 class="card-title mb-0">{{ __('Items') }}</h3></div>
                <div class="card-actions">
                    <!-- actions could go here -->
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Qty') }}</th>
                            <th>{{ __('Price') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                {{ $item->name }} @if(!empty($aovVariantLabels[$item->id]))<span class="text-muted small">—
                                    {{ $aovVariantLabels[$item->id] }}</span>@endif
                                @if(!empty($item->is_backorder))
                                <div class="mt-1"><span class="badge bg-warning text-dark">{{ __('Backorder') }}</span>
                                    <form method="POST"
                                        action="{{ route('admin.orders.cancelBackorderItem', ['order' => $order->id, 'item' => $item->id]) }}"
                                        class="d-inline ms-2 admin-form">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger"
                                            type="submit">{{ __('Cancel Backorder') }}</button>
                                    </form>
                                </div>
                                @endif
                            </td>
                            <td>
                                {{ $item->qty }}<br>
                                <span class="badge bg-{{ $item->committed? 'success':'secondary' }} small"
                                   >{{ $item->committed? __('Committed'):__('Not Committed') }}</span>
                                @if($item->restocked)
                                <span class="badge bg-info text-dark small"
                                   >{{ __('Restocked') }}</span>
                                @endif
                            </td>
                            <td>{{ number_format($item->price,2) }}</td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

            <div class="card modern-card mt-3">
            <div class="card-header d-flex align-items-center gap-2">
                <h3 class="card-title mb-0">{{ __('Customer & Shipping') }}</h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h5 class="mb-2">{{ __('Customer Details') }}</h5>
                        @if($order->user)
                        <div><strong>{{ $order->user->name ?? __('(No Name)') }}</strong></div>
                        <div class="small text-muted">{{ $order->user->email }}</div>
                        @if($order->user->phone ?? false)
                        <div class="small">{{ $order->user->phone }}</div>
                        @endif
                        @else
                        <div>{{ __('Guest') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-2">{{ __('Shipping Address') }}</h5>
                        <div class="small text-muted ws-pre-line">{{ $aovAddressText ?: __('N/A') }}</div>
                    </div>
                </div>

                @if(!empty($order->notes) || !empty($aovFirstPaymentNote))
                <hr>
                <div><strong>{{ __('Notes') }}</strong>
                    <div class="small text-muted">{{ $order->notes ?? $aovFirstPaymentNote }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="card modern-card mt-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-credit-card text-success"></i>
                <h3 class="card-title mb-0">{{ __('Payments') }}</h3>
            </div>
            <div class="card-body">
                @foreach($order->payments as $payment)
                <div class="payment-item mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $payment->method }}</strong>
                            <div class="small text-muted">{{ number_format($payment->amount,2) }}
                                {{ $payment->currency ?? '' }}
                            </div>
                        </div>
                        <div>
                            <span
                                class="badge {{ $payment->status === 'completed' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($payment->status) }}</span>
                        </div>
                    </div>

                    @if($payment->attachments->count())
                    <div class="mt-2">
                        <strong>{{ __('Attachments:') }}</strong>
                        @foreach($payment->attachments as $att)
                        <a href="{{ asset('storage/'.$att->path) }}" target="_blank"
                            class="d-block">{{ $att->path }}</a>
                        @endforeach
                    </div>
                    @endif

                    @if($payment->status !== 'completed' && !empty($aovOfflinePayments[$payment->id]))
                    <div class="mt-2">
                        <form method="POST" action="{{ route('admin.orders.payments.accept', $payment->id) }}"
                            class="d-inline admin-form">
                            @csrf
                            <button class="btn btn-sm btn-success">{{ __('Accept') }}</button>
                        </form>
                        <form method="POST" action="{{ route('admin.orders.payments.reject', $payment->id) }}"
                            class="d-inline admin-form">
                            @csrf
                            <button class="btn btn-sm btn-warning">{{ __('Reject') }}</button>
                        </form>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <div class="card modern-card mt-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-exchange-alt text-primary"></i>
                <h3 class="card-title mb-0">{{ __('Manage') }}</h3>
                <div class="card-actions">
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="admin-form">
                    @csrf
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <label class="form-label">{{ __('Change Status') }}</label>
                            <select name="status" class="form-select">
                                @foreach(['pending','processing','completed','cancelled','on-hold','refunded'] as $s)
                                <option value="{{ $s }}" {{ $order->status === $s? 'selected':'' }}>{{ ucfirst($s) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">{{ __('Note (optional)') }}</label>
                            <input type="text" name="note" class="form-control"
                                placeholder="{{ __('Provide optional note') }}">
                        </div>
                        <div class="col-auto self-end">
                            <button class="btn btn-primary">{{ __('Update') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection