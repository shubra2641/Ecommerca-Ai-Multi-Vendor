/**
 * Simple Charts - Basic Chart System
 * Ultra-simple, safe, and working chart implementation
 */
(function () {
    'use strict';

    // Simple chart configuration
    const SIMPLE_COLORS = {
        blue: '#007bff',
        green: '#28a745',
        orange: '#ffc107',
        red: '#dc3545',
        purple: '#6f42c1'
    };

    // Wait for Chart.js to load
    function waitForChart(callback) {
        if (window.Chart) {
            callback();
            return;
        }

        let attempts = 0;
        const check = () => {
            if (window.Chart) {
                callback();
            } else if (attempts++ < 20) {
                setTimeout(check, 200);
            }
        };
        check();
    }

    // Hide loading indicators
    function hideLoaders() {
        const loaders = document.querySelectorAll('.chart-loading, .loading');
        loaders.forEach(loader => {
            if (loader) loader.style.display = 'none';
        });
    }

    // Create simple line chart
    function createLineChart(canvasId, data) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        const ctx = canvas.getContext('2d');
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels || [],
                datasets: [{
                    label: data.title || 'Data',
                    data: data.values || [],
                    borderColor: data.color || SIMPLE_COLORS.blue,
                    backgroundColor: data.color + '20' || SIMPLE_COLORS.blue + '20',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Create simple pie chart
    function createPieChart(canvasId, data) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        const ctx = canvas.getContext('2d');
        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels || [],
                datasets: [{
                    data: data.values || [],
                    backgroundColor: data.colors || [
                        SIMPLE_COLORS.blue,
                        SIMPLE_COLORS.green,
                        SIMPLE_COLORS.orange,
                        SIMPLE_COLORS.red,
                        SIMPLE_COLORS.purple
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // Get data from HTML element
    function getChartData(elementId) {
        const element = document.getElementById(elementId);
        if (!element) return null;
        
        try {
            // Check for base64 encoded data (reports page)
            if (element.dataset.payload) {
                const decoded = atob(element.dataset.payload);
                return JSON.parse(decoded);
            }
            
            // Check for regular JSON data
            const text = element.textContent || element.innerText || '';
            if (text.trim()) {
                return JSON.parse(text);
            }
            
            return null;
        } catch {
            return null;
        }
    }

    // Initialize all charts
    function initCharts() {
        hideLoaders();

        // Sales Chart
        const salesData = getChartData('sales-chart-data');
        if (salesData && document.getElementById('salesChart')) {
            createLineChart('salesChart', salesData);
        }

        // Users Chart
        const usersData = getChartData('users-chart-data');
        if (usersData && document.getElementById('usersChart')) {
            createPieChart('usersChart', usersData);
        }

        // Orders Chart
        const ordersData = getChartData('orders-chart-data');
        if (ordersData && document.getElementById('ordersChart')) {
            createPieChart('ordersChart', ordersData);
        }

        // Dashboard Charts
        const dashboardData = getChartData('dashboard-chart-data');
        if (dashboardData) {
            if (dashboardData.sales && document.getElementById('salesChart')) {
                createLineChart('salesChart', dashboardData.sales);
            }
            if (dashboardData.users && document.getElementById('usersChart')) {
                createPieChart('usersChart', dashboardData.users);
            }
        }

        // Reports Charts
        const reportsData = getChartData('reports-data');
        if (reportsData) {
            if (reportsData.userAnalytics && document.getElementById('userAnalyticsChart')) {
                createLineChart('userAnalyticsChart', reportsData.userAnalytics);
            }
            if (reportsData.userDistribution && document.getElementById('userDistributionChart')) {
                createPieChart('userDistributionChart', reportsData.userDistribution);
            }
        }

        // Financial Charts
        const financialData = getChartData('report-financial-data');
        if (financialData) {
            if (financialData.balanceDistribution && document.getElementById('balanceDistributionChart')) {
                createPieChart('balanceDistributionChart', financialData.balanceDistribution);
            }
            if (financialData.monthlyTrends && document.getElementById('monthlyTrendsChart')) {
                createLineChart('monthlyTrendsChart', financialData.monthlyTrends);
            }
        }
    }

    // Start when page is ready
    function start() {
        waitForChart(initCharts);
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }

})();
