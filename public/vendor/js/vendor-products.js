/*
 * Vendor Products JavaScript
 * Professional interactive features for product management
 */

(function() {
    'use strict';

    // ==========================================================================
    // Configuration
    // ==========================================================================

    const CONFIG = {
        api: {
            baseUrl: '/vendor/api/products',
            endpoints: {
                list: '',
                create: '/create',
                update: '/update',
                delete: '/delete',
                upload: '/upload',
                search: '/search'
            }
        },
        upload: {
            maxFileSize: 5 * 1024 * 1024, // 5MB
            allowedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            maxFiles: 10
        },
        validation: {
            minTitleLength: 3,
            maxTitleLength: 100,
            minDescriptionLength: 10,
            maxDescriptionLength: 1000,
            minPrice: 0.01,
            maxPrice: 999999.99
        },
        animation: {
            duration: 300,
            easing: 'ease-in-out'
        }
    };

    // ==========================================================================
    // Utility Functions
    // ==========================================================================

    const Utils = {
        // Format currency
        formatCurrency: function(amount, currency = 'USD') {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: currency
            }).format(amount);
        },

        // Format file size
        formatFileSize: function(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        // Debounce function
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        // Show notification
        showNotification: function(message, type = 'info', duration = 5000) {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <span class="notification-message">${message}</span>
                    <button class="notification-close" aria-label="Close notification">&times;</button>
                </div>
            `;

            document.body.appendChild(notification);

            // Auto remove
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, duration);

            // Manual close
            notification.querySelector('.notification-close').addEventListener('click', () => {
                notification.remove();
            });
        },

        // Create loading spinner
        createSpinner: function(size = 'medium') {
            const spinner = document.createElement('div');
            spinner.className = `spinner spinner-${size}`;
            spinner.innerHTML = '<div class="spinner-circle"></div>';
            return spinner;
        },

        // Get CSRF token
        getCSRFToken: function() {
            const token = document.querySelector('meta[name="csrf-token"]');
            return token ? token.getAttribute('content') : null;
        },

        // Validate file
        validateFile: function(file) {
            const errors = [];

            // Check file size
            if (file.size > CONFIG.upload.maxFileSize) {
                errors.push(`File size must be less than ${Utils.formatFileSize(CONFIG.upload.maxFileSize)}`);
            }

            // Check file type
            if (!CONFIG.upload.allowedTypes.includes(file.type)) {
                errors.push('File type not allowed. Please use JPEG, PNG, GIF, or WebP images.');
            }

            return errors;
        }
    };

    // ==========================================================================
    // API Service
    // ==========================================================================

    const API = {
        // Make HTTP request
        request: async function(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'X-CSRF-TOKEN': Utils.getCSRFToken()
                }
            };

            // Don't set Content-Type for FormData
            if (!(options.body instanceof FormData)) {
                defaultOptions.headers['Content-Type'] = 'application/json';
            }

            try {
                const response = await fetch(CONFIG.api.baseUrl + url, {
                    ...defaultOptions,
                    ...options,
                    headers: { ...defaultOptions.headers, ...options.headers }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                return await response.json();
            } catch (error) {
                console.error('API request failed:', error);
                throw error;
            }
        },

        // Get products
        getProducts: function(params = {}) {
            const queryString = new URLSearchParams(params).toString();
            return this.request(CONFIG.api.endpoints.list + (queryString ? '?' + queryString : ''));
        },

        // Create product
        createProduct: function(formData) {
            return this.request(CONFIG.api.endpoints.create, {
                method: 'POST',
                body: formData
            });
        },

        // Update product
        updateProduct: function(id, formData) {
            return this.request(`${CONFIG.api.endpoints.update}/${id}`, {
                method: 'PUT',
                body: formData
            });
        },

        // Delete product
        deleteProduct: function(id) {
            return this.request(`${CONFIG.api.endpoints.delete}/${id}`, {
                method: 'DELETE'
            });
        },

        // Upload image
        uploadImage: function(formData) {
            return this.request(CONFIG.api.endpoints.upload, {
                method: 'POST',
                body: formData
            });
        },

        // Search products
        searchProducts: function(query) {
            return this.request(`${CONFIG.api.endpoints.search}?q=${encodeURIComponent(query)}`);
        }
    };

    // ==========================================================================
    // Product Form Manager
    // ==========================================================================

    class ProductFormManager {
        constructor(formElement) {
            this.form = formElement;
            this.imageUploader = null;
            this.init();
        }

        init() {
            this.setupValidation();
            this.setupImageUpload();
            this.setupFormSubmission();
            this.setupRealTimeValidation();
        }

        setupValidation() {
            this.validators = {
                title: (value) => {
                    if (!value || value.trim().length < CONFIG.validation.minTitleLength) {
                        return `Title must be at least ${CONFIG.validation.minTitleLength} characters long`;
                    }
                    if (value.length > CONFIG.validation.maxTitleLength) {
                        return `Title must be no more than ${CONFIG.validation.maxTitleLength} characters long`;
                    }
                    return null;
                },
                description: (value) => {
                    if (!value || value.trim().length < CONFIG.validation.minDescriptionLength) {
                        return `Description must be at least ${CONFIG.validation.minDescriptionLength} characters long`;
                    }
                    if (value.length > CONFIG.validation.maxDescriptionLength) {
                        return `Description must be no more than ${CONFIG.validation.maxDescriptionLength} characters long`;
                    }
                    return null;
                },
                price: (value) => {
                    const price = parseFloat(value);
                    if (isNaN(price) || price < CONFIG.validation.minPrice) {
                        return `Price must be at least $${CONFIG.validation.minPrice}`;
                    }
                    if (price > CONFIG.validation.maxPrice) {
                        return `Price must be no more than $${CONFIG.validation.maxPrice}`;
                    }
                    return null;
                },
                category: (value) => {
                    if (!value || value === '') {
                        return 'Please select a category';
                    }
                    return null;
                }
            };
        }

        setupRealTimeValidation() {
            Object.keys(this.validators).forEach(fieldName => {
                const field = this.form.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.addEventListener('blur', () => this.validateField(fieldName));
                    field.addEventListener('input', Utils.debounce(() => this.validateField(fieldName), 300));
                }
            });
        }

        validateField(fieldName) {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            const validator = this.validators[fieldName];
            
            if (!field || !validator) return true;

            const error = validator(field.value);
            this.updateFieldState(field, !error, error);
            
            return !error;
        }

        validateForm() {
            let isValid = true;
            
            Object.keys(this.validators).forEach(fieldName => {
                if (!this.validateField(fieldName)) {
                    isValid = false;
                }
            });

            return isValid;
        }

        updateFieldState(field, isValid, errorMessage) {
            const formGroup = field.closest('.form-group');
            if (!formGroup) return;

            const existingError = formGroup.querySelector('.field-error');
            
            if (isValid) {
                field.classList.remove('error');
                if (existingError) {
                    existingError.remove();
                }
            } else {
                field.classList.add('error');
                
                if (!existingError && errorMessage) {
                    const errorElement = document.createElement('div');
                    errorElement.className = 'field-error';
                    errorElement.textContent = errorMessage;
                    formGroup.appendChild(errorElement);
                }
            }
        }

        setupImageUpload() {
            const imageUploadArea = this.form.querySelector('.image-upload-area');
            if (!imageUploadArea) return;

            this.imageUploader = new ImageUploader(imageUploadArea);
        }

        setupFormSubmission() {
            this.form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                if (!this.validateForm()) {
                    Utils.showNotification('Please fix the errors before submitting', 'warning');
                    return;
                }

                await this.submitForm();
            });
        }

        async submitForm() {
            const submitButton = this.form.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            
            try {
                // Show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

                // Prepare form data
                const formData = new FormData(this.form);
                
                // Add uploaded images
                if (this.imageUploader) {
                    this.imageUploader.getUploadedFiles().forEach((file, index) => {
                        formData.append(`images[${index}]`, file);
                    });
                }

                // Submit form
                const isEdit = this.form.dataset.productId;
                let response;
                
                if (isEdit) {
                    response = await API.updateProduct(this.form.dataset.productId, formData);
                } else {
                    response = await API.createProduct(formData);
                }

                Utils.showNotification(response.message || 'Product saved successfully!', 'success');
                
                // Redirect after success
                setTimeout(() => {
                    window.location.href = '/vendor/products';
                }, 1500);
                
            } catch (error) {
                Utils.showNotification(error.message || 'Failed to save product', 'danger');
            } finally {
                // Reset button state
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        }
    }

    // ==========================================================================
    // Image Uploader
    // ==========================================================================

    class ImageUploader {
        constructor(container) {
            this.container = container;
            this.uploadedFiles = [];
            this.init();
        }

        init() {
            this.setupDropZone();
            this.setupFileInput();
            this.renderUploadArea();
        }

        renderUploadArea() {
            this.container.innerHTML = `
                <div class="upload-zone" id="uploadZone">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="upload-text">
                        <h4>Drop images here or click to browse</h4>
                        <p>Maximum ${CONFIG.upload.maxFiles} images, up to ${Utils.formatFileSize(CONFIG.upload.maxFileSize)} each</p>
                        <p>Supported formats: JPEG, PNG, GIF, WebP</p>
                    </div>
                    <input type="file" id="fileInput" multiple accept="${CONFIG.upload.allowedTypes.join(',')}" class="hidden-file-input">
                </div>
                <div class="uploaded-images" id="uploadedImages"></div>
            `;
        }

        setupDropZone() {
            this.container.addEventListener('dragover', (e) => {
                e.preventDefault();
                this.container.classList.add('dragover');
            });

            this.container.addEventListener('dragleave', (e) => {
                e.preventDefault();
                if (!this.container.contains(e.relatedTarget)) {
                    this.container.classList.remove('dragover');
                }
            });

            this.container.addEventListener('drop', (e) => {
                e.preventDefault();
                this.container.classList.remove('dragover');
                
                const files = Array.from(e.dataTransfer.files);
                this.handleFiles(files);
            });

            this.container.addEventListener('click', (e) => {
                if (e.target.closest('#uploadZone')) {
                    this.container.querySelector('#fileInput').click();
                }
            });
        }

        setupFileInput() {
            this.container.addEventListener('change', (e) => {
                if (e.target.id === 'fileInput') {
                    const files = Array.from(e.target.files);
                    this.handleFiles(files);
                    e.target.value = ''; // Reset input
                }
            });
        }

        handleFiles(files) {
            // Check total file limit
            if (this.uploadedFiles.length + files.length > CONFIG.upload.maxFiles) {
                Utils.showNotification(`Maximum ${CONFIG.upload.maxFiles} images allowed`, 'warning');
                return;
            }

            files.forEach(file => {
                const errors = Utils.validateFile(file);
                
                if (errors.length > 0) {
                    Utils.showNotification(errors.join(', '), 'danger');
                    return;
                }

                this.addFile(file);
            });
        }

        addFile(file) {
            const fileId = Date.now() + Math.random().toString(36).substr(2, 9);
            const fileObj = {
                id: fileId,
                file: file,
                preview: null
            };

            this.uploadedFiles.push(fileObj);
            this.createPreview(fileObj);
        }

        createPreview(fileObj) {
            const reader = new FileReader();
            
            reader.onload = (e) => {
                fileObj.preview = e.target.result;
                this.renderPreview(fileObj);
            };
            
            reader.readAsDataURL(fileObj.file);
        }

        renderPreview(fileObj) {
            const uploadedImagesContainer = this.container.querySelector('#uploadedImages');
            
            const previewElement = document.createElement('div');
            previewElement.className = 'image-preview';
            previewElement.dataset.fileId = fileObj.id;
            
            previewElement.innerHTML = `
                <div class="image-container">
                    <img src="${fileObj.preview}" alt="Preview">
                    <div class="image-overlay">
                        <button type="button" class="btn-remove" data-file-id="${fileObj.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="image-info">
                    <div class="image-name">${fileObj.file.name}</div>
                    <div class="image-size">${Utils.formatFileSize(fileObj.file.size)}</div>
                </div>
            `;
            
            uploadedImagesContainer.appendChild(previewElement);
            
            // Bind remove event
            previewElement.querySelector('.btn-remove').addEventListener('click', () => {
                this.removeFile(fileObj.id);
            });
        }

        removeFile(fileId) {
            // Remove from uploaded files array
            this.uploadedFiles = this.uploadedFiles.filter(file => file.id !== fileId);
            
            // Remove preview element
            const previewElement = this.container.querySelector(`[data-file-id="${fileId}"]`);
            if (previewElement) {
                previewElement.remove();
            }
        }

        getUploadedFiles() {
            return this.uploadedFiles.map(fileObj => fileObj.file);
        }
    }

    // ==========================================================================
    // Product List Manager
    // ==========================================================================

    class ProductListManager {
        constructor() {
            this.currentPage = 1;
            this.searchQuery = '';
            this.filters = {};
            this.init();
        }

        init() {
            this.setupSearch();
            this.setupFilters();
            this.setupPagination();
            this.setupBulkActions();
            this.bindEvents();
        }

        setupSearch() {
            const searchInput = document.querySelector('#productSearch');
            if (searchInput) {
                searchInput.addEventListener('input', Utils.debounce((e) => {
                    this.searchQuery = e.target.value;
                    this.loadProducts();
                }, 500));
            }
        }

        setupFilters() {
            const filterElements = document.querySelectorAll('.product-filter');
            filterElements.forEach(filter => {
                filter.addEventListener('change', (e) => {
                    this.filters[e.target.name] = e.target.value;
                    this.currentPage = 1;
                    this.loadProducts();
                });
            });
        }

        setupPagination() {
            document.addEventListener('click', (e) => {
                if (e.target.matches('.pagination-link')) {
                    e.preventDefault();
                    const page = parseInt(e.target.dataset.page);
                    if (page && page !== this.currentPage) {
                        this.currentPage = page;
                        this.loadProducts();
                    }
                }
            });
        }

        setupBulkActions() {
            const selectAllCheckbox = document.querySelector('#selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', (e) => {
                    const checkboxes = document.querySelectorAll('.product-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = e.target.checked;
                    });
                    this.updateBulkActions();
                });
            }

            document.addEventListener('change', (e) => {
                if (e.target.matches('.product-checkbox')) {
                    this.updateBulkActions();
                }
            });

            const bulkDeleteBtn = document.querySelector('#bulkDelete');
            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', () => {
                    this.handleBulkDelete();
                });
            }
        }

        bindEvents() {
            document.addEventListener('click', (e) => {
                if (e.target.matches('.btn-delete-product')) {
                    e.preventDefault();
                    const productId = e.target.dataset.productId;
                    this.deleteProduct(productId);
                }
            });
        }

        async loadProducts() {
            try {
                const params = {
                    page: this.currentPage,
                    search: this.searchQuery,
                    ...this.filters
                };

                const response = await API.getProducts(params);
                this.renderProducts(response.data);
                this.renderPagination(response.pagination);
            } catch (error) {
                Utils.showNotification('Failed to load products', 'danger');
            }
        }

        renderProducts(products) {
            const container = document.querySelector('#productsContainer');
            if (!container) return;

            if (products.length === 0) {
                container.innerHTML = `
                    <div class="no-products">
                        <i class="fas fa-box-open"></i>
                        <h3>No products found</h3>
                        <p>Start by adding your first product</p>
                        <a href="/vendor/products/create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                    </div>
                `;
                return;
            }

            container.innerHTML = products.map(product => `
                <div class="product-card" data-product-id="${product.id}">
                    <div class="product-checkbox-container">
                        <input type="checkbox" class="product-checkbox" value="${product.id}">
                    </div>
                    <div class="product-image">
                        <img src="${product.image || '/images/placeholder.jpg'}" alt="${product.title}">
                        <div class="product-status ${product.status}">${product.status}</div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">${product.title}</h3>
                        <p class="product-description">${product.description.substring(0, 100)}...</p>
                        <div class="product-meta">
                            <span class="product-price">${Utils.formatCurrency(product.price)}</span>
                            <span class="product-category">${product.category}</span>
                        </div>
                        <div class="product-stats">
                            <span><i class="fas fa-eye"></i> ${product.views || 0}</span>
                            <span><i class="fas fa-shopping-cart"></i> ${product.sales || 0}</span>
                        </div>
                    </div>
                    <div class="product-actions">
                        <a href="/vendor/products/${product.id}/edit" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-product" data-product-id="${product.id}">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            `).join('');
        }

        renderPagination(pagination) {
            const container = document.querySelector('#paginationContainer');
            if (!container || !pagination) return;

            const { current_page, last_page, prev_page_url, next_page_url } = pagination;
            
            let paginationHTML = '<div class="pagination">';
            
            // Previous button
            if (prev_page_url) {
                paginationHTML += `<a href="#" class="pagination-link" data-page="${current_page - 1}">Previous</a>`;
            }
            
            // Page numbers
            for (let i = Math.max(1, current_page - 2); i <= Math.min(last_page, current_page + 2); i++) {
                const activeClass = i === current_page ? 'active' : '';
                paginationHTML += `<a href="#" class="pagination-link ${activeClass}" data-page="${i}">${i}</a>`;
            }
            
            // Next button
            if (next_page_url) {
                paginationHTML += `<a href="#" class="pagination-link" data-page="${current_page + 1}">Next</a>`;
            }
            
            paginationHTML += '</div>';
            container.innerHTML = paginationHTML;
        }

        updateBulkActions() {
            const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
            const bulkActionsContainer = document.querySelector('#bulkActions');
            
            if (bulkActionsContainer) {
                if (selectedCheckboxes.length > 0) {
                    bulkActionsContainer.style.display = 'block';
                    const countElement = bulkActionsContainer.querySelector('.selected-count');
                    if (countElement) {
                        countElement.textContent = selectedCheckboxes.length;
                    }
                } else {
                    bulkActionsContainer.style.display = 'none';
                }
            }
        }

        async deleteProduct(productId) {
            if (!confirm('Are you sure you want to delete this product?')) {
                return;
            }

            try {
                await API.deleteProduct(productId);
                Utils.showNotification('Product deleted successfully', 'success');
                this.loadProducts();
            } catch (error) {
                Utils.showNotification('Failed to delete product', 'danger');
            }
        }

        async handleBulkDelete() {
            const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
            const productIds = Array.from(selectedCheckboxes).map(cb => cb.value);
            
            if (productIds.length === 0) {
                Utils.showNotification('Please select products to delete', 'warning');
                return;
            }

            if (!confirm(`Are you sure you want to delete ${productIds.length} product(s)?`)) {
                return;
            }

            try {
                await Promise.all(productIds.map(id => API.deleteProduct(id)));
                Utils.showNotification(`${productIds.length} product(s) deleted successfully`, 'success');
                this.loadProducts();
            } catch (error) {
                Utils.showNotification('Failed to delete some products', 'danger');
            }
        }
    }

    // ==========================================================================
    // Initialize Products
    // ==========================================================================

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeProducts);
    } else {
        initializeProducts();
    }

    function initializeProducts() {
        // Initialize product form if exists
        const productForm = document.querySelector('#productForm');
        if (productForm) {
            window.productFormManager = new ProductFormManager(productForm);
        }

        // Initialize product list if exists
        const productsList = document.querySelector('#productsList');
        if (productsList) {
            window.productListManager = new ProductListManager();
        }

        console.log('Vendor Products initialized successfully');
    }

    // Export for global access
    window.VendorProducts = {
        Utils,
        API,
        ProductFormManager,
        ImageUploader,
        ProductListManager,
        CONFIG
    };

})();