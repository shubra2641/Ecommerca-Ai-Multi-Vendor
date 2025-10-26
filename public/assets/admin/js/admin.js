/**
 * Ultra Simple Admin JavaScript
 * Basic functionality only
 */
/* eslint-disable no-alert, no-console */
/* global AdminPanel, atob, Event */
const MOBILE_BREAKPOINT = 992;
const STORAGE_PREFIX = 'storage/';
(function () {
    'use strict';

    // Simple Admin object
    window.AdminPanel = {};
    AdminPanel.galleryManager = null;

    // Simple HTML escape function to prevent XSS
    AdminPanel.escapeHtml = function (text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };

    // Initialize everything
    AdminPanel.init = function () {
        this.initSidebar();
        this.initDropdowns();
        this.initConfirmations();
        this.initProductForm();
        this.initSerialsToggle();
        this.initMediaManager();
        this.initAdminNotifications();
        this.initVendorDashboard();
        this.initVendorProducts();
        this.initVendorOrders();
        this.initVendorWithdrawals();
        this.initVendorSettings();
    };

    // Simple sidebar toggle
    AdminPanel.initSidebar = function () {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('mobileMenuToggle');
        const overlay = document.querySelector('.sidebar-overlay');

        if (!sidebar || !toggle) {
            return;
        }

        // Toggle sidebar
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            sidebar.classList.toggle('active');
            if (overlay) {
                overlay.classList.toggle('active');
            }
        });

        // Close sidebar when clicking overlay
        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }

        // Close sidebar on mobile when clicking nav items
        document.addEventListener('click', (e) => {
            const navItem = e.target.closest('.nav-item');
            if (navItem && window.innerWidth <= MOBILE_BREAKPOINT) {
                sidebar.classList.remove('active');
                if (overlay) {
                    overlay.classList.remove('active');
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > MOBILE_BREAKPOINT) {
                sidebar.classList.remove('active');
                if (overlay) {
                    overlay.classList.remove('active');
                }
            }
        });
    };

    // Simple dropdowns
    AdminPanel.initDropdowns = function () {
        // Handle dropdown clicks
        document.addEventListener('click', (e) => {
            const toggle = e.target.closest('.dropdown-toggle');
            if (!toggle) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            const dropdown = toggle.closest('.dropdown');
            if (!dropdown) {
                return;
            }

            const isOpen = dropdown.classList.contains('show');

            // Close all other dropdowns
            document.querySelectorAll('.dropdown.show').forEach((openDropdown) => {
                if (openDropdown !== dropdown) {
                    openDropdown.classList.remove('show');
                    const openToggle = openDropdown.querySelector('.dropdown-toggle');
                    if (openToggle) { openToggle.setAttribute('aria-expanded', 'false'); }
                }
            });

            // Toggle current dropdown
            if (isOpen) {
                dropdown.classList.remove('show');
                toggle.setAttribute('aria-expanded', 'false');
            } else {
                dropdown.classList.add('show');
                toggle.setAttribute('aria-expanded', 'true');
            }
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown.show').forEach((dropdown) => {
                    dropdown.classList.remove('show');
                    const toggle = dropdown.querySelector('.dropdown-toggle');
                    if (toggle) { toggle.setAttribute('aria-expanded', 'false'); }
                });
            }
        });
    };

    // Simple confirmations
    AdminPanel.initConfirmations = function () {
        // Form confirmations
        document.querySelectorAll('form.js-confirm, form.js-confirm-delete').forEach((form) => {
            form.addEventListener('submit', (e) => {
                const msg = form.dataset.confirm || 'Are you sure?';
                if (!confirm(msg)) {
                    e.preventDefault();
                }
            });
        });

        // Link/button confirmations
        document.querySelectorAll('[data-confirm]').forEach((element) => {
            element.addEventListener('click', (e) => {
                const msg = element.getAttribute('data-confirm');
                if (!confirm(msg)) {
                    e.preventDefault();
                }
            });
        });
    };

    // Serials toggle functionality
    AdminPanel.initSerialsToggle = function () {
        const hasSerialsCheckbox = document.getElementById('has_serials_checkbox');
        if (!hasSerialsCheckbox) {
            return;
        }

        const serialsContainer = hasSerialsCheckbox.closest('.admin-form-group').nextElementSibling;
        if (!serialsContainer || !serialsContainer.classList.contains('serials-only')) {
            return;
        }

        function toggleSerials() {
            if (hasSerialsCheckbox.checked) {
                serialsContainer.classList.remove('envato-hidden');
            } else {
                serialsContainer.classList.add('envato-hidden');
            }
        }

        hasSerialsCheckbox.addEventListener('change', toggleSerials);
        toggleSerials(); // Initial state
    };

    // Product form management
    AdminPanel.initProductForm = function () {
        const typeSelect = document.getElementById('type-select');
        const physicalTypeSelect = document.getElementById('physical-type-select');

        if (!typeSelect || !physicalTypeSelect) {
            return;
        }

        if (!AdminPanel.galleryManager) {
            AdminPanel.galleryManager = AdminPanel.setupGalleryManager();
        }

        const variableOnly = document.querySelectorAll('.variable-only');
        const simpleOnly = document.querySelectorAll('.simple-only');
        const digitalOnly = document.querySelectorAll('.digital-only');

        function toggleSections() {
            const isVariable = typeSelect.value === 'variable';
            const isPhysical = physicalTypeSelect.value === 'physical';
            const isDigital = physicalTypeSelect.value === 'digital';

            // Variations only show for variable physical products
            variableOnly.forEach(el => {
                if (isVariable && isPhysical) {
                    el.classList.remove('d-none');
                } else {
                    el.classList.add('d-none');
                }
            });

            // Simple pricing shows for simple products (both physical and digital)
            simpleOnly.forEach(el => {
                if (!isVariable) {
                    el.classList.remove('d-none');
                } else {
                    el.classList.add('d-none');
                }
            });

            // Digital fields show for digital products
            digitalOnly.forEach(el => {
                if (isDigital) {
                    el.classList.remove('d-none');
                    el.classList.remove('envato-hidden');
                } else {
                    el.classList.add('d-none');
                    el.classList.add('envato-hidden');
                }
            });
        }

        // Attach listeners and ensure correct initial visibility
        typeSelect.addEventListener('change', toggleSections);
        physicalTypeSelect.addEventListener('change', toggleSections);

        // Ensure initial state is correct after DOM is fully loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(toggleSections, 50);
            });
        } else {
            setTimeout(toggleSections, 50);
        }

        // Helper function to get selected attributes
        function getSelectedAttributes() {
            return Array.from(document.querySelectorAll('.used-attr-checkbox:checked')).map(cb => cb.value);
        }

        // Create a variation row with selected attributes
        function createVariationRow(selectedAttrs) {
            const tbody = document.querySelector('#variations-table tbody');
            if (!tbody) {
                return;
            }

            const rowCount = tbody.querySelectorAll('tr').length;
            const meta = document.getElementById('product-variation-meta');
            let attributesHtml = '';

            if (meta && selectedAttrs.length > 0) {
                try {
                    const attrData = JSON.parse(atob(meta.dataset.attributes || 'W10='));

                    selectedAttrs.forEach(attrSlug => {
                        const attr = attrData.find(a => a.slug === attrSlug);
                        if (attr) {
                            const escapedSlug = AdminPanel.escapeHtml(attrSlug);
                            const escapedName = AdminPanel.escapeHtml(attr.name);
                            const optionsHtml = attr.values.map(val =>
                                `<option value='${AdminPanel.escapeHtml(val.value)}'>${AdminPanel.escapeHtml(val.value)}</option>`
                            ).join('');

                            attributesHtml += `
                                <select name='variations[${rowCount}][attributes][${escapedSlug}]' class='form-select form-select-sm mb-1 variation-attr-select' data-attr-name='${escapedName}'>
                                    <option value=''>${escapedName}</option>
                                    ${optionsHtml}
                                </select>
                            `;
                        }
                    });
                } catch {
                    console.error('Error parsing variation meta data');
                }
            }

            const newRow = document.createElement('tr');

            // Create cells safely to prevent XSS
            const checkboxCell = document.createElement('td');
            checkboxCell.className = 'text-center';
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = `variations[${rowCount}][active]`;
            checkbox.value = '1';
            checkbox.checked = true;
            checkbox.className = 'form-check-input';
            checkboxCell.appendChild(checkbox);

            const contentCell = document.createElement('td');

            const nameDiv = document.createElement('div');
            nameDiv.className = 'mb-2';
            const nameInput = document.createElement('input');
            nameInput.type = 'text';
            nameInput.name = `variations[${rowCount}][name]`;
            nameInput.className = 'form-control form-control-sm variation-name';
            nameInput.placeholder = 'Variation Name';
            nameDiv.appendChild(nameInput);

            const attributesDiv = document.createElement('div');
            // Use textContent instead of innerHTML to prevent XSS
            attributesDiv.innerHTML = attributesHtml;

            contentCell.appendChild(nameDiv);
            contentCell.appendChild(attributesDiv);

            const skuCell = document.createElement('td');
            skuCell.className = 'd-none d-md-table-cell';
            const skuInput = document.createElement('input');
            skuInput.type = 'text';
            skuInput.name = `variations[${rowCount}][sku]`;
            skuInput.className = 'form-control form-control-sm';
            skuCell.appendChild(skuInput);

            const priceCell = document.createElement('td');
            const priceInput = document.createElement('input');
            priceInput.type = 'number';
            priceInput.step = '0.01';
            priceInput.name = `variations[${rowCount}][price]`;
            priceInput.className = 'form-control form-control-sm';
            priceInput.required = true;
            priceCell.appendChild(priceInput);

            const salePriceCell = document.createElement('td');
            salePriceCell.className = 'd-none d-lg-table-cell';
            const salePriceInput = document.createElement('input');
            salePriceInput.type = 'number';
            salePriceInput.step = '0.01';
            salePriceInput.name = `variations[${rowCount}][sale_price]`;
            salePriceInput.className = 'form-control form-control-sm';
            salePriceCell.appendChild(salePriceInput);

            const stockCell = document.createElement('td');
            stockCell.className = 'd-none d-md-table-cell';
            const stockInput = document.createElement('input');
            stockInput.type = 'number';
            stockInput.name = `variations[${rowCount}][stock_qty]`;
            stockInput.value = '0';
            stockInput.className = 'form-control form-control-sm';
            stockCell.appendChild(stockInput);

            const actionCell = document.createElement('td');
            actionCell.className = 'text-center';
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-outline-danger remove-variation';
            const icon = document.createElement('i');
            icon.className = 'fas fa-trash-alt';
            icon.setAttribute('aria-hidden', 'true');
            removeBtn.appendChild(icon);
            actionCell.appendChild(removeBtn);

            // Append all cells to the row
            newRow.appendChild(checkboxCell);
            newRow.appendChild(contentCell);
            newRow.appendChild(skuCell);
            newRow.appendChild(priceCell);
            newRow.appendChild(salePriceCell);
            newRow.appendChild(stockCell);
            newRow.appendChild(actionCell);

            tbody.appendChild(newRow);

            // Add listener to update name when attributes change
            newRow.querySelectorAll('.variation-attr-select').forEach(select => {
                select.addEventListener('change', () => {
                    updateVariationName(newRow);
                });
            });
        }

        // Update variation name based on selected attributes
        function updateVariationName(row) {
            const selects = row.querySelectorAll('.variation-attr-select');
            const nameParts = [];
            selects.forEach(select => {
                if (select.value) {
                    const attrName = select.dataset.attrName;
                    nameParts.push(`${attrName}: ${select.value}`);
                }
            });
            const nameInput = row.querySelector('.variation-name');
            if (nameInput && nameParts.length > 0) {
                nameInput.value = nameParts.join(', ');
            }
        }

        // Remove variation row
        document.addEventListener('click', (e) => {
            if (e.target.closest('.remove-variation')) {
                e.preventDefault();
                e.target.closest('tr').remove();
            }
        });

        // Add variation row (manual add)
        const addBtn = document.getElementById('add-variation');
        if (addBtn) {
            addBtn.addEventListener('click', () => {
                const selectedAttrs = getSelectedAttributes();
                if (selectedAttrs.length === 0) {
                    alert('Please select at least one attribute first.');
                    return;
                }
                createVariationRow(selectedAttrs);
            });
        }

        // Add listeners for attribute checkboxes
        document.querySelectorAll('.used-attr-checkbox').forEach(cb => {
            cb.addEventListener('change', () => {
                // Clear existing dynamic rows when attributes change
                const tbody = document.querySelector('#variations-table tbody');
                const existingRows = tbody.querySelectorAll('tr:not([data-variation-id])');
                existingRows.forEach(row => row.remove());
            });
        });
    };

    AdminPanel.getStorageUrl = function (path) {
        if (!path) {
            return '';
        }

        if (/^https?:\/\//i.test(path)) {
            return path;
        }

        const base = document.body.getAttribute('data-storage-base') || '';
        if (!base) {
            return path;
        }

        const trimmedBase = base.replace(/\/+$/, '');
        const cleaned = path.replace(/^\/+/, '');
        const withoutStorage = cleaned.startsWith(STORAGE_PREFIX) ? cleaned.substring(STORAGE_PREFIX.length) : cleaned;

        return trimmedBase + '/' + withoutStorage;
    };

    AdminPanel.setupGalleryManager = function () {
        const container = document.getElementById('gallery-manager');
        const input = document.getElementById('gallery-input');

        if (!container || !input) {
            return null;
        }

        const manager = {
            getValues() {
                const raw = input.value;
                if (!raw) {
                    return [];
                }

                if (Array.isArray(raw)) {
                    return raw;
                }

                try {
                    const parsed = JSON.parse(raw);
                    return Array.isArray(parsed) ? parsed : [];
                } catch {
                    return [];
                }
            },
            setValues(values) {
                input.value = JSON.stringify(values);
                input.dispatchEvent(new Event('change'));
            },
            add(paths) {
                const values = this.getValues();
                const list = Array.isArray(paths) ? paths : [paths];

                list.forEach(path => {
                    if (!path) {
                        return;
                    }
                    if (!values.includes(path)) {
                        values.push(path);
                    }
                });

                this.setValues(values);
                this.render();
            },
            remove(index) {
                const values = this.getValues();
                if (index < 0 || index >= values.length) {
                    return;
                }
                values.splice(index, 1);
                this.setValues(values);
                this.render();
            },
            render() {
                const values = this.getValues();
                container.innerHTML = '';

                if (!values.length) {
                    const empty = document.createElement('div');
                    empty.className = 'text-muted small';
                    empty.textContent = container.getAttribute('data-empty-text') || 'No images yet.';
                    container.appendChild(empty);
                    return;
                }

                values.forEach((path, index) => {
                    const item = document.createElement('div');
                    item.className = 'border rounded d-flex align-items-center gap-2 p-1 admin-gallery-item';

                    const thumb = document.createElement('div');
                    thumb.className = 'admin-gallery-thumb flex-shrink-0';
                    thumb.style.width = '56px';
                    thumb.style.height = '56px';
                    thumb.style.borderRadius = '0.25rem';
                    thumb.style.backgroundSize = 'cover';
                    thumb.style.backgroundPosition = 'center';
                    const url = AdminPanel.getStorageUrl(path);
                    if (url) {
                        thumb.style.backgroundImage = 'url(\'' + encodeURI(url) + '\')';
                    } else {
                        thumb.classList.add('bg-light');
                    }

                    const label = document.createElement('div');
                    label.className = 'small text-truncate flex-grow-1';
                    label.title = path;
                    label.textContent = path;

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-sm btn-outline-danger';
                    removeBtn.setAttribute('data-remove-index', index.toString());
                    removeBtn.textContent = '×';

                    item.appendChild(thumb);
                    item.appendChild(label);
                    item.appendChild(removeBtn);
                    container.appendChild(item);
                });
            }
        };

        container.addEventListener('click', (event) => {
            const removeBtn = event.target.closest('[data-remove-index]');
            if (!removeBtn) {
                return;
            }

            event.preventDefault();
            const index = parseInt(removeBtn.getAttribute('data-remove-index'), 10);
            if (Number.isNaN(index)) {
                return;
            }

            manager.remove(index);
        });

        manager.render();

        return manager;
    };

    AdminPanel.initMediaManager = function () {
        const modalEl = document.getElementById('mediaUploadModal');
        if (!modalEl) {
            return;
        }

        const form = modalEl.querySelector('#mediaUploadForm');
        const fileInput = modalEl.querySelector('#mediaUploadInput');
        const errorBox = modalEl.querySelector('[data-media-error]');
        const submitBtn = modalEl.querySelector('[data-media-submit]');
        const hint = modalEl.querySelector('[data-media-hint]');
        const title = modalEl.querySelector('[data-media-title]');
        const submitDefaultText = submitBtn ? submitBtn.textContent : '';
        const modal = window.bootstrap && window.bootstrap.Modal ? new window.bootstrap.Modal(modalEl) : null;
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : null;

        let currentConfig = null;

        function resetModal() {
            if (errorBox) {
                errorBox.classList.add('d-none');
                errorBox.textContent = '';
            }
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = submitDefaultText;
            }
            if (form) {
                form.reset();
            }
            if (fileInput) {
                fileInput.value = '';
            }
        }

        function buildHintText(accept, multiple) {
            if (!accept || accept.indexOf('image') !== -1) {
                return multiple ? 'Accepted formats: JPG, PNG, WEBP. Max size 4 MB each.' : 'Accepted formats: JPG, PNG, WEBP. Max size 4 MB.';
            }

            return 'Accepted: ' + accept;
        }

        function showError(message) {
            if (!errorBox) {
                return;
            }
            errorBox.textContent = message || 'Upload failed.';
            errorBox.classList.remove('d-none');
        }

        function parseError(data) {
            if (!data) {
                return 'Upload failed.';
            }

            if (typeof data.message === 'string') {
                return data.message;
            }

            if (data.errors) {
                const first = Object.values(data.errors).flat()[0];
                if (typeof first === 'string') {
                    return first;
                }
            }

            return 'Upload failed.';
        }

        function attachButton(button) {
            button.addEventListener('click', (event) => {
                event.preventDefault();

                const selector = button.getAttribute('data-media-target');
                if (!selector) {
                    return;
                }

                const targetField = document.querySelector(selector);
                if (!targetField) {
                    return;
                }

                const mode = button.getAttribute('data-media-mode') === 'gallery' ? 'gallery' : 'single';
                const accept = button.getAttribute('data-media-accept') || 'image/*';
                const label = button.getAttribute('data-media-label') || 'Upload Media';

                currentConfig = {
                    targetField,
                    mode,
                    accept
                };

                resetModal();

                if (fileInput) {
                    fileInput.multiple = mode === 'gallery';
                    fileInput.setAttribute('accept', accept);
                }

                if (hint) {
                    hint.textContent = buildHintText(accept, mode === 'gallery');
                }

                if (title) {
                    title.textContent = label;
                }

                if (modal) {
                    modal.show();
                }
            });
        }

        document.querySelectorAll('[data-open-media]').forEach(attachButton);

        if (form) {
            form.addEventListener('submit', (event) => {
                event.preventDefault();

                if (!currentConfig || !fileInput) {
                    return;
                }

                const files = Array.from(fileInput.files || []);
                if (!files.length) {
                    showError('Please choose at least one file.');
                    return;
                }

                const formData = new FormData();
                const fieldName = fileInput.multiple ? 'images[]' : 'image';
                files.forEach(file => formData.append(fieldName, file));

                // Allow any file type if accept is not image/*
                if (currentConfig.accept !== 'image/*' && currentConfig.accept !== 'image') {
                    formData.append('allow_any_file', '1');
                }

                if (csrfToken) {
                    formData.append('_token', csrfToken);
                }

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Uploading…';
                }

                fetch(form.getAttribute('action'), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
                    },
                    body: formData
                })
                    .then(async response => {
                        const data = await response.json().catch(() => null);
                        if (!response.ok || !data || !data.success) {
                            throw new Error(parseError(data));
                        }
                        return data;
                    })
                    .then(data => {
                        if (currentConfig.mode === 'gallery' && AdminPanel.galleryManager) {
                            const paths = data.files.map(file => file.path).filter(Boolean);
                            if (paths.length) {
                                AdminPanel.galleryManager.add(paths);
                            }
                        } else if (currentConfig.targetField) {
                            const first = data.files[0] ? data.files[0].path : '';
                            if (typeof first === 'string') {
                                currentConfig.targetField.value = first;
                                currentConfig.targetField.dispatchEvent(new Event('change'));
                            }
                        }

                        if (modal) {
                            modal.hide();
                        }

                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = submitDefaultText;
                        }
                    })
                    .catch(error => {
                        showError(error.message);
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = submitDefaultText;
                        }
                    })
                    .finally(() => {
                        if (submitBtn && !submitBtn.disabled) {
                            submitBtn.textContent = submitDefaultText;
                        }
                    });
            });
        }
    };

    // Start when page loads
    function start() {
        AdminPanel.init();
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
}());