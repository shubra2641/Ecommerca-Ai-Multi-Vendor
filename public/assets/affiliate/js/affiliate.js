/* Affiliate JavaScript - Consolidated JS for Affiliate Panel
 * This file contains all JavaScript functionality for the affiliate interface
 * Following rules: Progressive enhancement, no inline JS, unified structure
 */

(function() {
    'use strict';

    // Affiliate namespace
    window.AffiliatePanel = window.AffiliatePanel || {};

    // Initialize affiliate functionality
    AffiliatePanel.init = function() {
        this.initDashboard();
        this.initLinkManagement();
        this.initCommissionTracking();
        this.initReferralManagement();
        this.initReports();
        this.initNotifications();
    };

    // Dashboard functionality
    AffiliatePanel.initDashboard = function() {
        this.animateStats();
        this.initCharts();
        this.loadRecentActivity();
    };

    // Animate statistics on page load
    AffiliatePanel.animateStats = function() {
        const statValues = document.querySelectorAll('.affiliate-stat-value');
        
        statValues.forEach(function(statElement) {
            const finalValue = parseInt(statElement.textContent.replace(/[^\d]/g, ''));
            if (finalValue && finalValue > 0) {
                AffiliatePanel.animateNumber(statElement, finalValue);
            }
        });
    };

    // Animate number counting
    AffiliatePanel.animateNumber = function(element, target) {
        let current = 0;
        const increment = Math.ceil(target / 60);
        const originalText = element.textContent;
        
        const timer = setInterval(function() {
            current += increment;
            if (current >= target) {
                element.textContent = originalText;
                clearInterval(timer);
            } else {
                element.textContent = current.toLocaleString();
            }
        }, 30);
    };

    // Initialize charts
    AffiliatePanel.initCharts = function() {
        const commissionChart = document.getElementById('commissionChart');
        if (commissionChart && typeof Chart !== 'undefined') {
            AffiliatePanel.createCommissionChart(commissionChart);
        }

        const clicksChart = document.getElementById('clicksChart');
        if (clicksChart && typeof Chart !== 'undefined') {
            AffiliatePanel.createClicksChart(clicksChart);
        }
    };

    // Create commission chart
    AffiliatePanel.createCommissionChart = function(canvas) {
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Commission Earned',
                    data: [120, 190, 300, 250, 420, 380],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
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
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    };

    // Link Management
    AffiliatePanel.initLinkManagement = function() {
        this.initLinkGeneration();
        this.initLinkCopy();
        this.initLinkTracking();
    };

    // Generate affiliate links
    AffiliatePanel.initLinkGeneration = function() {
        const generateForm = document.querySelector('.link-generator-form');
        if (!generateForm) return;

        generateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            AffiliatePanel.generateAffiliateLink(this);
        });
    };

    // Generate affiliate link
    AffiliatePanel.generateAffiliateLink = function(form) {
        const formData = new FormData(form);
        
        fetch('/affiliate/links/generate', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                AffiliatePanel.displayGeneratedLink(data.link);
                AffiliatePanel.showNotification('Affiliate link generated successfully!', 'success');
            } else {
                AffiliatePanel.showNotification(data.message || 'Failed to generate link', 'error');
            }
        })
        .catch(function(error) {
            AffiliatePanel.showNotification('Error: ' + error.message, 'error');
        });
    };

    // Display generated link
    AffiliatePanel.displayGeneratedLink = function(linkData) {
        const linkContainer = document.querySelector('.generated-link-container');
        if (linkContainer) {
            linkContainer.innerHTML = `
                <div class="affiliate-link-item">
                    <div class="affiliate-link-info">
                        <div class="affiliate-link-url">${linkData.url}</div>
                        <div class="affiliate-link-stats">
                            <span class="affiliate-link-stat">Clicks: <strong>0</strong></span>
                            <span class="affiliate-link-stat">Conversions: <strong>0</strong></span>
                            <span class="affiliate-link-stat">Commission: <strong>$0.00</strong></span>
                        </div>
                    </div>
                    <div class="affiliate-link-actions">
                        <button class="copy-btn" data-copy="${linkData.url}">Copy</button>
                        <button class="affiliate-btn affiliate-btn-sm affiliate-btn-secondary" data-share="${linkData.url}">Share</button>
                    </div>
                </div>
            `;
            linkContainer.style.display = 'block';
        }
    };

    // Copy link functionality
    AffiliatePanel.initLinkCopy = function() {
        document.addEventListener('click', function(e) {
            if (e.target.matches('.copy-btn')) {
                const textToCopy = e.target.dataset.copy;
                AffiliatePanel.copyToClipboard(textToCopy, e.target);
            }
        });
    };

    // Copy to clipboard
    AffiliatePanel.copyToClipboard = function(text, button) {
        navigator.clipboard.writeText(text).then(function() {
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            button.classList.add('copied');
            
            setTimeout(function() {
                button.textContent = originalText;
                button.classList.remove('copied');
            }, 2000);
            
            AffiliatePanel.showNotification('Link copied to clipboard!', 'success');
        }).catch(function(err) {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            AffiliatePanel.showNotification('Link copied to clipboard!', 'success');
        });
    };

    // Link tracking and analytics
    AffiliatePanel.initLinkTracking = function() {
        this.refreshLinkStats();
        
        // Auto-refresh stats every 30 seconds
        setInterval(function() {
            AffiliatePanel.refreshLinkStats();
        }, 30000);
    };

    // Refresh link statistics
    AffiliatePanel.refreshLinkStats = function() {
        const linkItems = document.querySelectorAll('.affiliate-link-item');
        
        linkItems.forEach(function(item) {
            const linkUrl = item.querySelector('.affiliate-link-url').textContent;
            AffiliatePanel.fetchLinkStats(linkUrl, item);
        });
    };

    // Fetch link statistics
    AffiliatePanel.fetchLinkStats = function(linkUrl, itemElement) {
        fetch('/affiliate/links/stats', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ url: linkUrl })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            AffiliatePanel.updateLinkStats(itemElement, data);
        })
        .catch(function(error) {
            console.error('Failed to fetch link stats:', error);
        });
    };

    // Update link statistics in UI
    AffiliatePanel.updateLinkStats = function(itemElement, stats) {
        const statsContainer = itemElement.querySelector('.affiliate-link-stats');
        if (statsContainer) {
            statsContainer.innerHTML = `
                <span class="affiliate-link-stat">Clicks: <strong>${stats.clicks}</strong></span>
                <span class="affiliate-link-stat">Conversions: <strong>${stats.conversions}</strong></span>
                <span class="affiliate-link-stat">Commission: <strong>$${stats.commission.toFixed(2)}</strong></span>
            `;
        }
    };

    // Commission Tracking
    AffiliatePanel.initCommissionTracking = function() {
        this.initCommissionFilters();
        this.initPayoutRequests();
        this.loadCommissionHistory();
    };

    // Commission filters
    AffiliatePanel.initCommissionFilters = function() {
        const filterForm = document.querySelector('.commission-filter-form');
        if (!filterForm) return;

        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            AffiliatePanel.filterCommissions(this);
        });

        const statusFilter = filterForm.querySelector('select[name="status"]');
        if (statusFilter) {
            statusFilter.addEventListener('change', function() {
                AffiliatePanel.filterCommissionsByStatus(this.value);
            });
        }
    };

    // Filter commissions by status
    AffiliatePanel.filterCommissionsByStatus = function(status) {
        const rows = document.querySelectorAll('.commission-table tbody tr');
        
        rows.forEach(function(row) {
            const rowStatus = row.querySelector('.commission-status').textContent.trim();
            const shouldShow = !status || rowStatus.toLowerCase() === status.toLowerCase();
            row.style.display = shouldShow ? '' : 'none';
        });
    };

    // Payout requests
    AffiliatePanel.initPayoutRequests = function() {
        const payoutBtn = document.querySelector('.request-payout-btn');
        if (payoutBtn) {
            payoutBtn.addEventListener('click', function() {
                AffiliatePanel.requestPayout();
            });
        }
    };

    // Request payout
    AffiliatePanel.requestPayout = function() {
        const availableBalance = document.querySelector('.available-balance');
        const balance = availableBalance ? parseFloat(availableBalance.textContent.replace(/[^\d.]/g, '')) : 0;
        
        if (balance < 50) { // Minimum payout threshold
            AffiliatePanel.showNotification('Minimum payout amount is $50', 'warning');
            return;
        }

        if (confirm(`Request payout of $${balance.toFixed(2)}?`)) {
            fetch('/affiliate/payout/request', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ amount: balance })
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    AffiliatePanel.showNotification('Payout request submitted successfully!', 'success');
                    location.reload();
                } else {
                    AffiliatePanel.showNotification(data.message || 'Failed to submit payout request', 'error');
                }
            })
            .catch(function(error) {
                AffiliatePanel.showNotification('Error: ' + error.message, 'error');
            });
        }
    };

    // Referral Management
    AffiliatePanel.initReferralManagement = function() {
        this.initReferralInvites();
        this.initReferralTracking();
    };

    // Referral invites
    AffiliatePanel.initReferralInvites = function() {
        const inviteForm = document.querySelector('.referral-invite-form');
        if (!inviteForm) return;

        inviteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            AffiliatePanel.sendReferralInvite(this);
        });
    };

    // Send referral invite
    AffiliatePanel.sendReferralInvite = function(form) {
        const formData = new FormData(form);
        
        fetch('/affiliate/referral/invite', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                AffiliatePanel.showNotification('Referral invite sent successfully!', 'success');
                form.reset();
            } else {
                AffiliatePanel.showNotification(data.message || 'Failed to send invite', 'error');
            }
        })
        .catch(function(error) {
            AffiliatePanel.showNotification('Error: ' + error.message, 'error');
        });
    };

    // Reports functionality
    AffiliatePanel.initReports = function() {
        const exportBtn = document.querySelector('.export-report-btn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                AffiliatePanel.exportReport();
            });
        }

        const reportForm = document.querySelector('.report-form');
        if (reportForm) {
            reportForm.addEventListener('submit', function(e) {
                e.preventDefault();
                AffiliatePanel.generateReport(this);
            });
        }
    };

    // Generate report
    AffiliatePanel.generateReport = function(form) {
        const formData = new FormData(form);
        
        fetch('/affiliate/reports/generate', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                AffiliatePanel.displayReport(data.report);
            } else {
                AffiliatePanel.showNotification(data.message || 'Failed to generate report', 'error');
            }
        })
        .catch(function(error) {
            AffiliatePanel.showNotification('Error: ' + error.message, 'error');
        });
    };

    // Display report
    AffiliatePanel.displayReport = function(reportData) {
        const reportContainer = document.querySelector('.report-container');
        if (reportContainer) {
            reportContainer.innerHTML = reportData.html;
            reportContainer.style.display = 'block';
        }
    };

    // Export report
    AffiliatePanel.exportReport = function() {
        const reportData = AffiliatePanel.getReportData();
        
        fetch('/affiliate/reports/export', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(reportData)
        })
        .then(function(response) {
            return response.blob();
        })
        .then(function(blob) {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'affiliate-report-' + new Date().getTime() + '.csv';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            AffiliatePanel.showNotification('Report exported successfully!', 'success');
        })
        .catch(function(error) {
            AffiliatePanel.showNotification('Error exporting report: ' + error.message, 'error');
        });
    };

    // Get report data
    AffiliatePanel.getReportData = function() {
        return {
            dateRange: document.querySelector('[name="date_range"]')?.value || 'last_30_days',
            includeCommissions: true,
            includeClicks: true,
            includeConversions: true
        };
    };

    // Notification system
    AffiliatePanel.initNotifications = function() {
        // Auto-hide notifications
        const notifications = document.querySelectorAll('.affiliate-notification');
        notifications.forEach(function(notification) {
            if (notification.hasAttribute('data-auto-hide')) {
                setTimeout(function() {
                    AffiliatePanel.hideNotification(notification);
                }, 5000);
            }
        });

        // Close notification buttons
        document.addEventListener('click', function(e) {
            if (e.target.matches('.notification-close')) {
                const notification = e.target.closest('.affiliate-notification');
                AffiliatePanel.hideNotification(notification);
            }
        });
    };

    // Show notification
    AffiliatePanel.showNotification = function(message, type) {
        type = type || 'info';
        
        const notification = document.createElement('div');
        notification.className = 'affiliate-notification affiliate-notification-' + type;
        notification.innerHTML = `
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        `;
        
        const container = document.querySelector('.notification-container') || document.body;
        container.appendChild(notification);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            AffiliatePanel.hideNotification(notification);
        }, 5000);
    };

    // Hide notification
    AffiliatePanel.hideNotification = function(notification) {
        if (notification) {
            notification.style.opacity = '0';
            setTimeout(function() {
                notification.remove();
            }, 300);
        }
    };

    // Load recent activity
    AffiliatePanel.loadRecentActivity = function() {
        fetch('/affiliate/activity/recent', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            AffiliatePanel.displayRecentActivity(data.activities);
        })
        .catch(function(error) {
            console.error('Failed to load recent activity:', error);
        });
    };

    // Display recent activity
    AffiliatePanel.displayRecentActivity = function(activities) {
        const activityContainer = document.querySelector('.recent-activity-container');
        if (!activityContainer || !activities) return;

        const activityHTML = activities.map(function(activity) {
            return `
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-${activity.icon}"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-message">${activity.message}</div>
                        <div class="activity-time">${activity.time_ago}</div>
                    </div>
                    <div class="activity-amount">${activity.amount}</div>
                </div>
            `;
        }).join('');

        activityContainer.innerHTML = activityHTML;
    };

    // Initialize when DOM is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', AffiliatePanel.init);
    } else {
        AffiliatePanel.init();
    }

})();