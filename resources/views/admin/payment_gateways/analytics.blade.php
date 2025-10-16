@extends('layouts.admin')

@section('title', __('Gateway Analytics'))

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ __('Gateway Analytics') }}</h1>
            <p class="mb-0 text-muted">{{ __('Detailed performance analytics for payment gateways') }}</p>
        </div>
        <div class="btn-group">
            <select class="form-control" id="periodSelect" data-action="change-period">
                <option value="7">{{ __('Last 7 Days') }}</option>
                <option value="30" selected>{{ __('Last 30 Days') }}</option>
                <option value="90">{{ __('Last 90 Days') }}</option>
                <option value="365">{{ __('Last Year') }}</option>
            </select>
            <button type="button" class="btn btn-primary" data-action="export-analytics">
                <i class="fas fa-download"></i> {{ __('Export') }}
            </button>
            <a href="{{ route('admin.payment-gateways-management.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
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
    document.addEventListener('DOMContentLoaded', function() {
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
@endsection

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.querySelector('[data-action="change-period"]');
    const exportBtn = document.querySelector('[data-action="export-analytics"]');
    document.addEventListener('click', function(e) {
        const t = e.target.closest('[data-action]');
        if (!t) return;
        const act = t.getAttribute('data-action');
        if (act === 'toggle-chart') {
            e.preventDefault();
            toggleChartType(t.getAttribute('data-chart'));
        }
        if (act === 'download-chart') {
            e.preventDefault();
            downloadChart(t.getAttribute('data-chart'));
        }
    });
    if (periodSelect) {
        periodSelect.addEventListener('change', updatePeriod);
    }
    if (exportBtn) {
        exportBtn.addEventListener('click', exportAnalytics);
    }
    loadAnalyticsData();
    initializeCharts();
    if (window.jQuery) {
        jQuery('#analyticsTable').DataTable({
            pageLength: 10,
            ordering: true,
            searching: false,
            info: false,
            paging: false
        });
    }
});
let currentPeriod = 30;
let charts = {};

// Initialize charts when page loads
$(document).ready(function() {
    loadAnalyticsData();
    initializeCharts();
});

function updatePeriod() {
    currentPeriod = $('#periodSelect').val();
    loadAnalyticsData();
}

function loadAnalyticsData() {
    // Show loading state
    showLoadingState();

    // This would typically make an AJAX call to get analytics data
    // For demo purposes, we'll use mock data
    setTimeout(() => {
        const mockData = generateMockData();
        updateAnalyticsDisplay(mockData);
        updateCharts(mockData);
    }, 1000);
}

function generateMockData() {
    return {
        overview: {
            total_transactions: Math.floor(Math.random() * 10000) + 5000,
            success_rate: (Math.random() * 20 + 80).toFixed(1),
            total_revenue: (Math.random() * 100000 + 50000).toFixed(2),
            avg_transaction: (Math.random() * 200 + 50).toFixed(2)
        },
        daily_stats: generateDailyStats(),
        gateway_comparison: [{
                name: 'PayMob',
                transactions: 1250,
                revenue: 25000
            },
            {
                name: 'Fawry',
                transactions: 980,
                revenue: 19600
            },
            {
                name: 'PayTabs',
                transactions: 750,
                revenue: 15000
            },
            {
                name: 'Kashier',
                transactions: 620,
                revenue: 12400
            }
        ],
        gateway_details: [{
                name: 'PayMob',
                transactions: 1250,
                success_rate: 94.5,
                failed: 69,
                pending: 25,
                revenue: 25000,
                avg_amount: 20.00,
                response_time: 245
            },
            {
                name: 'Fawry',
                transactions: 980,
                success_rate: 91.2,
                failed: 86,
                pending: 12,
                revenue: 19600,
                avg_amount: 20.00,
                response_time: 312
            }
        ],
        errors: [{
                type: 'Network Timeout',
                count: 45
            },
            {
                type: 'Invalid Credentials',
                count: 23
            },
            {
                type: 'Insufficient Funds',
                count: 67
            },
            {
                type: 'Card Declined',
                count: 89
            }
        ]
    };
}

function generateDailyStats() {
    const stats = [];
    for (let i = currentPeriod; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        stats.push({
            date: date.toISOString().split('T')[0],
            transactions: Math.floor(Math.random() * 200) + 50,
            revenue: Math.floor(Math.random() * 5000) + 1000,
            success_rate: Math.random() * 10 + 85
        });
    }
    return stats;
}

function updateAnalyticsDisplay(data) {
    $('#totalTransactions').text(data.overview.total_transactions.toLocaleString());
    $('#successRate').text(data.overview.success_rate + '%');
    $('#totalRevenue').text('$' + parseFloat(data.overview.total_revenue).toLocaleString());
    $('#avgTransaction').text('$' + data.overview.avg_transaction);

    // Update analytics table
    updateAnalyticsTable(data.gateway_details);

    // Update error list
    updateErrorList(data.errors);
}

function updateAnalyticsTable(details) {
    const tbody = $('#analyticsTableBody');
    tbody.empty();

    details.forEach(gateway => {
        const row = `
            <tr>
                <td>${gateway.name}</td>
                <td>${gateway.transactions.toLocaleString()}</td>
                <td>
                    <div class="progress progress-sm">
                        <div class="progress-bar ${gateway.success_rate >= 95 ? 'bg-success' : (gateway.success_rate >= 85 ? 'bg-warning' : 'bg-danger') }" data-progress="${gateway.success_rate}">
                            ${gateway.success_rate}%
                        </div>
                    </div>
                </td>
                <td><span class="badge badge-danger">${gateway.failed}</span></td>
                <td><span class="badge badge-warning">${gateway.pending}</span></td>
                <td>$${gateway.revenue.toLocaleString()}</td>
                <td>$${gateway.avg_amount}</td>
                <td>${gateway.response_time}ms</td>
            </tr>
        `;
        tbody.append(row);
    });
}

function updateErrorList(errors) {
    const errorList = $('#errorList');
    errorList.empty();

    errors.forEach(error => {
        const item = `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                ${error.type}
                <span class="badge badge-danger badge-pill">${error.count}</span>
            </div>
        `;
        errorList.append(item);
    });
}

function initializeCharts() {
    // Volume Chart
    const volumeCtx = document.getElementById('volumeChart').getContext('2d');
    charts.volume = new Chart(volumeCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Transactions',
                data: [],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gateway Comparison Chart
    const gatewayCtx = document.getElementById('gatewayChart').getContext('2d');
    charts.gateway = new Chart(gatewayCtx, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Success Rate Chart
    const successCtx = document.getElementById('successRateChart').getContext('2d');
    charts.successRate = new Chart(successCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Success Rate (%)',
                data: [],
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    charts.revenue = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Revenue ($)',
                data: [],
                backgroundColor: '#36b9cc'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Error Chart
    const errorCtx = document.getElementById('errorChart').getContext('2d');
    charts.error = new Chart(errorCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Error Count',
                data: [],
                backgroundColor: '#e74a3b'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
}

function updateCharts(data) {
    // Update volume chart
    charts.volume.data.labels = data.daily_stats.map(stat => stat.date);
    charts.volume.data.datasets[0].data = data.daily_stats.map(stat => stat.transactions);
    charts.volume.update();

    // Update gateway comparison chart
    charts.gateway.data.labels = data.gateway_comparison.map(g => g.name);
    charts.gateway.data.datasets[0].data = data.gateway_comparison.map(g => g.transactions);
    charts.gateway.update();

    // Update success rate chart
    charts.successRate.data.labels = data.daily_stats.map(stat => stat.date);
    charts.successRate.data.datasets[0].data = data.daily_stats.map(stat => stat.success_rate);
    charts.successRate.update();

    // Update revenue chart
    charts.revenue.data.labels = data.daily_stats.map(stat => stat.date);
    charts.revenue.data.datasets[0].data = data.daily_stats.map(stat => stat.revenue);
    charts.revenue.update();

    // Update error chart
    charts.error.data.labels = data.errors.map(error => error.type);
    charts.error.data.datasets[0].data = data.errors.map(error => error.count);
    charts.error.update();
}

function showLoadingState() {
    $('#totalTransactions').text('...');
    $('#successRate').text('...');
    $('#totalRevenue').text('...');
    $('#avgTransaction').text('...');
}

function exportAnalytics() {
    toastr.info('{{ __('
        Exporting analytics data...') }}');
    // Implementation for exporting analytics
}

function toggleChartType(chartName) {
    if (charts[chartName]) {
        const currentType = charts[chartName].config.type;
        const newType = currentType === 'line' ? 'bar' : 'line';

        charts[chartName].config.type = newType;
        charts[chartName].update();
    }
}

function downloadChart(chartName) {
    if (charts[chartName]) {
        const url = charts[chartName].toBase64Image();
        const link = document.createElement('a');
        link.download = `${chartName}-chart.png`;
        link.href = url;
        link.click();
    }
}

// DataTable initialization moved to DOMContentLoaded without inline handlers
</script>
@endpush
 