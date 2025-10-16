/**
 * Reports Page JavaScript
 * Handles charts, data visualization, and interactive features
 */

// Chart instances
let userRegistrationChart = null;
let vendorActivityChart = null;

// Auto refresh settings
let autoRefreshInterval = null;
let liveUpdatesInterval = null;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    initializeCharts();
    initializeEventListeners();
    initializeDataTables();
    loadInitialData();
});

/**
 * Initialize Chart.js charts
 */
function initializeCharts()
{
    // User Registration Trends Chart
    const userRegCtx = document.getElementById('userRegistrationChart');
    if (userRegCtx) {
        userRegistrationChart = new Chart(userRegCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'New Users',
                    data: [],
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
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
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });
    }

    // Vendor Activity Pie Chart
    const vendorActCtx = document.getElementById('vendorActivityChart');
    if (vendorActCtx) {
        vendorActivityChart = new Chart(vendorActCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Pending', 'Inactive'],
                datasets: [{
                    data: [0, 0, 0],
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc'
                    ],
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
                },
                cutout: '60%'
            }
        });
    }
}

/**
 * Initialize event listeners
 */
function initializeEventListeners()
{
    // Chart period selectors
    const userRegPeriod = document.getElementById('userRegistrationPeriod');
    if (userRegPeriod) {
        userRegPeriod.addEventListener('change', function () {
            updateUserRegistrationChart(this.value);
        });
    }

    const vendorActPeriod = document.getElementById('vendorActivityPeriod');
    if (vendorActPeriod) {
        vendorActPeriod.addEventListener('change', function () {
            updateVendorActivityChart(this.value);
        });
    }

    // Auto refresh toggle
    const autoRefreshToggle = document.getElementById('autoRefreshReports');
    if (autoRefreshToggle) {
        autoRefreshToggle.addEventListener('change', function () {
            if (this.checked) {
                startAutoRefresh();
            } else {
                stopAutoRefresh();
            }
        });
    }

    // Live updates toggle
    const liveUpdatesToggle = document.getElementById('liveActivityUpdates');
    if (liveUpdatesToggle) {
        liveUpdatesToggle.addEventListener('change', function () {
            if (this.checked) {
                startLiveUpdates();
            } else {
                stopLiveUpdates();
            }
        });
    }

    // Refresh button
    const refreshBtn = document.getElementById('refreshReportsBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function () {
            refreshAllData();
        });
    }

    // Export buttons
    document.querySelectorAll('[data-export]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const format = this.getAttribute('data-export');
            exportReport(format);
        });
    });

    // Activity table actions
    document.querySelectorAll('[data-action]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const action = this.getAttribute('data-action');
            const id = this.getAttribute('data-id');
            handleActivityAction(action, id);
        });
    });
}

/**
 * Initialize DataTables
 */
function initializeDataTables()
{
    const activityTable = document.getElementById('recentActivityTable');
    if (activityTable && typeof $.fn.DataTable !== 'undefined') {
        $(activityTable).DataTable({
            responsive: true,
            pageLength: 10,
            order: [[3, 'desc']], // Sort by date column
            columnDefs: [
                { orderable: false, targets: [5] } // Disable sorting on actions column
            ],
            language: {
                search: 'Search activities:',
                lengthMenu: 'Show _MENU_ activities per page',
                info: 'Showing _START_ to _END_ of _TOTAL_ activities',
                paginate: {
                    first: 'First',
                    last: 'Last',
                    next: 'Next',
                    previous: 'Previous'
                }
            }
        });
    }
}

/**
 * Load initial data
 */
function loadInitialData()
{
    updateUserRegistrationChart(30);
    updateVendorActivityChart(30);
    updateStatistics();
}

/**
 * Update user registration chart
 */
function updateUserRegistrationChart(days)
{
    if (!userRegistrationChart) {
        return;
    }

    // Show loading state
    showChartLoading('userRegistrationChart');

    // Use dummy data for demo
    const dummyData = generateDummyUserData(days);
    userRegistrationChart.data.labels = dummyData.labels;
    userRegistrationChart.data.datasets[0].data = dummyData.data;
    userRegistrationChart.update();

    hideChartLoading('userRegistrationChart');
}

/**
 * Update vendor activity chart
 */
function updateVendorActivityChart(days)
{
    if (!vendorActivityChart) {
        return;
    }

    // Show loading state
    showChartLoading('vendorActivityChart');

    // Use dummy data for demo
    const dummyData = [65, 25, 10];
    vendorActivityChart.data.datasets[0].data = dummyData;
    vendorActivityChart.update();

    hideChartLoading('vendorActivityChart');
}

/**
 * Update statistics cards
 */
function updateStatistics()
{
    // Use dummy data for demo
    updateStatCard('totalUsers', 1247);
    updateStatCard('totalVendors', 89);
    updateStatCard('pendingUsers', 23);
    updateStatCard('totalBalance', 125430.50);
}

/**
 * Update individual stat card
 */
function updateStatCard(statName, value)
{
    const element = document.querySelector(`[data - stat = "${statName}"]`);
    if (element) {
        // Animate number change
        animateNumber(element, parseInt(element.textContent.replace(/[^0-9.]/g, '')), value);
    }
}

/**
 * Animate number changes
 */
function animateNumber(element, start, end)
{
    const duration = 1000;
    const startTime = performance.now();

    function update(currentTime)
    {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);

        const current = start + (end - start) * progress;

        if (element.dataset.stat === 'totalBalance') {
            element.textContent = '$' + current.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }

    requestAnimationFrame(update);
}

/**
 * Start auto refresh
 */
function startAutoRefresh()
{
    // Auto-refresh scheduling removed by policy. Call refreshAllData() manually when needed.
    if (typeof window.ADMIN_AUTO_REFRESH !== 'undefined' && !window.ADMIN_AUTO_REFRESH) {
        return;
    }
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }
}

/**
 * Stop auto refresh
 */
function stopAutoRefresh()
{
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }

    showNotification('Auto refresh disabled', 'info');
}

/**
 * Start live updates
 */
function startLiveUpdates()
{
    // Live updates scheduling removed by policy. Call updateRecentActivity() manually when needed.
    if (typeof window.ADMIN_AUTO_REFRESH !== 'undefined' && !window.ADMIN_AUTO_REFRESH) {
        return;
    }
    if (liveUpdatesInterval) {
        clearInterval(liveUpdatesInterval);
        liveUpdatesInterval = null;
    }
}

/**
 * Stop live updates
 */
function stopLiveUpdates()
{
    if (liveUpdatesInterval) {
        clearInterval(liveUpdatesInterval);
        liveUpdatesInterval = null;
    }

    showNotification('Live updates disabled', 'info');
}

/**
 * Refresh all data
 */
function refreshAllData(showToast = false)
{
    const refreshBtn = document.getElementById('refreshReportsBtn');
    if (refreshBtn) {
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
        refreshBtn.disabled = true;
    }

    setTimeout(() => {
        updateStatistics();
        updateUserRegistrationChart(document.getElementById('userRegistrationPeriod') ? .value || 30);
        updateVendorActivityChart(document.getElementById('vendorActivityPeriod') ? .value || 30);
        updateRecentActivity();

        if (refreshBtn) {
            refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh Data';
            refreshBtn.disabled = false;
        }
        if (showToast) {
            showNotification('Data refreshed successfully', 'success');
        }
    }, 1000);
}

/**
 * Update recent activity table
 */
function updateRecentActivity()
{
    console.log('Updating recent activity...');
}

/**
 * Handle activity actions
 */
function handleActivityAction(action, id)
{
    switch (action) {
        case 'view-details':
            viewActivityDetails(id);
            break;
        case 'approve':
            approveActivity(id);
            break;
        case 'reject':
            rejectActivity(id);
            break;
        case 'view-all-activity':
            window.location.href = '/admin/activity-log';
            break;
    }
}

/**
 * Export report
 */
function exportReport(format)
{
    showNotification(`Preparing ${format.toUpperCase()} export...`, 'info');

    setTimeout(() => {
        showNotification(`${format.toUpperCase()} report exported successfully`, 'success');
    }, 2000);
}

/**
 * Show chart loading state
 */
function showChartLoading(chartId)
{
    const canvas = document.getElementById(chartId);
    if (canvas) {
        canvas.style.opacity = '0.5';
    }
}

/**
 * Hide chart loading state
 */
function hideChartLoading(chartId)
{
    const canvas = document.getElementById(chartId);
    if (canvas) {
        canvas.style.opacity = '1';
    }
}

/**
 * Generate dummy user registration data
 */
function generateDummyUserData(days)
{
    const labels = [];
    const data = [];
    const now = new Date();

    for (let i = days - 1; i >= 0; i--) {
        const date = new Date(now);
        date.setDate(date.getDate() - i);
        labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
        data.push(Math.floor(Math.random() * 50) + 10);
    }

    return { labels, data };
}

/**
 * Show notification
 */
function showNotification(message, type = 'info')
{
    const notification = document.createElement('div');
    notification.className = `alert alert - ${type} alert - dismissible fade show position - fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        < button type = "button" class = "btn-close" data - bs - dismiss = "alert" > < / button >
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

/**
 * View activity details (placeholder)
 */
function viewActivityDetails(id)
{
    showNotification('Activity details feature coming soon', 'info');
}

/**
 * Approve activity (placeholder)
 */
function approveActivity(id)
{
    showNotification('Activity approved successfully', 'success');
}

/**
 * Reject activity (placeholder)
 */
function rejectActivity(id)
{
    showNotification('Activity rejected', 'warning');
}

// Cleanup on page unload
window.addEventListener('beforeunload', function () {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
    if (liveUpdatesInterval) {
        clearInterval(liveUpdatesInterval);
    }
});