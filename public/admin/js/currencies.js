/**
 * Currencies Management JavaScript
 * Handles currency-related functionality in the admin panel
 */

// Initialize currencies functionality when DOM is loaded
// Wait for AdminPanel to be fully initialized first
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        // Add small delay to ensure AdminPanel is fully initialized
        setTimeout(() => {
            initializeCurrenciesPage();
        }, 100);
    });
} else {
    // DOM already loaded, initialize immediately with delay
    setTimeout(() => {
        initializeCurrenciesPage();
    }, 100);
}

/**
 * Initialize currencies page functionality
 */
function initializeCurrenciesPage()
{
    console.log('💰 Initializing currencies page...');

    // Ensure we don't interfere with AdminPanel dropdown functionality
    // Only initialize currency-specific features
    initializeDeleteCurrency();
    initializeRefreshRates();
    initializeUpdateExchangeRates();
    initializeDefaultCurrencyConfirmation();
    initializeFormValidation();
    initializeCreateEditForm();
    initializeIndexPage();

    console.log('✅ Currencies page initialized successfully');
}

/**
 * Initialize create/edit form functionality
 */
function initializeCreateEditForm()
{
    // Auto-uppercase currency code
    const codeInput = document.getElementById('code');
    if (codeInput) {
        codeInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    }

    // Exchange rate validation
    const exchangeRateInput = document.getElementById('exchange_rate');
    if (exchangeRateInput) {
        exchangeRateInput.addEventListener('input', function () {
            const value = parseFloat(this.value);
            if (value <= 0) {
                this.setCustomValidity('معدل الصرف يجب أن يكون أكبر من 0');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Default currency warning
    const isDefaultCheckbox = document.getElementById('is_default');
    if (isDefaultCheckbox) {
        isDefaultCheckbox.addEventListener('change', function () {
            if (this.checked) {
                if (!confirm('تعيين هذه العملة كافتراضية سيحل محل العملة الافتراضية الحالية. هل تريد المتابعة؟')) {
                    this.checked = false;
                }
            }
        });
    }

    // Form validation for create page
    const form = document.querySelector('.currency-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            const code = document.getElementById('code') ? .value;
            const exchangeRate = parseFloat(document.getElementById('exchange_rate') ? .value);

            if (code && code.length !== 3) {
                e.preventDefault();
                alert('رمز العملة يجب أن يكون 3 أحرف بالضبط');
                return false;
            }

            if (exchangeRate && exchangeRate <= 0) {
                e.preventDefault();
                alert('معدل الصرف يجب أن يكون أكبر من 0');
                return false;
            }
        });
    }
}

/**
 * Initialize index page functionality
 */
function initializeIndexPage()
{
    // Handle delete confirmation for index page
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const confirmMessage = this.querySelector('button') ? .dataset.confirm || 'هل أنت متأكد من حذف هذه العملة؟';
            if (!confirm(confirmMessage)) {
                e.preventDefault();
            }
        });
    });

    // Handle set default confirmation for index page
    const setDefaultForms = document.querySelectorAll('.set-default-form');
    setDefaultForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const confirmMessage = this.querySelector('button') ? .dataset.confirm || 'تعيين هذه العملة كافتراضية؟';
            if (!confirm(confirmMessage)) {
                e.preventDefault();
            }
        });
    });

    // Handle refresh rates for index page
    const refreshButtons = document.querySelectorAll('[data-action="refresh-rates"], [data-action="update-rates"]');
    refreshButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Add loading state
            this.disabled = true;
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';

            // Simulate API call (replace with actual implementation)
            setTimeout(() => {
                location.reload();
            }, 2000);
        });
    });
}

/**
 * Initialize delete currency functionality
 */
function initializeDeleteCurrency()
{
    const deleteBtn = document.querySelector('[data-action="delete-currency"]');

    if (deleteBtn) {
        deleteBtn.addEventListener('click', deleteCurrency);
    }
}

/**
 * Initialize refresh rates functionality
 */
function initializeRefreshRates()
{
    const refreshBtn = document.querySelector('[data-action="refresh-rates"]');

    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshRates);
    }
}

/**
 * Initialize update exchange rates functionality
 */
function initializeUpdateExchangeRates()
{
    const updateBtn = document.querySelector('[data-action="update-rates"]');

    if (updateBtn) {
        updateBtn.addEventListener('click', updateExchangeRates);
    }
}

/**
 * Initialize default currency confirmation
 */
function initializeDefaultCurrencyConfirmation()
{
    const defaultBtns = document.querySelectorAll('[data-action="set-default"]');

    defaultBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const currencyId = this.dataset.currencyId;
            setAsDefault(currencyId);
        });
    });
}

/**
 * Initialize form validation
 */
function initializeFormValidation()
{
    const forms = document.querySelectorAll('.currency-form');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const codeInput = form.querySelector('#code');
            const rateInput = form.querySelector('#exchange_rate');

            if (codeInput && codeInput.value.length !== 3) {
                e.preventDefault();
                alert('رمز العملة يجب أن يكون 3 أحرف');
                return;
            }

            if (rateInput && parseFloat(rateInput.value) <= 0) {
                e.preventDefault();
                alert('معدل الصرف يجب أن يكون أكبر من 0');
                return;
            }
        });
    });
}

/**
 * Delete currency function
 */
function deleteCurrency()
{
    if (confirm('هل أنت متأكد من حذف هذه العملة؟')) {
        const deleteForm = document.getElementById('deleteForm');
        if (deleteForm) {
            deleteForm.submit();
        }
    }
}

/**
 * Refresh exchange rates
 */
function refreshRates()
{
    const refreshBtn = document.querySelector('[data-action="refresh-rates"]');

    if (refreshBtn) {
        const originalText = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';
        refreshBtn.disabled = true;
    }

    fetch('/admin/currencies/refresh-rates', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (refreshBtn) {
            refreshBtn.innerHTML = originalText;
            refreshBtn.disabled = false;
        }

        if (data.success) {
            updateRatesInTable(data.rates);
            alert((typeof __tFn === 'function' ? __tFn('currencies.rates.refresh_success') : 'Exchange rates updated successfully'));
        } else {
            alert((typeof __tFn === 'function' ? __tFn('currencies.rates.refresh_failed') : 'Failed to refresh exchange rates'));
        }
    })
    .catch(error => {
        if (refreshBtn) {
            refreshBtn.innerHTML = originalText;
            refreshBtn.disabled = false;
        }
        console.error('Error refreshing rates:', error);
        alert((typeof __tFn === 'function' ? __tFn('currencies.rates.refresh_error') : 'Error refreshing exchange rates'));
    });
}

/**
 * Update exchange rates
 */
function updateExchangeRates()
{
    const updateBtn = document.querySelector('[data-action="update-rates"]');

    if (updateBtn) {
        const originalText = updateBtn.innerHTML;
        updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';
        updateBtn.disabled = true;
    }

    fetch('/admin/currencies/update-rates', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (updateBtn) {
            updateBtn.innerHTML = originalText;
            updateBtn.disabled = false;
        }

        if (data.success) {
            updateRatesInTable(data.rates);
            alert((typeof __tFn === 'function' ? __tFn('currencies.rates.update_success') : 'Exchange rates updated successfully'));
        } else {
            alert((typeof __tFn === 'function' ? __tFn('currencies.rates.update_failed') : 'Failed to update exchange rates'));
        }
    })
    .catch(error => {
        if (updateBtn) {
            updateBtn.innerHTML = originalText;
            updateBtn.disabled = false;
        }
        console.error('Error updating rates:', error);
        alert((typeof __tFn === 'function' ? __tFn('currencies.rates.update_error') : 'Error updating exchange rates'));
    });
}

/**
 * Update rates in table
 */
function updateRatesInTable(rates)
{
    Object.keys(rates).forEach(currencyCode => {
        const rateCell = document.querySelector(`[data - currency = "${currencyCode}"] .exchange - rate`);
        if (rateCell) {
            rateCell.textContent = rates[currencyCode];
        }
    });
}

/**
 * Set currency as default
 */
function setAsDefault(currencyId)
{
    // Use AdminPanel's setDefaultCurrency method to avoid duplication
    if (window.adminPanel && window.adminPanel.setDefaultCurrency) {
        window.adminPanel.setDefaultCurrency(currencyId);
    } else if (window.setDefaultCurrency) {
        window.setDefaultCurrency(currencyId);
    } else {
        console.error('AdminPanel setDefaultCurrency method not available');
    }
}

/**
 * Format currency display
 */
function formatCurrency(amount, currencyCode, symbol)
{
    const formatter = new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: currencyCode,
        currencyDisplay: 'symbol'
    });

    return formatter.format(amount);
}

/**
 * Convert between currencies
 */
function convertCurrency(amount, fromRate, toRate)
{
    return (amount / fromRate) * toRate;
}

// Export functions for global access
window.CurrenciesManager = {
    deleteCurrency,
    refreshRates,
    updateExchangeRates,
    setAsDefault,
    formatCurrency,
    convertCurrency
};

// Note: This file works in conjunction with admin-unified.js
// Currency status toggle and default setting functions are handled by AdminPanel
// to avoid code duplication and ensure consistency across the admin interface