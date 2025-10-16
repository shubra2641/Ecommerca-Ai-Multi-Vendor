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
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('Total Gateways') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_gateways'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('Active Gateways') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['enabled_gateways'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ __('Transactions (30d)') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_transactions']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('Revenue (30d)') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($stats['total_revenue'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
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
                        <table class="table table-bordered table-sm" id="performanceTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{ __('Gateway') }}</th>
                                    <th>{{ __('Success Rate') }}</th>
                                    <th>{{ __('Transactions') }}</th>
                                    <th class="d-none d-md-table-cell">{{ __('Avg Response Time') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($performanceMetrics as $metric)
                                <tr>
                                    <td>{{ $metric['gateway'] }}</td>
                                    <td>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar {{ $metric['success_rate'] >= 95 ? 'bg-success' : ($metric['success_rate'] >= 80 ? 'bg-warning' : 'bg-danger') }}"
                                                role="progressbar" data-progress="{{ $metric['success_rate'] }}"
                                                aria-valuenow="{{ $metric['success_rate'] }}" aria-valuemin="0"
                                                aria-valuemax="100">
                                                {{ $metric['success_rate'] }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($metric['total_transactions']) }}</td>
                                    <td class="d-none d-md-table-cell">{{ $metric['avg_response_time'] }}ms</td>
                                    <td>
                                        @if($metric['success_rate'] >= 95)
                                        <span class="badge badge-success">{{ __('Excellent') }}</span>
                                        @elseif($metric['success_rate'] >= 80)
                                        <span class="badge badge-warning">{{ __('Good') }}</span>
                                        @else
                                        <span class="badge badge-danger">{{ __('Poor') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-action="test-gateway"
                                            data-gateway="{{ $metric['gateway'] }}">
                                            <i class="fas fa-vial"></i> {{ __('Test') }}
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" data-action="view-analytics"
                                            data-gateway="{{ $metric['gateway'] }}">
                                            <i class="fas fa-chart-line"></i> {{ __('Analytics') }}
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