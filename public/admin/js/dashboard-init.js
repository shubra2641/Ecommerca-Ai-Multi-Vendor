/**
 * Dashboard Initialization Script
 * Handles dashboard initialization with data from Laravel controller
 */

// Initialize dashboard with current data
document.addEventListener('DOMContentLoaded', function () {
    // Get data from global variables set by Laravel
    const chartData = window.dashboardChartData || [];
    const systemHealth = window.dashboardSystemHealth || [];

    // Initialize user registration chart
    if (chartData && Object.keys(chartData).length > 0) {
        window.dashboardFunctions.initializeChart(chartData);
    }

    // Set up refresh button
    const refreshBtn = document.getElementById('refreshDashboardBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function () {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';
            this.disabled = true;

            // Refresh dashboard data
            window.dashboardFunctions.refreshDashboard();

            // Reset button after 2 seconds
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-sync-alt"></i> تحديث';
                this.disabled = false;
            }, 2000);
        });
    }

    // Set up auto-refresh toggle
    const autoRefreshToggle = document.getElementById('autoRefreshToggle');
    if (autoRefreshToggle) {
        autoRefreshToggle.addEventListener('change', function () {
            if (this.checked) {
                window.dashboardFunctions.setupAutoRefresh();
                console.log('Auto-refresh enabled');
            } else {
                window.dashboardFunctions.clearAutoRefresh();
                console.log('Auto-refresh disabled');
            }
        });
    }

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add last updated timestamp
    const dashboardHeader = document.querySelector('.dashboard-header');
    if (dashboardHeader) {
        const lastUpdatedElement = document.createElement('div');
        lastUpdatedElement.id = 'last-updated';
        lastUpdatedElement.textContent = 'آخر تحديث: ' + new Date().toLocaleString('ar-SA');
        dashboardHeader.appendChild(lastUpdatedElement);
    }
});