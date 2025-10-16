/**
 * Vendor Withdrawals JavaScript
 * Enhanced interactive functionality for withdrawal management
 */

class VendorWithdrawals {
    constructor() {
        this.init();
        this.bindEvents();
        this.initAnimations();
    }

    init() {
        this.form = document.getElementById('withdrawalForm');
        this.amountInput = document.getElementById('amount');
    // Support legacy id "submitWithdrawal" or new standardized "submitBtn"
    this.submitBtn = document.getElementById('submitBtn') || document.getElementById('submitWithdrawal') || (this.form ? this.form.querySelector('button[type="submit"]') : null);
        this.amountBtns = document.querySelectorAll('.amount-btn');
        this.paymentRadios = document.querySelectorAll('.payment-radio');
        // Fallback: blade markup may not include .payment-radio class
        if (!this.paymentRadios || this.paymentRadios.length === 0) {
            this.paymentRadios = document.querySelectorAll('input[type="radio"][name="payment_method"]');
        }
        this.termsCheckbox = document.getElementById('terms');

        // Capture dataset-based constraints (because input[min] may be 0 while business min is higher)
        this.datasetMin = 0;
        this.datasetMax = null;
        try {
            if (this.form && this.form.dataset) {
                if (this.form.dataset.minimum) {
                    const m = parseFloat(this.form.dataset.minimum);
                    if (!isNaN(m) && m > 0) this.datasetMin = m;
                }
                if (this.form.dataset.available) {
                    const a = parseFloat(this.form.dataset.available);
                    if (!isNaN(a) && a >= 0) this.datasetMax = a;
                }
            }
        } catch (e) { /* ignore */ }
        
        // Initialize tooltips and popovers
        this.initTooltips();
        
        // Set up form validation
        this.setupFormValidation();
        
        // Initialize amount suggestions
        this.initAmountSuggestions();
        
        // Setup payment method selection
        this.initPaymentMethods();
    }

    bindEvents() {
        // Amount input events
        if (this.amountInput) {
            this.amountInput.addEventListener('input', this.validateAmount.bind(this));
            this.amountInput.addEventListener('blur', this.formatAmount.bind(this));
            this.amountInput.addEventListener('focus', this.highlightAmountField.bind(this));
        }

        // Amount suggestion buttons
        this.amountBtns.forEach(btn => {
            btn.addEventListener('click', this.setAmount.bind(this));
        });

        // Payment method selection
        this.paymentRadios.forEach(radio => {
            try { radio.addEventListener('change', this.updatePaymentMethod.bind(this)); } catch (e) {}
        });

        // Terms checkbox
        if (this.termsCheckbox) {
            this.termsCheckbox.addEventListener('change', this.validateForm.bind(this));
        }

        // Form submission
        if (this.form) {
            this.form.addEventListener('submit', this.handleFormSubmit.bind(this));
        }

        // Search and filter functionality for index page
        this.initSearchAndFilter();
        
        // Copy functionality
        this.initCopyButtons();
        
        // Refresh functionality
        this.initRefreshButton();
    }

    initAnimations() {
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-visible');
                }
            });
        }, observerOptions);

        // Observe animated elements
        document.querySelectorAll('.animate-fade-in, .animate-slide-in, .animate-scale-in').forEach(el => {
            observer.observe(el);
        });
    }

    initTooltips() {
        // Initialize Bootstrap tooltips if available
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }

    setupFormValidation() {
        if (!this.form) return;

        // Real-time validation
        const inputs = this.form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    }

    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        let isValid = true;
        let errorMessage = '';

        // Amount validation
        if (fieldName === 'amount') {
            const amount = parseFloat(value);
            const minAmount = parseFloat(field.min);
            const maxAmount = parseFloat(field.max);

            if (!value) {
                isValid = false;
                errorMessage = 'Amount is required';
            } else if (isNaN(amount) || amount <= 0) {
                isValid = false;
                errorMessage = 'Please enter a valid amount';
            } else if (amount < minAmount) {
                isValid = false;
                errorMessage = `Minimum amount is ${minAmount}`;
            } else if (amount > maxAmount) {
                isValid = false;
                errorMessage = `Maximum amount is ${maxAmount}`;
            }
        }

        // Terms validation
        if (fieldName === 'terms' && !field.checked) {
            isValid = false;
            errorMessage = 'You must agree to the terms and conditions';
        }

        this.showFieldValidation(field, isValid, errorMessage);
        return isValid;
    }

    showFieldValidation(field, isValid, errorMessage) {
        // Safely locate feedback element in several fallback locations
        let feedbackEl = null;
        try {
            if (field && field.parentNode) {
                feedbackEl = field.parentNode.querySelector('.invalid-feedback');
            }
        } catch (e) { feedbackEl = null; }

        if (!feedbackEl) {
            const grp = field && field.closest ? field.closest('.form-group') : null;
            if (grp) {
                feedbackEl = grp.querySelector('.invalid-feedback');
            }
        }

        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            if (feedbackEl) feedbackEl.style.display = 'none';
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            if (feedbackEl) {
                feedbackEl.textContent = errorMessage;
                feedbackEl.style.display = 'block';
            }
        }
    }

    clearFieldError(field) {
        field.classList.remove('is-invalid');
        let feedbackEl = null;
        try {
            if (field && field.parentNode) feedbackEl = field.parentNode.querySelector('.invalid-feedback');
        } catch (e) { feedbackEl = null; }
        if (!feedbackEl) {
            const grp = field && field.closest ? field.closest('.form-group') : null;
            if (grp) feedbackEl = grp.querySelector('.invalid-feedback');
        }
        if (feedbackEl) feedbackEl.style.display = 'none';
    }

    validateAmount() {
        if (!this.amountInput) return;
        
        const amount = parseFloat(this.amountInput.value);
        const minAmount = parseFloat(this.amountInput.min);
        const maxAmount = parseFloat(this.amountInput.max);
        
        this.validateField(this.amountInput);
        this.validateForm();
    }

    formatAmount() {
        if (!this.amountInput || !this.amountInput.value) return;
        
        const amount = parseFloat(this.amountInput.value);
        if (!isNaN(amount)) {
            this.amountInput.value = amount.toFixed(2);
        }
    }

    highlightAmountField() {
        if (this.amountInput) {
            this.amountInput.parentNode.classList.add('focused');
            setTimeout(() => {
                this.amountInput.parentNode.classList.remove('focused');
            }, 200);
        }
    }

    initAmountSuggestions() {
        this.amountBtns.forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                btn.style.transform = 'scale(1.05)';
            });
            btn.addEventListener('mouseleave', () => {
                btn.style.transform = 'scale(1)';
            });
        });
    }

    setAmount(event) {
        event.preventDefault();
        const amount = event.target.dataset.amount;
        if (this.amountInput && amount) {
            this.amountInput.value = amount;
            this.amountInput.focus();
            this.validateAmount();
            
            // Add visual feedback
            event.target.classList.add('selected');
            setTimeout(() => {
                event.target.classList.remove('selected');
            }, 300);
        }
    }

    initPaymentMethods() {
        this.paymentRadios.forEach(radio => {
            // Support multiple markup patterns: .payment-label wrapper, .form-check, or parentNode
            let label = radio.closest ? radio.closest('.payment-label') : null;
            if (!label) label = radio.closest ? radio.closest('.form-check') : null;
            if (!label) label = radio.parentNode || null;

            if (label) {
                label.addEventListener('mouseenter', () => {
                    if (!radio.checked) {
                        label.style.transform = 'translateY(-2px)';
                        label.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
                    }
                });
                label.addEventListener('mouseleave', () => {
                    if (!radio.checked) {
                        label.style.transform = 'translateY(0)';
                        label.style.boxShadow = '';
                    }
                });
            }
        });
    }

    updatePaymentMethod(event) {
        const selectedRadio = event.target;
        
        // Update visual states
        this.paymentRadios.forEach(radio => {
            let label = radio.closest ? radio.closest('.payment-label') : null;
            if (!label) label = radio.closest ? radio.closest('.form-check') : null;
            if (!label) label = radio.parentNode || null;
            const card = label ? (label.querySelector('.payment-card') || label) : null;

            if (radio === selectedRadio) {
                if (card) card.classList.add('selected');
                if (label) {
                    label.style.transform = 'translateY(-2px)';
                    label.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
                }
            } else {
                if (card) card.classList.remove('selected');
                if (label) {
                    label.style.transform = 'translateY(0)';
                    label.style.boxShadow = '';
                }
            }
        });
        
        this.validateForm();
    }

    validateForm() {
        if (!this.form || !this.submitBtn) return;
        
        const rawVal = this.amountInput ? this.amountInput.value : '';
        const amount = parseFloat(rawVal);
        // derive min/max: prefer dataset business rules if present
        let minAmount = this.datasetMin != null ? this.datasetMin : (this.amountInput ? parseFloat(this.amountInput.min) : 0);
        if (isNaN(minAmount)) minAmount = 0;
        let maxAmount = this.datasetMax != null ? this.datasetMax : (this.amountInput ? parseFloat(this.amountInput.max) : 0);
        if (isNaN(maxAmount) || maxAmount <= 0) {
            // final fallback: if user typed something use it as baseline max to avoid perma-disable
            maxAmount = amount > 0 ? amount : 999999999;
        }
        const termsAccepted = this.termsCheckbox ? this.termsCheckbox.checked : false;
        const paymentMethodSelected = this.paymentRadios && this.paymentRadios.length > 0
            ? Array.from(this.paymentRadios).some(radio => radio && radio.checked)
            : true; // if no radios found treat as valid (avoid perma-disabled)
        
        const amountValid = !isNaN(amount) && amount >= minAmount && amount <= maxAmount;
        const isValid = amountValid && termsAccepted && paymentMethodSelected;
        
        this.submitBtn.disabled = !isValid;
        this.submitBtn.classList.toggle('disabled', !isValid);

        // Debug (non-intrusive): attach data-state for possible CSS highlighting in dev
        this.submitBtn.dataset.state = isValid ? 'ready' : 'blocked';
    }

    handleFormSubmit(event) {
        event.preventDefault();
        
        // Validate all fields
        const inputs = this.form.querySelectorAll('input[required], select[required], textarea[required]');
        let isFormValid = true;
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isFormValid = false;
            }
        });
        
        if (!isFormValid) {
            this.showNotification('Please correct the errors before submitting', 'error');
            return;
        }
        
        // Show loading state
        this.setSubmitLoading(true);
        
        // Submit form
        setTimeout(() => {
            this.form.submit();
        }, 500);
    }

    setSubmitLoading(loading) {
        if (!this.submitBtn) return;
        
        if (loading) {
            this.submitBtn.disabled = true;
            this.submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            this.submitBtn.classList.add('loading');
        } else {
            this.submitBtn.disabled = false;
            this.submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Withdrawal Request';
            this.submitBtn.classList.remove('loading');
        }
    }

    // Search and Filter functionality for index page
    initSearchAndFilter() {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const dateFromFilter = document.getElementById('dateFrom');
        const dateToFilter = document.getElementById('dateTo');
        const amountFromFilter = document.getElementById('amountFrom');
        const amountToFilter = document.getElementById('amountTo');
        
        if (searchInput) {
            searchInput.addEventListener('input', this.debounce(this.performSearch.bind(this), 300));
        }
        
        [statusFilter, dateFromFilter, dateToFilter, amountFromFilter, amountToFilter].forEach(filter => {
            if (filter) {
                filter.addEventListener('change', this.performSearch.bind(this));
            }
        });
    }

    performSearch() {
        // This would typically make an AJAX request to filter results
        // For now, we'll just show a loading state
        const tableBody = document.querySelector('.withdrawals-table tbody');
        if (tableBody) {
            tableBody.style.opacity = '0.5';
            setTimeout(() => {
                tableBody.style.opacity = '1';
            }, 500);
        }
    }

    // Copy functionality
    initCopyButtons() {
        const copyBtns = document.querySelectorAll('.copy-btn');
        copyBtns.forEach(btn => {
            btn.addEventListener('click', this.copyToClipboard.bind(this));
        });
    }

    copyToClipboard(event) {
        const btn = event.currentTarget;
        const textToCopy = btn.dataset.copy;
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(textToCopy).then(() => {
                this.showCopyFeedback(btn);
            });
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = textToCopy;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            this.showCopyFeedback(btn);
        }
    }

    showCopyFeedback(btn) {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.classList.add('copied');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('copied');
        }, 2000);
    }

    // Refresh functionality
    initRefreshButton() {
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', this.refreshData.bind(this));
        }
    }

    refreshData() {
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            const icon = refreshBtn.querySelector('i');
            icon.classList.add('fa-spin');
            
            // Simulate refresh
            setTimeout(() => {
                icon.classList.remove('fa-spin');
                // Notification suppressed by default for automated refreshes.
                if (window.vendorWithdrawalsShowToast === true) {
                    this.showNotification('Data refreshed successfully', 'success');
                }
            }, 1000);
        }
    }

    // Utility functions
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Auto remove
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
        
        // Close button
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        });
    }

    // Format currency
    formatCurrency(amount, currency = 'USD') {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency
        }).format(amount);
    }

    // Format date
    formatDate(date) {
        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date(date));
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new VendorWithdrawals();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = VendorWithdrawals;
}