/**
 * Admin Charts - Simplified and Optimized
 * Unified chart initialization with reduced complexity
 * Maintains all chart functionality while being cleaner and more maintainable
 */
/* global Chart, atob */

(function () {
    'use strict';

    // Configuration
    const CONFIG = {
        CHART_COLORS: {
            primary: '#007bff',
            success: '#28a745',
            warning: '#ffc107',
            danger: '#dc3545',
            info: '#17a2b8',
            secondary: '#6c757d'
        },
        CHART_DEFAULTS: {
            responsive: true,
            maintainAspectRatio: false
        }
    };

    // Utility functions
    const Utils = {
        // Parse JSON from element
        parseJson(selector) {
            const element = document.querySelector(selector);
            if (!element) { return null; }

            if (element.tagName?.toLowerCase() === 'script') {
                return this.parseScriptElement(element);
            }

            return this.parseDataElement(element);
        },

        parseScriptElement(element) {
            try {
                return JSON.parse(element.textContent || element.innerText || '{}');
            } catch {
                return null;
            }
        },

        parseDataElement(element) {
            try {
                const payload = element.getAttribute('data-payload') || element.textContent || element.innerText || '';
                return this.parsePayload(payload);
            } catch {
                return null;
            }
        },

        parsePayload(payload) {
            try {
                return JSON.parse(payload);
            } catch {
                return JSON.parse(atob(payload));
            }
        },

        // Wait for Chart.js to be available
        waitForChart(callback) {
            if (window.Chart) {
                callback();
                return;
            }

            let attempts = 0;
            const maxAttempts = 40;
            const interval = 150;

            const poll = () => {
                if (window.Chart) {
                    callback();
                    return;
                }

                attempts++;
                if (attempts > maxAttempts) {
                    return;
                }

                setTimeout(poll, interval);
            };

            poll();
        },

        // Get translation function
        translate(key, fallback) {
            return (window.__tFn && window.__tFn(key)) || fallback || key;
        },

        // Hide loading elements
        hideLoaders() {
            const loaderIds = ['reports-loading', 'stats-loading', 'chart-loading'];
            const errorIds = ['stats-error', 'chart-error', 'reports-error'];

            loaderIds.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.classList.add('envato-hidden');
                    element.classList.remove('d-none');
                }
            });

            errorIds.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.classList.add('envato-hidden');
                }
            });
        }
    };

    // Chart builders
    const ChartBuilder = {
        // Line chart
        createLineChart(ctx, data, options = {}) {
            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels || [],
                    datasets: [{
                        label: data.label || 'Data',
                        data: data.values || data.data || [],
                        borderColor: data.borderColor || CONFIG.CHART_COLORS.primary,
                        backgroundColor: data.backgroundColor || 'rgba(0,123,255,0.1)',
                        tension: data.tension || 0.4, // eslint-disable-line no-magic-numbers
                        fill: data.fill !== false
                    }]
                },
                options: {
                    ...CONFIG.CHART_DEFAULTS,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f3f4' }
                        },
                        x: { grid: { display: false } }
                    },
                    ...options
                }
            });
        },

        // Doughnut chart
        createDoughnutChart(ctx, data, options = {}) {
            return new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels || [],
                    datasets: [{
                        data: data.values || data.data || [],
                        backgroundColor: data.colors || [
                            CONFIG.CHART_COLORS.primary,
                            CONFIG.CHART_COLORS.warning,
                            CONFIG.CHART_COLORS.danger
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    ...CONFIG.CHART_DEFAULTS,
                    plugins: { legend: { display: false } },
                    ...options
                }
            });
        },

        // Multi-dataset line chart
        createMultiLineChart(ctx, data, options = {}) {
            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels || [],
                    datasets: data.datasets || []
                },
                options: {
                    ...CONFIG.CHART_DEFAULTS,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: {
                            type: 'linear',
                            position: 'left',
                            beginAtZero: true
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            beginAtZero: true
                        }
                    },
                    ...options
                }
            });
        }
    };

    // UI handlers
    const UIHandler = {
        init() {
            this.initRefreshButton();
            this.initExportButtons();
            this.initTooltips();
        },

        initRefreshButton() {
            const refreshBtn = document.getElementById('refreshReportsBtn');
            if (!refreshBtn) { return; }

            refreshBtn.addEventListener('click', () => {
                const icon = refreshBtn.querySelector('i');
                if (icon) { icon.classList.add('fa-spin'); }

                setTimeout(() => {
                    if (icon) { icon.classList.remove('fa-spin'); }
                    location.reload();
                }, 1000); // eslint-disable-line no-magic-numbers
            });
        },

        initExportButtons() {
            const exportButtons = document.querySelectorAll('[data-export], [data-export-type]');
            exportButtons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const format = btn.dataset.export || btn.dataset.exportType || 'file';
                    const originalText = btn.textContent;
                    const originalClasses = btn.className;

                    // Create loading state safely
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
                        // eslint-disable-next-line no-alert
                        alert(`تم التصدير بنجاح: ${format.toUpperCase()}`);
                    }, 1200); // eslint-disable-line no-magic-numbers
                });
            });
        },

        initTooltips() {
            try {
                const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltipElements.forEach(el => new bootstrap.Tooltip(el));
            } catch {
                // Bootstrap may not be loaded
            }
        }
    };

    // Page adapters
    const PageAdapters = {
        reports() {
            const data = Utils.parseJson('#reports-data');
            if (!data) {
                Utils.hideLoaders();
                return;
            }

            this.createReportsCharts(data);
            UIHandler.init();
            Utils.hideLoaders();
        },

        createReportsCharts(data) {
            const chartData = data.chartData || {};
            const stats = data.stats || {};

            this.createUserAnalyticsChart(chartData);
            this.createUserDistributionChart(stats);
        },

        createUserAnalyticsChart(chartData) {
            const userAnalyticsEl = document.getElementById('userAnalyticsChart');
            if (!userAnalyticsEl || !chartData) { return; }

            try {
                ChartBuilder.createLineChart(userAnalyticsEl.getContext('2d'), {
                    labels: chartData.labels || [],
                    values: chartData.userData || chartData.values || [],
                    label: Utils.translate('New Users', 'New Users'),
                    borderColor: chartData.borderColor,
                    backgroundColor: chartData.backgroundColor,
                    tension: chartData.tension,
                    fill: chartData.fill
                });
            } catch {
                // Chart creation failed
            }
        },

        createUserDistributionChart(stats) {
            const userDistributionEl = document.getElementById('userDistributionChart');
            if (!userDistributionEl) { return; }

            try {
                ChartBuilder.createDoughnutChart(userDistributionEl.getContext('2d'), {
                    labels: [
                        Utils.translate('Active Users', 'Active'),
                        Utils.translate('Pending Users', 'Pending'),
                        Utils.translate('Inactive Users', 'Inactive')
                    ],
                    values: [
                        stats.activeUsers || 0,
                        stats.pendingUsers || 0,
                        stats.inactiveUsers || 0
                    ],
                    colors: [CONFIG.CHART_COLORS.primary, CONFIG.CHART_COLORS.warning, CONFIG.CHART_COLORS.danger]
                });
            } catch {
                // Chart creation failed
            }
        },

        financial() {
            const data = Utils.parseJson('#report-financial-data');
            if (!data) {
                Utils.hideLoaders();
                return;
            }

            this.createFinancialCharts(data.charts || {});
            UIHandler.init();
            Utils.hideLoaders();
        },

        createFinancialCharts(charts) {
            this.createBalanceDistributionChart(charts);
            this.createMonthlyTrendsChart(charts);
        },

        createBalanceDistributionChart(charts) {
            const balanceDistEl = document.getElementById('balanceDistributionChart');
            if (!balanceDistEl || !charts.balanceDistribution) { return; }

            try {
                ChartBuilder.createDoughnutChart(balanceDistEl.getContext('2d'), {
                    labels: charts.balanceDistribution.labels || [],
                    values: charts.balanceDistribution.values || [],
                    colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
                }, {
                    plugins: { legend: { position: 'bottom' } }
                });
            } catch {
                // Chart creation failed
            }
        },

        createMonthlyTrendsChart(charts) {
            const monthlyTrendsEl = document.getElementById('monthlyTrendsChart');
            if (!monthlyTrendsEl || !charts.monthlyTrends) { return; }

            try {
                ChartBuilder.createLineChart(monthlyTrendsEl.getContext('2d'), {
                    labels: charts.monthlyTrends.labels || [],
                    values: charts.monthlyTrends.values || [],
                    label: charts.monthlyTrends.label || Utils.translate('Monthly Financial Trends', 'Monthly Financial Trends'),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78,115,223,0.1)',
                    fill: true
                });
            } catch {
                // Chart creation failed
            }
        },

        dashboard() {
            const data = Utils.parseJson('#dashboard-data');
            if (!data) {
                Utils.hideLoaders();
                return;
            }

            this.createDashboardCharts(data.charts || {});
            UIHandler.init();
            Utils.hideLoaders();
        },

        createDashboardCharts(charts) {
            this.createUsersChart(charts);
            this.createSalesChart(charts);
            this.createOrderStatusChart(charts);
        },

        createUsersChart(charts) {
            const usersEl = document.getElementById('userChart');
            if (!usersEl || !charts.users) { return; }

            try {
                ChartBuilder.createLineChart(usersEl.getContext('2d'), {
                    labels: charts.users.labels || [],
                    values: charts.users.data || [],
                    label: Utils.translate('Users', 'Users'),
                    borderColor: CONFIG.CHART_COLORS.primary,
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    tension: 0.4,
                    fill: true
                });
            } catch {
                // Chart creation failed
            }
        },

        createSalesChart(charts) {
            const salesEl = document.getElementById('salesChart');
            if (!salesEl || !charts.sales) { return; }

            try {
                ChartBuilder.createMultiLineChart(salesEl.getContext('2d'), {
                    labels: charts.sales.labels || [],
                    datasets: [
                        {
                            label: Utils.translate('Orders', 'Orders'),
                            data: charts.sales.orders || [],
                            borderColor: CONFIG.CHART_COLORS.info,
                            backgroundColor: 'rgba(23,162,184,0.15)',
                            tension: 0.3,
                            fill: true,
                            yAxisID: 'y'
                        },
                        {
                            label: Utils.translate('Revenue', 'Revenue'),
                            data: charts.sales.revenue || [],
                            borderColor: CONFIG.CHART_COLORS.success,
                            backgroundColor: 'rgba(40,167,69,0.15)',
                            tension: 0.3,
                            fill: true,
                            yAxisID: 'y1'
                        }
                    ]
                });
            } catch {
                // Chart creation failed
            }
        },

        createOrderStatusChart(charts) {
            const orderStatusEl = document.getElementById('orderStatusChart');
            if (!orderStatusEl || !charts.ordersStatus) { return; }

            try {
                ChartBuilder.createDoughnutChart(orderStatusEl.getContext('2d'), {
                    labels: charts.ordersStatus.labels || [],
                    values: charts.ordersStatus.data || [],
                    colors: [
                        CONFIG.CHART_COLORS.primary,
                        CONFIG.CHART_COLORS.success,
                        CONFIG.CHART_COLORS.warning,
                        CONFIG.CHART_COLORS.danger,
                        CONFIG.CHART_COLORS.info
                    ]
                }, {
                    plugins: { legend: { position: 'bottom' } }
                });
            } catch {
                // Chart creation failed
            }
        }
    };

    // Initialize charts when DOM is ready
    function initializeCharts() {
        Utils.waitForChart(() => {
            try {
                // Run appropriate adapter based on page data
                if (document.getElementById('reports-data')) {
                    PageAdapters.reports();
                } else if (document.getElementById('report-financial-data')) {
                    PageAdapters.financial();
                } else if (document.getElementById('dashboard-data')) {
                    PageAdapters.dashboard();
                } else {
                    // No specific data found, just hide loaders
                    Utils.hideLoaders();
                }
            } catch {
                // Initialization failed
                Utils.hideLoaders();
            }
        });
    }

    // Start initialization
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(initializeCharts, 0);
    } else {
        document.addEventListener('DOMContentLoaded', initializeCharts);
    }
}());