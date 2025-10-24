@extends('layouts.admin')

@section('title', __('Financial Report'))

@section('content')
<div class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="admin-order-title">
                <h1>
                    <i class="fas fa-chart-line"></i>
                    {{ __('Financial Report') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Financial analysis and balance statistics') }}</p>
            </div>
            </div>
        </div>

        <!-- Financial Overview Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ number_format($financialData['totalBalance'], 2, '.', '') }}">${{ number_format($financialData['totalBalance'], 2) }}</div>
                    <div class="admin-stat-label">{{ __('Total Balance') }}</div>
                    <div class="admin-stat-description">{{ __('System total') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('All time') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-primary">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ number_format($financialData['vendorBalance'], 2, '.', '') }}">${{ number_format($financialData['vendorBalance'], 2) }}</div>
                    <div class="admin-stat-label">{{ __('Vendor Balance') }}</div>
                    <div class="admin-stat-description">{{ __('Vendor earnings') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('Vendor earnings') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ number_format($financialData['customerBalance'], 2, '.', '') }}">${{ number_format($financialData['customerBalance'], 2) }}</div>
                    <div class="admin-stat-label">{{ __('Customer Balance') }}</div>
                    <div class="admin-stat-description">{{ __('Customer deposits') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ __('Customer deposits') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-warning">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ number_format($financialData['averageBalance'], 2, '.', '') }}">${{ number_format($financialData['averageBalance'], 2) }}</div>
                    <div class="admin-stat-label">{{ __('Average Balance') }}</div>
                    <div class="admin-stat-description">{{ __('Per account') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <i class="fas fa-dot-circle"></i>
                        <span>{{ __('Per account') }}</span>
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
                                    <strong>${{ number_format($financialData['totalBalance'], 2) }}</strong>
                                </td>
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