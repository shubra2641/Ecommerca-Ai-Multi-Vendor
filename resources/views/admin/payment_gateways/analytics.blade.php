@extends('layouts.admin')

@section('title', __('Gateway Analytics'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <i class="fas fa-chart-line"></i>
                    {{ __('Gateway Analytics') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Detailed performance analytics for payment gateways') }}</p>
            </div>
            <div class="header-actions">
                <select class="admin-form-select" id="periodSelect" data-action="change-period">
                    <option value="7">{{ __('Last 7 Days') }}</option>
                    <option value="30" selected>{{ __('Last 30 Days') }}</option>
                    <option value="90">{{ __('Last 90 Days') }}</option>
                    <option value="365">{{ __('Last Year') }}</option>
                </select>
                <button type="button" class="admin-btn admin-btn-primary" data-action="export-analytics">
                    <i class="fas fa-file-export"></i>
                    {{ __('Export') }}
                </button>
                <a href="{{ route('admin.payment-gateways-management.dashboard') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        <!-- Analytics Overview -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    {{ __('Total Transactions') }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalTransactions">-</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    {{ __('Success Rate') }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="successRate">-</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-percentage fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    {{ __('Total Revenue') }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalRevenue">-</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    {{ __('Avg Transaction') }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgTransaction">-</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calculator fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Transaction Volume Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Transaction Volume') }}</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                aria-labelledby="dropdownMenuLink">
                                <div class="dropdown-header">{{ __('Chart Options') }}:</div>
                                <a class="dropdown-item" href="#" data-action="toggle-chart"
                                    data-chart="volume">{{ __('Toggle Chart Type') }}</a>
                                <a class="dropdown-item" href="#" data-action="download-chart"
                                    data-chart="volume">{{ __('Download Chart') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="volumeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gateway Comparison -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Gateway Comparison') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="gatewayChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small" id="gatewayLegend">
                            <!-- Legend will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Rate Trends -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Success Rate Trends') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="successRateChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Revenue Trends') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analytics Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Gateway Performance Details') }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="analyticsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>{{ __('Gateway') }}</th>
                                <th>{{ __('Transactions') }}</th>
                                <th>{{ __('Success Rate') }}</th>
                                <th>{{ __('Failed') }}</th>
                                <th>{{ __('Pending') }}</th>
                                <th>{{ __('Revenue') }}</th>
                                <th>{{ __('Avg Amount') }}</th>
                                <th>{{ __('Response Time') }}</th>
                            </tr>
                        </thead>
                        <tbody id="analyticsTableBody">
                            <!-- Data will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Error Analysis -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Error Analysis') }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="chart-bar">
                            <canvas id="errorChart"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="list-group" id="errorList">
                            <!-- Error list will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Analytics JavaScript code will be added here
            console.log('Analytics page loaded');
        });
    </script>
    @endsection