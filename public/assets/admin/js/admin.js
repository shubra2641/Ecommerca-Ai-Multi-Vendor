/* Admin JavaScript - Consolidated JS for Admin Panel
 * This file contains all JavaScript functionality for the admin interface
 * Following rules: Progressive enhancement, no inline JS, unified structure
 */

(function () {
    'use strict';

    // Admin namespace
    window.AdminPanel = window.AdminPanel || {};

    // Initialize admin functionality
    AdminPanel.init = function () {
        this.initSidebar();
        this.initTables();
        this.initForms();
        this.initModals();
        this.initUserBalance();
        this.initConfirmations();
        this.initNotifications();
    };

    // Sidebar functionality
    AdminPanel.initSidebar = function () {
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
            e && e.preventDefault();
            if (!sidebar) return;
            sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
        }

        // attach to all toggles
        toggles.forEach(function (t) {
            t.addEventListener('click', toggleSidebar);
        });

        // Close sidebar when clicking overlay
        if (overlay) {
            overlay.addEventListener('click', function () {
                if (!sidebar) return;
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }

        // Close sidebar when clicking a nav item on small screens
        document.addEventListener('click', function (e) {
            if (!sidebar) return;
            const navItem = e.target.closest('.sidebar-nav a, .nav-item');
            if (!navItem) return;
            // only auto-close for narrower viewports where sidebar overlays content
            if (window.innerWidth <= 992 && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
            }
        });
    };

    // Enhanced table functionality
    AdminPanel.initTables = function () {
        const tables = document.querySelectorAll('.admin-table');

        tables.forEach(function (table) {
            // Add sorting functionality
            const headers = table.querySelectorAll('th[data-sortable]');
            headers.forEach(function (header) {
                header.style.cursor = 'pointer';
                header.addEventListener('click', function () {
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
    AdminPanel.sortTable = function (table, header) {
        const columnIndex = Array.from(header.parentNode.children).indexOf(header);
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        const isAscending = !header.classList.contains('sort-asc');

        rows.sort(function (a, b) {
            const aText = a.children[columnIndex].textContent.trim();
            const bText = b.children[columnIndex].textContent.trim();

            if (isAscending) {
                return aText.localeCompare(bText);
            } else {
                return bText.localeCompare(aText);
            }
        });

        // Remove existing sort classes
        table.querySelectorAll('th').forEach(function (th) {
            th.classList.remove('sort-asc', 'sort-desc');
        });

        // Add sort class to current header
        header.classList.add(isAscending ? 'sort-asc' : 'sort-desc');

        // Reorder rows
        const tbody = table.querySelector('tbody');
        rows.forEach(function (row) {
            tbody.appendChild(row);
        });
    };

    // Table selection functionality
    AdminPanel.initTableSelection = function (table) {
        const selectAll = table.querySelector('thead input[type="checkbox"]');
        const rowCheckboxes = table.querySelectorAll('tbody input[type="checkbox"]');

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                rowCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = selectAll.checked;
                    AdminPanel.updateRowSelection(checkbox);
                });
            });
        }

        rowCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                AdminPanel.updateRowSelection(checkbox);
                AdminPanel.updateSelectAll(table);
            });
        });
    };

    // Update row selection visual state
    AdminPanel.updateRowSelection = function (checkbox) {
        const row = checkbox.closest('tr');
        if (checkbox.checked) {
            row.classList.add('selected');
        } else {
            row.classList.remove('selected');
        }
    };

    // Update select all checkbox state
    AdminPanel.updateSelectAll = function (table) {
        const selectAll = table.querySelector('thead input[type="checkbox"]');
        const rowCheckboxes = table.querySelectorAll('tbody input[type="checkbox"]');
        const checkedBoxes = table.querySelectorAll('tbody input[type="checkbox"]:checked');

        if (selectAll) {
            selectAll.checked = checkedBoxes.length === rowCheckboxes.length;
            selectAll.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < rowCheckboxes.length;
        }
    };

    // Form enhancements
    AdminPanel.initForms = function () {
        const forms = document.querySelectorAll('.admin-form');

        forms.forEach(function (form) {
            // Add form validation
            form.addEventListener('submit', function (e) {
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
    AdminPanel.validateForm = function (form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');

        requiredFields.forEach(function (field) {
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
    AdminPanel.showFieldError = function (field, message) {
        AdminPanel.clearFieldError(field);

        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;

        field.classList.add('error');
        field.parentNode.appendChild(errorDiv);
    };

    // Clear field error
    AdminPanel.clearFieldError = function (field) {
        field.classList.remove('error');
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    };

    // Modal functionality
    AdminPanel.initModals = function () {
        const modalTriggers = document.querySelectorAll('[data-modal]');

        modalTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                const modalId = trigger.getAttribute('data-modal');
                AdminPanel.openModal(modalId);
            });
        });

        // Close modal functionality
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('modal-overlay') || e.target.classList.contains('modal-close')) {
                AdminPanel.closeModal();
            }
        });

        // Escape key to close modal
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                AdminPanel.closeModal();
            }
        });
    };

    // Open modal
    AdminPanel.openModal = function (modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.classList.add('modal-open');
        }
    };

    // Close modal
    AdminPanel.closeModal = function () {
        const activeModal = document.querySelector('.modal.active');
        if (activeModal) {
            activeModal.classList.remove('active');
            document.body.classList.remove('modal-open');
        }
    };

    // Notification system
    AdminPanel.initNotifications = function () {
        // Auto-hide notifications
        const notifications = document.querySelectorAll('.notification');
        notifications.forEach(function (notification) {
            if (notification.hasAttribute('data-auto-hide')) {
                setTimeout(function () {
                    AdminPanel.hideNotification(notification);
                }, 5000);
            }
        });

        // Close notification buttons
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('notification-close')) {
                const notification = e.target.closest('.notification');
                AdminPanel.hideNotification(notification);
            }
        });
    };

    // User Balance page adapter
    AdminPanel.initUserBalance = function () {
        // Look for the config template inserted by the Blade view
        const tpl = document.getElementById('user-balance-config');
        if (!tpl) return; // not present on this page

        let cfg = {};
        try {
            cfg = JSON.parse(tpl.textContent || tpl.innerText || '{}');
        } catch (e) {
            console.error('user-balance-config JSON parse error', e);
            return;
        }

        const urls = (cfg.urls) ? cfg.urls : {};
        const currency = (cfg.currency) ? cfg.currency : { code: 'USD', symbol: '$' };

        function fmtAmount(v) {
            try {
                return new Intl.NumberFormat(document.documentElement.lang || 'en', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(v || 0)) + ' ' + (currency.symbol || '$');
            } catch (e) { return (parseFloat(v || 0).toFixed(2) + ' ' + (currency.symbol || '$')); }
        }

        // Refresh stats from server and update DOM
        async function refreshStats() {
            if (!urls.stats) return;
            try {
                const res = await fetch(urls.stats, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) throw new Error('Network response not ok');
                const data = await res.json();

                // Update balance display
                const balanceEls = document.querySelectorAll('[data-countup][data-target]');
                balanceEls.forEach(function (el) {
                    const key = el.getAttribute('data-stat') || el.getAttribute('data-key');
                    if (key && data.hasOwnProperty(key)) {
                        el.textContent = fmtAmount(data[key]);
                        // update dataset target for potential CountUp observers
                        el.dataset.target = Number(data[key]);
                        delete el.dataset.counted; // allow re-animate if CountUp observes again
                    }
                });

                // Fallback: update elements with specific data-stat attributes
                ['total_added', 'total_deducted', 'net_balance_change', 'balance'].forEach(function (k) {
                    const sel = document.querySelector('[data-stat="' + k + '"]');
                    if (sel && data.hasOwnProperty(k)) sel.textContent = fmtAmount(data[k]);
                });

                AdminPanel.showNotification(cfg.i18n && cfg.i18n.balance_refreshed ? cfg.i18n.balance_refreshed : 'Data refreshed', 'success');
            } catch (err) {
                console.error('Failed to refresh balance stats', err);
                AdminPanel.showNotification(cfg.i18n && cfg.i18n.error_refresh ? cfg.i18n.error_refresh : 'Failed to refresh', 'danger');
            }
        }

        // Wire refresh buttons
        document.querySelectorAll('.btn-refresh-balance').forEach(function (btn) {
            btn.addEventListener('click', function (e) { e.preventDefault(); refreshStats(); });
        });

        // Open modals for add / deduct
        document.querySelectorAll('.btn-add-balance').forEach(function (btn) {
            btn.addEventListener('click', function (e) { e.preventDefault(); AdminPanel.openModal('addBalanceModal'); });
        });
        document.querySelectorAll('.btn-deduct-balance').forEach(function (btn) {
            btn.addEventListener('click', function (e) { e.preventDefault(); AdminPanel.openModal('deductBalanceModal'); });
        });

        // View history
        document.querySelectorAll('.btn-view-history').forEach(function (btn) {
            btn.addEventListener('click', async function (e) {
                e.preventDefault();
                AdminPanel.openModal('balanceHistoryModal');
                const container = document.getElementById('balanceHistoryContainer');
                if (!container || !urls.history) return;
                // show loading
                container.innerHTML = '<div class="text-center p-4"><div class="loading-spinner mx-auto"></div><p class="mt-2">' + (cfg.i18n && cfg.i18n.loading_history ? cfg.i18n.loading_history : 'Loading history...') + '</p></div>';
                try {
                    const res = await fetch(urls.history, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!res.ok) throw new Error('Network response not ok');
                    const html = await res.text();
                    container.innerHTML = html || ('<div class="empty-state text-center p-4">' + (cfg.i18n && cfg.i18n.no_history_desc ? cfg.i18n.no_history_desc : 'No previous transactions found') + '</div>');
                } catch (err) {
                    console.error('Failed to load history', err);
                    container.innerHTML = '<div class="alert alert-danger">' + (cfg.i18n && cfg.i18n.error_history ? cfg.i18n.error_history : 'Failed to load balance history') + '</div>';
                }
            });
        });

        // AJAX form submit for add/deduct
        function wireForm(formId, urlKey, successMessageKey) {
            const form = document.getElementById(formId);
            if (!form) return;
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) submitBtn.disabled = true;
                const formData = new FormData(form);
                const url = urls[urlKey];
                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || ''
                        }
                    });
                    const json = await res.json();
                    if (res.ok) {
                        AdminPanel.showNotification((cfg.i18n && cfg.i18n[successMessageKey]) || 'Success', 'success');
                        // close modal
                        AdminPanel.closeModal();
                        // refresh stats
                        refreshStats();
                    } else {
                        AdminPanel.showNotification(json.message || (cfg.i18n && cfg.i18n.error_server) || 'Error', 'danger');
                    }
                } catch (err) {
                    console.error('Form submit failed', err);
                    AdminPanel.showNotification((cfg.i18n && cfg.i18n.error_server) || 'Server error', 'danger');
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                }
            });
        }

        wireForm('addBalanceForm', 'add', 'balance_added');
        wireForm('deductBalanceForm', 'deduct', 'balance_deducted');

        // initial refresh to populate numbers if needed
        setTimeout(refreshStats, 400);
    };

    // Centralized confirm handlers for forms and links
    AdminPanel.initConfirmations = function () {
        // Forms with js-confirm or js-confirm-delete
        document.querySelectorAll('form.js-confirm, form.js-confirm-delete').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                const msg = form.dataset.confirm || form.getAttribute('data-confirm') || 'Are you sure?';
                if (!window.confirm(msg)) {
                    e.preventDefault();
                }
            });
        });

        // Links/buttons with data-confirm attribute
        document.querySelectorAll('[data-confirm]').forEach(function (el) {
            // only handle anchors or buttons that navigate or submit forms
            el.addEventListener('click', function (e) {
                const msg = el.getAttribute('data-confirm');
                if (!window.confirm(msg)) {
                    e.preventDefault();
                }
            });
        });
    };

    // Hide notification
    AdminPanel.hideNotification = function (notification) {
        if (notification) {
            notification.style.opacity = '0';
            setTimeout(function () {
                notification.remove();
            }, 300);
        }
    };

    // Show notification
    AdminPanel.showNotification = function (message, type) {
        type = type || 'info';
        const notification = document.createElement('div');
        notification.className = 'notification notification-' + type;
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
        const autoHide = setTimeout(function () {
            AdminPanel.hideNotification(notification);
        }, 5000);

        // Close handler
        const closeBtn = notification.querySelector('.notification-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                clearTimeout(autoHide);
                AdminPanel.hideNotification(notification);
            });
        }
    };

    // Auto-save functionality
    AdminPanel.initAutoSave = function (form) {
        const fields = form.querySelectorAll('input, textarea, select');
        let saveTimeout;

        fields.forEach(function (field) {
            field.addEventListener('input', function () {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(function () {
                    AdminPanel.autoSave(form);
                }, 2000);
            });
        });
    };

    // Auto-save implementation
    AdminPanel.autoSave = function (form) {
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
                .then(function (response) {
                    if (response.ok) {
                        AdminPanel.showNotification('Changes saved automatically', 'success');
                    }
                })
                .catch(function (error) {
                    console.error('Auto-save failed:', error);
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
    (function () {
        // Defer execution until DOM ready
        function initNotificationsLoader() {
            const badge = document.getElementById('adminNotificationBadge');
            const menu = document.getElementById('adminNotificationsMenu');
            const placeholder = document.getElementById('adminNotificationsPlaceholder');
            if (!badge || !menu || !placeholder) {
                return; // markup missing on this page
            }

            function showBadge(count) {
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.style.display = 'inline-block';
                    badge.classList.remove('envato-hidden');
                } else {
                    badge.textContent = '';
                    badge.style.display = 'none';
                }
            }

            // Build base URL using body[data-admin-base] when present. This helps when app is hosted
            // in a subdirectory (e.g., http://localhost/easy) so fetch('/admin/...') would otherwise miss.
            const baseEl = document.querySelector('body') || document.documentElement;
            const rawBase = (baseEl && baseEl.getAttribute) ? (baseEl.getAttribute('data-admin-base') || '') : '';
            // Ensure no trailing slash
            let baseUrl = rawBase.replace(/\/$/, '');
            // If data-admin-base is not accurate or empty, derive base from current location by
            // finding the first '/admin' segment in the pathname. This handles deployments
            // where the app is served from a subdirectory (e.g., /easy) but APP_URL was not set.
            if (!baseUrl) {
                try {
                    const loc = window.location;
                    const idx = loc.pathname.indexOf('/admin');
                    const prefix = idx !== -1 ? loc.pathname.slice(0, idx) : '';
                    baseUrl = loc.origin + prefix;
                } catch (e) {
                    baseUrl = ''; // fallback to absolute-root-style paths
                }
            }

            async function preflightUnread() {
                try {
                    const url = (baseUrl ? (baseUrl + '/admin/notifications/unread-count') : '/admin/notifications/unread-count');
                    const res = await fetch(url, { credentials: 'same-origin' });
                    if (!res.ok) return;
                    const j = await res.json().catch(() => ({}));
                    if (j && typeof j.unread === 'number') showBadge(j.unread);
                } catch (e) {
                    /* ignore network error */
                }
            }

            async function loadNotifications() {
                try {
                    const url = (baseUrl ? (baseUrl + '/admin/notifications/latest') : '/admin/notifications/latest');
                    const res = await fetch(url, { credentials: 'same-origin' });
                    if (!res.ok) {
                        placeholder.innerHTML = '<div class="px-3 py-2 text-muted">Could not load (status: ' + res.status + ')</div>';
                        return;
                    }
                    let json;
                    try { json = await res.json(); } catch (e) { placeholder.innerHTML = '<div class="px-3 py-2 text-muted">Could not parse response</div>'; return; }
                    if (!json.ok) { placeholder.innerHTML = '<div class="px-3 py-2 text-muted">No notifications</div>'; return; }
                    const items = json.notifications || [];
                    const unread = (json.unread ?? items.filter(i => !i.read_at).length) || 0;
                    showBadge(unread);
                    if (items.length === 0) {
                        placeholder.innerHTML = '<div class="px-3 py-2 text-muted">' + ((window.__t && typeof window.__t === 'function') ? window.__t('No notifications') : 'No notifications') + '</div>';
                        return;
                    }
                    placeholder.innerHTML = '';
                    items.forEach(n => {
                        const a = document.createElement('a');
                        a.className = 'dropdown-item d-flex align-items-start';
                        a.href = n.data.url || '#';
                        a.dataset.notificationId = n.id;
                        const icon = document.createElement('div');
                        icon.className = 'me-2';
                        icon.innerHTML = '<i class="fas fa-' + (n.data.icon || 'bell') + ' fa-lg text-primary"></i>';
                        const body = document.createElement('div');
                        body.style.flex = '1';
                        const title = document.createElement('div');
                        title.className = 'fw-semibold';
                        function humanizeType(t) { if (!t) return ''; return t.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()); }
                        const titleText = (n.data?.title) || ((typeof window.__t === 'function') ? (window.__t(n.data?.type || '') || '') : '') || humanizeType(n.data?.type) || (n.data?.type || 'Notification');
                        title.textContent = titleText;
                        const subtitle = document.createElement('div');
                        subtitle.className = 'small text-muted';
                        subtitle.textContent = n.data?.message || n.data?.text || '';
                        const ts = document.createElement('div');
                        ts.className = 'small text-muted ms-2';
                        ts.textContent = n.created_at;
                        body.appendChild(title);
                        body.appendChild(subtitle);
                        a.appendChild(icon);
                        a.appendChild(body);
                        a.appendChild(ts);

                        a.addEventListener('click', async function (ev) {
                            ev.preventDefault();
                            const id = this.dataset.notificationId;
                            let ok = false; let msg = '';
                            try {
                                const readUrl = (baseUrl ? (baseUrl + '/admin/notifications/' + encodeURIComponent(id) + '/read') : ('/admin/notifications/' + encodeURIComponent(id) + '/read'));
                                const res = await fetch(readUrl, {
                                    method: 'POST', credentials: 'same-origin',
                                    headers: { 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')) || '', 'Accept': 'application/json' },
                                });
                                const j = await res.json().catch(() => ({}));
                                ok = j.ok || res.ok;
                                msg = j.message || (ok ? ((typeof window.__t === 'function' && window.__t('Marked read')) || 'Marked read') : (j.error || ((typeof window.__t === 'function' && window.__t('Failed')) || 'Failed')));
                            } catch (e) { msg = ((typeof window.__t === 'function') ? window.__t('Network error') : 'Network error'); }

                            // show toast feedback via AdminPanel or fallback
                            if (window.AdminPanel && typeof window.AdminPanel.showNotification === 'function') {
                                window.AdminPanel.showNotification(msg, ok ? 'success' : 'error');
                            } else if (window.alert) {
                                alert(msg);
                            }

                            try {
                                if (!this.classList.contains('text-muted')) this.classList.add('text-muted');
                                const current = parseInt((badge.textContent || '0').replace('+', ''), 10) || 0;
                                const next = Math.max(0, current - 1);
                                showBadge(next);
                            } catch (e) { /* ignore */ }

                            loadNotifications().catch(() => { });
                            const url = this.getAttribute('href');
                            if (url && url !== '#') { window.location.href = url; }
                        });

                        placeholder.appendChild(a);
                    });
                } catch (e) {
                    placeholder.innerHTML = '<div class="px-3 py-2 text-muted">Could not load</div>';
                }
            }

            window.refreshAdminNotifications = loadNotifications;

            const markAllBtn = document.getElementById('adminMarkAllReadBtn');
            if (markAllBtn) {
                markAllBtn.addEventListener('click', async function (ev) {
                    ev.preventDefault();
                    try {
                        const markAllUrl = (baseUrl ? (baseUrl + '/admin/notifications/mark-all-read') : '/admin/notifications/mark-all-read');
                        const res = await fetch(markAllUrl, { method: 'POST', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')) || '' } });
                        const j = await res.json().catch(() => ({}));
                        if (j.ok || res.ok) {
                            if (window.AdminPanel && window.AdminPanel.showNotification) window.AdminPanel.showNotification((typeof window.__t === 'function') ? window.__t('All marked read') : 'All marked read', 'success');
                            showBadge(0);
                            loadNotifications().catch(() => { });
                        } else {
                            if (window.AdminPanel && window.AdminPanel.showNotification) window.AdminPanel.showNotification(j.message || ((typeof window.__t === 'function') ? window.__t('Failed') : 'Failed'), 'error');
                        }
                    } catch (e) {
                        if (window.AdminPanel && window.AdminPanel.showNotification) window.AdminPanel.showNotification((typeof window.__t === 'function') ? window.__t('Network error') : 'Network error', 'error');
                    }
                });
            }

            preflightUnread().finally(loadNotifications);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initNotificationsLoader);
        } else {
            initNotificationsLoader();
        }
    })();

    // Sidebar submenu toggle fallback (robust)
    // - removes data-bs-toggle to avoid Bootstrap interference
    // - toggles .show on the parent .nav-dropdown
    // - closes other open nav-dropdowns when opening a new one
    // - updates aria-expanded on toggles
    AdminPanel.initSidebarSubmenus = function () {
        // remove bootstrap data attribute to prevent popper/bootstrap from hijacking behavior
        document.querySelectorAll('.nav-item.dropdown-toggle').forEach(function (el) {
            try { el.removeAttribute('data-bs-toggle'); } catch (e) { /* ignore */ }
            // ensure aria-expanded is present
            const p = el.closest('.nav-dropdown');
            if (p && p.classList.contains('show')) {
                el.setAttribute('aria-expanded', 'true');
            } else {
                el.setAttribute('aria-expanded', 'false');
            }
        });

        // delegate clicks for toggles
        document.addEventListener('click', function (e) {
            const toggle = e.target.closest('.nav-item.dropdown-toggle');
            if (!toggle) return;

            e.preventDefault();
            e.stopPropagation();

            const parent = toggle.closest('.nav-dropdown');
            if (!parent) return;

            const willOpen = !parent.classList.contains('show');

            // close all other open dropdowns
            document.querySelectorAll('.nav-dropdown.show').forEach(function (openEl) {
                if (openEl !== parent) {
                    openEl.classList.remove('show');
                    const t = openEl.querySelector('.nav-item.dropdown-toggle');
                    if (t) t.setAttribute('aria-expanded', 'false');
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
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.modern-sidebar') && !e.target.closest('.admin-sidebar')) {
                document.querySelectorAll('.nav-dropdown.show').forEach(function (openEl) {
                    openEl.classList.remove('show');
                    const t = openEl.querySelector('.nav-item.dropdown-toggle');
                    if (t) t.setAttribute('aria-expanded', 'false');
                });
            }
        }, true);
    };

    // call submenu init on DOM ready
    if (typeof AdminPanel.initSidebarSubmenus === 'function') {
        AdminPanel.initSidebarSubmenus();
    }


})();