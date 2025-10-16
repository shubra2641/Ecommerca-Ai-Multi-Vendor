/*
 * Vendor Dashboard JavaScript
 * Professional interactive features for vendor dashboard
 */

(function() {
    'use strict';

    // ==========================================================================
    // Configuration
    // ==========================================================================

    const CONFIG = {
        api: {
            baseUrl: '/vendor/api',
            endpoints: {
                stats: '/stats',
                notifications: '/notifications',
                activities: '/activities'
            }
        },
        animation: {
            duration: 300,
            easing: 'ease-in-out'
        },
        refresh: {
            statsInterval: 30000, // 30 seconds
            notificationsInterval: 60000 // 1 minute
        },
        chart: {
            colors: {
                primary: '#3b82f6',
                success: '#10b981',
                warning: '#f59e0b',
                danger: '#ef4444',
                info: '#06b6d4'
            }
        }
    };

    // ==========================================================================
    // Utility Functions
    // ==========================================================================

    const Utils = {
        // Format currency
        formatCurrency: function(amount, currency = 'USD') {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: currency
            }).format(amount);
        },

        // Format numbers with commas
        formatNumber: function(number) {
            return new Intl.NumberFormat().format(number);
        },

        // Format dates
        formatDate: function(date, options = {}) {
            const defaultOptions = {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            return new Intl.DateTimeFormat('en-US', { ...defaultOptions, ...options }).format(new Date(date));
        },

        // Debounce function
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        // Throttle function
        throttle: function(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        // Show notification
        showNotification: function(message, type = 'info', duration = 5000) {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <span class="notification-message">${message}</span>
                    <button class="notification-close" aria-label="Close notification">&times;</button>
                </div>
            `;

            document.body.appendChild(notification);

            // Auto remove
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, duration);

            // Manual close
            notification.querySelector('.notification-close').addEventListener('click', () => {
                notification.remove();
            });
        },

        // Create loading spinner
        createSpinner: function(size = 'medium') {
            const spinner = document.createElement('div');
            spinner.className = `spinner spinner-${size}`;
            spinner.innerHTML = '<div class="spinner-circle"></div>';
            return spinner;
        },

        // Animate counter
        animateCounter: function(element, start, end, duration = 2000) {
            const startTime = performance.now();
            const range = end - start;

            function updateCounter(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const current = Math.floor(start + (range * progress));
                
                element.textContent = Utils.formatNumber(current);
                
                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                }
            }
            
            requestAnimationFrame(updateCounter);
        },

        // Get CSRF token
        getCSRFToken: function() {
            const token = document.querySelector('meta[name="csrf-token"]');
            return token ? token.getAttribute('content') : null;
        }
    };

    // ==========================================================================
    // API Service
    // ==========================================================================

    const API = {
        // Make HTTP request
        request: async function(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': Utils.getCSRFToken()
                }
            };

            try {
                const response = await fetch(CONFIG.api.baseUrl + url, {
                    ...defaultOptions,
                    ...options,
                    headers: { ...defaultOptions.headers, ...options.headers }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                return await response.json();
            } catch (error) {
                console.error('API request failed:', error);
                throw error;
            }
        },

        // Get dashboard stats
        getStats: function() {
            return this.request(CONFIG.api.endpoints.stats);
        },

        // Get notifications
        getNotifications: function() {
            return this.request(CONFIG.api.endpoints.notifications);
        },

        // Get recent activities
        getActivities: function() {
            return this.request(CONFIG.api.endpoints.activities);
        }
    };

    // ==========================================================================
    // Dashboard Manager
    // ==========================================================================

    class DashboardManager {
        constructor() {
            this.statsRefreshInterval = null;
            this.notificationsRefreshInterval = null;
            this.init();
        }

        init() {
            this.setupEventListeners();
            this.initializeStats();
            this.initializeCharts();
                // Auto-start removed by policy. Manual refresh remains available.
            this.initializeAnimations();
        }

        setupEventListeners() {
            // Sidebar toggle for mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('vendorSidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('open');
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 768 && sidebar && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            });

            // Refresh button
            const refreshBtn = document.querySelector('[data-action="refresh"]');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', () => {
                    this.refreshStats();
                });
            }

            // Quick action buttons
            document.querySelectorAll('.action-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    this.handleQuickAction(e);
                });
            });
        }

        initializeStats() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                // Add entrance animation delay
                card.style.animationDelay = `${index * 100}ms`;
                card.classList.add('fade-in');

                // Animate counter values
                const valueElement = card.querySelector('.stat-value');
                if (valueElement) {
                    const finalValue = parseInt(valueElement.textContent.replace(/[^0-9]/g, '')) || 0;
                    Utils.animateCounter(valueElement, 0, finalValue, 1500 + (index * 200));
                }
            });
        }

        initializeCharts() {
            // Initialize any charts if Chart.js is available
            if (typeof Chart !== 'undefined') {
                this.initSalesChart();
                this.initOrdersChart();
            }
        }

        initSalesChart() {
            const ctx = document.getElementById('salesChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Sales',
                        data: [12, 19, 3, 5, 2, 3],
                        borderColor: CONFIG.chart.colors.primary,
                        backgroundColor: CONFIG.chart.colors.primary + '20',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        initOrdersChart() {
            const ctx = document.getElementById('ordersChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Pending', 'Cancelled'],
                    datasets: [{
                        data: [65, 25, 10],
                        backgroundColor: [
                            CONFIG.chart.colors.success,
                            CONFIG.chart.colors.warning,
                            CONFIG.chart.colors.danger
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        async refreshStats() {
            try {
                const refreshBtn = document.querySelector('[data-action="refresh"]');
                if (refreshBtn) {
                    refreshBtn.disabled = true;
                    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
                }

                const stats = await API.getStats();
                this.updateStatsDisplay(stats);

                Utils.showNotification('Stats updated successfully', 'success');
            } catch (error) {
                Utils.showNotification('Failed to refresh stats', 'danger');
            } finally {
                const refreshBtn = document.querySelector('[data-action="refresh"]');
                if (refreshBtn) {
                    refreshBtn.disabled = false;
                    refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                }
            }
        }

        updateStatsDisplay(stats) {
            // Update stat cards with new data
            Object.keys(stats).forEach(key => {
                const element = document.querySelector(`[data-stat="${key}"]`);
                if (element) {
                    const currentValue = parseInt(element.textContent.replace(/[^0-9]/g, '')) || 0;
                    const newValue = stats[key];
                    Utils.animateCounter(element, currentValue, newValue, 1000);
                }
            });
        }

        handleQuickAction(e) {
            const action = e.currentTarget.dataset.action;
            
            switch (action) {
                case 'add-product':
                    window.location.href = '/vendor/products/create';
                    break;
                case 'view-orders':
                    window.location.href = '/vendor/orders';
                    break;
                case 'manage-withdrawals':
                    window.location.href = '/vendor/withdrawals';
                    break;
                default:
                    console.log('Unknown action:', action);
            }
        }

        startAutoRefresh() {
            // Automatic periodic refresh disabled by policy. Call refreshStats()/refreshNotifications() manually when needed.
                if (this.statsRefreshInterval) { clearInterval(this.statsRefreshInterval); this.statsRefreshInterval = null; }
                if (this.notificationsRefreshInterval) { clearInterval(this.notificationsRefreshInterval); this.notificationsRefreshInterval = null; }
        }

        async refreshNotifications() {
            try {
                const notifications = await API.getNotifications();
                this.updateNotificationsDisplay(notifications);
            } catch (error) {
                console.error('Failed to refresh notifications:', error);
            }
        }

        updateNotificationsDisplay(notifications) {
            const container = document.querySelector('.notifications-container');
            if (!container) return;

            container.innerHTML = notifications.map(notification => `
                <div class="notification-item ${notification.read ? 'read' : 'unread'}">
                    <div class="notification-icon">
                        <i class="${notification.icon}"></i>
                    </div>
                    <div class="notification-content">
                        <p class="notification-text">${notification.message}</p>
                        <span class="notification-time">${Utils.formatDate(notification.created_at)}</span>
                    </div>
                </div>
            `).join('');
        }

        initializeAnimations() {
            // Intersection Observer for scroll animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, {
                threshold: 0.1
            });

            // Observe elements for animation
            document.querySelectorAll('.stat-card, .quick-actions, .recent-activity').forEach(el => {
                observer.observe(el);
            });
        }

        destroy() {
            // Clean up intervals
            if (this.statsRefreshInterval) {
                clearInterval(this.statsRefreshInterval);
            }
            if (this.notificationsRefreshInterval) {
                clearInterval(this.notificationsRefreshInterval);
            }
        }
    }

    // ==========================================================================
    // Theme Manager
    // ==========================================================================

    class ThemeManager {
        constructor() {
            this.currentTheme = localStorage.getItem('vendor-theme') || 'light';
            this.init();
        }

        init() {
            this.applyTheme(this.currentTheme);
            this.setupThemeToggle();
        }

        setupThemeToggle() {
            const themeToggle = document.querySelector('[data-action="toggle-theme"]');
            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    this.toggleTheme();
                });
            }
        }

        toggleTheme() {
            const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
            this.applyTheme(newTheme);
        }

        applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            this.currentTheme = theme;
            localStorage.setItem('vendor-theme', theme);

            // Update theme toggle button
            const themeToggle = document.querySelector('[data-action="toggle-theme"]');
            if (themeToggle) {
                const icon = themeToggle.querySelector('i');
                if (icon) {
                    icon.className = theme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
                }
            }
        }
    }

    // ==========================================================================
    // Notification Styles (Injected CSS)
    // ==========================================================================

    const notificationStyles = `
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 1060;
            max-width: 400px;
            animation: slideInRight 0.3s ease-out;
        }
        
        .notification-info {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            color: #1e40af;
        }
        
        .notification-success {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            color: #065f46;
        }
        
        .notification-warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            color: #92400e;
        }
        
        .notification-danger {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }
        
        .notification-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .notification-close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            margin-left: 12px;
            opacity: 0.7;
        }
        
        .notification-close:hover {
            opacity: 1;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .spinner {
            display: inline-block;
        }
        
        .spinner-circle {
            width: 20px;
            height: 20px;
            border: 2px solid #e5e7eb;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .spinner-small .spinner-circle {
            width: 16px;
            height: 16px;
        }
        
        .spinner-large .spinner-circle {
            width: 32px;
            height: 32px;
        }
        
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;

    // Inject notification styles
    const styleSheet = document.createElement('style');
    styleSheet.textContent = notificationStyles;
    document.head.appendChild(styleSheet);

    // ==========================================================================
    // Initialize Dashboard
    // ==========================================================================

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeDashboard);
    } else {
        initializeDashboard();
    }

    function initializeDashboard() {
        // Initialize dashboard manager
        window.vendorDashboard = new DashboardManager();
        
        // Initialize theme manager
        window.vendorTheme = new ThemeManager();
        
        // Global error handler
        window.addEventListener('error', (e) => {
            console.error('Dashboard error:', e.error);
            Utils.showNotification('An error occurred. Please refresh the page.', 'danger');
        });
        
        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                // Page is hidden, pause auto-refresh
                if (window.vendorDashboard) {
                    window.vendorDashboard.destroy();
                }
            } else {
                // Page is visible, resume auto-refresh
                if (window.vendorDashboard) {
                    window.vendorDashboard.startAutoRefresh();
                }
            }
        });
        
        console.log('Vendor Dashboard initialized successfully');
    }

    // Export for global access
    window.VendorDashboard = {
        Utils,
        API,
        DashboardManager,
        ThemeManager,
        CONFIG
    };

})();