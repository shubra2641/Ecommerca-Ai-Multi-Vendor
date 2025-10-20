@extends('layouts.admin')

@section('title', __('Financial Report'))

@section('content')
<div class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="admin-order-title">
                <h1>
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 1v6m0 0l3-3m-3 3l-3-3m6 9v6m0 0l3 3m-3-3l3 3M5 12H1m0 0l3 3m-3-3l3-3m18 0h-4m0 0l3 3m-3-3l3-3" />
                    </svg>
                    {{ __('Financial Report') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Financial analysis and balance statistics') }}</p>
            </div>
            <div class="header-actions">
                <button type="button" class="admin-btn admin-btn-secondary js-refresh-page" data-action="refresh">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M23 4v6h-6M1 20v-6h6m15-4a9 9 0 11-18 0 9 9 0 0118 0zM1 10a9 9 0 0118 0" />
                    </svg>
                    {{ __('Refresh') }}
                </button>
                <div class="dropdown">
                    <button class="admin-btn admin-btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{ __('Export') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item js-export" href="#" data-export-type="excel" data-report="financial">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ __('Excel') }}
                            </a></li>
                        <li><a class="dropdown-item js-export" href="#" data-export-type="pdf" data-report="financial">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14,2 14,8 20,8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10,9 9,9 8,9" />
                                </svg>
                                {{ __('PDF') }}
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Financial Overview Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card admin-stat-success">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 1v6m0 0l3-3m-3 3l-3-3m6 9v6m0 0l3 3m-3-3l3 3M5 12H1m0 0l3 3m-3-3l3-3m18 0h-4m0 0l3 3m-3-3l3-3" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ number_format($financialData['totalBalance'], 2, '.', '') }}">${{ number_format($financialData['totalBalance'], 2) }}</div>
                    <div class="admin-stat-label">{{ __('Total Balance') }}</div>
                    <div class="admin-stat-description">{{ __('System total') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                            <path d="M17 9l-5 5-3-3-3 3" />
                        </svg>
                        <span>{{ __('All time') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-primary">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ number_format($financialData['vendorBalance'], 2, '.', '') }}">${{ number_format($financialData['vendorBalance'], 2) }}</div>
                    <div class="admin-stat-label">{{ __('Vendor Balance') }}</div>
                    <div class="admin-stat-description">{{ __('Vendor earnings') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                            <path d="M17 9l-5 5-3-3-3 3" />
                        </svg>
                        <span>{{ __('Vendor earnings') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-info">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ number_format($financialData['customerBalance'], 2, '.', '') }}">${{ number_format($financialData['customerBalance'], 2) }}</div>
                    <div class="admin-stat-label">{{ __('Customer Balance') }}</div>
                    <div class="admin-stat-description">{{ __('Customer deposits') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M7 14l3-3 3 3 5-5" />
                            <path d="M17 9l-5 5-3-3-3 3" />
                        </svg>
                        <span>{{ __('Customer deposits') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card admin-stat-warning">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 3v18h18M7 12l3-3 3 3 5-5" />
                        </svg>
                    </div>
                    <div class="admin-stat-badge">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup="{{ number_format($financialData['averageBalance'], 2, '.', '') }}">${{ number_format($financialData['averageBalance'], 2) }}</div>
                    <div class="admin-stat-label">{{ __('Average Balance') }}</div>
                    <div class="admin-stat-description">{{ __('Per account') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
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