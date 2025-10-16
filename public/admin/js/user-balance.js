/**
 * User Balance Management JavaScript
 * Handles balance operations, history display, and UI interactions
 */

class UserBalanceManager {
    constructor(userId)
    {
        this.userId = userId;
        this.currentBalance = 0;
        this.isLoading = false;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        // Ensure config objects exist by parsing <template id="user-balance-config"> if present
        this.ensureConfigFromTemplate();
        this.init();
    }

    ensureConfigFromTemplate()
    {
        try {
            if (!window.userBalanceUrls || !window.defaultCurrency || !window.i18n) {
                var tpl = document.getElementById('user-balance-config');
                if (tpl) {
                    var raw = tpl.textContent || tpl.innerText || tpl.innerHTML || '';
                    var cfg = JSON.parse(raw || '{}');
                    window.userBalanceUrls = window.userBalanceUrls || cfg.urls || {};
                    window.defaultCurrency = window.defaultCurrency || cfg.currency || {};
                    window.i18n = window.i18n || cfg.i18n || {};
                }
            }
        } catch (e) {
            console && console.warn && console.warn('user-balance: failed to parse config template', e);
        }
    }

    init()
    {
        this.bindEvents();
        this.loadUserData();
    // Load a small recent-transactions preview on page load
        this.loadRecentTransactionsPreview();
        this.setupModals();
        this.setupFormValidation();
        this.setupRealTimeUpdates();
    }

    bindEvents()
    {
        // Add Balance Button
        $(document).on('click', '.btn-add-balance', (e) => {
            e.preventDefault();
            this.showAddBalanceModal();
        });

        // Deduct Balance Button
        $(document).on('click', '.btn-deduct-balance', (e) => {
            e.preventDefault();
            this.showDeductBalanceModal();
        });

        // Refresh Balance Button
        $(document).on('click', '.btn-refresh-balance', (e) => {
            e.preventDefault();
            this.refreshBalance();
        });

        // View History Button
        $(document).on('click', '.btn-view-history', (e) => {
            e.preventDefault();
            this.showBalanceHistory();
        });

        // Form Submissions
        $('#addBalanceForm').on('submit', (e) => {
            e.preventDefault();
            this.handleAddBalance();
        });

        $('#deductBalanceForm').on('submit', (e) => {
            e.preventDefault();
            this.handleDeductBalance();
        });

        // Real-time balance updates
        this.setupRealTimeUpdates();
    }

    loadUserData()
    {
        const userIdElement = document.querySelector('[data-user-id]');
        if (userIdElement) {
            this.userId = userIdElement.getAttribute('data-user-id');
        }

        const balanceElement = document.querySelector('.balance-value');
        if (balanceElement) {
            this.currentBalance = parseFloat(balanceElement.textContent.replace(/[^\d.-]/g, '')) || 0;
        }
    }

    showAddBalanceModal()
    {
        const modalElement = document.getElementById('addBalanceModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();

            // Reset form
            this.resetForm('#addBalanceForm');

            // Focus on amount input
            setTimeout(() => {
                document.querySelector('#addBalanceModal input[name="amount"]') ? .focus();
            }, 300);
        }
    }

    showDeductBalanceModal()
    {
        const modalElement = document.getElementById('deductBalanceModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();

            // Reset form
            this.resetForm('#deductBalanceForm');

            // Focus on amount input
            setTimeout(() => {
                document.querySelector('#deductBalanceModal input[name="amount"]') ? .focus();
            }, 300);
        }
    }

    showBalanceHistory()
    {
        const modalElement = document.getElementById('balanceHistoryModal');
        if (!modalElement) {
            return;
        }
        // Ensure aria-hidden removed before focusing inside
        modalElement.addEventListener('shown.bs.modal', () => {
            // Focus close button for accessibility
            const closeBtn = modalElement.querySelector('[data-bs-dismiss="modal"]');
            if (closeBtn) {
                closeBtn.focus();
            }
            this.loadBalanceHistory();
        }, { once: true });
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }

    async handleAddBalance()
    {
        if (this.isLoading) {
            return;
        }

        const amount = parseFloat($('#addAmount').val());
        const note = $('#addReason').val().trim();

        if (!this.validateAmount(amount) || !note) {
            this.showError(window.i18n ? .balance_invalid_add || 'Please enter a valid amount and a reason');
            return;
        }

        this.setLoading(true);

        try {
            const url = window.userBalanceUrls ? .add;
            if (!url) {
                this.showError(window.i18n ? .error_server || 'Server communication error');
                console.warn('user-balance: missing config URL for add');
                this.setLoading(false);
                return;
            }
            const response = await this.makeRequest(url, {
                method: 'POST',
                body: JSON.stringify({
                    amount: amount,
                    note: note
                })
            });

            if (response.success) {
                this.updateBalanceDisplay(response.new_balance);
                this.showSuccess(window.i18n ? .balance_added || 'Balance added successfully');
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('addBalanceModal'));
                if (modalInstance) {
                    modalInstance.hide();
                }
                this.resetForm('#addBalanceForm');
                this.refreshStats();
            } else {
                this.showError(response.message || window.i18n ? .error_add || 'Error while adding balance');
            }
        } catch (error) {
            this.showError(window.i18n ? .error_server || 'Server communication error');
            console.error('Add balance error:', error);
        } finally {
            this.setLoading(false);
        }
    }

    async handleDeductBalance()
    {
        if (this.isLoading) {
            return;
        }

        const amount = parseFloat($('#deductAmount').val());
        const note = $('#deductReason').val().trim();

        if (!this.validateAmount(amount) || !note) {
            this.showError(window.i18n ? .balance_invalid_deduct || 'Please enter a valid amount and a reason');
            return;
        }

        if (amount > this.currentBalance) {
            this.showError(window.i18n ? .balance_exceeds || 'Amount exceeds current balance');
            return;
        }

        this.setLoading(true);

        try {
            const url = window.userBalanceUrls ? .deduct;
            if (!url) {
                this.showError(window.i18n ? .error_server || 'Server communication error');
                console.warn('user-balance: missing config URL for deduct');
                this.setLoading(false);
                return;
            }
            const response = await this.makeRequest(url, {
                method: 'POST',
                body: JSON.stringify({
                    amount: amount,
                    note: note
                })
            });

            if (response.success) {
                this.updateBalanceDisplay(response.new_balance);
                this.showSuccess(window.i18n ? .balance_deducted || 'Balance deducted successfully');
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('deductBalanceModal'));
                if (modalInstance) {
                    modalInstance.hide();
                }
                this.resetForm('#deductBalanceForm');
                this.refreshStats();
            } else {
                this.showError(response.message || window.i18n ? .error_deduct || 'Error while deducting balance');
            }
        } catch (error) {
            this.showError(window.i18n ? .error_server || 'Server communication error');
            console.error('Deduct balance error:', error);
        } finally {
            this.setLoading(false);
        }
    }

    async refreshBalance()
    {
        if (this.isLoading) {
            return;
        }

        this.setLoading(true, window.i18n ? .loading_refresh || 'Refreshing data...');

        try {
            const url = window.userBalanceUrls ? .refresh;
            if (!url) {
                this.showError(window.i18n ? .error_server || 'Server communication error');
                console.warn('user-balance: missing config URL for refresh');
                this.setLoading(false);
                return;
            }
            const response = await this.makeRequest(url, {
                method: 'POST'
            });

            if (response.success) {
                this.updateBalanceDisplay(response.balance.current);
                this.refreshStats();
                // Only show toast if caller explicitly requested it via config
                if (window.userBalanceShowToast === true) {
                    this.showSuccess(window.i18n ? .balance_refreshed || 'Data refreshed successfully');
                }
            } else {
                this.showError(response.message || window.i18n ? .error_refresh || 'Error while refreshing data');
            }
        } catch (error) {
            this.showError(window.i18n ? .error_server || 'Server communication error');
            console.error('Refresh balance error:', error);
        } finally {
            this.setLoading(false);
        }
    }

    async loadBalanceHistory()
    {
        const historyBody = document.querySelector('#balanceHistoryModal .modal-body');
        const historyContainer = document.getElementById('balanceHistoryContainer');
        if (!historyBody || !historyContainer) {
            return;
        }

        // Show spinner
        historyContainer.innerHTML = `
            < div class = "text-center p-4" >
                < div class = "loading-spinner mx-auto" > < / div >
                < p class = "mt-2" > ${window.i18n ? .loading_history || 'Loading...'} < / p >
            <  / div > `;

        try {
            const url = window.userBalanceUrls ? .history;
            if (!url) {
                console.warn('user-balance: missing config URL for history');
                const historyContainer = document.getElementById('balanceHistoryContainer');
                if (historyContainer) {
                    historyContainer.innerHTML = '<div class="empty-state">' + (window.i18n ? .no_history_desc || 'No previous transactions found') + '</div>';
                }
                return;
            }
            const response = await this.makeRequest(url);
            console.log('Balance history raw response:', response);
            if (response.success && Array.isArray(response.history)) {
                this.renderBalanceHistory(response.history);
            } else {
                historyContainer.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h4>' + (window.i18n ? .error || 'Error') + '</h4><p>' + (window.i18n ? .error_history || 'Failed to load balance history') + '</p></div>';
            }
        } catch (error) {
            console.error('Load balance history error:', error);
            historyContainer.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h4>' + (window.i18n ? .error || 'Error') + '</h4><p>' + (window.i18n ? .error_history || 'An error occurred while loading balance history') + '</p></div>';
        } finally {
            // Remove spinner handled via render / error replacement
        }
    }

    renderBalanceHistory(history)
    {
        let historyContainerEl = document.getElementById('balanceHistoryContainer');
        if (!historyContainerEl) {
            // Create container if missing
            const body = document.querySelector('#balanceHistoryModal .modal-body');
            if (body) {
                historyContainerEl = document.createElement('div');
                historyContainerEl.id = 'balanceHistoryContainer';
                body.appendChild(historyContainerEl);
            }
        }
        const historyContainer = $(historyContainerEl);

        if (!history || history.length === 0) {
            historyContainer.html('<div class="empty-state"><i class="fas fa-history"></i><h4>' + (window.i18n ? .no_history || 'No history') + '</h4><p>' + (window.i18n ? .no_history_desc || 'No previous transactions found') + '</p></div>');
            return;
        }

        let tableHtml = `
            < div class = "balance-history-table" >
                < table class = "table table-hover" >
                    < thead >
                        < tr >
                            < th > التاريخ < / th >
                            < th > نوع العملية < / th >
                            < th > المبلغ < / th >
                            < th > الرصيد بعد العملية < / th >
                            < th > السبب < / th >
                        <  / tr >
                    <  / thead >
                    < tbody >
        `;

        const escapeHtml = (unsafe) => {
            if (unsafe == null) {
                return '';
            }
            return String(unsafe)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };

        history.forEach(transaction => {
            const isCredit = transaction.type === 'credit';
            const typeClass = isCredit ? 'credit' : 'debit';
            const amountClass = isCredit ? 'positive' : 'negative';
            const typeIcon = isCredit ? 'fas fa-plus-circle' : 'fas fa-minus-circle';
            const typeText = isCredit ? (window.i18n ? .credit || 'Credit') : (window.i18n ? .debit || 'Debit');
            const amountPrefix = isCredit ? '+' : '-';

            const dateText = transaction.formatted_date || transaction.date || '';
            tableHtml += `
                < tr >
                    < td > ${dateText} < / td >
                    < td >
                        < span class = "transaction-type ${typeClass}" >
                            < i class = "${typeIcon}" > < / i >
                            ${typeText}
                        <  / span >
                    <  / td >
                    < td >
                        < span class = "transaction-amount ${amountClass}" >
                            ${amountPrefix}${this.formatCurrency(transaction.amount)}
                        <  / span >
                    <  / td >
                    < td > ${this.formatCurrency(transaction.new_balance)} < / td >
                    < td > ${escapeHtml(transaction.note) || (window.i18n ? .not_specified || 'Not specified')} < / td >
                <  / tr >
            `;
        });

        tableHtml += `
                    <  / tbody >
                <  / table >
            <  / div >
        `;

        historyContainer.html(tableHtml);
    }

    // Load a compact recent-transactions preview into the card placeholder
    async loadRecentTransactionsPreview()
    {
        const placeholder = document.querySelector('.history-placeholder');
        if (!placeholder) {
            return;
        }

        // show temporary spinner
        placeholder.innerHTML = `
            < div class = "text-center p-4" >
                < div class = "loading-spinner mx-auto" > < / div >
                < p class = "mt-2" > ${window.i18n ? .loading_history || 'Loading...'} < / p >
            <  / div > `;

        try {
            const url = window.userBalanceUrls ? .history;
            if (!url) {
                placeholder.innerHTML = '<div class="empty-state">' + (window.i18n ? .no_history_desc || 'No previous transactions found') + '</div>';
                return;
            }

            const response = await this.makeRequest(url);
            if (response.success && Array.isArray(response.history) && response.history.length > 0) {
                // limit to 5 most recent
                const items = response.history.slice(0, 5);
                let html = '<div class="list-group list-group-flush">';
                items.forEach(tx => {
                    const isCredit = tx.type === 'credit';
                    const sign = isCredit ? '+' : '-';
                    const title = isCredit ? (window.i18n ? .credit || 'Credit') : (window.i18n ? .debit || 'Debit');
                    const dateText = tx.formatted_date || tx.date || '';
                    html += `
                        < div class = "list-group-item d-flex justify-content-between align-items-start" >
                            < div >
                                < div class = "fw-semibold" > ${title} < small class = "text-muted" > ${dateText} < / small > < / div >
                                < div class = "text-muted small" > ${(tx.note) ? tx.note : (window.i18n ? .not_specified || 'Not specified')} < / div >
                            <  / div >
                            < div class = "text-end" >
                                < div class = "fw-bold ${isCredit ? 'text-success' : 'text-danger'}" > ${sign}${this.formatCurrency(tx.amount)} < / div >
                                < div class = "small text-muted" > ${this.formatCurrency(tx.new_balance)} < / div >
                            <  / div >
                        <  / div > `;
                });
                html += '</div>';
                placeholder.innerHTML = html;
            } else {
                placeholder.innerHTML = '<div class="empty-state text-center"><p>' + (window.i18n ? .no_history_desc || 'No previous transactions found') + '</p></div>';
            }
        } catch (err) {
            console.error('Recent transactions preview error:', err);
            placeholder.innerHTML = '<div class="empty-state text-center"><p>' + (window.i18n ? .error_history || 'Failed to load balance history') + '</p></div>';
        }
    }

    updateBalanceDisplay(newBalance)
    {
        this.currentBalance = newBalance;
        const balanceElement = document.querySelector('.balance-value');
        if (balanceElement) {
            // Animate the balance change
            $(balanceElement).fadeOut(200, function () {
                const defaultCurrency = window.defaultCurrency || { code: 'SAR', symbol: 'ر.س' };
                $(this).text(new Intl.NumberFormat('ar-SA', {
                    style: 'currency',
                    currency: defaultCurrency.code
                }).format(newBalance)).fadeIn(200);
            });
        }
    }

    updateStats(stats)
    {
        if (!stats) {
            return;
        }

        Object.keys(stats).forEach(key => {
            const element = document.querySelector(`[data - stat = "${key}"]`);
            if (element) {
                $(element).fadeOut(200, function () {
                    $(this).text(stats[key]).fadeIn(200);
                });
            }
        });
    }

    async refreshStats()
    {
        try {
            const url = window.userBalanceUrls ? .stats;
            if (!url) {
                console.warn('user-balance: missing config URL for stats');
                return;
            }
            const response = await this.makeRequest(url);

            if (response.success && response.stats) {
                this.updateStats(response.stats);
            }
        } catch (error) {
            console.error('Refresh stats error:', error);
        }
    }

    validateAmount(amount)
    {
        return !isNaN(amount) && amount > 0 && amount <= 999999;
    }

    async makeRequest(url, options = {})
    {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json'
            }
        };

        const finalOptions = { ...defaultOptions, ...options };

        try {
            const response = await fetch(url, finalOptions);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Request failed:', error);
            throw error;
        }
    }

    setLoading(loading, message = (window.i18n ? .processing || 'Processing...'))
    {
        this.isLoading = loading;

        if (loading) {
            // Show loading overlay
            if (!document.querySelector('.loading-overlay')) {
                const overlay = document.createElement('div');
                overlay.className = 'loading-overlay';
                overlay.innerHTML = `
                    < div class = "text-center" >
                        < div class = "loading-spinner mx-auto" > < / div >
                        < p class = "mt-2 mb-0" > ${message} < / p >
                    <  / div >
                `;
                document.querySelector('.balance-overview').style.position = 'relative';
                document.querySelector('.balance-overview').appendChild(overlay);
            }
        } else {
            // Hide loading overlay
            const overlay = document.querySelector('.loading-overlay');
            if (overlay) {
                overlay.remove();
            }
        }
    }

    showSuccess(message)
    {
        this.showNotification(message, 'success');
    }

    showError(message)
    {
        this.showNotification(message, 'error');
    }

    showNotification(message, type = 'info')
    {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert - ${type === 'error' ? 'danger' : type} alert - dismissible fade show position - fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            < button type = "button" class = "btn-close" data - bs - dismiss = "alert" aria - label = "Close" > < / button >
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    resetForm(formSelector)
    {
        const form = typeof formSelector === 'string' ? document.querySelector(formSelector) : formSelector;
        if (form) {
            form.reset();

            // Clear validation states
            form.querySelectorAll('.is-invalid').forEach(element => {
                element.classList.remove('is-invalid');
            });

            form.querySelectorAll('.invalid-feedback').forEach(element => {
                element.remove();
            });
        }
    }

    setupModals()
    {
        // Handle modal close events
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', () => {
                // Reset forms when modal is closed
                const forms = modal.querySelectorAll('form');
                forms.forEach(form => {
                    if (form.id) {
                        this.resetForm('#' + form.id);
                    }
                });
            });
        });

        // Handle modal backdrop clicks
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }
            });
        });
    }

    setupFormValidation()
    {
        // Real-time validation for amount inputs
        $('input[type="number"]').on('input', function () {
            const value = parseFloat($(this).val());
            const isValid = !isNaN(value) && value > 0 && value <= 999999;

            $(this).toggleClass('is-invalid', !isValid && $(this).val() !== '');
            $(this).toggleClass('is-valid', isValid);
        });

        // Real-time validation for reason inputs
        $('textarea[required]').on('input', function () {
            const isValid = $(this).val().trim().length >= 3;

            $(this).toggleClass('is-invalid', !isValid && $(this).val() !== '');
            $(this).toggleClass('is-valid', isValid);
        });
    }

    setupRealTimeUpdates()
    {
        // Auto-refresh balance every 30 seconds (disabled when global ADMIN_AUTO_REFRESH=false)
    // Automatic periodic balance refresh removed by policy. Manual refresh remains available.

        // Refresh when page becomes visible
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible' && !this.isLoading) {
                this.refreshStats();
            }
        });
    }

    formatCurrency(amount)
    {
        // Get default currency injected from backend or fallback to USD
        const defaultCurrency = window.defaultCurrency || window.appCurrency || { code: 'USD', symbol: '$' };
        const locale = document.documentElement.lang || 'en-US';
        return new Intl.NumberFormat(locale, {
            style: 'currency',
            currency: defaultCurrency.code
        }).format(amount);
    }

    formatDate(dateString)
    {
        const date = new Date(dateString);
        const locale = document.documentElement.lang || 'en-US';
        return new Intl.DateTimeFormat(locale, {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(date);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    // Get user ID from the page
    const userIdElement = document.querySelector('[data-user-id]');
    if (userIdElement) {
        const userId = userIdElement.getAttribute('data-user-id');
        window.balanceManager = new UserBalanceManager(userId);
    } else {
        // Try to get user ID from URL or other sources
        const pathParts = window.location.pathname.split('/');
        const userIndex = pathParts.indexOf('users');
        if (userIndex !== -1 && pathParts[userIndex + 1]) {
            const userId = pathParts[userIndex + 1];
            if (userId !== 'balance' && !isNaN(userId)) {
                window.balanceManager = new UserBalanceManager(userId);
            }
        }
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = UserBalanceManager;
}