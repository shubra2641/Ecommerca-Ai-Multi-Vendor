/* Admin JavaScript - Consolidated JS for Admin Panel
 * This file contains all JavaScript functionality for the admin interface
 * Following rules: Progressive enhancement, no inline JS, unified structure
 */
/* global AdminPanel */

(function () {
    'use strict';

    // Admin namespace
    window.AdminPanel = window.AdminPanel || {};

    // Initialize admin functionality
    AdminPanel.init = function () {
        this.initSidebar();
        this.initTables();
        this.initModals();
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
    AdminPanel.initTables = function () {
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
    AdminPanel.sortTable = function (table, header) {
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
    AdminPanel.initTableSelection = function (table) {
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


    // Modal functionality
    AdminPanel.initModals = function () {
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


    // Centralized confirm handlers for forms and links
    AdminPanel.initConfirmations = function () {
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
    AdminPanel.hideNotification = function (notification) {
        if (notification) {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300); // eslint-disable-line no-magic-numbers
        }
    };

    // Show notification
    AdminPanel.showNotification = function (message, type) {
        const notificationType = type || 'info';
        const notification = document.createElement('div');
        notification.className = 'notification notification-' + notificationType;
        // Create notification content safely to prevent XSS
        const content = document.createElement('div');
        content.className = 'notification-content';

        const messageDiv = document.createElement('div');
        messageDiv.className = 'notification-message';
        messageDiv.textContent = message; // Use textContent instead of innerHTML

        const closeBtn = document.createElement('button');
        closeBtn.className = 'notification-close';
        closeBtn.type = 'button';
        closeBtn.setAttribute('aria-label', 'Close notification');
        closeBtn.textContent = '×';

        content.appendChild(messageDiv);
        content.appendChild(closeBtn);
        notification.appendChild(content);

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


    // Sidebar submenu toggle fallback (robust)
    // - removes data-bs-toggle to avoid Bootstrap interference
    // - toggles .show on the parent .nav-dropdown
    // - closes other open nav-dropdowns when opening a new one
    // - updates aria-expanded on toggles
    AdminPanel.initSidebarSubmenus = function () {
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