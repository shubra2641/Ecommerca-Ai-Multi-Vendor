@extends('layouts.admin')

@section('title', __('Payment Gateway Management'))

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <h1 class="h3 mb-0 text-gray-800">{{ __('Payment Gateway Management') }}</h1>
            <p class="mb-0 text-muted">{{ __('Monitor and manage your payment gateways') }}</p>
        </div>
        <div class="btn-group ms-md-3">
            <button type="button" class="btn btn-primary" data-action="sync-gateways">
                <i class="fas fa-sync-alt me-2"></i>
                <span class="d-none d-sm-inline">{{ __('Sync Gateways') }}</span>
                <span class="d-inline d-sm-none">{{ __('Sync') }}</span>
            </button>
            <a href="{{ route('admin.payment-gateways.index') }}" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-list"></i>
                <span class="d-none d-sm-inline">{{ __('Gateway List') }}</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-primary h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="{{ (int)$stats['total_gateways'] }}">{{ $stats['total_gateways'] }}</div>
                        <div class="stats-label">{{ __('Total Gateways') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-credit-card text-primary"></i>
                            <span class="text-primary">{{ __('Payment Methods') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-credit-card"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-success h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="{{ (int)$stats['enabled_gateways'] }}">{{ $stats['enabled_gateways'] }}</div>
                        <div class="stats-label">{{ __('Active Gateways') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-arrow-up text-success"></i>
                            <span class="text-success">{{ number_format((($stats['enabled_gateways'] / max($stats['total_gateways'], 1)) * 100), 1) }}% {{ __('active') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-info h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="{{ (int)$stats['total_transactions'] }}">{{ number_format($stats['total_transactions']) }}</div>
                        <div class="stats-label">{{ __('Transactions (30d)') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-exchange-alt text-info"></i>
                            <span class="text-info">{{ __('Last 30 days') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-exchange-alt"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-warning h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number">${{ number_format($stats['total_revenue'], 2) }}</div>
                        <div class="stats-label">{{ __('Revenue (30d)') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-dollar-sign text-warning"></i>
                            <span class="text-warning">{{ __('Total earnings') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-dollar-sign"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gateway Performance -->
    <div class="row mb-4">
        <div class="col-12 col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Gateway Performance') }}</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">{{ __('Actions') }}:</div>
                            <a class="dropdown-item" href="#"
                                data-action="refresh-performance-data">{{ __('Refresh Data') }}</a>
                            <a class="dropdown-item" href="#"
                                data-action="export-performance-report">{{ __('Export Report') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="gatewaysTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{ __('Gateway') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <!-- Gateway Status -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Gateway Status') }}</h6>
                </div>
                <div class="card-body">
                    @foreach($gateways as $gateway)
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3">
                            @if($gateway->enabled)
                            <div class="icon-circle bg-success">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            @else
                            <div class="icon-circle bg-secondary">
                                <i class="fas fa-times text-white"></i>
                            </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="small text-gray-500">{{ $gateway->name }}</div>
                            <div class="font-weight-bold">
                                @if($gateway->enabled)
                                {{ __('Active') }}
                                @else
                                {{ __('Inactive') }}
                                @endif
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-secondary" data-action="toggle-gateway"
                                data-id="{{ $gateway->id }}">
                                @if($gateway->enabled)
                                <i class="fas fa-pause"></i>
                                @else
                                <i class="fas fa-play"></i>
                                @endif
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Quick Actions') }}</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action" data-action="test-all-gateways">
                            <i class="fas fa-vial text-primary"></i>
                            {{ __('Test All Gateways') }}
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" data-action="generate-report">
                            <i class="fas fa-file-alt text-info"></i>
                            {{ __('Generate Report') }}
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" data-action="view-logs">
                            <i class="fas fa-list text-warning"></i>
                            {{ __('View Logs') }}
                        </a>
                        <a href="{{ route('admin.payment-gateways.create') }}"
                            class="list-group-item list-group-item-action">
                            <i class="fas fa-plus text-success"></i>
                            {{ __('Add Gateway') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Recent Transactions') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="transactionsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Gateway') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="d-none d-md-table-cell">{{ __('Customer') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $transaction)
                        <tr>
                            <td>#{{ $transaction->id }}</td>
                            <td>{{ $transaction->paymentGateway->name ?? 'N/A' }}</td>
                            <td>${{ number_format($transaction->amount, 2) }}</td>
                            <td>
                                @switch($transaction->status)
                                @case('completed')
                                <span class="badge badge-success">{{ __('Completed') }}</span>
                                @break
                                @case('pending')
                                <span class="badge badge-warning">{{ __('Pending') }}</span>
                                @break
                                @case('failed')
                                <span class="badge badge-danger">{{ __('Failed') }}</span>
                                @break
                                @default
                                <span class="badge badge-secondary">{{ ucfirst($transaction->status) }}</span>
                                @endswitch
                            </td>
                            <td class="d-none d-md-table-cell">{{ $transaction->order->user->name ?? 'Guest' }}</td>
                            <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-action="view-transaction"
                                    data-id="{{ $transaction->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Test Gateway Modal -->
<div class="modal fade" id="testGatewayModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Test Gateway Connection') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="testGatewayForm">
                    <div class="form-group">
                        <label>{{ __('Test Amount') }}</label>
                        <input type="number" id="testAmount" name="amount" class="form-control" value="1.00"
                            step="0.01" />
                    </div>
                </form>
                <div id="testResults" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" class="btn btn-primary"
                    data-action="run-gateway-test">{{ __('Run Test') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Data root: provide URLs and small payloads for externally loaded JS (no inline JS) --}}
<div id="pgMgmtRoot" class="d-none" data-gateways='@json($gateways->pluck("id","name"))'
    data-sync-url='{{ route("admin.payment-gateways-management.sync") }}'
    data-test-base='{{ url("admin/payment-gateways-management") }}'
    data-toggle-base='{{ route("admin.payment-gateways.index") }}'
    data-translate-testing='{{ addslashes(__('Testing connection...')) }}'
    data-translate-test-success='{{ addslashes(__('Test Successful')) }}'
    data-translate-test-failed='{{ addslashes(__('Test Failed')) }}'
    data-translate-gateway-not-found='{{ addslashes(__('Gateway not found')) }}'
    data-translate-sync-failed='{{ addslashes(__('Failed to sync gateways')) }}'></div>

@endsection