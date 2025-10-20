@extends('layouts.admin')

@section('title', 'الرسوم البيانية البسيطة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">الرسوم البيانية البسيطة</h1>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">مبيعات الشهر</h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Distribution -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">توزيع المستخدمين</h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 250px;">
                        <canvas id="usersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">حالات الطلبات</h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 250px;">
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Scripts -->
    <script type="application/json" id="sales-chart-data">
        {
            !!json_encode([
                'labels' => ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                'values' => [120, 190, 300, 500, 200, 300],
                'title' => 'المبيعات الشهرية',
                'color' => '#007bff'
            ]) !!
        }
    </script>

    <script type="application/json" id="users-chart-data">
        {
            !!json_encode([
                'labels' => ['نشط', 'معلق', 'معطل'],
                'values' => [65, 25, 10],
                'colors' => ['#28a745', '#ffc107', '#dc3545']
            ]) !!
        }
    </script>

    <script type="application/json" id="orders-chart-data">
        {
            !!json_encode([
                'labels' => ['مكتمل', 'قيد المراجعة', 'ملغي', 'معلق'],
                'values' => [45, 30, 15, 10],
                'colors' => ['#28a745', '#007bff', '#dc3545', '#ffc107']
            ]) !!
        }
    </script>
    @endsection

    @section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/admin/js/simple-charts.js') }}"></script>
    <script>
        // Initialize charts when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Sales Chart
            const salesData = JSON.parse(document.getElementById('sales-chart-data').textContent);
            if (salesData && document.getElementById('salesChart')) {
                const ctx = document.getElementById('salesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: salesData.labels,
                        datasets: [{
                            label: salesData.title,
                            data: salesData.values,
                            borderColor: salesData.color,
                            backgroundColor: salesData.color + '20',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Users Chart
            const usersData = JSON.parse(document.getElementById('users-chart-data').textContent);
            if (usersData && document.getElementById('usersChart')) {
                const ctx = document.getElementById('usersChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: usersData.labels,
                        datasets: [{
                            data: usersData.values,
                            backgroundColor: usersData.colors,
                            borderWidth: 0
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
            }

            // Orders Chart
            const ordersData = JSON.parse(document.getElementById('orders-chart-data').textContent);
            if (ordersData && document.getElementById('ordersChart')) {
                const ctx = document.getElementById('ordersChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ordersData.labels,
                        datasets: [{
                            data: ordersData.values,
                            backgroundColor: ordersData.colors,
                            borderWidth: 0
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
            }
        });
    </script>
    @endsection