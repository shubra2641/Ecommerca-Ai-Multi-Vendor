/**
 * Super Simple Charts - Ultra Basic
 * Just works, no complexity
 */
(function () {
    'use strict';

    // Wait for Chart.js
    function waitForChart() {
        if (window.Chart) {
            initCharts();
        } else {
            setTimeout(waitForChart, 100);
        }
    }

    // Simple line chart
    function makeLineChart(canvasId, labels, data, color) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        new window.Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    borderColor: color || '#007bff',
                    backgroundColor: (color || '#007bff') + '20',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Simple pie chart
    function makePieChart(canvasId, labels, data, colors) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        new window.Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors || ['#007bff', '#28a745', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }

    // Get data from element
    function getData(id) {
        const el = document.getElementById(id);
        if (!el) {return null;}

        try {
            if (el.dataset.payload) {
                return JSON.parse(atob(el.dataset.payload));
            }
            return JSON.parse(el.textContent || '{}');
        } catch {
            return null;
        }
    }

    // Main function
    function initCharts() {
        // Dashboard charts
        const dashboard = getData('dashboard-data');
        if (dashboard && dashboard.charts) {
            if (dashboard.charts.users) {
                makeLineChart('userChart',
                    dashboard.charts.users.labels || [],
                    dashboard.charts.users.data || [],
                    '#007bff'
                );
            }

            if (dashboard.charts.sales) {
                const salesCanvas = document.getElementById('salesChart');
                if (salesCanvas) {
                    new window.Chart(salesCanvas, {
                        type: 'line',
                        data: {
                            labels: dashboard.charts.sales.labels || [],
                            datasets: [
                                {
                                    label: 'Orders',
                                    data: dashboard.charts.sales.orders || [],
                                    borderColor: '#17a2b8',
                                    backgroundColor: 'rgba(23,162,184,0.1)',
                                    tension: 0.3,
                                    fill: true
                                },
                                {
                                    label: 'Revenue',
                                    data: dashboard.charts.sales.revenue || [],
                                    borderColor: '#28a745',
                                    backgroundColor: 'rgba(40,167,69,0.1)',
                                    tension: 0.3,
                                    fill: true
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: true } },
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                }
            }

            if (dashboard.charts.ordersStatus) {
                makePieChart('orderStatusChart',
                    dashboard.charts.ordersStatus.labels || [],
                    dashboard.charts.ordersStatus.data || [],
                    dashboard.charts.ordersStatus.colors
                );
            }
        }

        // Reports charts
        const reports = getData('reports-data');
        if (reports) {
            if (reports.chartData) {
                makeLineChart('userAnalyticsChart',
                    reports.chartData.labels || [],
                    reports.chartData.userData || [],
                    '#007bff'
                );
            }

            if (reports.stats) {
                makePieChart('userDistributionChart',
                    ['Active', 'Pending', 'Inactive'],
                    [
                        reports.stats.activeUsers || 0,
                        reports.stats.pendingUsers || 0,
                        reports.stats.inactiveUsers || 0
                    ],
                    ['#28a745', '#ffc107', '#dc3545']
                );
            }
        }
    }

    // Start
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', waitForChart);
    } else {
        waitForChart();
    }

})();
