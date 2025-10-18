@extends('layouts.admin')

@section('title', __('Financial Report'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title mb-1">{{ __('Financial Report') }}</h1>
            <p class="text-muted mb-0">{{ __('Financial analysis and balance statistics') }}</p>
        </div>
        <div class="page-actions">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary js-refresh-page" data-action="refresh" title="{{ __('Refresh') }}">
                    <i class="fas fa-sync-alt" aria-hidden="true"></i> {{ __('Refresh') }}
                </button>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" title="{{ __('Export') }}">
                        <i class="fas fa-download" aria-hidden="true"></i> {{ __('Export') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item js-export" href="#" data-export-type="excel" data-report="financial" title="{{ __('Excel') }}">{{ __('Excel') }}</a></li>
                        <li><a class="dropdown-item js-export" href="#" data-export-type="pdf" data-report="financial" title="{{ __('PDF') }}">{{ __('PDF') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-success h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" id="financial-total-balance" data-countup data-decimals="2" data-target="{{ number_format($financialData['totalBalance'], 2, '.', '') }}">${{ number_format($financialData['totalBalance'], 2) }}</div>
                        <div class="stats-label">{{ __('Total Balance') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-dollar-sign text-success"></i>
                            <span class="text-success">{{ __('System total') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-dollar-sign"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-primary h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" id="financial-vendor-balance" data-countup data-decimals="2" data-target="{{ number_format($financialData['vendorBalance'], 2, '.', '') }}">${{ number_format($financialData['vendorBalance'], 2) }}</div>
                        <div class="stats-label">{{ __('Vendor Balance') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-store text-primary"></i>
                            <span class="text-primary">{{ __('Vendor earnings') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-store"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-info h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" id="financial-customer-balance" data-countup data-decimals="2" data-target="{{ number_format($financialData['customerBalance'], 2, '.', '') }}">${{ number_format($financialData['customerBalance'], 2) }}</div>
                        <div class="stats-label">{{ __('Customer Balance') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-users text-info"></i>
                            <span class="text-info">{{ __('Customer deposits') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-warning h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" id="financial-average-balance" data-countup data-decimals="2" data-target="{{ number_format($financialData['averageBalance'], 2, '.', '') }}">${{ number_format($financialData['averageBalance'], 2) }}</div>
                        <div class="stats-label">{{ __('Average Balance') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-chart-line text-warning"></i>
                            <span class="text-warning">{{ __('Per account') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Statistics -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Balance Statistics') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><strong>{{ __('Maximum Balance') }}:</strong></td>
                                    <td class="text-success">${{ number_format($financialData['maxBalance'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Minimum Balance') }}:</strong></td>
                                    <td class="text-danger">${{ number_format($financialData['minBalance'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Average Balance') }}:</strong></td>
                                    <td class="text-info">${{ number_format($financialData['averageBalance'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Total Balance') }}:</strong></td>
                                    <td class="text-primary">${{ number_format($financialData['totalBalance'], 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Balance Distribution') }}</h6>
                </div>
                <div class="card-body">
                    @if(isset($financialData['balanceDistribution']) && count($financialData['balanceDistribution']) >
                    0)
                    <div class="chart-container h-380">
                        <canvas id="balanceDistributionChart"></canvas>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-pie fa-3x text-gray-300 mb-3" aria-hidden="true"></i>
                        <p class="text-muted">{{ __('No distribution data available') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends -->
    @if(isset($financialData['monthlyTrends']) && count($financialData['monthlyTrends']) > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Monthly Financial Trends') }}</h6>
        </div>
        <div class="card-body">
            <div class="chart-container h-400">
                <canvas id="monthlyTrendsChart"></canvas>
            </div>
        </div>
    </div>
    @endif

    <!-- Financial Summary Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Financial Summary') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Total Balance') }}</th>
                            <th>{{ __('Average Balance') }}</th>
                            <th>{{ __('Count') }}</th>
                            <th>{{ __('Percentage') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>{{ __('Vendors') }}</strong></td>
                            <td class="text-success">${{ number_format($financialData['vendorBalance'], 2) }}</td>
                            <td>${{ $financialData['totalBalance'] > 0 ? number_format($financialData['vendorBalance'] / max(1, $financialData['totalBalance']) * 100, 1) : '0' }}%
                            </td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('Customers') }}</strong></td>
                            <td class="text-info">${{ number_format($financialData['customerBalance'], 2) }}</td>
                            <td>${{ $financialData['totalBalance'] > 0 ? number_format($financialData['customerBalance'] / max(1, $financialData['totalBalance']) * 100, 1) : '0' }}%
                            </td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                        <tr class="table-active">
                            <td><strong>{{ __('Total') }}</strong></td>
                            <td class="text-primary">
                                <strong>${{ number_format($financialData['totalBalance'], 2) }}</strong></td>
                            <td>100%</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
