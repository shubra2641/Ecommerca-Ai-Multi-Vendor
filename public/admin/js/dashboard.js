/**
 * Dashboard JavaScript Functions
 * Handles dashboard interactions, data refresh, and chart management
 */

// Global dashboard functions namespace
window.dashboardFunctions = {
    autoRefreshInterval: null,
    charts: {},
    _scriptPromises: {},
    t: function (key, fallback) {
        if (typeof __tFn === 'function') {
            try {
                return __tFn(key) || fallback || key; } catch (e) {
                return fallback || key; } }
        return fallback || key;
    },

    /**
     * Initialize dashboard
     */
    init: function () {
        // Check if Chart.js is loaded and initialize charts
        if (typeof Chart !== 'undefined') {
            this.initializeCharts();
        } else {
            // Wait for Chart.js to load if it's still loading
            const checkChart = () => {
                if (typeof Chart !== 'undefined') {
                    this.initializeCharts();
                } else {
                    setTimeout(checkChart, 100);
                }
            };
            setTimeout(checkChart, 100);
        }
        this.bindEvents();
        this.updateLastRefreshTime();
    },

    /**
     * Defer chart initialization until Chart global becomes available
     */
    deferChartInit: function () {
 /* no-op: CDN fallback removed */ },
    loadScriptOnce: function (src, id) {
        if (id && document.getElementById(id) && typeof Chart !== 'undefined') {
            return Promise.resolve();
        }
        if (this._scriptPromises[src]) {
            return this._scriptPromises[src];
        }
        this._scriptPromises[src] = new Promise((resolve, reject) => {
            const s = document.createElement('script');
            if (id) {
                s.id = id;
            }
            s.src = src;
            s.async = true;
            s.onload = () => resolve();
            s.onerror = () => reject(new Error('Failed to load ' + src));
            document.head.appendChild(s);
        });
        return this._scriptPromises[src];
    },

    /**
     * Initialize chart with data from Laravel
     */
    initializeChart: function (chartData) {
        const ctx = document.getElementById('userChart');
        if (ctx && chartData) {
            // Destroy existing chart if it exists
            if (this.charts.userChart) {
                this.charts.userChart.destroy();
            }

            const isDark = document.body.classList.contains('dark-mode');
            const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.1)';
            const textColor = isDark ? '#e2e8f0' : '#334155';
            const primary = isDark ? '#6366f1' : '#3b82f6';
            const vendorC = '#10b981';
            const adminC = isDark ? '#fbbf24' : '#f59e0b';
            this.charts.userChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels || [],
                    datasets: [{
                        label: this.t('dashboard.chart.total_users','Total Users'),
                        data: chartData.data || [],
                        borderColor: primary,
                        backgroundColor: (isDark ? 'rgba(99,102,241,0.15)' : 'rgba(59,130,246,0.1)'),
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: this.t('dashboard.chart.vendors','Vendors'),
                        data: chartData.vendorData || [],
                        borderColor: vendorC,
                        backgroundColor: 'rgba(16,185,129,0.12)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4
                    }, {
                        label: this.t('dashboard.chart.admins','Admins'),
                        data: chartData.adminData || [],
                        borderColor: adminC,
                        backgroundColor: 'rgba(245,158,11,0.12)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { color: textColor }
                        },
                        title: {
                            display: true,
                            text: this.t('dashboard.chart.title','User Registrations Over Time'),
                            color: textColor
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: gridColor
                            },
                            ticks: { color: textColor }
                        },
                        x: {
                            grid: { color: gridColor },
                            ticks: { color: textColor }
                        }
                    }
                }
            });
        }
    },

    /**
     * Refresh dashboard data
     */
    refreshDashboard: function (showToast = false) {
        // Show loading state
        this.showLoadingState();
        const csrf = document.querySelector('meta[name="csrf-token"]');
        fetch('/admin/dashboard/refresh', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf ? csrf.getAttribute('content') : ''
            },
            body: JSON.stringify({})
        })
        .then(async resp => {
            let json = null; try {
                json = await resp.json(); } catch (e) {
                }
                if (!resp.ok) {
                    throw new Error(json && (json.message || json.error) || ('HTTP ' + resp.status)); }
                return json;
        })
        .then(data => {
            if (data && data.success) {
                if (data.data) {
                    this.updateDashboardData(data.data); // may be missing; guard
                }
                this.updateLastRefreshTime();
                if (showToast) {
                    this.showSuccessMessage(this.t('dashboard.refresh.success','Data refreshed successfully'));
                }
            } else {
                    this.showErrorMessage(this.t('dashboard.refresh.error','Error refreshing data'));
            }
        })
        .catch(err => {
            // Suppress noisy error if triggered very early before backend ready
            console.error('Dashboard refresh error:', err);
                this.showErrorMessage(this.t('dashboard.refresh.error','Error refreshing data'));
        })
        .finally(() => { this.hideLoadingState(); });
    },

    /**
     * Update dashboard data with fresh information
     */
    updateDashboardData: function (data) {
        if (!data) {
            return; // guard against undefined
        }
        // Update statistics cards
        if (data.stats) {
            Object.keys(data.stats).forEach(key => {
                const element = document.querySelector(`[data - stat = "${key}"]`);
                if (element) {
                    element.textContent = data.stats[key];
                }
            });
        }

        // Update charts
        if (data.charts) {
            this.updateCharts(data.charts);
        }

        // Update recent activities
        if (data.activities) {
            this.updateRecentActivities(data.activities);
        }
    },

    /**
     * Initialize charts
     */
    initializeCharts: function () {
        // Initialize user registration chart if data is available
        if (window.dashboardChartData) {
            this.initializeChart(window.dashboardChartData);
        }

        // Initialize sales chart
        if (window.dashboardSalesChartData) {
            this.initializeSalesChart(window.dashboardSalesChartData);
        }

        // Order status distribution
        if (window.dashboardStats && window.dashboardStats.ordersStatusCounts) {
            this.initializeOrderStatusChart(window.dashboardStats.ordersStatusCounts);
        }
    },

    /**
     * Update charts with new data
     */
    updateCharts: function (chartData) {
        if (chartData && this.charts.userChart) {
            this.charts.userChart.data.labels = chartData.labels || [];
            this.charts.userChart.data.datasets[0].data = chartData.data || [];
            this.charts.userChart.data.datasets[1].data = chartData.vendorData || [];
            this.charts.userChart.data.datasets[2].data = chartData.adminData || [];
            this.charts.userChart.update();
        }
    },

    /**
     * Initialize sales (orders + revenue) combo chart
     */
    initializeSalesChart: function (data) {
        const ctx = document.getElementById('salesChart');
        if (!ctx) {
            return;
        }
        if (this.charts.salesChart) {
            this.charts.salesChart.destroy();
        }
        const isDark = document.body.classList.contains('dark-mode');
        const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.1)';
        const textColor = isDark ? '#e2e8f0' : '#334155';
        this.charts.salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels || [],
                datasets: [
                    {
                        type: 'bar',
                        label: (window.__tFn ? __tFn('Total Orders') : 'Orders'),
                        data: data.orders || [],
                        backgroundColor: 'rgba(59,130,246,0.6)',
                        borderRadius: 4,
                        maxBarThickness: 18
                },
                    {
                        type: 'line',
                        label: (window.__tFn ? __tFn('Revenue') : 'Revenue'),
                        data: data.revenue || [],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.15)',
                        tension: 0.35,
                        yAxisID: 'y1'
                }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { labels: { color: textColor } },
                    tooltip: { callbacks: { label: (ctx) => ctx.dataset.label + ': ' + ctx.formattedValue } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor } },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { color: textColor } },
                    x: { grid: { color: gridColor }, ticks: { color: textColor } }
                }
            }
        });
    },

    /**
     * Initialize order status distribution chart
     */
    initializeOrderStatusChart: function (statusCounts) {
        const ctx = document.getElementById('orderStatusChart');
        if (!ctx) {
            return;
        }
        if (this.charts.orderStatusChart) {
            this.charts.orderStatusChart.destroy();
        }
        const labels = Object.keys(statusCounts);
        const data = Object.values(statusCounts);
        const palette = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1', '#14b8a6', '#8b5cf6'];
        this.charts.orderStatusChart = new Chart(ctx, {
            type: 'doughnut',
            data: { labels, datasets: [{ data, backgroundColor: labels.map((_, i) => palette[i % palette.length]), borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    },

    /**
     * Update recent activities list
     */
    updateRecentActivities: function (activities) {
        const container = document.querySelector('.recent-activities-list');
        if (container && activities.length > 0) {
            container.innerHTML = activities.map(activity => `
                < div class = "activity-item" >
                    < div class = "activity-icon bg-${activity.type}" >
                        < i class = "${activity.icon}" > < / i >
                    <  / div >
                    < div class = "activity-content" >
                        < div class = "activity-title" > ${activity.title} < / div >
                        < div class = "activity-description" > ${activity.description} < / div >
                        < div class = "activity-time" > ${activity.time} < / div >
                    <  / div >
                <  / div >
            `).join('');
        }
    },

    /**
     * Setup auto-refresh functionality
     */
    setupAutoRefresh: function () {
    // Automatic periodic refresh removed by policy.
    // To refresh manually, call dashboardFunctions.refreshDashboard().
        if (typeof window.ADMIN_AUTO_REFRESH !== 'undefined' && !window.ADMIN_AUTO_REFRESH) {
            return;
        }
        this.clearAutoRefresh();
    // Intentionally do not schedule recurring setInterval here.
    },

    /**
     * Clear auto-refresh interval
     */
    clearAutoRefresh: function () {
        if (this.autoRefreshInterval) {
            clearInterval(this.autoRefreshInterval);
            this.autoRefreshInterval = null;
        }
    },

    /**
     * Show loading state
     */
    showLoadingState: function () {
        const loadingElements = document.querySelectorAll('.loading-overlay');
        loadingElements.forEach(el => el.style.display = 'flex');
    },

    /**
     * Hide loading state
     */
    hideLoadingState: function () {
        const loadingElements = document.querySelectorAll('.loading-overlay');
        loadingElements.forEach(el => el.style.display = 'none');
    },

    /**
     * Update last refresh time
     */
    updateLastRefreshTime: function () {
        const lastUpdatedElement = document.getElementById('last-updated');
        if (lastUpdatedElement) {
                lastUpdatedElement.textContent = this.t('dashboard.last_updated_prefix','Last updated:') + ' ' + new Date().toLocaleString();
        }
    },

    /**
     * Show success message
     */
    showSuccessMessage: function (message) {
        this.showToast(message || this.t('common.success','نجاح'), 'success');
    },

    /**
     * Show error message
     */
    showErrorMessage: function (message) {
        this.showToast(message || this.t('common.error','خطأ'), 'error');
    },

    /**
     * Show toast notification
     */
    showToast: function (message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast align - items - center text - white bg - ${type === 'success' ? 'success' : 'danger'} border - 0`;
        toast.setAttribute('role', 'alert');
        const fallback = (type === 'success') ? this.t('common.success','Success') : (type === 'error' ? this.t('common.error','Error') : this.t('common.notice','Notice'));
        toast.innerHTML = `
            < div class = "d-flex" >
                < div class = "toast-body" >
                    ${message || fallback}
                <  / div >
                < button type = "button" class = "btn-close btn-close-white me-2 m-auto" data - bs - dismiss = "toast" > < / button >
            <  / div >
        `;

        // Add to toast container
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            // Place toast based on document direction to avoid overlapping RTL side menus
            const isRtl = (document.documentElement && document.documentElement.getAttribute('dir') === 'rtl') || document.body.classList.contains('rtl');
            const sideClass = isRtl ? 'start-0' : 'end-0';
            toastContainer.className = `toast - container position - fixed top - 0 ${sideClass} p - 3`;
            document.body.appendChild(toastContainer);
        }

        toastContainer.appendChild(toast);

        // Show toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    },

    /**
     * Bind event listeners
     */
    bindEvents: function () {
        // Chart period buttons
        document.querySelectorAll('.chart-period-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();

                // Remove active class from all buttons
                document.querySelectorAll('.chart-period-btn').forEach(b => b.classList.remove('active'));

                // Add active class to clicked button
                btn.classList.add('active');

                // Update chart based on period
                const period = btn.dataset.period;
                this.updateChartPeriod(period);
            });
        });

        // Stats card click handlers
        document.querySelectorAll('.stats-card').forEach(card => {
            card.addEventListener('click', function () {
                const link = this.querySelector('.stats-link');
                if (link) {
                    window.location.href = link.getAttribute('href');
                }
            });
        });
    },

    /**
     * Update chart based on selected period
     */
    updateChartPeriod: function (period) {
    // Fetch data for the selected period
        fetch(` / admin / dashboard / chart - data ? period = ${period}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.chartData) {
                    this.updateCharts(data.chartData);
                }
            })
            .catch(error => {
                console.error('Error updating chart:', error);
            });
    }
};

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    window.dashboardFunctions.init();
});

// Re-render charts on theme change to adapt colors instantly
document.addEventListener('admin:theme-changed', function () {
    if (window.dashboardChartData && window.dashboardFunctions && typeof window.dashboardFunctions.initializeChart === 'function') {
        window.dashboardFunctions.initializeChart(window.dashboardChartData);
    }
});