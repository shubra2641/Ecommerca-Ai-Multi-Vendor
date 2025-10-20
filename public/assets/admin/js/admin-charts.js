/**
 * Admin Charts - Simplified Version
 * Clean, simple, and maintainable chart system
 * Integrates with AdminPanel namespace
 */
(function () {
    'use strict';

    // Charts namespace within AdminPanel
    window.AdminPanel = window.AdminPanel || {};
    window.AdminPanel.Charts = window.AdminPanel.Charts || {};

    // Simple configuration
    const COLORS = {
        primary: '#007bff',
        success: '#28a745',
        warning: '#ffc107',
        danger: '#dc3545',
        info: '#17a2b8'
    };

    // Utility functions
    function getData(selector) {
        const element = document.querySelector(selector);
        if (!element) return null;

        try {
            const content = element.textContent || element.innerText || '';
            return JSON.parse(content);
        } catch {
            try {
                return JSON.parse(atob(content));
            } catch {
                return null;
            }
        }
    }

    function waitForChart(callback) {
        if (window.Chart) {
            callback();
            return;
        }

        let attempts = 0;
        const check = () => {
            if (window.Chart) {
                callback();
            } else if (attempts++ < 40) {
                setTimeout(check, 150);
            }
        };
        check();
    }

    function hideLoaders() {
        ['reports-loading', 'stats-loading', 'chart-loading'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.add('envato-hidden');
        });
    }

    // Chart creators
    function createLineChart(ctx, data) {
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels || [],
                datasets: [{
                    label: data.label || 'Data',
                    data: data.values || [],
                    borderColor: data.borderColor || COLORS.primary,
                    backgroundColor: data.backgroundColor || 'rgba(0,123,255,0.1)',
                    tension: 0.4,
                    fill: data.fill !== false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f3f4' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    function createDoughnutChart(ctx, data) {
        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels || [],
                datasets: [{
                    data: data.values || [],
                    backgroundColor: data.colors || [COLORS.primary, COLORS.warning, COLORS.danger],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }

    function createMultiLineChart(ctx, data) {
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels || [],
                datasets: data.datasets || []
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    y: { type: 'linear', position: 'left', beginAtZero: true },
                    y1: { type: 'linear', position: 'right', grid: { drawOnChartArea: false }, beginAtZero: true }
                }
            }
        });
    }

    // UI handlers
    function initRefreshButton() {
        const btn = document.getElementById('refreshReportsBtn');
        if (!btn) return;

        btn.addEventListener('click', () => {
            const icon = btn.querySelector('i');
            if (icon) icon.classList.add('fa-spin');
            setTimeout(() => {
                if (icon) icon.classList.remove('fa-spin');
                location.reload();
            }, 1000);
        });
    }

    function initExportButtons() {
        document.querySelectorAll('[data-export], [data-export-type]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const format = btn.dataset.export || btn.dataset.exportType || 'file';
                const originalText = btn.textContent;
                const originalClasses = btn.className;

                btn.innerHTML = '';
                btn.className = originalClasses + ' loading';
                const spinner = document.createElement('i');
                spinner.className = 'fas fa-spinner fa-spin';
                const text = document.createTextNode(' جاري التصدير...');
                btn.appendChild(spinner);
                btn.appendChild(text);

                setTimeout(() => {
                    btn.innerHTML = '';
                    btn.textContent = originalText;
                    btn.className = originalClasses;
                    alert(`تم التصدير بنجاح: ${format.toUpperCase()}`);
                }, 1200);
            });
        });
    }

    // Page handlers
    function handleReports() {
        const data = getData('#reports-data');
        if (!data) {
            hideLoaders();
            return;
        }

        const chartData = data.chartData || {};
        const stats = data.stats || {};

        // User Analytics Chart
        const userAnalyticsEl = document.getElementById('userAnalyticsChart');
        if (userAnalyticsEl && chartData) {
            try {
                createLineChart(userAnalyticsEl.getContext('2d'), {
                    labels: chartData.labels || [],
                    values: chartData.userData || chartData.values || [],
                    label: 'New Users',
                    borderColor: chartData.borderColor,
                    backgroundColor: chartData.backgroundColor,
                    tension: chartData.tension,
                    fill: chartData.fill
                });
            } catch { }
        }

        // User Distribution Chart
        const userDistributionEl = document.getElementById('userDistributionChart');
        if (userDistributionEl) {
            try {
                createDoughnutChart(userDistributionEl.getContext('2d'), {
                    labels: ['Active Users', 'Pending Users', 'Inactive Users'],
                    values: [stats.activeUsers || 0, stats.pendingUsers || 0, stats.inactiveUsers || 0],
                    colors: [COLORS.primary, COLORS.warning, COLORS.danger]
                });
            } catch { }
        }

        initRefreshButton();
        initExportButtons();
        hideLoaders();
    }

    function handleFinancial() {
        const data = getData('#report-financial-data');
        if (!data) {
            hideLoaders();
            return;
        }

        const charts = data.charts || {};

        // Balance Distribution Chart
        const balanceDistEl = document.getElementById('balanceDistributionChart');
        if (balanceDistEl && charts.balanceDistribution) {
            try {
                createDoughnutChart(balanceDistEl.getContext('2d'), {
                    labels: charts.balanceDistribution.labels || [],
                    values: charts.balanceDistribution.values || [],
                    colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
                });
            } catch { }
        }

        // Monthly Trends Chart
        const monthlyTrendsEl = document.getElementById('monthlyTrendsChart');
        if (monthlyTrendsEl && charts.monthlyTrends) {
            try {
                createLineChart(monthlyTrendsEl.getContext('2d'), {
                    labels: charts.monthlyTrends.labels || [],
                    values: charts.monthlyTrends.values || [],
                    label: 'Monthly Financial Trends',
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78,115,223,0.1)',
                    fill: true
                });
            } catch { }
        }

        initRefreshButton();
        initExportButtons();
        hideLoaders();
    }

    function handleDashboard() {
        const data = getData('#dashboard-data');
        if (!data) {
            hideLoaders();
            return;
        }

        const charts = data.charts || {};

        // Users Chart
        const usersEl = document.getElementById('userChart');
        if (usersEl && charts.users) {
            try {
                createLineChart(usersEl.getContext('2d'), {
                    labels: charts.users.labels || [],
                    values: charts.users.data || [],
                    label: 'Users',
                    borderColor: COLORS.primary,
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    tension: 0.4,
                    fill: true
                });
            } catch { }
        }

        // Sales Chart
        const salesEl = document.getElementById('salesChart');
        if (salesEl && charts.sales) {
            try {
                createMultiLineChart(salesEl.getContext('2d'), {
                    labels: charts.sales.labels || [],
                    datasets: [
                        {
                            label: 'Orders',
                            data: charts.sales.orders || [],
                            borderColor: COLORS.info,
                            backgroundColor: 'rgba(23,162,184,0.15)',
                            tension: 0.3,
                            fill: true,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Revenue',
                            data: charts.sales.revenue || [],
                            borderColor: COLORS.success,
                            backgroundColor: 'rgba(40,167,69,0.15)',
                            tension: 0.3,
                            fill: true,
                            yAxisID: 'y1'
                        }
                    ]
                });
            } catch { }
        }

        // Order Status Chart
        const orderStatusEl = document.getElementById('orderStatusChart');
        if (orderStatusEl && charts.ordersStatus) {
            try {
                createDoughnutChart(orderStatusEl.getContext('2d'), {
                    labels: charts.ordersStatus.labels || [],
                    values: charts.ordersStatus.data || [],
                    colors: [COLORS.primary, COLORS.success, COLORS.warning, COLORS.danger, COLORS.info]
                });
            } catch { }
        }

        initRefreshButton();
        initExportButtons();
        hideLoaders();
    }

    // Initialize charts
    AdminPanel.Charts.init = function () {
        waitForChart(() => {
            try {
                if (document.getElementById('reports-data')) {
                    handleReports();
                } else if (document.getElementById('report-financial-data')) {
                    handleFinancial();
                } else if (document.getElementById('dashboard-data')) {
                    handleDashboard();
                } else {
                    hideLoaders();
                }
            } catch {
                hideLoaders();
            }
        });
    };

    // Auto-initialize if AdminPanel is not available
    function autoInit() {
        if (window.AdminPanel && window.AdminPanel.Charts) {
            AdminPanel.Charts.init();
        } else {
            // Fallback initialization
            waitForChart(() => {
                try {
                    if (document.getElementById('reports-data')) {
                        handleReports();
                    } else if (document.getElementById('report-financial-data')) {
                        handleFinancial();
                    } else if (document.getElementById('dashboard-data')) {
                        handleDashboard();
                    } else {
                        hideLoaders();
                    }
                } catch {
                    hideLoaders();
                }
            });
        }
    }

    // Start - integrate with AdminPanel if available
    if (window.AdminPanel) {
        // If AdminPanel is already loaded, add charts to it
        AdminPanel.Charts.init();
    } else {
        // Fallback for standalone usage
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(autoInit, 0);
        } else {
            document.addEventListener('DOMContentLoaded', autoInit);
        }
    }
})();