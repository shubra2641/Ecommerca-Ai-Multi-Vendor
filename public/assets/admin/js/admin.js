/**
 * Simplified Admin JavaScript
 * Minimal functionality with reduced complexity
 */
(function () {
    'use strict';

    window.AdminPanel = {
        galleryManager: null,

        init() {
            this.initSidebar();
            this.initDropdowns();
            this.initConfirmations();
            this.initProductForm();
            this.initMediaManager();
        },

        initSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('mobileMenuToggle');
            const overlay = document.querySelector('.sidebar-overlay');

            if (!sidebar || !toggle) return;

            const toggleSidebar = () => {
                sidebar.classList.toggle('active');
                overlay?.classList.toggle('active');
            };

            toggle.addEventListener('click', e => {
                e.preventDefault();
                toggleSidebar();
            });

            overlay?.addEventListener('click', toggleSidebar);

            document.addEventListener('click', e => {
                if (e.target.closest('.nav-item') && window.innerWidth <= 992) {
                    toggleSidebar();
                }
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 992) {
                    sidebar.classList.remove('active');
                    overlay?.classList.remove('active');
                }
            });
        },

        initDropdowns() {
            document.addEventListener('click', e => {
                const toggle = e.target.closest('.dropdown-toggle');
                if (!toggle) return;

                e.preventDefault();
                e.stopPropagation();

                const dropdown = toggle.closest('.dropdown');
                if (!dropdown) return;

                // Close other dropdowns
                document.querySelectorAll('.dropdown.show').forEach(d => {
                    if (d !== dropdown) {
                        d.classList.remove('show');
                        d.querySelector('.dropdown-toggle')?.setAttribute('aria-expanded', 'false');
                    }
                });

                // Toggle current
                const isOpen = dropdown.classList.contains('show');
                dropdown.classList.toggle('show');
                toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            });

            // Close on outside click
            document.addEventListener('click', e => {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown.show').forEach(d => {
                        d.classList.remove('show');
                        d.querySelector('.dropdown-toggle')?.setAttribute('aria-expanded', 'false');
                    });
                }
            });
        },

        initConfirmations() {
            const confirmAction = e => {
                const msg = e.target.dataset.confirm || e.target.closest('[data-confirm]')?.dataset.confirm || 'Are you sure?';
                if (!confirm(msg)) e.preventDefault();
            };

            document.querySelectorAll('form.js-confirm, form.js-confirm-delete').forEach(form => {
                form.addEventListener('submit', confirmAction);
            });

            document.querySelectorAll('[data-confirm]').forEach(el => {
                el.addEventListener('click', confirmAction);
            });
        },

        initProductForm() {
            const typeSelect = document.getElementById('type-select');
            const physicalTypeSelect = document.getElementById('physical-type-select');

            if (!typeSelect || !physicalTypeSelect) return;

            this.galleryManager = this.setupGalleryManager();

            const toggleSections = () => {
                const isVariable = typeSelect.value === 'variable';
                const isPhysical = physicalTypeSelect.value === 'physical';
                const isDigital = physicalTypeSelect.value === 'digital';

                document.querySelectorAll('.variable-only').forEach(el =>
                    el.classList.toggle('d-none', !(isVariable && isPhysical))
                );

                document.querySelectorAll('.simple-only').forEach(el =>
                    el.classList.toggle('d-none', isVariable)
                );

                document.querySelectorAll('.digital-only').forEach(el => {
                    el.classList.toggle('d-none', !isDigital);
                    el.classList.toggle('envato-hidden', !isDigital);
                });
            };

            typeSelect.addEventListener('change', toggleSections);
            physicalTypeSelect.addEventListener('change', toggleSections);
            toggleSections();

            // Simplified variation management
            const getSelectedAttrs = () => Array.from(document.querySelectorAll('.used-attr-checkbox:checked')).map(cb => cb.value);

            const createVariationRow = selectedAttrs => {
                const tbody = document.querySelector('#variations-table tbody');
                if (!tbody) return;

                const rowCount = tbody.querySelectorAll('tr').length;
                const meta = document.getElementById('product-variation-meta');
                let attributesHtml = '';

                if (meta && selectedAttrs.length > 0) {
                    try {
                        const attrData = JSON.parse(atob(meta.dataset.attributes || 'W10='));
                        attributesHtml = selectedAttrs.map(attrSlug => {
                            const attr = attrData.find(a => a.slug === attrSlug);
                            return attr ? `
                                <select name="variations[${rowCount}][attributes][${attrSlug}]" class="form-select form-select-sm mb-1 variation-attr-select" data-attr-name="${attr.name}">
                                    <option value="">${attr.name}</option>
                                    ${attr.values.map(val => `<option value="${val.value}">${val.value}</option>`).join('')}
                                </select>` : '';
                        }).join('');
                    } catch (e) {
                        console.error('Error parsing variation meta:', e);
                    }
                }

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td class="text-center"><input type="checkbox" name="variations[${rowCount}][active]" value="1" checked class="form-check-input"></td>
                    <td>
                        <div class="mb-2"><input type="text" name="variations[${rowCount}][name]" class="form-control form-control-sm variation-name" placeholder="Variation Name"></div>
                        <div>${attributesHtml}</div>
                    </td>
                    <td class="d-none d-md-table-cell"><input type="text" name="variations[${rowCount}][sku]" class="form-control form-control-sm"></td>
                    <td><input type="number" step="0.01" name="variations[${rowCount}][price]" class="form-control form-control-sm" required></td>
                    <td class="d-none d-lg-table-cell"><input type="number" step="0.01" name="variations[${rowCount}][sale_price]" class="form-control form-control-sm"></td>
                    <td class="d-none d-md-table-cell"><input type="number" name="variations[${rowCount}][stock_qty]" value="0" class="form-control form-control-sm"></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-variation"><i class="fas fa-trash-alt"></i></button></td>
                `;

                tbody.appendChild(newRow);

                newRow.querySelectorAll('.variation-attr-select').forEach(select => {
                    select.addEventListener('change', () => this.updateVariationName(newRow));
                });
            };

            document.addEventListener('click', e => {
                if (e.target.closest('.remove-variation')) {
                    e.preventDefault();
                    e.target.closest('tr').remove();
                }
            });

            document.getElementById('add-variation')?.addEventListener('click', () => {
                const selectedAttrs = getSelectedAttrs();
                if (selectedAttrs.length === 0) {
                    alert('Please select at least one attribute first.');
                    return;
                }
                createVariationRow(selectedAttrs);
            });

            document.querySelectorAll('.used-attr-checkbox').forEach(cb => {
                cb.addEventListener('change', () => {
                    document.querySelectorAll('#variations-table tbody tr:not([data-variation-id])').forEach(row => row.remove());
                });
            });
        },

        updateVariationName(row) {
            const selects = row.querySelectorAll('.variation-attr-select');
            const nameParts = Array.from(selects).map(select => select.value ? `${select.dataset.attrName}: ${select.value}` : '').filter(Boolean);
            const nameInput = row.querySelector('.variation-name');
            if (nameInput && nameParts.length > 0) {
                nameInput.value = nameParts.join(', ');
            }
        },

        getStorageUrl(path) {
            if (!path || /^https?:\/\//i.test(path)) return path || '';
            const base = document.body.getAttribute('data-storage-base') || '';
            if (!base) return path;
            const cleaned = path.replace(/^\/+/, '').replace(/^storage\//, '');
            return base.replace(/\/+$/, '') + '/' + cleaned;
        },

        setupGalleryManager() {
            const container = document.getElementById('gallery-manager');
            const input = document.getElementById('gallery-input');
            if (!container || !input) return null;

            const manager = {
                getValues: () => {
                    try {
                        const parsed = JSON.parse(input.value || '[]');
                        return Array.isArray(parsed) ? parsed : [];
                    } catch {
                        return [];
                    }
                },
                setValues: values => {
                    input.value = JSON.stringify(values);
                    input.dispatchEvent(new Event('change'));
                },
                add: paths => {
                    const values = this.getValues();
                    const list = Array.isArray(paths) ? paths : [paths];
                    list.forEach(path => {
                        if (path && !values.includes(path)) values.push(path);
                    });
                    this.setValues(values);
                    this.render();
                },
                remove: index => {
                    const values = this.getValues();
                    if (index >= 0 && index < values.length) {
                        values.splice(index, 1);
                        this.setValues(values);
                        this.render();
                    }
                },
                render: () => {
                    const values = this.getValues();
                    container.innerHTML = '';

                    if (!values.length) {
                        container.innerHTML = `<div class="text-muted small">${container.getAttribute('data-empty-text') || 'No images yet.'}</div>`;
                        return;
                    }

                    values.forEach((path, index) => {
                        const item = document.createElement('div');
                        item.className = 'border rounded d-flex align-items-center gap-2 p-1 admin-gallery-item';

                        const thumb = document.createElement('div');
                        thumb.className = 'admin-gallery-thumb flex-shrink-0';
                        thumb.style.cssText = 'width:56px;height:56px;border-radius:0.25rem;background-size:cover;background-position:center;';
                        const url = AdminPanel.getStorageUrl(path);
                        thumb.style.backgroundImage = url ? `url('${encodeURI(url)}')` : '';
                        if (!url) thumb.classList.add('bg-light');

                        const label = document.createElement('div');
                        label.className = 'small text-truncate flex-grow-1';
                        label.title = label.textContent = path;

                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'btn btn-sm btn-outline-danger';
                        removeBtn.setAttribute('data-remove-index', index.toString());
                        removeBtn.textContent = '×';

                        item.append(thumb, label, removeBtn);
                        container.appendChild(item);
                    });
                }
            };

            container.addEventListener('click', e => {
                const btn = e.target.closest('[data-remove-index]');
                if (btn) {
                    e.preventDefault();
                    manager.remove(parseInt(btn.getAttribute('data-remove-index')));
                }
            });

            manager.render();
            return manager;
        },

        initMediaManager() {
            const modalEl = document.getElementById('mediaUploadModal');
            if (!modalEl) return;

            const form = modalEl.querySelector('#mediaUploadForm');
            const fileInput = modalEl.querySelector('#mediaUploadInput');
            const errorBox = modalEl.querySelector('[data-media-error]');
            const submitBtn = modalEl.querySelector('[data-media-submit]');
            const hint = modalEl.querySelector('[data-media-hint]');
            const title = modalEl.querySelector('[data-media-title]');
            const modal = window.bootstrap?.Modal ? new window.bootstrap.Modal(modalEl) : null;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            let currentConfig = null;

            const resetModal = () => {
                errorBox?.classList.add('d-none');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = submitBtn.textContent;
                }
                form?.reset();
                fileInput.value = '';
            };

            const showError = msg => {
                if (errorBox) {
                    errorBox.textContent = msg || 'Upload failed.';
                    errorBox.classList.remove('d-none');
                }
            };

            document.querySelectorAll('[data-open-media]').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    const selector = btn.getAttribute('data-media-target');
                    const targetField = selector ? document.querySelector(selector) : null;
                    if (!targetField) return;

                    currentConfig = {
                        targetField,
                        mode: btn.getAttribute('data-media-mode') === 'gallery' ? 'gallery' : 'single',
                        accept: btn.getAttribute('data-media-accept') || 'image/*'
                    };

                    resetModal();

                    if (fileInput) {
                        fileInput.multiple = currentConfig.mode === 'gallery';
                        fileInput.setAttribute('accept', currentConfig.accept);
                    }

                    hint.textContent = currentConfig.accept.includes('image') ?
                        (currentConfig.mode === 'gallery' ? 'Accepted formats: JPG, PNG, WEBP. Max size 4 MB each.' : 'Accepted formats: JPG, PNG, WEBP. Max size 4 MB.') :
                        'Accepted: ' + currentConfig.accept;

                    title.textContent = btn.getAttribute('data-media-label') || 'Upload Media';
                    modal?.show();
                });
            });

            form?.addEventListener('submit', async e => {
                e.preventDefault();
                if (!currentConfig || !fileInput?.files.length) {
                    showError('Please choose at least one file.');
                    return;
                }

                const formData = new FormData();
                const fieldName = fileInput.multiple ? 'images[]' : 'image';
                Array.from(fileInput.files).forEach(file => formData.append(fieldName, file));
                if (csrfToken) formData.append('_token', csrfToken);

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Uploading…';
                }

                try {
                    const response = await fetch(form.getAttribute('action'), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
                        },
                        body: formData
                    });

                    const data = await response.json().catch(() => null);
                    if (!response.ok || !data?.success) {
                        throw new Error(data?.message || 'Upload failed.');
                    }

                    if (currentConfig.mode === 'gallery' && this.galleryManager) {
                        const paths = data.files.map(f => f.path).filter(Boolean);
                        if (paths.length) this.galleryManager.add(paths);
                    } else if (currentConfig.targetField) {
                        const path = data.files[0]?.path;
                        if (path) {
                            currentConfig.targetField.value = path;
                            currentConfig.targetField.dispatchEvent(new Event('change'));
                        }
                    }

                    modal?.hide();
                } catch (error) {
                    showError(error.message);
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = submitBtn.textContent;
                    }
                }
            });
        }
    };

    // Initialize on DOM ready
    document.readyState === 'loading' ?
        document.addEventListener('DOMContentLoaded', () => AdminPanel.init()) :
        AdminPanel.init();

})();