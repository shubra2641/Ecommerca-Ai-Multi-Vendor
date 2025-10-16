/**
 * Vendor Main JavaScript
 * Core functionality and utilities for vendor dashboard
 * Professional, accessible, and performance-optimized
 */

(function(window, document) {
    'use strict';

    // Namespace for vendor functionality
    window.VendorApp = window.VendorApp || {};

    /**
     * Core Utilities
     */
    const Utils = {
        // DOM Utilities
        $: function(selector, context = document) {
            return context.querySelector(selector);
        },

        $$: function(selector, context = document) {
            return Array.from(context.querySelectorAll(selector));
        },

        createElement: function(tag, attributes = {}, content = '') {
            const element = document.createElement(tag);
            
            Object.keys(attributes).forEach(key => {
                if (key === 'className') {
                    element.className = attributes[key];
                } else if (key === 'dataset') {
                    Object.keys(attributes[key]).forEach(dataKey => {
                        element.dataset[dataKey] = attributes[key][dataKey];
                    });
                } else {
                    element.setAttribute(key, attributes[key]);
                }
            });
            
            if (content) {
                element.innerHTML = content;
            }
            
            return element;
        },

        // Event Utilities
        on: function(element, event, handler, options = {}) {
            if (typeof element === 'string') {
                element = this.$(element);
            }
            if (element) {
                element.addEventListener(event, handler, options);
            }
        },

        off: function(element, event, handler) {
            if (typeof element === 'string') {
                element = this.$(element);
            }
            if (element) {
                element.removeEventListener(event, handler);
            }
        },

        delegate: function(parent, selector, event, handler) {
            this.on(parent, event, function(e) {
                const target = e.target.closest(selector);
                if (target && parent.contains(target)) {
                    handler.call(target, e);
                }
            });
        },

        // Performance Utilities
        debounce: function(func, wait, immediate = false) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    timeout = null;
                    if (!immediate) func.apply(this, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(this, args);
            };
        },

        throttle: function(func, limit) {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        // Animation Utilities
        animate: function(element, properties, duration = 300, easing = 'ease') {
            return new Promise(resolve => {
                const startTime = performance.now();
                const startValues = {};
                
                // Get initial values
                Object.keys(properties).forEach(prop => {
                    const computedStyle = getComputedStyle(element);
                    startValues[prop] = parseFloat(computedStyle[prop]) || 0;
                });
                
                function animate(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    // Apply easing
                    let easedProgress;
                    switch (easing) {
                        case 'ease-in':
                            easedProgress = progress * progress;
                            break;
                        case 'ease-out':
                            easedProgress = 1 - Math.pow(1 - progress, 2);
                            break;
                        case 'ease-in-out':
                            easedProgress = progress < 0.5 
                                ? 2 * progress * progress 
                                : 1 - Math.pow(-2 * progress + 2, 2) / 2;
                            break;
                        default:
                            easedProgress = progress;
                    }
                    
                    // Update properties
                    Object.keys(properties).forEach(prop => {
                        const startValue = startValues[prop];
                        const endValue = properties[prop];
                        const currentValue = startValue + (endValue - startValue) * easedProgress;
                        
                        if (prop === 'opacity') {
                            element.style[prop] = currentValue;
                        } else {
                            element.style[prop] = currentValue + 'px';
                        }
                    });
                    
                    if (progress < 1) {
                        requestAnimationFrame(animate);
                    } else {
                        resolve();
                    }
                }
                
                requestAnimationFrame(animate);
            });
        },

        // Data Utilities
        formatCurrency: function(amount, currency = 'USD', locale = 'en-US') {
            return new Intl.NumberFormat(locale, {
                style: 'currency',
                currency: currency
            }).format(amount);
        },

        formatDate: function(date, options = {}) {
            const defaultOptions = {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            return new Intl.DateTimeFormat('en-US', { ...defaultOptions, ...options }).format(new Date(date));
        },

        formatNumber: function(number, locale = 'en-US') {
            return new Intl.NumberFormat(locale).format(number);
        },

        // Validation Utilities
        validateEmail: function(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },

        validatePhone: function(phone) {
            const re = /^[\+]?[1-9][\d]{0,15}$/;
            return re.test(phone.replace(/\s/g, ''));
        },

        validateRequired: function(value) {
            return value !== null && value !== undefined && value.toString().trim() !== '';
        },

        // Storage Utilities
        storage: {
            set: function(key, value, expiry = null) {
                const item = {
                    value: value,
                    expiry: expiry ? Date.now() + expiry : null
                };
                localStorage.setItem(key, JSON.stringify(item));
            },

            get: function(key) {
                const itemStr = localStorage.getItem(key);
                if (!itemStr) return null;

                try {
                    const item = JSON.parse(itemStr);
                    if (item.expiry && Date.now() > item.expiry) {
                        localStorage.removeItem(key);
                        return null;
                    }
                    return item.value;
                } catch (e) {
                    return null;
                }
            },

            remove: function(key) {
                localStorage.removeItem(key);
            },

            clear: function() {
                localStorage.clear();
            }
        },

        // HTTP Utilities
        request: async function(url, options = {}) {
            const defaultOptions = {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };

            // Add CSRF token if available
            const csrfToken = this.$('meta[name="csrf-token"]');
            if (csrfToken) {
                defaultOptions.headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
            }

            const config = { ...defaultOptions, ...options };
            
            if (config.body && typeof config.body === 'object' && !(config.body instanceof FormData)) {
                config.body = JSON.stringify(config.body);
            }

            try {
                const response = await fetch(url, config);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return await response.json();
                }
                
                return await response.text();
            } catch (error) {
                console.error('Request failed:', error);
                throw error;
            }
        }
    };

    /**
     * Notification System
     */
    const NotificationManager = {
        container: null,
        notifications: [],
        maxNotifications: 5,

        init: function() {
            this.createContainer();
        },

        createContainer: function() {
            if (this.container) return;

            this.container = Utils.createElement('div', {
                className: 'notification-container',
                style: `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 1080;
                    max-width: 400px;
                    pointer-events: none;
                `
            });

            document.body.appendChild(this.container);
        },

        show: function(message, type = 'info', duration = 5000, options = {}) {
            const notification = this.createNotification(message, type, duration, options);
            this.addNotification(notification);
            return notification;
        },

        createNotification: function(message, type, duration, options) {
            const id = 'notification-' + Date.now() + Math.random().toString(36).substr(2, 9);
            
            const typeClasses = {
                success: 'alert-success',
                error: 'alert-danger',
                warning: 'alert-warning',
                info: 'alert-primary'
            };

            const typeIcons = {
                success: '✓',
                error: '✕',
                warning: '⚠',
                info: 'ℹ'
            };

            const notification = Utils.createElement('div', {
                className: `alert ${typeClasses[type] || typeClasses.info} notification fade-in-down`,
                id: id,
                role: 'alert',
                'aria-live': 'polite',
                style: `
                    margin-bottom: 10px;
                    pointer-events: auto;
                    cursor: pointer;
                    transform: translateX(100%);
                    transition: transform 0.3s ease;
                `
            }, `
                <div class="alert-icon">${typeIcons[type] || typeIcons.info}</div>
                <div class="alert-content">
                    ${options.title ? `<div class="alert-title">${options.title}</div>` : ''}
                    <div class="alert-message">${message}</div>
                </div>
                <button type="button" class="btn-close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            `);

            // Auto-hide
            if (duration > 0) {
                setTimeout(() => {
                    this.hide(id);
                }, duration);
            }

            // Click to close
            Utils.on(notification, 'click', () => {
                this.hide(id);
            });

            return { element: notification, id: id, type: type };
        },

        addNotification: function(notification) {
            this.notifications.push(notification);
            this.container.appendChild(notification.element);

            // Animate in
            requestAnimationFrame(() => {
                notification.element.style.transform = 'translateX(0)';
            });

            // Remove oldest if too many
            if (this.notifications.length > this.maxNotifications) {
                const oldest = this.notifications.shift();
                this.hide(oldest.id);
            }
        },

        hide: function(id) {
            const notification = this.notifications.find(n => n.id === id);
            if (!notification) return;

            notification.element.style.transform = 'translateX(100%)';
            notification.element.style.opacity = '0';

            setTimeout(() => {
                if (notification.element.parentNode) {
                    notification.element.parentNode.removeChild(notification.element);
                }
                this.notifications = this.notifications.filter(n => n.id !== id);
            }, 300);
        },

        success: function(message, options = {}) {
            return this.show(message, 'success', 5000, options);
        },

        error: function(message, options = {}) {
            return this.show(message, 'error', 8000, options);
        },

        warning: function(message, options = {}) {
            return this.show(message, 'warning', 6000, options);
        },

        info: function(message, options = {}) {
            return this.show(message, 'info', 5000, options);
        }
    };

    /**
     * Loading Manager
     */
    const LoadingManager = {
        overlay: null,
        activeLoaders: new Set(),

        show: function(target = document.body, message = 'Loading...') {
            const loaderId = 'loader-' + Date.now();
            this.activeLoaders.add(loaderId);

            if (target === document.body) {
                this.showGlobalLoader(message);
            } else {
                this.showLocalLoader(target, message, loaderId);
            }

            return loaderId;
        },

        showGlobalLoader: function(message) {
            if (this.overlay) return;

            this.overlay = Utils.createElement('div', {
                className: 'loading-overlay'
            }, `
                <div class="loading-content">
                    <div class="loading-spinner"></div>
                    <div class="loading-message">${message}</div>
                </div>
            `);

            document.body.appendChild(this.overlay);
            document.body.style.overflow = 'hidden';
        },

        showLocalLoader: function(target, message, loaderId) {
            const originalPosition = getComputedStyle(target).position;
            if (originalPosition === 'static') {
                target.style.position = 'relative';
            }

            const loader = Utils.createElement('div', {
                className: 'local-loading-overlay',
                'data-loader-id': loaderId
            }, `
                <div class="loading-content">
                    <div class="loading-spinner"></div>
                    <div class="loading-message">${message}</div>
                </div>
            `);

            target.appendChild(loader);
        },

        hide: function(loaderId = null) {
            if (loaderId) {
                this.activeLoaders.delete(loaderId);
                const localLoader = Utils.$(`[data-loader-id="${loaderId}"]`);
                if (localLoader) {
                    localLoader.remove();
                }
            }

            if (this.activeLoaders.size === 0 && this.overlay) {
                this.overlay.remove();
                this.overlay = null;
                document.body.style.overflow = '';
            }
        },

        hideAll: function() {
            this.activeLoaders.clear();
            
            // Remove global overlay
            if (this.overlay) {
                this.overlay.remove();
                this.overlay = null;
                document.body.style.overflow = '';
            }

            // Remove all local loaders
            Utils.$$('.local-loading-overlay').forEach(loader => {
                loader.remove();
            });
        }
    };

    /**
     * Form Manager
     */
    const FormManager = {
        forms: new Map(),

        init: function() {
            this.bindEvents();
            this.initializeForms();
        },

        bindEvents: function() {
            // Auto-save functionality
            Utils.delegate(document, 'form[data-auto-save]', 'input', 
                Utils.debounce(this.autoSave.bind(this), 1000)
            );

            // Real-time validation
            Utils.delegate(document, '.form-control', 'blur', this.validateField.bind(this));
            Utils.delegate(document, '.form-control', 'input', 
                Utils.debounce(this.validateField.bind(this), 500)
            );

            // Form submission
            Utils.delegate(document, 'form[data-ajax]', 'submit', this.handleAjaxSubmit.bind(this));
        },

        initializeForms: function() {
            Utils.$$('form').forEach(form => {
                this.registerForm(form);
            });
        },

        registerForm: function(form) {
            const formId = form.id || 'form-' + Date.now();
            if (!form.id) form.id = formId;

            const formData = {
                element: form,
                validators: new Map(),
                isDirty: false,
                isValid: true
            };

            this.forms.set(formId, formData);
            return formId;
        },

        addValidator: function(formId, fieldName, validator) {
            const form = this.forms.get(formId);
            if (form) {
                form.validators.set(fieldName, validator);
            }
        },

        validateField: function(event) {
            const field = event.target;
            const form = field.closest('form');
            if (!form) return;

            const formData = this.forms.get(form.id);
            if (!formData) return;

            const fieldName = field.name;
            const validator = formData.validators.get(fieldName);
            
            let isValid = true;
            let message = '';

            // Built-in validation
            if (field.hasAttribute('required') && !Utils.validateRequired(field.value)) {
                isValid = false;
                message = 'This field is required';
            } else if (field.type === 'email' && field.value && !Utils.validateEmail(field.value)) {
                isValid = false;
                message = 'Please enter a valid email address';
            } else if (field.type === 'tel' && field.value && !Utils.validatePhone(field.value)) {
                isValid = false;
                message = 'Please enter a valid phone number';
            }

            // Custom validation
            if (isValid && validator && typeof validator === 'function') {
                const result = validator(field.value, field);
                if (result !== true) {
                    isValid = false;
                    message = result || 'Invalid value';
                }
            }

            this.updateFieldValidation(field, isValid, message);
            this.updateFormValidation(form);

            return isValid;
        },

        updateFieldValidation: function(field, isValid, message) {
            const formGroup = field.closest('.form-group');
            if (!formGroup) return;

            // Remove existing validation classes and messages
            field.classList.remove('is-valid', 'is-invalid');
            const existingError = formGroup.querySelector('.form-error');
            const existingSuccess = formGroup.querySelector('.form-success');
            
            if (existingError) existingError.remove();
            if (existingSuccess) existingSuccess.remove();

            if (field.value.trim() === '') {
                // No validation styling for empty fields (unless required)
                return;
            }

            if (isValid) {
                field.classList.add('is-valid');
                const successMessage = Utils.createElement('div', {
                    className: 'form-success'
                }, '✓ Valid');
                formGroup.appendChild(successMessage);
            } else {
                field.classList.add('is-invalid');
                const errorMessage = Utils.createElement('div', {
                    className: 'form-error'
                }, `⚠ ${message}`);
                formGroup.appendChild(errorMessage);
            }
        },

        updateFormValidation: function(form) {
            const formData = this.forms.get(form.id);
            if (!formData) return;

            const invalidFields = form.querySelectorAll('.form-control.is-invalid');
            const isValid = invalidFields.length === 0;
            
            formData.isValid = isValid;

            // Update submit button state
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = !isValid;
            }
        },

        autoSave: function(event) {
            const form = event.target.closest('form');
            if (!form) return;

            const formData = this.forms.get(form.id);
            if (!formData) return;

            formData.isDirty = true;

            const data = new FormData(form);
            const autoSaveUrl = form.dataset.autoSaveUrl;
            
            if (autoSaveUrl) {
                Utils.request(autoSaveUrl, {
                    method: 'POST',
                    body: data
                }).then(() => {
                    NotificationManager.success('Changes saved automatically', { title: 'Auto-save' });
                }).catch(() => {
                    NotificationManager.warning('Auto-save failed', { title: 'Warning' });
                });
            } else {
                // Save to localStorage
                const formObject = {};
                data.forEach((value, key) => {
                    formObject[key] = value;
                });
                Utils.storage.set(`form-${form.id}`, formObject, 24 * 60 * 60 * 1000); // 24 hours
            }
        },

        handleAjaxSubmit: function(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = this.forms.get(form.id);
            
            if (!formData || !formData.isValid) {
                NotificationManager.error('Please fix the errors in the form');
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            const originalText = submitBtn ? submitBtn.textContent : '';
            
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('loading');
            }

            const loaderId = LoadingManager.show(form);
            const data = new FormData(form);
            const url = form.action || window.location.href;
            const method = form.method || 'POST';

            Utils.request(url, {
                method: method,
                body: data
            }).then(response => {
                if (response.success) {
                    NotificationManager.success(response.message || 'Form submitted successfully');
                    
                    if (response.redirect) {
                        setTimeout(() => {
                            window.location.href = response.redirect;
                        }, 1000);
                    } else if (form.dataset.resetOnSuccess) {
                        form.reset();
                        this.clearValidation(form);
                    }
                } else {
                    NotificationManager.error(response.message || 'An error occurred');
                    
                    if (response.errors) {
                        this.displayServerErrors(form, response.errors);
                    }
                }
            }).catch(error => {
                NotificationManager.error('Network error. Please try again.');
                console.error('Form submission error:', error);
            }).finally(() => {
                LoadingManager.hide(loaderId);
                
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('loading');
                }
            });
        },

        displayServerErrors: function(form, errors) {
            Object.keys(errors).forEach(fieldName => {
                const field = form.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    this.updateFieldValidation(field, false, errors[fieldName][0]);
                }
            });
        },

        clearValidation: function(form) {
            const fields = form.querySelectorAll('.form-control');
            fields.forEach(field => {
                field.classList.remove('is-valid', 'is-invalid');
            });

            const messages = form.querySelectorAll('.form-error, .form-success');
            messages.forEach(message => message.remove());
        }
    };

    /**
     * Theme Manager
     */
    const ThemeManager = {
        currentTheme: 'light',
        
        init: function() {
            this.loadTheme();
            this.bindEvents();
        },

        bindEvents: function() {
            const themeToggle = Utils.$('[data-theme-toggle]');
            if (themeToggle) {
                Utils.on(themeToggle, 'click', this.toggle.bind(this));
            }
        },

        loadTheme: function() {
            const savedTheme = Utils.storage.get('vendor-theme') || 'light';
            this.setTheme(savedTheme);
        },

        setTheme: function(theme) {
            this.currentTheme = theme;
            document.documentElement.setAttribute('data-theme', theme);
            Utils.storage.set('vendor-theme', theme);
            
            // Update toggle button
            const themeToggle = Utils.$('[data-theme-toggle]');
            if (themeToggle) {
                const icon = themeToggle.querySelector('i, .icon');
                if (icon) {
                    icon.className = theme === 'dark' ? 'icon-sun' : 'icon-moon';
                }
            }
        },

        toggle: function() {
            const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
            this.setTheme(newTheme);
        }
    };

    /**
     * Performance Monitor
     */
    const PerformanceMonitor = {
        init: function() {
            this.initLazyLoading();
            this.initIntersectionObserver();
            this.monitorPerformance();
        },

        initLazyLoading: function() {
            const lazyImages = Utils.$$('img[data-src]');
            
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                lazyImages.forEach(img => {
                    img.classList.add('lazy');
                    imageObserver.observe(img);
                });
            } else {
                // Fallback for older browsers
                lazyImages.forEach(img => {
                    img.src = img.dataset.src;
                });
            }
        },

        initIntersectionObserver: function() {
            if (!('IntersectionObserver' in window)) return;

            // Animate elements on scroll
            const animateElements = Utils.$$('[data-animate]');
            const animateObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        const animation = element.dataset.animate;
                        element.classList.add(animation);
                        animateObserver.unobserve(element);
                    }
                });
            }, { threshold: 0.1 });

            animateElements.forEach(element => {
                animateObserver.observe(element);
            });
        },

        monitorPerformance: function() {
            // Monitor page load performance
            window.addEventListener('load', () => {
                if ('performance' in window) {
                    const perfData = performance.getEntriesByType('navigation')[0];
                    const loadTime = perfData.loadEventEnd - perfData.loadEventStart;
                    
                    if (loadTime > 3000) {
                        console.warn('Page load time is slow:', loadTime + 'ms');
                    }
                }
            });

            // Monitor memory usage (if available)
            if ('memory' in performance) {
                setInterval(() => {
                    const memory = performance.memory;
                    if (memory.usedJSHeapSize > memory.jsHeapSizeLimit * 0.9) {
                        console.warn('High memory usage detected');
                    }
                }, 30000);
            }
        }
    };

    /**
     * Accessibility Manager
     */
    const AccessibilityManager = {
        init: function() {
            this.enhanceKeyboardNavigation();
            this.addAriaLabels();
            this.initFocusManagement();
        },

        enhanceKeyboardNavigation: function() {
            // Escape key handling
            Utils.on(document, 'keydown', (e) => {
                if (e.key === 'Escape') {
                    // Close modals, dropdowns, etc.
                    const activeModal = Utils.$('.modal.show');
                    if (activeModal) {
                        this.closeModal(activeModal);
                    }

                    const activeDropdown = Utils.$('.dropdown.show');
                    if (activeDropdown) {
                        activeDropdown.classList.remove('show');
                    }
                }
            });

            // Tab navigation for custom components
            Utils.delegate(document, '[role="button"]', 'keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    e.target.click();
                }
            });
        },

        addAriaLabels: function() {
            // Add missing aria-labels
            Utils.$$('button:not([aria-label]):not([aria-labelledby])').forEach(button => {
                if (!button.textContent.trim()) {
                    button.setAttribute('aria-label', 'Button');
                }
            });

            // Add role attributes
            Utils.$$('.btn:not([role])').forEach(btn => {
                btn.setAttribute('role', 'button');
            });
        },

        initFocusManagement: function() {
            // Focus trap for modals
            Utils.delegate(document, '.modal', 'keydown', (e) => {
                if (e.key === 'Tab') {
                    this.trapFocus(e, e.currentTarget);
                }
            });
        },

        trapFocus: function(e, container) {
            const focusableElements = container.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            if (e.shiftKey) {
                if (document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        },

        closeModal: function(modal) {
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
            
            // Return focus to trigger element
            const trigger = Utils.$(`[data-target="#${modal.id}"]`);
            if (trigger) {
                trigger.focus();
            }
        }
    };

    /**
     * Initialize Application
     */
    function initializeApp() {
        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            .lazy {
                opacity: 0;
                transition: opacity 0.3s;
            }
            
            .lazy.loaded {
                opacity: 1;
            }
        `;
        document.head.appendChild(style);

        // Initialize managers
        NotificationManager.init();
        FormManager.init();
        ThemeManager.init();
        PerformanceMonitor.init();
        AccessibilityManager.init();

        // Global error handling
        window.addEventListener('error', (e) => {
            console.error('Global error:', e.error);
            NotificationManager.error('An unexpected error occurred. Please refresh the page.');
        });

        window.addEventListener('unhandledrejection', (e) => {
            console.error('Unhandled promise rejection:', e.reason);
            NotificationManager.error('A network error occurred. Please check your connection.');
        });

        // Expose API
        window.VendorApp = {
            Utils,
            NotificationManager,
            LoadingManager,
            FormManager,
            ThemeManager,
            PerformanceMonitor,
            AccessibilityManager
        };

        // Dispatch ready event
        document.dispatchEvent(new CustomEvent('VendorApp:ready'));
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeApp);
    } else {
        initializeApp();
    }

})(window, document);

/**
 * Progressive Enhancement
 * Graceful degradation for JavaScript-disabled environments
 */

// Add no-js class removal
document.documentElement.classList.remove('no-js');
document.documentElement.classList.add('js');

// Feature detection
if (!('fetch' in window)) {
    console.warn('Fetch API not supported. Consider adding a polyfill.');
}

if (!('IntersectionObserver' in window)) {
    console.warn('IntersectionObserver not supported. Using fallback.');
}

if (!('localStorage' in window)) {
    console.warn('localStorage not supported. Some features may not work.');
    // Provide fallback storage
    window.VendorApp = window.VendorApp || {};
    window.VendorApp.Utils = window.VendorApp.Utils || {};
    window.VendorApp.Utils.storage = {
        set: function() {},
        get: function() { return null; },
        remove: function() {},
        clear: function() {}
    };
}