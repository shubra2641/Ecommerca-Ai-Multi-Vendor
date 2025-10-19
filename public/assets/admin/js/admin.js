/* Admin JavaScript - Consolidated JS for Admin Panel
 * This file contains all JavaScript functionality for the admin interface
 * Following rules: Progressive enhancement, no inline JS, unified structure
 */
/* global AdminPanel */

(function() {
    'use strict';

    // Admin namespace
    window.AdminPanel = window.AdminPanel || {};

    // Initialize admin functionality
    AdminPanel.init = function() {
        this.initSidebar();
        this.initTables();
        this.initForms();
        this.initModals();
        this.initUserBalance();
        this.initConfirmations();
        this.initNotifications();
    };

    // Sidebar functionality
    AdminPanel.initSidebar = function() {
        // Find sidebar element across admin/vendor layouts
        const sidebar = document.querySelector('.admin-sidebar, .modern-sidebar, .vendor-sidebar, #sidebar');
        // Support multiple toggles (desktop compact, mobile toggle)
        const toggles = Array.from(document.querySelectorAll('.sidebar-toggle, .mobile-menu-toggle'));
        let overlay = document.querySelector('.sidebar-overlay');

        // If overlay missing, create one and append after sidebar to keep markup consistent
        if (!overlay && sidebar && sidebar.parentNode) {
            overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            sidebar.parentNode.insertBefore(overlay, sidebar.nextSibling);
        }

        // toggle handler
        function toggleSidebar(e) {
            if (e) {
                e.preventDefault();
            }
            if (!sidebar) { return; }
            sidebar.classList.toggle('active');
            if (overlay) { overlay.classList.toggle('active'); }
        }

        // attach to all toggles
        toggles.forEach((t) => {
            t.addEventListener('click', toggleSidebar);
        });

        // Close sidebar when clicking overlay
        if (overlay) {
            overlay.addEventListener('click', () => {
                if (!sidebar) { return; }
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }

        // Close sidebar when clicking a nav item on small screens
        document.addEventListener('click', (e) => {
            if (!sidebar) { return; }
            const navItem = e.target.closest('.sidebar-nav a, .nav-item');
            if (!navItem) { return; }
            // only auto-close for narrower viewports where sidebar overlays content
            const MOBILE_BREAKPOINT = 992;
            if (window.innerWidth <= MOBILE_BREAKPOINT && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                if (overlay) { overlay.classList.remove('active'); }
            }
        });
    };

    // Enhanced table functionality
    AdminPanel.initTables = function() {
        const tables = document.querySelectorAll('.admin-table');

        tables.forEach((table) => {
            // Add sorting functionality
            const headers = table.querySelectorAll('th[data-sortable]');
            headers.forEach((header) => {
                header.style.cursor = 'pointer';
                header.addEventListener('click', () => {
                    AdminPanel.sortTable(table, header);
                });
            });

            // Add row selection
            const checkboxes = table.querySelectorAll('input[type="checkbox"]');
            if (checkboxes.length > 0) {
                AdminPanel.initTableSelection(table);
            }
        });
    };

    // Table sorting
    AdminPanel.sortTable = function(table, header) {
        const columnIndex = Array.from(header.parentNode.children).indexOf(header);
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        const isAscending = !header.classList.contains('sort-asc');

        rows.sort((a, b) => {
            const aText = a.children[columnIndex].textContent.trim();
            const bText = b.children[columnIndex].textContent.trim();

            if (isAscending) {
                return aText.localeCompare(bText);
            }
            return bText.localeCompare(aText);
        });

        // Remove existing sort classes
        table.querySelectorAll('th').forEach((th) => {
            th.classList.remove('sort-asc', 'sort-desc');
        });

        // Add sort class to current header
        header.classList.add(isAscending ? 'sort-asc' : 'sort-desc');

        // Reorder rows
        const tbody = table.querySelector('tbody');
        rows.forEach((row) => {
            tbody.appendChild(row);
        });
    };

    // Table selection functionality
    AdminPanel.initTableSelection = function(table) {
        const selectAll = table.querySelector('thead input[type="checkbox"]');
        const rowCheckboxes = table.querySelectorAll('tbody input[type="checkbox"]');

        if (selectAll) {
            selectAll.addEventListener('change', () => {
                rowCheckboxes.forEach((checkbox) => {
                    checkbox.checked = selectAll.checked;
                    AdminPanel.updateRowSelection(checkbox);
                });
            });
        }

        rowCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                AdminPanel.updateRowSelection(checkbox);
                AdminPanel.updateSelectAll(table);
            });
        });
    };

    // Update row selection visual state
    AdminPanel.updateRowSelection = function(checkbox) {
        const row = checkbox.closest('tr');
        if (checkbox.checked) {
            row.classList.add('selected');
        } else {
            row.classList.remove('selected');
        }
    };

    // Update select all checkbox state
    AdminPanel.updateSelectAll = function(table) {
        const selectAll = table.querySelector('thead input[type="checkbox"]');
        const rowCheckboxes = table.querySelectorAll('tbody input[type="checkbox"]');
        const checkedBoxes = table.querySelectorAll('tbody input[type="checkbox"]:checked');

        if (selectAll) {
            selectAll.checked = checkedBoxes.length === rowCheckboxes.length;
            selectAll.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < rowCheckboxes.length;
        }
    };

    // Form enhancements
    AdminPanel.initForms = function() {
        const forms = document.querySelectorAll('.admin-form');

        forms.forEach((form) => {
            // Add form validation
            form.addEventListener('submit', (e) => {
                if (!AdminPanel.validateForm(form)) {
                    e.preventDefault();
                }
            });

            // Auto-save functionality
            if (form.hasAttribute('data-auto-save')) {
                AdminPanel.initAutoSave(form);
            }
        });
    };

    // Form validation
    AdminPanel.validateForm = function(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');

        requiredFields.forEach((field) => {
            if (!field.value.trim()) {
                AdminPanel.showFieldError(field, 'This field is required');
                isValid = false;
            } else {
                AdminPanel.clearFieldError(field);
            }
        });

        return isValid;
    };

    // Show field error
    AdminPanel.showFieldError = function(field, message) {
        AdminPanel.clearFieldError(field);

        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;

        field.classList.add('error');
        field.parentNode.appendChild(errorDiv);
    };

    // Clear field error
    AdminPanel.clearFieldError = function(field) {
        field.classList.remove('error');
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    };

    // Modal functionality
    AdminPanel.initModals = function() {
        const modalTriggers = document.querySelectorAll('[data-modal]');

        modalTriggers.forEach((trigger) => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const modalId = trigger.getAttribute('data-modal');
                AdminPanel.openModal(modalId);
            });
        });

        // Close modal functionality
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay') || e.target.classList.contains('modal-close')) {
                AdminPanel.closeModal();
            }
        });

        // Escape key to close modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                AdminPanel.closeModal();
            }
        });
    };

    // Open modal
    AdminPanel.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.classList.add('modal-open');
        }
    };

    // Close modal
    AdminPanel.closeModal = function() {
        const activeModal = document.querySelector('.modal.active');
        if (activeModal) {
            activeModal.classList.remove('active');
            document.body.classList.remove('modal-open');
        }
    };

    // Notification system
    AdminPanel.initNotifications = function() {
        // Auto-hide notifications
        const notifications = document.querySelectorAll('.notification');
        notifications.forEach((notification) => {
            if (notification.hasAttribute('data-auto-hide')) {
                setTimeout(() => {
                    AdminPanel.hideNotification(notification);
                }, 5000); // eslint-disable-line no-magic-numbers
            }
        });

        // Close notification buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('notification-close')) {
                const notification = e.target.closest('.notification');
                AdminPanel.hideNotification(notification);
            }
        });
    };

    // User Balance page adapter
    AdminPanel.initUserBalance = function() {
        // Look for the config template inserted by the Blade view
        const tpl = document.getElementById('user-balance-config');
        if (!tpl) { return; } // not present on this page

        let cfg = {};
        try {
            cfg = JSON.parse(tpl.textContent || tpl.innerText || '{}');
        } catch {
            // console.error('user-balance-config JSON parse error');
            return;
        }

        const urls = (cfg.urls) ? cfg.urls : {};
        const currency = (cfg.currency) ? cfg.currency : {
            code: 'USD',
            symbol: '$'
        };

        function fmtAmount(v) {
            try {
                return new Intl.NumberFormat(document.documentElement.lang || 'en', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(parseFloat(v || 0)) + ' ' + (currency.symbol || '$');
            } catch {
                return (parseFloat(v || 0).toFixed(2) + ' ' + (currency.symbol || '$')); // eslint-disable-line no-magic-numbers
            }
        }

        // Refresh stats from server and update DOM
        async function refreshStats() {
            if (!urls.stats) { return; }
            try {
                const res = await fetch(urls.stats, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) { throw new Error('Network response not ok'); }
                const data = await res.json();

                // Update balance display
                const balanceEls = document.querySelectorAll('[data-countup][data-target]');
                balanceEls.forEach((el) => {
                    const key = el.getAttribute('data-stat') || el.getAttribute('data-key');
                    if (key && Object.prototype.hasOwnProperty.call(data, key)) {
                        el.textContent = fmtAmount(data[key]);
                        // update dataset target for potential CountUp observers
                        el.dataset.target = Number(data[key]);
                        delete el.dataset.counted; // allow re-animate if CountUp observes again
                    }
                });

                // Fallback: update elements with specific data-stat attributes
                ['total_added', 'total_deducted', 'net_balance_change', 'balance'].forEach((k) => {
                    const sel = document.querySelector('[data-stat="' + k + '"]');
                    if (sel && Object.prototype.hasOwnProperty.call(data, k)) {
                        sel.textContent = fmtAmount(data[k]);
                    }
                });

                AdminPanel.showNotification(cfg.i18n && cfg.i18n.balance_refreshed ? cfg.i18n.balance_refreshed : 'Data refreshed', 'success');
            } catch {
                // console.error('Failed to refresh balance stats');
                AdminPanel.showNotification(cfg.i18n && cfg.i18n.error_refresh ? cfg.i18n.error_refresh : 'Failed to refresh', 'danger');
            }
        }

        // Wire refresh buttons
        document.querySelectorAll('.btn-refresh-balance').forEach((btn) => {
            btn.addEventListener('click', (e) => { e.preventDefault(); refreshStats(); });
        });

        // Open modals for add / deduct
        document.querySelectorAll('.btn-add-balance').forEach((btn) => {
            btn.addEventListener('click', (e) => { e.preventDefault(); AdminPanel.openModal('addBalanceModal'); });
        });
        document.querySelectorAll('.btn-deduct-balance').forEach((btn) => {
            btn.addEventListener('click', (e) => { e.preventDefault(); AdminPanel.openModal('deductBalanceModal'); });
        });

        // View history
        document.querySelectorAll('.btn-view-history').forEach((btn) => {
            btn.addEventListener('click', async(e) => {
                e.preventDefault();
                await handleHistoryView(cfg, urls);
            });
        });

        async function handleHistoryView(cfg, urls) {
            AdminPanel.openModal('balanceHistoryModal');
            const container = document.getElementById('balanceHistoryContainer');
            if (!container || !urls.history) { return; }

            showLoadingState(container, cfg);
            await loadHistoryData(container, urls, cfg);
        }

        function showLoadingState(container, cfg) {
            container.innerHTML = '<div class="text-center p-4"><div class="loading-spinner mx-auto"></div><p class="mt-2">' +
                (cfg.i18n && cfg.i18n.loading_history ? cfg.i18n.loading_history : 'Loading history...') + '</p></div>';
        }

        async function loadHistoryData(container, urls, cfg) {
            try {
                const res = await fetch(urls.history, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) { throw new Error('Network response not ok'); }
                const html = await res.text();
                container.innerHTML = html || ('<div class="empty-state text-center p-4">' +
                    (cfg.i18n && cfg.i18n.no_history_desc ? cfg.i18n.no_history_desc : 'No previous transactions found') + '</div>');
            } catch {
                // console.error('Failed to load history');
                container.innerHTML = '<div class="alert alert-danger">' +
                    (cfg.i18n && cfg.i18n.error_history ? cfg.i18n.error_history : 'Failed to load balance history') + '</div>';
            }
        }

        // AJAX form submit for add/deduct
        function wireForm(formId, urlKey, successMessageKey) {
            const form = document.getElementById(formId);
            if (!form) { return; }
            form.addEventListener('submit', async(e) => {
                e.preventDefault();
                await handleFormSubmit(form, urlKey, successMessageKey);
            });
        }

        async function handleFormSubmit(form, urlKey, successMessageKey) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) { submitBtn.disabled = true; }

            try {
                const result = await submitFormData(form, urlKey);
                handleFormResponse(result, successMessageKey);
            } catch {
                handleFormError();
            } finally {
                if (submitBtn) { submitBtn.disabled = false; }
            }
        }

        async function submitFormData(form, urlKey) {
            const formData = new FormData(form);
            const url = urls[urlKey];
            const res = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || ''
                }
            });
            const json = await res.json();
            return {
                res,
                json
            };
        }

        function handleFormResponse(result, successMessageKey) {
            const { res, json } = result;
            if (res.ok) {
                AdminPanel.showNotification((cfg.i18n && cfg.i18n[successMessageKey]) || 'Success', 'success');
                AdminPanel.closeModal();
                refreshStats();
            } else {
                AdminPanel.showNotification(json.message || (cfg.i18n && cfg.i18n.error_server) || 'Error', 'danger');
            }
        }

        function handleFormError() {
            // console.error('Form submit failed');
            AdminPanel.showNotification((cfg.i18n && cfg.i18n.error_server) || 'Server error', 'danger');
        }

        wireForm('addBalanceForm', 'add', 'balance_added');
        wireForm('deductBalanceForm', 'deduct', 'balance_deducted');

        // initial refresh to populate numbers if needed
        setTimeout(refreshStats, 400); // eslint-disable-line no-magic-numbers
    };

    // Centralized confirm handlers for forms and links
    AdminPanel.initConfirmations = function() {
        // Forms with js-confirm or js-confirm-delete
        document.querySelectorAll('form.js-confirm, form.js-confirm-delete').forEach((form) => {
            form.addEventListener('submit', (e) => {
                const msg = form.dataset.confirm || form.getAttribute('data-confirm') || 'Are you sure?';
                if (!window.confirm(msg)) { // eslint-disable-line no-alert
                    e.preventDefault();
                }
            });
        });

        // Links/buttons with data-confirm attribute
        document.querySelectorAll('[data-confirm]').forEach((el) => {
            // only handle anchors or buttons that navigate or submit forms
            el.addEventListener('click', (e) => {
                const msg = el.getAttribute('data-confirm');
                if (!window.confirm(msg)) { // eslint-disable-line no-alert
                    e.preventDefault();
                }
            });
        });
    };

    // Hide notification
    AdminPanel.hideNotification = function(notification) {
        if (notification) {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300); // eslint-disable-line no-magic-numbers
        }
    };

    // Show notification
    AdminPanel.showNotification = function(message, type) {
        const notificationType = type || 'info';
        const notification = document.createElement('div');
        notification.className = 'notification notification-' + notificationType;
        notification.innerHTML = '<div class="notification-content"><div class="notification-message">' + message + '</div><button class="notification-close" type="button" aria-label="Close notification">&times;</button></div>';

        // Ensure a dedicated container exists
        let container = document.querySelector('.notification-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'notification-container';
            // Prefer a top-right position via CSS; append to body
            document.body.appendChild(container);
        }

        container.appendChild(notification);

        // Auto-hide after 5 seconds
        const autoHide = setTimeout(() => {
            AdminPanel.hideNotification(notification);
        }, 5000); // eslint-disable-line no-magic-numbers

        // Close handler
        const closeBtn = notification.querySelector('.notification-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                clearTimeout(autoHide);
                AdminPanel.hideNotification(notification);
            });
        }
    };

    // Auto-save functionality
    AdminPanel.initAutoSave = function(form) {
        const fields = form.querySelectorAll('input, textarea, select');
        let saveTimeout;

        fields.forEach((field) => {
            field.addEventListener('input', () => {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    AdminPanel.autoSave(form);
                }, 2000); // eslint-disable-line no-magic-numbers
            });
        });
    };

    // Auto-save implementation
    AdminPanel.autoSave = function(form) {
        const formData = new FormData(form);
        const autoSaveUrl = form.getAttribute('data-auto-save-url');

        if (autoSaveUrl) {
            fetch(autoSaveUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then((response) => {
                    if (response.ok) {
                        AdminPanel.showNotification('Changes saved automatically', 'success');
                    }
                })
                .catch(() => {
                    // console.error('Auto-save failed');
                });
        }
    };

    // Initialize when DOM is loaded (ensure correct `this` binding)
    function _adminInit() {
        // Call init with AdminPanel as `this` to ensure methods referenced via `this` are found
        if (typeof AdminPanel.init === 'function') {
            AdminPanel.init();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', _adminInit);
    } else {
        _adminInit();
    }

    /* ===== Admin Notifications Loader (migrated from admin-notifications.js) ===== */
    (function() {
        // Defer execution until DOM ready
        function initNotificationsLoader() {
            const elements = getNotificationElements();
            if (!elements) { return; }

            const { badge, menu, placeholder } = elements;
            const baseUrl = getBaseUrl();

            setupNotificationSystem(badge, menu, placeholder, baseUrl);
        }

        function getNotificationElements() {
            const badge = document.getElementById('adminNotificationBadge');
            const menu = document.getElementById('adminNotificationsMenu');
            const placeholder = document.getElementById('adminNotificationsPlaceholder');
            if (!badge || !menu || !placeholder) {
                return null; // markup missing on this page
            }
            return {
                badge,
                menu,
                placeholder
            };
        }

        function getBaseUrl() {
            const baseEl = document.querySelector('body') || document.documentElement;
            const rawBase = (baseEl && baseEl.getAttribute) ? (baseEl.getAttribute('data-admin-base') || '') : '';
            let baseUrl = rawBase.replace(/\/$/, '');

            if (!baseUrl) {
                baseUrl = deriveBaseUrl();
            }
            return baseUrl;
        }

        function deriveBaseUrl() {
            try {
                const loc = window.location;
                const idx = loc.pathname.indexOf('/admin');
                const prefix = idx !== -1 ? loc.pathname.slice(0, idx) : '';
                return loc.origin + prefix;
            } catch {
                return ''; // fallback to absolute-root-style paths
            }
        }

        function setupNotificationSystem(badge, menu, placeholder, baseUrl) {
            function showBadge(count) {
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count; // eslint-disable-line no-magic-numbers
                    badge.style.display = 'inline-block';
                    badge.classList.remove('envato-hidden');
                } else {
                    badge.textContent = '';
                    badge.style.display = 'none';
                }
            }

            async function preflightUnread() {
                try {
                    const url = (baseUrl ? (baseUrl + '/admin/notifications/unread-count') : '/admin/notifications/unread-count');
                    const res = await fetch(url, { credentials: 'same-origin' });
                    if (!res.ok) { return; }
                    const j = await res.json().catch(() => ({}));
                    if (j && typeof j.unread === 'number') { showBadge(j.unread); }
                } catch {
                    /* ignore network error */
                }
            }

            async function loadNotifications() {
                try {
                    const response = await fetchNotifications(baseUrl);
                    if (!response) { return; }

                    const { json, items } = response;
                    const unread = calculateUnreadCount(json, items);
                    showBadge(unread);

                    if (items.length === 0) {
                        showNoNotificationsMessage();
                        return;
                    }

                    renderNotifications(items);
                } catch {
                    showErrorMessage('Could not load notifications');
                }
            }

            async function fetchNotifications(baseUrl) {
                const url = (baseUrl ? (baseUrl + '/admin/notifications/latest') : '/admin/notifications/latest');
                const res = await fetch(url, { credentials: 'same-origin' });
                if (!res.ok) {
                    showErrorMessage('Could not load (status: ' + res.status + ')');
                    return null;
                }

                let json;
                try {
                    json = await res.json();
                } catch {
                    showErrorMessage('Could not parse response');
                    return null;
                }

                if (!json.ok) {
                    showErrorMessage('No notifications');
                    return null;
                }

                const items = json.notifications || [];
                return {
                    json,
                    items
                };
            }

            function calculateUnreadCount(json, items) {
                return (json.unread ?? items.filter(i => !i.read_at).length) || 0;
            }

            function showNoNotificationsMessage() {
                const message = (window.__t && typeof window.__t === 'function') ?
                    window.__t('No notifications') : 'No notifications';
                placeholder.innerHTML = '<div class="px-3 py-2 text-muted">' + message + '</div>';
            }

            function showErrorMessage(message) {
                placeholder.innerHTML = '<div class="px-3 py-2 text-muted">' + message + '</div>';
            }

            function renderNotifications(items) {
                try {
                    placeholder.innerHTML = '';
                    items.forEach(n => {
                        const notificationElement = createNotificationElement(n);
                        placeholder.appendChild(notificationElement);
                    });
                } catch {
                    placeholder.innerHTML = '<div class="px-3 py-2 text-muted">Could not load</div>';
                }
            }

            function createNotificationElement(n) {
                const a = document.createElement('a');
                a.className = 'dropdown-item d-flex align-items-start';
                a.href = n.data.url || '#';
                a.dataset.notificationId = n.id;

                const icon = createNotificationIcon(n);
                const body = createNotificationBody(n);
                const ts = createNotificationTimestamp(n);

                a.appendChild(icon);
                a.appendChild(body);
                a.appendChild(ts);

                setupNotificationClickHandler(a);

                return a;
            }

            function createNotificationIcon(n) {
                const icon = document.createElement('div');
                icon.className = 'me-2';
                icon.innerHTML = '<i class="fas fa-' + (n.data.icon || 'bell') + ' fa-lg text-primary"></i>';
                return icon;
            }

            function createNotificationBody(n) {
                const body = document.createElement('div');
                body.style.flex = '1';

                const title = document.createElement('div');
                title.className = 'fw-semibold';
                title.textContent = getNotificationTitle(n);

                const subtitle = document.createElement('div');
                subtitle.className = 'small text-muted';
                subtitle.textContent = n.data?.message || n.data?.text || '';

                body.appendChild(title);
                body.appendChild(subtitle);
                return body;
            }

            function createNotificationTimestamp(n) {
                const ts = document.createElement('div');
                ts.className = 'small text-muted ms-2';
                ts.textContent = n.created_at;
                return ts;
            }

            function getNotificationTitle(n) {
                if (n.data?.title) {
                    return n.data.title;
                }

                const translatedTitle = getTranslatedTitle(n);
                if (translatedTitle) {
                    return translatedTitle;
                }

                const humanizedTitle = getHumanizedTitle(n);
                if (humanizedTitle) {
                    return humanizedTitle;
                }

                return n.data?.type || 'Notification';
            }

            function getTranslatedTitle(n) {
                if (typeof window.__t === 'function') {
                    return window.__t(n.data?.type || '') || '';
                }
                return '';
            }

            function getHumanizedTitle(n) {
                if (!n.data?.type) {
                    return '';
                }
                return humanizeType(n.data.type);
            }

            function humanizeType(t) {
                if (!t) { return ''; }
                return t.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            }

            function setupNotificationClickHandler(a) {
                a.addEventListener('click', async function(ev) {
                    ev.preventDefault();
                    await handleNotificationClick(this);
                });
            }

            async function handleNotificationClick(element) {
                const id = element.dataset.notificationId;
                const { ok, msg } = await markNotificationAsRead(id);

                showNotificationFeedback(msg, ok);
                updateBadgeCount();
                refreshNotifications();
                navigateToUrl(element);
            }

            async function markNotificationAsRead(id) {
                try {
                    const response = await sendMarkAsReadRequest(id);
                    return processMarkAsReadResponse(response);
                } catch {
                    return handleMarkAsReadError();
                }
            }

            async function sendMarkAsReadRequest(id) {
                const readUrl = buildReadUrl(id);
                const headers = buildRequestHeaders();

                const res = await fetch(readUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers
                });

                const j = await res.json().catch(() => ({}));
                return { res,
                    j };
            }

            function buildReadUrl(id) {
                return (baseUrl ?
                    (baseUrl + '/admin/notifications/' + encodeURIComponent(id) + '/read') :
                    ('/admin/notifications/' + encodeURIComponent(id) + '/read'));
            }

            function buildRequestHeaders() {
                return {
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')) || '',
                    'Accept': 'application/json'
                };
            }

            function processMarkAsReadResponse(response) {
                const { res, j } = response;
                const ok = j.ok || res.ok;
                const msg = j.message || getSuccessOrFailureMessage(ok, j);
                return { ok,
                    msg };
            }

            function getSuccessOrFailureMessage(ok, j) {
                if (ok) {
                    return getTranslatedMessage('Marked read', 'Marked read');
                }
                return j.error || getTranslatedMessage('Failed', 'Failed');
            }

            function getTranslatedMessage(key, fallback) {
                return ((typeof window.__t === 'function' && window.__t(key)) || fallback);
            }

            function handleMarkAsReadError() {
                const msg = getTranslatedMessage('Network error', 'Network error');
                return { ok: false,
                    msg };
            }

            function showNotificationFeedback(msg, ok) {
                if (window.AdminPanel && typeof window.AdminPanel.showNotification === 'function') {
                    window.AdminPanel.showNotification(msg, ok ? 'success' : 'error');
                } else if (window.alert) {
                    alert(msg); // eslint-disable-line no-alert
                }
            }

            function updateBadgeCount() {
                try {
                    if (!this.classList.contains('text-muted')) { this.classList.add('text-muted'); }
                    const current = parseInt((badge.textContent || '0').replace('+', ''), 10) || 0;
                    const next = Math.max(0, current - 1);
                    showBadge(next);
                } catch { /* ignore */ }
            }

            function refreshNotifications() {
                loadNotifications().catch(() => {
                    // Ignore notification load errors
                });
            }

            function navigateToUrl(element) {
                const url = element.getAttribute('href');
                if (url && url !== '#') { window.location.href = url; }
            }

            window.refreshAdminNotifications = loadNotifications;

            const markAllBtn = document.getElementById('adminMarkAllReadBtn');
            if (markAllBtn) {
                markAllBtn.addEventListener('click', async(ev) => {
                    ev.preventDefault();
                    await handleMarkAllRead();
                });
            }

            async function handleMarkAllRead() {
                try {
                    const response = await sendMarkAllReadRequest();
                    handleMarkAllReadResponse(response);
                } catch {
                    handleMarkAllReadError();
                }
            }

            async function sendMarkAllReadRequest() {
                const markAllUrl = (baseUrl ?
                    (baseUrl + '/admin/notifications/mark-all-read') :
                    '/admin/notifications/mark-all-read');

                const res = await fetch(markAllUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')) || ''
                    }
                });

                const j = await res.json().catch(() => ({}));
                return { res,
                    j };
            }

            function handleMarkAllReadResponse(response) {
                const { res, j } = response;
                if (j.ok || res.ok) {
                    showMarkAllReadSuccess();
                    showBadge(0);
                    refreshNotifications();
                } else {
                    showMarkAllReadFailure(j.message);
                }
            }

            function showMarkAllReadSuccess() {
                if (window.AdminPanel && window.AdminPanel.showNotification) {
                    const message = getTranslatedMessage('All marked read', 'All marked read');
                    window.AdminPanel.showNotification(message, 'success');
                }
            }

            function showMarkAllReadFailure(message) {
                if (window.AdminPanel && window.AdminPanel.showNotification) {
                    const errorMessage = message || getTranslatedMessage('Failed', 'Failed');
                    window.AdminPanel.showNotification(errorMessage, 'error');
                }
            }

            function handleMarkAllReadError() {
                if (window.AdminPanel && window.AdminPanel.showNotification) {
                    const message = getTranslatedMessage('Network error', 'Network error');
                    window.AdminPanel.showNotification(message, 'error');
                }
            }

            preflightUnread().finally(loadNotifications);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initNotificationsLoader);
        } else {
            initNotificationsLoader();
        }
    }());

    // Sidebar submenu toggle fallback (robust)
    // - removes data-bs-toggle to avoid Bootstrap interference
    // - toggles .show on the parent .nav-dropdown
    // - closes other open nav-dropdowns when opening a new one
    // - updates aria-expanded on toggles
    AdminPanel.initSidebarSubmenus = function() {
        // remove bootstrap data attribute to prevent popper/bootstrap from hijacking behavior
        document.querySelectorAll('.nav-item.dropdown-toggle').forEach((el) => {
            try { el.removeAttribute('data-bs-toggle'); } catch { /* ignore */ }
            // ensure aria-expanded is present
            const p = el.closest('.nav-dropdown');
            if (p && p.classList.contains('show')) {
                el.setAttribute('aria-expanded', 'true');
            } else {
                el.setAttribute('aria-expanded', 'false');
            }
        });

        // delegate clicks for toggles
        document.addEventListener('click', (e) => {
            const toggle = e.target.closest('.nav-item.dropdown-toggle');
            if (!toggle) { return; }

            e.preventDefault();
            e.stopPropagation();

            const parent = toggle.closest('.nav-dropdown');
            if (!parent) { return; }

            const willOpen = !parent.classList.contains('show');

            // close all other open dropdowns
            document.querySelectorAll('.nav-dropdown.show').forEach((openEl) => {
                if (openEl !== parent) {
                    openEl.classList.remove('show');
                    const t = openEl.querySelector('.nav-item.dropdown-toggle');
                    if (t) { t.setAttribute('aria-expanded', 'false'); }
                }
            });

            // toggle the clicked dropdown
            if (willOpen) {
                parent.classList.add('show');
                toggle.setAttribute('aria-expanded', 'true');
            } else {
                parent.classList.remove('show');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        // close dropdowns when clicking outside the sidebar
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.modern-sidebar') && !e.target.closest('.admin-sidebar')) {
                document.querySelectorAll('.nav-dropdown.show').forEach((openEl) => {
                    openEl.classList.remove('show');
                    const t = openEl.querySelector('.nav-item.dropdown-toggle');
                    if (t) { t.setAttribute('aria-expanded', 'false'); }
                });
            }
        }, true);
    };

    // call submenu init on DOM ready
    if (typeof AdminPanel.initSidebarSubmenus === 'function') {
        AdminPanel.initSidebarSubmenus();
    }
}());