/**
 * Reports Initialization Script
 * Handles reports page initialization with data from Laravel controller
 */

// Initialize reports with current data
document.addEventListener('DOMContentLoaded', function () {
    // Prefer reading data from the DOM bridge element to avoid inline scripts in Blade
    let reportsData = {};
    try {
        const bridge = document.getElementById('reports-data');
        if (bridge && bridge.dataset && bridge.dataset.payload) {
            const decoded = atob(bridge.dataset.payload);
            reportsData = JSON.parse(decoded || '{}');
        } else if (window.reportsData) {
            reportsData = window.reportsData;
        }
    } catch (err) {
        console.warn('Failed to parse reports payload:', err);
        reportsData = window.reportsData || {};
    }

    const chartData = reportsData.chartData || {};
    const systemHealth = reportsData.systemHealth || {};
    const stats = reportsData.stats || {};

    // Initialize User Analytics Chart
    const userAnalyticsCtx = document.getElementById('userAnalyticsChart');
    if (userAnalyticsCtx) {
        new Chart(userAnalyticsCtx, {
            type: 'line',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: (window.__tFn ? __tFn('New Users') : 'مستخدمون جدد'),
                    data: chartData.userData || [],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
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
                        beginAtZero: true,
                        grid: {
                            color: '#f1f3f4'
                        }
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

    // Initialize User Distribution Chart
    const userDistributionCtx = document.getElementById('userDistributionChart');
    if (userDistributionCtx) {
        new Chart(userDistributionCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    (window.__tFn ? __tFn('Active Users') : 'نشط'),
                    (window.__tFn ? __tFn('Pending Users') : 'معلق'),
                    (window.__tFn ? __tFn('Inactive Users') : 'غير نشط')
                ],
                datasets: [{
                    data: [
                        stats.activeUsers || 0,
                        stats.pendingUsers || 0,
                        stats.inactiveUsers || 0
                    ],
                    backgroundColor: ['#007bff', '#ffc107', '#dc3545'],
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

    // Refresh Reports Button
    const refreshBtn = document.getElementById('refreshReportsBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function () {
            const icon = this.querySelector('i');
            icon.classList.add('fa-spin');

            // Simulate refresh
            setTimeout(() => {
                icon.classList.remove('fa-spin');
                location.reload();
            }, 1000);
        });
    }

    // Export functionality
    document.querySelectorAll('[data-export]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const format = this.dataset.export;

            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التصدير...';

            // Simulate export
            setTimeout(() => {
                this.innerHTML = originalText;
                alert(`تم تصدير التقرير بصيغة ${format.toUpperCase()} بنجاح!`);
            }, 2000);
        });
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});