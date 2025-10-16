/* Merchant JavaScript - Consolidated JS for Merchant Panel
 * This file contains all JavaScript functionality for the merchant interface
 * Following rules: Progressive enhancement, no inline JS, unified structure
 */

(function() {
    'use strict';

    // Merchant namespace
    window.MerchantPanel = window.MerchantPanel || {};

    // Initialize merchant functionality
    MerchantPanel.init = function() {
        this.initDashboard();
        this.initProductManagement();
        this.initOrderManagement();
        this.initReports();
        this.initNotifications();
        this.initForms();
    };

    // Dashboard functionality
    MerchantPanel.initDashboard = function() {
        this.initStatCards();
        this.initCharts();
        this.loadRecentActivity();
    };

    // Statistics cards with animations
    MerchantPanel.initStatCards = function() {
        const statCards = document.querySelectorAll('.merchant-card');
        
        // Animate numbers on load
        statCards.forEach(function(card) {
            const statValue = card.querySelector('.merchant-stat-value');
            if (statValue && statValue.dataset.value) {
                MerchantPanel.animateNumber(statValue, parseInt(statValue.dataset.value));
            }
        });
    };

    // Animate number counting
    MerchantPanel.animateNumber = function(element, target) {
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(function() {
            current += increment;
            if (current >= target) {
                element.textContent = target.toLocaleString();
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current).toLocaleString();
            }
        }, 20);
    };

    // Initialize charts
    MerchantPanel.initCharts = function() {
        const salesChart = document.getElementById('salesChart');
        if (salesChart && typeof Chart !== 'undefined') {
            MerchantPanel.createSalesChart(salesChart);
        }

        const ordersChart = document.getElementById('ordersChart');
        if (ordersChart && typeof Chart !== 'undefined') {
            MerchantPanel.createOrdersChart(ordersChart);
        }
    };

    // Create sales chart
    MerchantPanel.createSalesChart = function(canvas) {
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Sales',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
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
    };

    // Product Management
    MerchantPanel.initProductManagement = function() {
        this.initProductFilters();
        this.initProductActions();
        this.initImageUpload();
    };

    // Product filters
    MerchantPanel.initProductFilters = function() {
        const filterForm = document.querySelector('.product-filters');
        if (!filterForm) return;

        const inputs = filterForm.querySelectorAll('input, select');
        inputs.forEach(function(input) {
            input.addEventListener('change', function() {
                MerchantPanel.filterProducts();
            });
        });
    };

    // Filter products
    MerchantPanel.filterProducts = function() {
        const form = document.querySelector('.product-filters');
        const formData = new FormData(form);
        const products = document.querySelectorAll('.merchant-product-card');

        products.forEach(function(product) {
            let shouldShow = true;

            // Apply filters
            for (let [key, value] of formData.entries()) {
                if (value && !MerchantPanel.productMatchesFilter(product, key, value)) {
                    shouldShow = false;
                    break;
                }
            }

            product.style.display = shouldShow ? 'block' : 'none';
        });
    };

    // Check if product matches filter
    MerchantPanel.productMatchesFilter = function(product, filterKey, filterValue) {
        const productData = product.dataset[filterKey];
        if (!productData) return true;

        switch (filterKey) {
            case 'category':
                return productData === filterValue;
            case 'priceRange':
                const price = parseFloat(productData);
                const [min, max] = filterValue.split('-').map(parseFloat);
                return price >= min && (isNaN(max) || price <= max);
            default:
                return productData.toLowerCase().includes(filterValue.toLowerCase());
        }
    };

    // Product actions (edit, delete, etc.)
    MerchantPanel.initProductActions = function() {
        document.addEventListener('click', function(e) {
            if (e.target.matches('.product-edit-btn')) {
                e.preventDefault();
                const productId = e.target.dataset.productId;
                MerchantPanel.editProduct(productId);
            }

            if (e.target.matches('.product-delete-btn')) {
                e.preventDefault();
                const productId = e.target.dataset.productId;
                MerchantPanel.deleteProduct(productId);
            }

            if (e.target.matches('.product-duplicate-btn')) {
                e.preventDefault();
                const productId = e.target.dataset.productId;
                MerchantPanel.duplicateProduct(productId);
            }
        });
    };

    // Edit product
    MerchantPanel.editProduct = function(productId) {
        window.location.href = '/merchant/products/' + productId + '/edit';
    };

    // Delete product
    MerchantPanel.deleteProduct = function(productId) {
        if (confirm('Are you sure you want to delete this product?')) {
            fetch('/merchant/products/' + productId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(function(response) {
                if (response.ok) {
                    MerchantPanel.showNotification('Product deleted successfully', 'success');
                    location.reload();
                } else {
                    MerchantPanel.showNotification('Failed to delete product', 'error');
                }
            })
            .catch(function(error) {
                MerchantPanel.showNotification('Error: ' + error.message, 'error');
            });
        }
    };

    // Order Management
    MerchantPanel.initOrderManagement = function() {
        this.initOrderFilters();
        this.initOrderActions();
        this.initBulkActions();
    };

    // Order filters
    MerchantPanel.initOrderFilters = function() {
        const statusFilter = document.querySelector('.order-status-filter');
        if (statusFilter) {
            statusFilter.addEventListener('change', function() {
                MerchantPanel.filterOrders(this.value);
            });
        }

        const dateFilter = document.querySelector('.order-date-filter');
        if (dateFilter) {
            dateFilter.addEventListener('change', function() {
                MerchantPanel.filterOrdersByDate(this.value);
            });
        }
    };

    // Filter orders by status
    MerchantPanel.filterOrders = function(status) {
        const orders = document.querySelectorAll('.merchant-orders-table tbody tr');
        
        orders.forEach(function(order) {
            const orderStatus = order.querySelector('.merchant-order-status').textContent.trim();
            const shouldShow = !status || orderStatus.toLowerCase() === status.toLowerCase();
            order.style.display = shouldShow ? '' : 'none';
        });
    };

    // Order actions
    MerchantPanel.initOrderActions = function() {
        document.addEventListener('click', function(e) {
            if (e.target.matches('.order-update-status-btn')) {
                e.preventDefault();
                const orderId = e.target.dataset.orderId;
                const newStatus = e.target.dataset.status;
                MerchantPanel.updateOrderStatus(orderId, newStatus);
            }

            if (e.target.matches('.order-print-invoice-btn')) {
                e.preventDefault();
                const orderId = e.target.dataset.orderId;
                MerchantPanel.printInvoice(orderId);
            }
        });
    };

    // Update order status
    MerchantPanel.updateOrderStatus = function(orderId, status) {
        fetch('/merchant/orders/' + orderId + '/status', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: status })
        })
        .then(function(response) {
            if (response.ok) {
                MerchantPanel.showNotification('Order status updated successfully', 'success');
                location.reload();
            } else {
                MerchantPanel.showNotification('Failed to update order status', 'error');
            }
        })
        .catch(function(error) {
            MerchantPanel.showNotification('Error: ' + error.message, 'error');
        });
    };

    // Image upload functionality
    MerchantPanel.initImageUpload = function() {
        const imageInputs = document.querySelectorAll('.image-upload-input');
        
        imageInputs.forEach(function(input) {
            input.addEventListener('change', function(e) {
                MerchantPanel.handleImageUpload(e.target);
            });
        });

        // Drag and drop
        const dropZones = document.querySelectorAll('.image-drop-zone');
        dropZones.forEach(function(zone) {
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();
                zone.classList.add('dragover');
            });

            zone.addEventListener('dragleave', function() {
                zone.classList.remove('dragover');
            });

            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                zone.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const input = zone.querySelector('.image-upload-input');
                    input.files = files;
                    MerchantPanel.handleImageUpload(input);
                }
            });
        });
    };

    // Handle image upload
    MerchantPanel.handleImageUpload = function(input) {
        const file = input.files[0];
        if (!file) return;

        // Validate file type
        if (!file.type.startsWith('image/')) {
            MerchantPanel.showNotification('Please select a valid image file', 'error');
            return;
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            MerchantPanel.showNotification('Image file is too large. Maximum size is 5MB', 'error');
            return;
        }

        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = input.parentNode.querySelector('.image-preview');
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    };

    // Reports functionality
    MerchantPanel.initReports = function() {
        const reportForm = document.querySelector('.reports-form');
        if (reportForm) {
            reportForm.addEventListener('submit', function(e) {
                e.preventDefault();
                MerchantPanel.generateReport(this);
            });
        }

        const exportBtn = document.querySelector('.export-report-btn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                MerchantPanel.exportReport();
            });
        }
    };

    // Generate report
    MerchantPanel.generateReport = function(form) {
        const formData = new FormData(form);
        
        fetch('/merchant/reports/generate', {
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
            MerchantPanel.displayReportResults(data);
        })
        .catch(function(error) {
            MerchantPanel.showNotification('Error generating report: ' + error.message, 'error');
        });
    };

    // Display report results
    MerchantPanel.displayReportResults = function(data) {
        const resultsContainer = document.querySelector('.report-results');
        if (resultsContainer) {
            resultsContainer.innerHTML = data.html;
            resultsContainer.style.display = 'block';
        }
    };

    // Notification system
    MerchantPanel.initNotifications = function() {
        // Auto-hide notifications
        const notifications = document.querySelectorAll('.merchant-notification');
        notifications.forEach(function(notification) {
            if (notification.hasAttribute('data-auto-hide')) {
                setTimeout(function() {
                    MerchantPanel.hideNotification(notification);
                }, 5000);
            }
        });

        // Close notification buttons
        document.addEventListener('click', function(e) {
            if (e.target.matches('.notification-close')) {
                const notification = e.target.closest('.merchant-notification');
                MerchantPanel.hideNotification(notification);
            }
        });
    };

    // Show notification
    MerchantPanel.showNotification = function(message, type) {
        type = type || 'info';
        
        const notification = document.createElement('div');
        notification.className = 'merchant-notification merchant-notification-' + type;
        notification.innerHTML = '<span>' + message + '</span><button class="notification-close">&times;</button>';
        
        const container = document.querySelector('.notification-container') || document.body;
        container.appendChild(notification);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            MerchantPanel.hideNotification(notification);
        }, 5000);
    };

    // Hide notification
    MerchantPanel.hideNotification = function(notification) {
        if (notification) {
            notification.style.opacity = '0';
            setTimeout(function() {
                notification.remove();
            }, 300);
        }
    };

    // Form enhancements
    MerchantPanel.initForms = function() {
        const forms = document.querySelectorAll('.merchant-form');
        
        forms.forEach(function(form) {
            // Add form validation
            form.addEventListener('submit', function(e) {
                if (!MerchantPanel.validateForm(form)) {
                    e.preventDefault();
                }
            });

            // Auto-save for product forms
            if (form.classList.contains('product-form')) {
                MerchantPanel.initAutoSave(form);
            }
        });
    };

    // Form validation
    MerchantPanel.validateForm = function(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');

        requiredFields.forEach(function(field) {
            if (!field.value.trim()) {
                MerchantPanel.showFieldError(field, 'This field is required');
                isValid = false;
            } else {
                MerchantPanel.clearFieldError(field);
            }
        });

        return isValid;
    };

    // Show field error
    MerchantPanel.showFieldError = function(field, message) {
        MerchantPanel.clearFieldError(field);
        
        field.classList.add('error');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    };

    // Clear field error
    MerchantPanel.clearFieldError = function(field) {
        field.classList.remove('error');
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    };

    // Initialize when DOM is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', MerchantPanel.init);
    } else {
        MerchantPanel.init();
    }

})();