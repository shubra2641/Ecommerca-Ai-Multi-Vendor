/**
 * Admin JavaScript - Optimized and Simplified
 * Consolidated JS for Admin Panel with improved maintainability
 * Features: Progressive enhancement, no inline JS, unified structure
 */

/* global window, document, fetch, URL, FormData, setTimeout, clearTimeout, console, Intl, AdminPanel, requestAnimationFrame */

(function () {
    'use strict';

    // Admin namespace
    window.AdminPanel = window.AdminPanel || {};

    // Configuration constants
    const CONFIG = {
        AUTO_SAVE_DELAY: 2000,
        NOTIFICATION_DURATION: 5000,
        SIDEBAR_BREAKPOINT: 992
    };

    // Utility functions
    const Utils = {
        // Safe element selection
        select: (selector, context = document) => context.querySelector(selector),
        selectAll: (selector, context = document) => Array.from(context.querySelectorAll(selector)),

        // Event handling
        on: (element, event, handler) => element?.addEventListener(event, handler),
        off: (element, event, handler) => element?.removeEventListener(event, handler),

        // DOM manipulation
        addClass: (element, className) => element?.classList.add(className),
        removeClass: (element, className) => element?.classList.remove(className),
        toggleClass: (element, className) => element?.classList.toggle(className),
        hasClass: (element, className) => element?.classList.contains(className),

        // AJAX helpers
        fetch: async (url, options = {}) => {
            // Validate URL to prevent SSRF attacks
            try {
                const urlObj = new URL(url, window.location.origin);
                if (urlObj.origin !== window.location.origin) {
                    throw new Error('Cross-origin requests not allowed');
                }
            } catch (e) {
                throw new Error('Invalid URL');
            }

            const defaultOptions = {
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': Utils.getCSRFToken()
                }
            };
            return fetch(url, { ...defaultOptions, ...options });
        },

        getCSRFToken: () => {
            const meta = Utils.select('meta[name="csrf-token"]');
            return meta?.getAttribute('content') || '';
        },

        // Format currency
        formatCurrency: (amount, currency = { symbol: '$', code: 'USD' }) => {
            try {
                return new Intl.NumberFormat(document.documentElement.lang || 'en', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(parseFloat(amount || 0)) + ' ' + currency.symbol;
            } catch (e) {
                return (parseFloat(amount || 0).toFixed(2) + ' ' + currency.symbol);
            }
        }
    };

    // Sidebar Manager
    const SidebarManager = {
        init() {
            this.sidebar = Utils.select('.admin-sidebar, .modern-sidebar, .vendor-sidebar, #sidebar');
            this.toggles = Utils.selectAll('.sidebar-toggle, .mobile-menu-toggle');
            this.overlay = this.createOverlay();
            this.bindEvents();
        },

        createOverlay() {
            let overlay = Utils.select('.sidebar-overlay');
            if (!overlay && this.sidebar?.parentNode) {
                overlay = document.createElement('div');
                overlay.className = 'sidebar-overlay';
                this.sidebar.parentNode.insertBefore(overlay, this.sidebar.nextSibling);
            }
            return overlay;
        },

        bindEvents() {
            this.toggles.forEach(toggle => {
                Utils.on(toggle, 'click', this.toggle.bind(this));
            });

            Utils.on(this.overlay, 'click', this.close.bind(this));
            Utils.on(document, 'click', this.handleNavClick.bind(this));
        },

        toggle(e) {
            e?.preventDefault();
            if (!this.sidebar) return;
            Utils.toggleClass(this.sidebar, 'active');
            Utils.toggleClass(this.overlay, 'active');
        },

        close() {
            if (!this.sidebar) return;
            Utils.removeClass(this.sidebar, 'active');
            Utils.removeClass(this.overlay, 'active');
        },

        handleNavClick(e) {
            if (!this.sidebar) return;
            const navItem = e.target.closest('.sidebar-nav a, .nav-item');
            if (!navItem) return;

            if (window.innerWidth <= CONFIG.SIDEBAR_BREAKPOINT && Utils.hasClass(this.sidebar, 'active')) {
                this.close();
            }
        }
    };

    // Dropdown Manager - Simple dropdown functionality
    const DropdownManager = {
        init() {
            this.dropdowns = Utils.selectAll('.dropdown, .nav-dropdown');
            this.bindEvents();

            // Initialize dropdowns

            // Initialize all dropdowns as closed
            this.dropdowns.forEach(dropdown => {
                const menu = Utils.select('.dropdown-menu', dropdown);
                if (menu) {
                    menu.style.display = 'none';
                }
            });
        },

        bindEvents() {
            this.dropdowns.forEach(dropdown => {
                const toggle = Utils.select('.dropdown-toggle, .nav-item.dropdown-toggle', dropdown);
                const menu = Utils.select('.dropdown-menu', dropdown);

                if (toggle && menu) {
                    // Handle both data-bs-toggle and regular dropdowns
                    if (toggle.hasAttribute('data-bs-toggle') || toggle.classList.contains('dropdown-toggle')) {
                        Utils.on(toggle, 'click', (e) => this.handleToggle(e, dropdown, toggle, menu));
                        // Dropdown bound
                    }
                }
            });

            // Close dropdowns when clicking outside
            Utils.on(document, 'click', (e) => this.handleOutsideClick(e));
        },

        handleToggle(e, dropdown, toggle, menu) {
            e.preventDefault();
            e.stopPropagation();

            // Dropdown clicked

            // Close other dropdowns
            this.closeAllDropdowns(dropdown);

            // Toggle current dropdown
            const isOpen = Utils.hasClass(dropdown, 'show');
            if (isOpen) {
                this.closeDropdown(dropdown, toggle, menu);
            } else {
                this.openDropdown(dropdown, toggle, menu);
            }
        },

        openDropdown(dropdown, toggle, menu) {
            Utils.addClass(dropdown, 'show');
            Utils.addClass(menu, 'show');
            toggle.setAttribute('aria-expanded', 'true');

            // Opening dropdown

            // Force display and remove inline styles that might conflict
            menu.style.display = 'block';
            menu.style.opacity = '1';
            menu.style.transform = 'translateY(0)';
            menu.style.maxHeight = '500px';
        },

        closeDropdown(dropdown, toggle, menu) {
            Utils.removeClass(dropdown, 'show');
            Utils.removeClass(menu, 'show');
            toggle.setAttribute('aria-expanded', 'false');

            // Closing dropdown

            // Force hide
            menu.style.display = 'none';
            menu.style.opacity = '0';
            menu.style.transform = 'translateY(-10px)';
            menu.style.maxHeight = '0';
        },

        closeAllDropdowns(excludeDropdown = null) {
            this.dropdowns.forEach(dropdown => {
                if (dropdown !== excludeDropdown) {
                    const toggle = Utils.select('.dropdown-toggle, .nav-item.dropdown-toggle', dropdown);
                    const menu = Utils.select('.dropdown-menu', dropdown);
                    if (toggle && menu) {
                        this.closeDropdown(dropdown, toggle, menu);
                    }
                }
            });
        },

        handleOutsideClick(e) {
            const clickedDropdown = e.target.closest('.dropdown, .nav-dropdown');
            if (!clickedDropdown) {
                this.closeAllDropdowns();
            }
        }
    };

    // Table Manager
    const TableManager = {
        init() {
            const tables = Utils.selectAll('.admin-table');
            tables.forEach(table => {
                this.initSorting(table);
                this.initSelection(table);
            });
        },

        initSorting(table) {
            const headers = Utils.selectAll('th[data-sortable]', table);
            headers.forEach(header => {
                header.style.cursor = 'pointer';
                Utils.on(header, 'click', () => this.sortTable(table, header));
            });
        },

        sortTable(table, header) {
            const columnIndex = Array.from(header.parentNode.children).indexOf(header);
            const rows = Array.from(Utils.selectAll('tbody tr', table));
            const isAscending = !Utils.hasClass(header, 'sort-asc');

            rows.sort((a, b) => {
                const aText = a.children[columnIndex].textContent.trim();
                const bText = b.children[columnIndex].textContent.trim();
                return isAscending ? aText.localeCompare(bText) : bText.localeCompare(aText);
            });

            // Update sort classes
            Utils.selectAll('th', table).forEach(th => {
                Utils.removeClass(th, 'sort-asc');
                Utils.removeClass(th, 'sort-desc');
            });
            Utils.addClass(header, isAscending ? 'sort-asc' : 'sort-desc');

            // Reorder rows
            const tbody = Utils.select('tbody', table);
            rows.forEach(row => tbody.appendChild(row));
        },

        initSelection(table) {
            const selectAll = Utils.select('thead input[type="checkbox"]', table);
            const rowCheckboxes = Utils.selectAll('tbody input[type="checkbox"]', table);

            if (selectAll) {
                Utils.on(selectAll, 'change', () => {
                    rowCheckboxes.forEach(checkbox => {
                        checkbox.checked = selectAll.checked;
                        this.updateRowSelection(checkbox);
                    });
                });
            }

            rowCheckboxes.forEach(checkbox => {
                Utils.on(checkbox, 'change', () => {
                    this.updateRowSelection(checkbox);
                    this.updateSelectAll(table);
                });
            });
        },

        updateRowSelection(checkbox) {
            const row = checkbox.closest('tr');
            Utils.toggleClass(row, 'selected', checkbox.checked);
        },

        updateSelectAll(table) {
            const selectAll = Utils.select('thead input[type="checkbox"]', table);
            const rowCheckboxes = Utils.selectAll('tbody input[type="checkbox"]', table);
            const checkedBoxes = Utils.selectAll('tbody input[type="checkbox"]:checked', table);

            if (selectAll) {
                selectAll.checked = checkedBoxes.length === rowCheckboxes.length;
                selectAll.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < rowCheckboxes.length;
            }
        }
    };

    // Form Manager
    const FormManager = {
        init() {
            const forms = Utils.selectAll('.admin-form');
            forms.forEach(form => {
                this.initValidation(form);
                if (form.hasAttribute('data-auto-save')) {
                    this.initAutoSave(form);
                }
            });
        },

        initValidation(form) {
            Utils.on(form, 'submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });
        },

        validateForm(form) {
            let isValid = true;
            const requiredFields = Utils.selectAll('[required]', form);

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    this.showFieldError(field, 'This field is required');
                    isValid = false;
                } else {
                    this.clearFieldError(field);
                }
            });

            return isValid;
        },

        showFieldError(field, message) {
            this.clearFieldError(field);
            Utils.addClass(field, 'error');

            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        },

        clearFieldError(field) {
            Utils.removeClass(field, 'error');
            const existingError = Utils.select('.field-error', field.parentNode);
            existingError?.remove();
        },

        initAutoSave(form) {
            const fields = Utils.selectAll('input, textarea, select', form);
            let saveTimeout;

            fields.forEach(field => {
                Utils.on(field, 'input', () => {
                    clearTimeout(saveTimeout);
                    saveTimeout = setTimeout(() => this.autoSave(form), CONFIG.AUTO_SAVE_DELAY);
                });
            });
        },

        autoSave(form) {
            const formData = new FormData(form);
            const autoSaveUrl = form.getAttribute('data-auto-save-url');

            if (autoSaveUrl) {
                Utils.fetch(autoSaveUrl, {
                    method: 'POST',
                    body: formData
                }).then(response => {
                    if (response.ok) {
                        NotificationManager.show('Changes saved automatically', 'success');
                    }
                }).catch(() => { /* Auto-save failed silently */ });
            }
        }
    };

    // Modal Manager
    const ModalManager = {
        init() {
            this.bindTriggers();
            this.bindCloseHandlers();
        },

        bindTriggers() {
            const triggers = Utils.selectAll('[data-modal]');
            triggers.forEach(trigger => {
                Utils.on(trigger, 'click', (e) => {
                    e.preventDefault();
                    const modalId = trigger.getAttribute('data-modal');
                    this.open(modalId);
                });
            });
        },

        bindCloseHandlers() {
            Utils.on(document, 'click', (e) => {
                if (Utils.hasClass(e.target, 'modal-overlay') || Utils.hasClass(e.target, 'modal-close')) {
                    this.close();
                }
            });

            Utils.on(document, 'keydown', (e) => {
                if (e.key === 'Escape') {
                    this.close();
                }
            });
        },

        open(modalId) {
            const modal = Utils.select(`#${modalId}`);
            if (modal) {
                Utils.addClass(modal, 'active');
                Utils.addClass(document.body, 'modal-open');
            }
        },

        close() {
            const activeModal = Utils.select('.modal.active');
            if (activeModal) {
                Utils.removeClass(activeModal, 'active');
                Utils.removeClass(document.body, 'modal-open');
            }
        }
    };

    // Notification Manager
    const NotificationManager = {
        init() {
            this.initAutoHide();
            this.bindCloseHandlers();
        },

        initAutoHide() {
            const notifications = Utils.selectAll('.notification');
            notifications.forEach(notification => {
                if (notification.hasAttribute('data-auto-hide')) {
                    setTimeout(() => this.hide(notification), CONFIG.NOTIFICATION_DURATION);
                }
            });
        },

        bindCloseHandlers() {
            Utils.on(document, 'click', (e) => {
                if (Utils.hasClass(e.target, 'notification-close')) {
                    const notification = e.target.closest('.notification');
                    this.hide(notification);
                }
            });
        },

        show(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            const content = document.createElement('div');
            content.className = 'notification-content';

            const messageDiv = document.createElement('div');
            messageDiv.className = 'notification-message';
            messageDiv.textContent = message;

            const closeBtn = document.createElement('button');
            closeBtn.className = 'notification-close';
            closeBtn.type = 'button';
            closeBtn.setAttribute('aria-label', 'Close notification');
            closeBtn.textContent = '×';

            content.appendChild(messageDiv);
            content.appendChild(closeBtn);
            notification.appendChild(content);

            let container = Utils.select('.notification-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'notification-container';
                document.body.appendChild(container);
            }

            container.appendChild(notification);

            const autoHide = setTimeout(() => this.hide(notification), CONFIG.NOTIFICATION_DURATION);

            const closeButton = Utils.select('.notification-close', notification);
            if (closeButton) {
                Utils.on(closeButton, 'click', () => {
                    clearTimeout(autoHide);
                    this.hide(notification);
                });
            }
        },

        hide(notification) {
            if (notification) {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }
        }
    };

    // User Balance Manager
    const UserBalanceManager = {
        init() {
            const config = this.getConfig();
            if (!config) return;

            this.config = config;
            this.bindEvents();
            setTimeout(() => this.refreshStats(), 400);
        },

        getConfig() {
            const tpl = Utils.select('#user-balance-config');
            if (!tpl) return null;

            try {
                return JSON.parse(tpl.textContent || tpl.innerText || '{}');
            } catch (e) {
                // JSON parse error handled silently
                return null;
            }
        },

        bindEvents() {
            Utils.selectAll('.btn-refresh-balance').forEach(btn => {
                Utils.on(btn, 'click', (e) => {
                    e.preventDefault();
                    this.refreshStats();
                });
            });

            Utils.selectAll('.btn-add-balance').forEach(btn => {
                Utils.on(btn, 'click', (e) => {
                    e.preventDefault();
                    ModalManager.open('addBalanceModal');
                });
            });

            Utils.selectAll('.btn-deduct-balance').forEach(btn => {
                Utils.on(btn, 'click', (e) => {
                    e.preventDefault();
                    ModalManager.open('deductBalanceModal');
                });
            });

            Utils.selectAll('.btn-view-history').forEach(btn => {
                Utils.on(btn, 'click', (e) => {
                    e.preventDefault();
                    this.viewHistory();
                });
            });

            this.wireForm('addBalanceForm', 'add', 'balance_added');
            this.wireForm('deductBalanceForm', 'deduct', 'balance_deducted');
        },

        async refreshStats() {
            if (!this.config.urls?.stats) return;

            try {
                const response = await Utils.fetch(this.config.urls.stats);
                if (!response.ok) throw new Error('Network response not ok');
                const data = await response.json();

                this.updateBalanceDisplay(data);
                NotificationManager.show(
                    this.config.i18n?.balance_refreshed || 'Data refreshed',
                    'success'
                );
            } catch (err) {
                // Failed to refresh balance stats
                NotificationManager.show(
                    this.config.i18n?.error_refresh || 'Failed to refresh',
                    'danger'
                );
            }
        },

        updateBalanceDisplay(data) {
            const balanceEls = Utils.selectAll('[data-countup][data-target]');
            balanceEls.forEach(el => {
                const key = el.getAttribute('data-stat') || el.getAttribute('data-key');
                if (key && data && key in data) {
                    el.textContent = Utils.formatCurrency(data[key], this.config.currency);
                    el.dataset.target = Number(data[key]);
                    delete el.dataset.counted;
                }
            });

            ['total_added', 'total_deducted', 'net_balance_change', 'balance'].forEach(key => {
                const element = Utils.select(`[data-stat="${key}"]`);
                if (element && Object.prototype.hasOwnProperty.call(data, key)) {
                    element.textContent = Utils.formatCurrency(data[key], this.config.currency);
                }
            });
        },

        async viewHistory() {
            ModalManager.open('balanceHistoryModal');
            const container = Utils.select('#balanceHistoryContainer');
            if (!container || !this.config.urls?.history) return;

            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'text-center p-4';

            const spinner = document.createElement('div');
            spinner.className = 'loading-spinner mx-auto';

            const text = document.createElement('p');
            text.className = 'mt-2';
            text.textContent = this.config.i18n?.loading_history || 'Loading history...';

            loadingDiv.appendChild(spinner);
            loadingDiv.appendChild(text);
            container.appendChild(loadingDiv);

            try {
                const response = await Utils.fetch(this.config.urls.history);
                if (!response.ok) throw new Error('Network response not ok');
                const html = await response.text();
                if (html) {
                    // Create a temporary container to parse HTML safely
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;

                    // Move all child nodes to the container
                    while (tempDiv.firstChild) {
                        container.appendChild(tempDiv.firstChild);
                    }
                } else {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'empty-state text-center p-4';
                    emptyDiv.textContent = this.config.i18n?.no_history_desc || 'No previous transactions found';
                    container.appendChild(emptyDiv);
                }
            } catch (err) {
                    // Failed to load history
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger';
                errorDiv.textContent = this.config.i18n?.error_history || 'Failed to load balance history';
                container.appendChild(errorDiv);
            }
        },

        wireForm(formId, urlKey, successMessageKey) {
            const form = Utils.select(`#${formId}`);
            if (!form) return;

            Utils.on(form, 'submit', async (e) => {
                e.preventDefault();
                const submitBtn = Utils.select('button[type="submit"]', form);
                if (submitBtn) submitBtn.disabled = true;

                try {
                    const formData = new FormData(form);
                    const response = await Utils.fetch(this.config.urls[urlKey], {
                        method: 'POST',
                        body: formData
                    });
                    const json = await response.json();

                    if (response.ok) {
                        NotificationManager.show(
                            this.config.i18n?.[successMessageKey] || 'Success',
                            'success'
                        );
                        ModalManager.close();
                        this.refreshStats();
                    } else {
                        NotificationManager.show(
                            json.message || this.config.i18n?.error_server || 'Error',
                            'danger'
                        );
                    }
                } catch (err) {
                    // Form submit failed
                    NotificationManager.show(
                        this.config.i18n?.error_server || 'Server error',
                        'danger'
                    );
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                }
            });
        }
    };

    // Confirmation Manager
    const ConfirmationManager = {
        init() {
            this.bindFormConfirmations();
            this.bindElementConfirmations();
        },

        bindFormConfirmations() {
            Utils.selectAll('form.js-confirm, form.js-confirm-delete').forEach(form => {
                Utils.on(form, 'submit', (e) => {
                    const msg = form.dataset.confirm || form.getAttribute('data-confirm') || 'Are you sure?';
                    if (!window.confirm(msg)) {
                        e.preventDefault();
                    }
                });
            });
        },

        bindElementConfirmations() {
            Utils.selectAll('[data-confirm]').forEach(element => {
                Utils.on(element, 'click', (e) => {
                    const msg = element.getAttribute('data-confirm');
                    if (!window.confirm(msg)) {
                        e.preventDefault();
                    }
                });
            });
        }
    };

    // Main initialization
    AdminPanel.init = function () {
        SidebarManager.init();
        DropdownManager.init();
        TableManager.init();
        FormManager.init();
        ModalManager.init();
        UserBalanceManager.init();
        ConfirmationManager.init();
        NotificationManager.init();
    };

    // Initialize when DOM is ready
    function initAdmin() {
        if (typeof AdminPanel.init === 'function') {
            AdminPanel.init();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdmin);
    } else {
        initAdmin();
    }

    // Expose utility functions for external use
    if (typeof window.AdminPanel !== 'undefined') {
        window.AdminPanel.Utils = Utils;
        window.AdminPanel.NotificationManager = NotificationManager;
        window.AdminPanel.ModalManager = ModalManager;
    }

})();