/**
 * Admin Panel JavaScript - Simple & Clean
 * إدارة لوحة التحكم - بسيط ونظيف
 */

/* eslint-env browser */
/* global document, window, setTimeout, clearTimeout, FormData, fetch, confirm */

(function () {
    'use strict';

    // متغيرات عامة
    const Admin = {
        sidebar: null,
        dropdowns: [],
        modals: []
    };

    // دوال مساعدة بسيطة
    const $ = (selector) => document.querySelector(selector);
    const $$ = (selector) => document.querySelectorAll(selector);
    const addClass = (el, className) => el?.classList.add(className);
    const removeClass = (el, className) => el?.classList.remove(className);
    const toggleClass = (el, className) => el?.classList.toggle(className);
    const hasClass = (el, className) => el?.classList.contains(className);

    // إدارة الشريط الجانبي
    function initSidebar() {
        Admin.sidebar = $('.modern-sidebar, #sidebar');
        const toggle = $('.sidebar-toggle');
        const overlay = $('.sidebar-overlay');

        if (toggle) {
            toggle.addEventListener('click', () => {
                toggleClass(Admin.sidebar, 'active');
                toggleClass(overlay, 'active');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                removeClass(Admin.sidebar, 'active');
                removeClass(overlay, 'active');
            });
        }

        // إغلاق الشريط عند النقر على رابط في الشاشات الصغيرة
        $$('.sidebar-nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 992 && hasClass(Admin.sidebar, 'active')) {
                    removeClass(Admin.sidebar, 'active');
                    removeClass(overlay, 'active');
                }
            });
        });
    }

    // إدارة القوائم المنسدلة
    function initDropdowns() {
        $$('.dropdown, .nav-dropdown').forEach(dropdown => {
            const toggle = $('.dropdown-toggle', dropdown);
            const menu = $('.dropdown-menu', dropdown);

            if (toggle && menu) {
                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    // إغلاق القوائم الأخرى
                    closeAllDropdowns(dropdown);

                    // فتح/إغلاق القائمة الحالية
                    const isOpen = hasClass(dropdown, 'show');
                    if (isOpen) {
                        closeDropdown(dropdown, toggle, menu);
                    } else {
                        openDropdown(dropdown, toggle, menu);
                    }
                });
            }
        });

        // إغلاق القوائم عند النقر خارجها
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.dropdown, .nav-dropdown')) {
                closeAllDropdowns();
            }
        });
    }

    function openDropdown(dropdown, toggle, menu) {
        addClass(dropdown, 'show');
        addClass(menu, 'show');
        toggle.setAttribute('aria-expanded', 'true');
        menu.style.display = 'block';
    }

    function closeDropdown(dropdown, toggle, menu) {
        removeClass(dropdown, 'show');
        removeClass(menu, 'show');
        toggle.setAttribute('aria-expanded', 'false');
        menu.style.display = 'none';
    }

    function closeAllDropdowns(excludeDropdown = null) {
        $$('.dropdown, .nav-dropdown').forEach(dropdown => {
            if (dropdown !== excludeDropdown) {
                const toggle = $('.dropdown-toggle', dropdown);
                const menu = $('.dropdown-menu', dropdown);
                if (toggle && menu) {
                    closeDropdown(dropdown, toggle, menu);
                }
            }
        });
    }

    // إدارة الجداول
    function initTables() {
        $$('.admin-table').forEach(table => {
            // ترتيب الجداول
            $$('th[data-sortable]', table).forEach(header => {
                header.style.cursor = 'pointer';
                header.addEventListener('click', () => sortTable(table, header));
            });

            // تحديد الصفوف
            const selectAll = $('thead input[type="checkbox"]', table);
            const rowCheckboxes = $$('tbody input[type="checkbox"]', table);

            if (selectAll) {
                selectAll.addEventListener('change', () => {
                    rowCheckboxes.forEach(checkbox => {
                        checkbox.checked = selectAll.checked;
                        updateRowSelection(checkbox);
                    });
                });
            }

            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    updateRowSelection(checkbox);
                    updateSelectAll(table);
                });
            });
        });
    }

    function sortTable(table, header) {
        const columnIndex = Array.from(header.parentNode.children).indexOf(header);
        const rows = Array.from($$('tbody tr', table));
        const isAscending = !hasClass(header, 'sort-asc');

        rows.sort((a, b) => {
            const aText = a.children[columnIndex].textContent.trim();
            const bText = b.children[columnIndex].textContent.trim();
            return isAscending ? aText.localeCompare(bText) : bText.localeCompare(aText);
        });

        // تحديث فئات الترتيب
        $$('th', table).forEach(th => {
            removeClass(th, 'sort-asc');
            removeClass(th, 'sort-desc');
        });
        addClass(header, isAscending ? 'sort-asc' : 'sort-desc');

        // إعادة ترتيب الصفوف
        const tbody = $('tbody', table);
        rows.forEach(row => tbody.appendChild(row));
    }

    function updateRowSelection(checkbox) {
        const row = checkbox.closest('tr');
        toggleClass(row, 'selected', checkbox.checked);
    }

    function updateSelectAll(table) {
        const selectAll = $('thead input[type="checkbox"]', table);
        const rowCheckboxes = $$('tbody input[type="checkbox"]', table);
        const checkedBoxes = $$('tbody input[type="checkbox"]:checked', table);

        if (selectAll) {
            selectAll.checked = checkedBoxes.length === rowCheckboxes.length;
            selectAll.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < rowCheckboxes.length;
        }
    }

    // إدارة النماذج
    function initForms() {
        $$('.admin-form').forEach(form => {
            // التحقق من صحة النماذج
            form.addEventListener('submit', (e) => {
                if (!validateForm(form)) {
                    e.preventDefault();
                }
            });

            // الحفظ التلقائي
            if (form.hasAttribute('data-auto-save')) {
                initAutoSave(form);
            }
        });
    }

    function validateForm(form) {
        let isValid = true;
        const requiredFields = $$('[required]', form);

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                showFieldError(field, 'هذا الحقل مطلوب');
                isValid = false;
            } else {
                clearFieldError(field);
            }
        });

        return isValid;
    }

    function showFieldError(field, message) {
        clearFieldError(field);
        addClass(field, 'error');

        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }

    function clearFieldError(field) {
        removeClass(field, 'error');
        const existingError = $('.field-error', field.parentNode);
        existingError?.remove();
    }

    function initAutoSave(form) {
        const fields = $$('input, textarea, select', form);
        let saveTimeout;

        fields.forEach(field => {
            field.addEventListener('input', () => {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => autoSave(form), 2000);
            });
        });
    }

    function autoSave(form) {
        const formData = new FormData(form);
        const autoSaveUrl = form.getAttribute('data-auto-save-url');

        if (autoSaveUrl) {
            fetch(autoSaveUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': getCSRFToken()
                }
            }).then(response => {
                if (response.ok) {
                    showNotification('تم الحفظ تلقائياً', 'success');
                }
            }).catch(() => { });
        }
    }

    function getCSRFToken() {
        const meta = $('meta[name="csrf-token"]');
        return meta?.getAttribute('content') || '';
    }

    // إدارة النوافذ المنبثقة
    function initModals() {
        $$('[data-modal]').forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const modalId = trigger.getAttribute('data-modal');
                openModal(modalId);
            });
        });

        // إغلاق النوافذ
        document.addEventListener('click', (e) => {
            if (hasClass(e.target, 'modal-overlay') || hasClass(e.target, 'modal-close')) {
                closeModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    }

    function openModal(modalId) {
        const modal = $(`#${modalId}`);
        if (modal) {
            addClass(modal, 'active');
            addClass(document.body, 'modal-open');
        }
    }

    function closeModal() {
        const activeModal = $('.modal.active');
        if (activeModal) {
            removeClass(activeModal, 'active');
            removeClass(document.body, 'modal-open');
        }
    }

    // إدارة الإشعارات
    function initNotifications() {
        $$('.notification').forEach(notification => {
            if (notification.hasAttribute('data-auto-hide')) {
                setTimeout(() => hideNotification(notification), 5000);
            }
        });

        document.addEventListener('click', (e) => {
            if (hasClass(e.target, 'notification-close')) {
                const notification = e.target.closest('.notification');
                hideNotification(notification);
            }
        });
    }

    function showNotification(message, type = 'info') {
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
        closeBtn.textContent = '×';

        content.appendChild(messageDiv);
        content.appendChild(closeBtn);
        notification.appendChild(content);

        let container = $('.notification-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'notification-container';
            document.body.appendChild(container);
        }

        container.appendChild(notification);

        setTimeout(() => hideNotification(notification), 5000);

        closeBtn.addEventListener('click', () => hideNotification(notification));
    }

    function hideNotification(notification) {
        if (notification) {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }
    }

    // فلترة المواقع (الدول والمحافظات والمدن)
    function initLocationFilter() {
        document.addEventListener('change', (e) => {
            if (e.target.name && e.target.name.includes('country_id')) {
                filterByCountry(e.target);
            }
            if (e.target.name && e.target.name.includes('governorate_id')) {
                filterByGovernorate(e.target);
            }
        });

        // تهيئة الفلاتر الموجودة
        $$('select[name*="country_id"]').forEach(countrySelect => {
            if (countrySelect.value) {
                filterByCountry(countrySelect);
            }
        });
    }

    function filterByCountry(countrySelect) {
        const countryId = countrySelect.value;
        const row = countrySelect.closest('.row, .card-body');
        const governorateSelect = $('select[name*="governorate_id"]', row);
        const citySelect = $('select[name*="city_id"]', row);

        if (governorateSelect) {
            filterSelect(governorateSelect, 'data-country', countryId);
            if (!governorateSelect.value || !isOptionVisible(governorateSelect, governorateSelect.value)) {
                governorateSelect.value = '';
            }
        }

        if (citySelect) {
            clearSelect(citySelect);
            citySelect.value = '';
        }
    }

    function filterByGovernorate(governorateSelect) {
        const governorateId = governorateSelect.value;
        const row = governorateSelect.closest('.row, .card-body');
        const citySelect = $('select[name*="city_id"]', row);

        if (citySelect) {
            filterSelect(citySelect, 'data-governorate', governorateId);
            if (!citySelect.value || !isOptionVisible(citySelect, citySelect.value)) {
                citySelect.value = '';
            }
        }
    }

    function filterSelect(select, attribute, value) {
        $$('option', select).forEach(option => {
            const show = option.value === '' || option.getAttribute(attribute) === value;
            option.style.display = show ? 'block' : 'none';
            option.disabled = !show;
        });
    }

    function clearSelect(select) {
        $$('option', select).forEach(option => {
            if (option.value !== '') {
                option.style.display = 'none';
                option.disabled = true;
            }
        });
    }

    function isOptionVisible(select, value) {
        const option = $(`option[value="${value}"]`, select);
        return option && option.style.display !== 'none' && !option.disabled;
    }

    // تأكيد العمليات
    function initConfirmations() {
        $$('form.js-confirm, form.js-confirm-delete').forEach(form => {
            form.addEventListener('submit', (e) => {
                const msg = form.dataset.confirm || 'هل أنت متأكد؟';
                if (!confirm(msg)) {
                    e.preventDefault();
                }
            });
        });

        $$('[data-confirm]').forEach(element => {
            element.addEventListener('click', (e) => {
                const msg = element.getAttribute('data-confirm');
                if (!confirm(msg)) {
                    e.preventDefault();
                }
            });
        });
    }

    // تهيئة كل شيء
    function init() {
        initSidebar();
        initDropdowns();
        initTables();
        initForms();
        initModals();
        initNotifications();
        initLocationFilter();
        initConfirmations();
    }

    // بدء التطبيق
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // تصدير الدوال للاستخدام الخارجي
    window.AdminPanel = {
        showNotification,
        openModal,
        closeModal
    };

})();