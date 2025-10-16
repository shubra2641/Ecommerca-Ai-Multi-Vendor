/**
 * Modern E-Commerce Frontend JavaScript
 * Features: Theme switching, Mobile navigation, Smooth scrolling, Lazy loading, PWA support
 */

// Modern JavaScript with ES6+ features
class ECommerceApp {
    constructor()
    {
        this.init();
    }

    init()
    {
        this.setupEventListeners();
        this.initTheme();
        this.initMobileNav();
        this.initLazyLoading();
        this.initSmoothScrolling();
        this.initAnimations();
        this.initDropdowns();
        this.initForms();
        this.initTooltips();
        this.initModals();
        this.initSearch();
        this.initCart();
        this.initWishlist();
        this.initProductGallery();
        this.initFilters();
        this.initInfiniteScroll();
        this.initNotifications();
        this.initPerformanceOptimizations();
    }

    setupEventListeners()
    {
        // DOM Content Loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.onDOMReady());
        } else {
            this.onDOMReady();
        }

        // Window events
        window.addEventListener('load', () => this.onWindowLoad());
        window.addEventListener('resize', this.debounce(() => this.onWindowResize(), 250));
        window.addEventListener('scroll', this.throttle(() => this.onWindowScroll(), 16));

        // Keyboard navigation
        document.addEventListener('keydown', (e) => this.handleKeyboardNavigation(e));

        // Focus management
        document.addEventListener('focusin', (e) => this.handleFocusIn(e));
        document.addEventListener('focusout', (e) => this.handleFocusOut(e));
    }

    onDOMReady()
    {
        this.hideLoader();
        this.initAccessibility();
        this.initServiceWorker();
    }

    onWindowLoad()
    {
        this.initPerformanceMetrics();
        this.preloadCriticalResources();
    }

    onWindowResize()
    {
        this.handleResponsiveChanges();
        this.updateViewportHeight();
    }

    onWindowScroll()
    {
        this.updateScrollProgress();
        this.handleStickyElements();
        this.revealAnimations();
    }

    // Theme Management
    initTheme()
    {
        const savedTheme = localStorage.getItem('theme');
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        const theme = savedTheme || systemTheme;

        this.setTheme(theme);

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                this.setTheme(e.matches ? 'dark' : 'light');
            }
        });

        // Theme toggle button
        const themeToggle = document.querySelector('.theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => this.toggleTheme());
        }
    }

    setTheme(theme)
    {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);

        // Update theme toggle icon
        const themeToggle = document.querySelector('.theme-toggle');
        if (themeToggle) {
            const icon = themeToggle.querySelector('i') || themeToggle.querySelector('svg');
            if (icon) {
                icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
        }

        // Dispatch theme change event
        window.dispatchEvent(new CustomEvent('themechange', { detail: { theme } }));
    }

    toggleTheme()
    {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        this.setTheme(newTheme);
    }

    // Mobile Navigation
    initMobileNav()
    {
        const navToggle = document.querySelector('.nav-toggle');
        const mobileNav = document.querySelector('.nav-mobile');

        if (navToggle && mobileNav) {
            navToggle.addEventListener('click', () => this.toggleMobileNav());

            // Close on outside click
            document.addEventListener('click', (e) => {
                if (!navToggle.contains(e.target) && !mobileNav.contains(e.target)) {
                    this.closeMobileNav();
                }
            });

            // Close on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeMobileNav();
                }
            });
        }
    }

    toggleMobileNav()
    {
        const mobileNav = document.querySelector('.nav-mobile');
        const navToggle = document.querySelector('.nav-toggle');

        if (mobileNav) {
            const isOpen = mobileNav.classList.contains('show');

            if (isOpen) {
                this.closeMobileNav();
            } else {
                this.openMobileNav();
            }
        }
    }

    openMobileNav()
    {
        const mobileNav = document.querySelector('.nav-mobile');
        const navToggle = document.querySelector('.nav-toggle');

        if (mobileNav) {
            mobileNav.classList.add('show');
            navToggle ?.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';

            // Focus first link
            const firstLink = mobileNav.querySelector('a');
            firstLink ?.focus();
        }
    }

    closeMobileNav()
    {
        const mobileNav = document.querySelector('.nav-mobile');
        const navToggle = document.querySelector('.nav-toggle');

        if (mobileNav && mobileNav.classList.contains('show')) {
            mobileNav.classList.remove('show');
            navToggle ?.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }
    }

    // Dropdown Management
    initDropdowns()
    {
        const dropdowns = document.querySelectorAll('[data-dropdown]');

        dropdowns.forEach(dropdown => {
            const trigger = dropdown.querySelector('[data-dropdown-trigger]');
            const menu = dropdown.querySelector('[data-dropdown-menu]');

            if (trigger && menu) {
                trigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.toggleDropdown(dropdown);
                });

                // Close on outside click
                document.addEventListener('click', (e) => {
                    if (!dropdown.contains(e.target)) {
                        this.closeDropdown(dropdown);
                    }
                });

                // Keyboard navigation
                trigger.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.toggleDropdown(dropdown);
                    }
                });
            }
        });
    }

    toggleDropdown(dropdown)
    {
        const menu = dropdown.querySelector('[data-dropdown-menu]');
        const trigger = dropdown.querySelector('[data-dropdown-trigger]');

        if (menu) {
            const isOpen = menu.classList.contains('show');

            // Close all other dropdowns
            this.closeAllDropdowns();

            if (!isOpen) {
                menu.classList.add('show');
                trigger ?.setAttribute('aria-expanded', 'true');

                // Focus first item
                const firstItem = menu.querySelector('a, button');
                firstItem ?.focus();
            }
        }
    }

    closeDropdown(dropdown)
    {
        const menu = dropdown.querySelector('[data-dropdown-menu]');
        const trigger = dropdown.querySelector('[data-dropdown-trigger]');

        if (menu && menu.classList.contains('show')) {
            menu.classList.remove('show');
            trigger ?.setAttribute('aria-expanded', 'false');
        }
    }

    closeAllDropdowns()
    {
        const openDropdowns = document.querySelectorAll('[data-dropdown-menu].show');
        openDropdowns.forEach(menu => {
            const dropdown = menu.closest('[data-dropdown]');
            if (dropdown) {
                this.closeDropdown(dropdown);
            }
        });
    }

    // Lazy Loading
    initLazyLoading()
    {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        this.loadImage(img);
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px 0px'
            });

            const lazyImages = document.querySelectorAll('img[data-src]');
            lazyImages.forEach(img => imageObserver.observe(img));
        } else {
            // Fallback for older browsers
            this.loadAllImages();
        }
    }

    loadImage(img)
    {
        const src = img.getAttribute('data-src');
        if (src) {
            img.src = src;
            img.removeAttribute('data-src');
            img.classList.add('loaded');

            img.addEventListener('load', () => {
                img.classList.add('fade-in');
            });
        }
    }

    loadAllImages()
    {
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => this.loadImage(img));
    }

    // Smooth Scrolling
    initSmoothScrolling()
    {
        const smoothLinks = document.querySelectorAll('a[href^="#"]');

        smoothLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');
                if (href === '#') {
                    return;
                }

                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    this.smoothScrollTo(target);
                }
            });
        });
    }

    smoothScrollTo(target, offset = 80)
    {
        const targetPosition = target.offsetTop - offset;

        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });
    }

    // Animations
    initAnimations()
    {
        if ('IntersectionObserver' in window) {
            const animationObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in-view');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            const animatedElements = document.querySelectorAll('[data-animate]');
            animatedElements.forEach(el => {
                animationObserver.observe(el);
            });
        }
    }

    revealAnimations()
    {
        const elements = document.querySelectorAll('[data-animate]:not(.in-view)');

        elements.forEach(el => {
            const rect = el.getBoundingClientRect();
            const windowHeight = window.innerHeight;

            if (rect.top < windowHeight * 0.8) {
                el.classList.add('in-view');
            }
        });
    }

    // Form Enhancements
    initForms()
    {
        const forms = document.querySelectorAll('form[data-ajax]');

        forms.forEach(form => {
            form.addEventListener('submit', (e) => this.handleAjaxForm(e));
        });

        // Input validation
        const inputs = document.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateInput(input));
            input.addEventListener('input', () => this.clearValidationError(input));
        });

        // Password visibility toggle
        const passwordToggles = document.querySelectorAll('[data-password-toggle]');
        passwordToggles.forEach(toggle => {
            toggle.addEventListener('click', () => this.togglePasswordVisibility(toggle));
        });
    }

    async handleAjaxForm(e)
    {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const url = form.action || window.location.href;
        const method = form.method || 'POST';

        try {
            this.showFormLoading(form);

            const response = await fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ?.content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(data.message || 'Success!', 'success');
                form.reset();
            } else {
                this.showFormErrors(form, data.errors || {});
                this.showNotification(data.message || 'Please check the form for errors.', 'error');
            }
        } catch (error) {
            console.error('Form submission error:', error);
            this.showNotification('An error occurred. Please try again.', 'error');
        } finally {
            this.hideFormLoading(form);
        }
    }

    validateInput(input)
    {
        const value = input.value.trim();
        const type = input.type;
        const required = input.hasAttribute('required');

        let isValid = true;
        let message = '';

        if (required && !value) {
            isValid = false;
            message = 'This field is required.';
        } else if (value) {
            switch (type) {
                case 'email':
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        isValid = false;
                        message = 'Please enter a valid email address.';
                    }
                    break;
                case 'tel':
                    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
                    if (!phoneRegex.test(value.replace(/\s/g, ''))) {
                        isValid = false;
                        message = 'Please enter a valid phone number.';
                    }
                    break;
                case 'password':
                    if (value.length < 8) {
                        isValid = false;
                        message = 'Password must be at least 8 characters long.';
                    }
                    break;
            }
        }

        if (isValid) {
            this.clearValidationError(input);
        } else {
            this.showValidationError(input, message);
        }

        return isValid;
    }

    showValidationError(input, message)
    {
        input.classList.add('error');

        let errorElement = input.parentNode.querySelector('.error-message');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message text-sm text-error-500 mt-1';
            input.parentNode.appendChild(errorElement);
        }

        errorElement.textContent = message;
    }

    clearValidationError(input)
    {
        input.classList.remove('error');
        const errorElement = input.parentNode.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
        }
    }

    showFormLoading(form)
    {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
            submitBtn.setAttribute('data-original-text', submitBtn.textContent);
            submitBtn.textContent = 'Loading...';
        }
    }

    hideFormLoading(form)
    {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            const originalText = submitBtn.getAttribute('data-original-text');
            if (originalText) {
                submitBtn.textContent = originalText;
                submitBtn.removeAttribute('data-original-text');
            }
        }
    }

    showFormErrors(form, errors)
    {
        Object.keys(errors).forEach(fieldName => {
            const input = form.querySelector(`[name = "${fieldName}"]`);
            if (input && errors[fieldName][0]) {
                this.showValidationError(input, errors[fieldName][0]);
            }
        });
    }

    togglePasswordVisibility(toggle)
    {
        const input = toggle.previousElementSibling || toggle.nextElementSibling;
        if (input && input.type === 'password') {
            input.type = 'text';
            toggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else if (input && input.type === 'text') {
            input.type = 'password';
            toggle.innerHTML = '<i class="fas fa-eye"></i>';
        }
    }

    // Notifications
    initNotifications()
    {
        this.createNotificationContainer();
    }

    createNotificationContainer()
    {
        if (!document.querySelector('.notification-container')) {
            const container = document.createElement('div');
            container.className = 'notification-container fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(container);
        }
    }

    showNotification(message, type = 'info', duration = 5000)
    {
        const container = document.querySelector('.notification-container');
        if (!container) {
            return;
        }

        const notification = document.createElement('div');
        notification.className = `notification alert alert - ${type} max - w - sm shadow - lg transform translate - x - full transition - transform duration - 300`;
        notification.innerHTML = `
            < div class = "flex items-center justify-between" >
                < span > ${message} < / span >
                < button class = "ml-4 text-current opacity-70 hover:opacity-100" onclick = "this.parentElement.parentElement.remove()" >
                    < i class = "fas fa-times" > < / i >
                <  / button >
            <  / div >
        `;

        container.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 10);

        // Auto remove
        if (duration > 0) {
            setTimeout(() => {
                this.removeNotification(notification);
            }, duration);
        }
    }

    removeNotification(notification)
    {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }

    // Shopping Cart
    initCart()
    {
        // Support both declarative data-add-to-cart buttons and legacy quick buttons (.cart-quick)
        const cartButtons = document.querySelectorAll('[data-add-to-cart], .cart-quick, [data-cart-quick]');
        cartButtons.forEach(button => {
            button.addEventListener('click', (e) => this.addToCart(e));
        });

        const cartItems = document.querySelectorAll('[data-cart-item]');
        cartItems.forEach(item => {
            const removeBtn = item.querySelector('[data-remove-item]');
            const quantityInput = item.querySelector('[data-quantity]');

            if (removeBtn) {
                removeBtn.addEventListener('click', (e) => this.removeFromCart(e));
            }

            if (quantityInput) {
                quantityInput.addEventListener('change', (e) => this.updateCartQuantity(e));
            }
        });
    }

    initAll()
    {
        this.initNotifications();
        this.initCart();
        this.initNotifyButtons();
    }

    initNotifyButtons()
    {
        const buttons = document.querySelectorAll('.notify-btn[data-product]');
        buttons.forEach(btn => {
            btn.addEventListener('click', async(e) => {
                e.preventDefault();
                if (btn.classList.contains('subscribed')) {
                    return;
                }
                const productId = btn.getAttribute('data-product');
                const type = btn.getAttribute('data-type') || 'stock';
                let email = btn.getAttribute('data-email');
                if (!email) {
                    email = prompt('Email?');
                    if (!email) {
                        return;
                    }
                }
                btn.disabled = true;
                try {
                    const res = await fetch('/notify/product', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ product_id: productId, email, type })
                    });
                    const data = await res.json();
                    this.showNotification(data.message || 'Saved', 'success');
                    btn.classList.add('subscribed');
                    btn.querySelector('.notify-label') ?.classList.add('d-none');
                    btn.querySelector('.notify-subscribed') ?.classList.remove('d-none');
                } catch (err) {
                    this.showNotification('Error saving notification', 'error');
                } finally {
                    btn.disabled = false;
                }
            });
        });
    }

    async addToCart(e)
    {
        e.preventDefault();
        // support multiple button shapes: [data-add-to-cart], .cart-quick or [data-cart-quick]
        const button = e.target.closest('[data-add-to-cart], .cart-quick, [data-cart-quick]');
        if (!button) {
            return;
        }
        const productId = button.getAttribute('data-product-id') || button.getAttribute('data-product') || button.dataset.product;
        const quantity = button.getAttribute('data-quantity') || button.getAttribute('data-qty') || 1;
        const variationId = button.getAttribute('data-variation-id') || button.dataset.variationId || null;

        try {
            button.disabled = true;
            button.classList.add('loading');

            // send keys expected by the server-side CartController (product_id, qty, optional variation_id)
            const payload = {
                product_id: productId,
                qty: quantity
            };
            if (variationId) {
                payload.variation_id = variationId;
            }

            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ?.content
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                this.updateCartCount(data.cart_count);
                this.showNotification((window.__tFn && window.__tFn('added_to_cart', 'Product added to cart!')) || 'Product added to cart!', 'success');

                // Update button text temporarily
                const originalText = button.textContent;
                button.textContent = 'Added!';
                setTimeout(() => {
                    button.textContent = originalText;
                }, 2000);
            } else {
                this.showNotification(data.message || (window.__tFn && window.__tFn('failed_add_to_cart', 'Failed to add product to cart.')) || 'Failed to add product to cart.', 'error');
            }
        } catch (error) {
            console.error('Add to cart error:', error);
            this.showNotification((window.__tFn && window.__tFn('failed_add_to_cart', 'An error occurred. Please try again.')) || 'An error occurred. Please try again.', 'error');
        } finally {
            button.disabled = false;
            button.classList.remove('loading');
        }
    }

    async removeFromCart(e)
    {
        e.preventDefault();
        const button = e.target.closest('[data-remove-item]');
        const itemId = button.getAttribute('data-item-id');

        try {
            const response = await fetch('/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ?.content
                },
                body: JSON.stringify({ item_id: itemId })
            });

            const data = await response.json();

            if (data.success) {
                const cartItem = button.closest('[data-cart-item]');
                if (cartItem) {
                    cartItem.remove();
                }
                this.updateCartCount(data.cart_count);
                this.updateCartTotal(data.cart_total);
                this.showNotification((window.__tFn && window.__tFn('removed_from_cart', 'Item removed from cart.')) || 'Item removed from cart.', 'success');
            } else {
                this.showNotification(data.message || (window.__tFn && window.__tFn('failed_add_to_cart', 'Failed to remove item.')) || 'Failed to remove item.', 'error');
            }
        } catch (error) {
            console.error('Remove from cart error:', error);
            this.showNotification((window.__tFn && window.__tFn('failed_add_to_cart', 'An error occurred. Please try again.')) || 'An error occurred. Please try again.', 'error');
        }
    }

    async updateCartQuantity(e)
    {
        const input = e.target;
        const itemId = input.getAttribute('data-item-id');
        const quantity = parseInt(input.value);

        if (quantity < 1) {
            input.value = 1;
            return;
        }

        try {
            const response = await fetch('/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ?.content
                },
                body: JSON.stringify({
                    item_id: itemId,
                    quantity: quantity
                })
            });

            const data = await response.json();

            if (data.success) {
                this.updateCartCount(data.cart_count);
                this.updateCartTotal(data.cart_total);

                // Update item total
                const cartItem = input.closest('[data-cart-item]');
                const itemTotal = cartItem ?.querySelector('[data-item-total]');
                if (itemTotal && data.item_total) {
                    itemTotal.textContent = data.item_total;
                }
            } else {
                this.showNotification(data.message || 'Failed to update quantity.', 'error');
                // Revert to previous value
                input.value = input.getAttribute('data-original-value') || 1;
            }
        } catch (error) {
            console.error('Update cart quantity error:', error);
            this.showNotification('An error occurred. Please try again.', 'error');
        }
    }

    updateCartCount(count)
    {
        const cartCountElements = document.querySelectorAll('[data-cart-count]');
        cartCountElements.forEach(element => {
            element.textContent = count;

            if (count > 0) {
                element.classList.remove('hidden');
            } else {
                element.classList.add('hidden');
            }
        });
    }

    updateCartTotal(total)
    {
        const cartTotalElements = document.querySelectorAll('[data-cart-total]');
        cartTotalElements.forEach(element => {
            element.textContent = total;
        });
    }

    // Wishlist
    initWishlist()
    {
        const wishlistButtons = document.querySelectorAll('[data-toggle-wishlist]');
        wishlistButtons.forEach(button => {
            button.addEventListener('click', (e) => this.toggleWishlist(e));
        });
    }

    async toggleWishlist(e)
    {
        e.preventDefault();
        const button = e.target.closest('[data-toggle-wishlist]');
        const productId = button.getAttribute('data-product-id');
        const isInWishlist = button.classList.contains('in-wishlist');

        try {
            button.disabled = true;

            const response = await fetch('/wishlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ?.content
                },
                body: JSON.stringify({ product_id: productId })
            });

            const data = await response.json();

            if (data.success) {
                if (data.in_wishlist) {
                    button.classList.add('in-wishlist');
                    button.innerHTML = '<i class="fas fa-heart"></i>';
                    this.showNotification('Added to wishlist!', 'success');
                } else {
                    button.classList.remove('in-wishlist');
                    button.innerHTML = '<i class="far fa-heart"></i>';
                    this.showNotification('Removed from wishlist.', 'success');
                }
            } else {
                this.showNotification(data.message || 'Failed to update wishlist.', 'error');
            }
        } catch (error) {
            console.error('Wishlist toggle error:', error);
            this.showNotification('An error occurred. Please try again.', 'error');
        } finally {
            button.disabled = false;
        }
    }

    // Product Gallery
    initProductGallery()
    {
        const galleries = document.querySelectorAll('[data-product-gallery]');

        galleries.forEach(gallery => {
            const mainImage = gallery.querySelector('[data-main-image]');
            const thumbnails = gallery.querySelectorAll('[data-thumbnail]');

            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', (e) => {
                    e.preventDefault();
                    const newSrc = thumbnail.getAttribute('data-full-image');
                    if (mainImage && newSrc) {
                        mainImage.src = newSrc;

                        // Update active thumbnail
                        thumbnails.forEach(t => t.classList.remove('active'));
                        thumbnail.classList.add('active');
                    }
                });
            });

            // Zoom functionality
        if (mainImage) {
            mainImage.addEventListener('click', () => this.openImageModal(mainImage.src));
        }
        });
    }

    openImageModal(imageSrc)
    {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4';
        modal.innerHTML = `
            < div class = "relative max-w-full max-h-full" >
                < img src = "${imageSrc}" alt = "Product Image" class = "max-w-full max-h-full object-contain" >
                < button class = "absolute top-4 right-4 text-white text-2xl hover:text-gray-300" onclick = "this.closest('.fixed').remove()" >
                    < i class = "fas fa-times" > < / i >
                <  / button >
            <  / div >
        `;

        document.body.appendChild(modal);

        // Close on background click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });

        // Close on escape key
        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                modal.remove();
                document.removeEventListener('keydown', handleEscape);
            }
        };
        document.addEventListener('keydown', handleEscape);
    }

    // Search
    initSearch()
    {
        const searchInputs = document.querySelectorAll('[data-search]');

        searchInputs.forEach(input => {
            let searchTimeout;

            input.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.performSearch(e.target.value, input);
                }, 300);
            });

            // Clear search
            const clearButton = input.parentNode.querySelector('[data-search-clear]');
        if (clearButton) {
            clearButton.addEventListener('click', () => {
                input.value = '';
                this.clearSearchResults(input);
                });
        }
        });
    }

    async performSearch(query, input)
    {
        if (query.length < 2) {
            this.clearSearchResults(input);
            return;
        }

        try {
            const response = await fetch(` / search ? q = ${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            this.displaySearchResults(data.results, input);
        } catch (error) {
            console.error('Search error:', error);
        }
    }

    displaySearchResults(results, input)
    {
        let resultsContainer = input.parentNode.querySelector('[data-search-results]');

        if (!resultsContainer) {
            resultsContainer = document.createElement('div');
            resultsContainer.setAttribute('data-search-results', '');
            resultsContainer.className = 'absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-md shadow-lg max-h-64 overflow-y-auto z-50';
            input.parentNode.appendChild(resultsContainer);
        }

        if (results.length === 0) {
            resultsContainer.innerHTML = '<div class="p-4 text-gray-500">No results found</div>';
        } else {
            resultsContainer.innerHTML = results.map(result => `
                < a href = "${result.url}" class = "block p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0" >
                    < div class = "font-medium" > ${result.title} < / div >
                    < div class = "text-sm text-gray-500" > ${result.description} < / div >
                <  / a >
            `).join('');
        }

        resultsContainer.style.display = 'block';
    }

    clearSearchResults(input)
    {
        const resultsContainer = input.parentNode.querySelector('[data-search-results]');
        if (resultsContainer) {
            resultsContainer.style.display = 'none';
        }
    }

    // Filters
    initFilters()
    {
        const filterForms = document.querySelectorAll('[data-filter-form]');

        filterForms.forEach(form => {
            const inputs = form.querySelectorAll('input, select');

            inputs.forEach(input => {
                input.addEventListener('change', () => {
                    this.debounce(() => this.applyFilters(form), 300)();
                });
            });
        });
    }

    async applyFilters(form)
    {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        try {
            const response = await fetch(`${form.action} ? ${params}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.html) {
                const resultsContainer = document.querySelector('[data-filter-results]');
                if (resultsContainer) {
                    resultsContainer.innerHTML = data.html;
                    this.initLazyLoading(); // Re-initialize lazy loading for new content
                }
            }

            // Update URL without page reload
            const newUrl = `${window.location.pathname} ? ${params}`;
            window.history.pushState({}, '', newUrl);
        } catch (error) {
            console.error('Filter error:', error);
        }
    }

    // Infinite Scroll
    initInfiniteScroll()
    {
        const containers = document.querySelectorAll('[data-infinite-scroll]');

        containers.forEach(container => {
            const loadMore = container.querySelector('[data-load-more]');

            if (loadMore) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.loadMoreContent(container);
                        }
                    });
                }, {
                    rootMargin: '100px'
                });

                observer.observe(loadMore);
            }
        });
    }

    async loadMoreContent(container)
    {
        const loadMore = container.querySelector('[data-load-more]');
        const nextPage = loadMore.getAttribute('data-next-page');

        if (!nextPage || loadMore.classList.contains('loading')) {
            return;
        }

        try {
            loadMore.classList.add('loading');
            loadMore.textContent = 'Loading...';

            const response = await fetch(nextPage, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.html) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.html;

                // Append new content
                const newItems = tempDiv.querySelectorAll('[data-item]');
                newItems.forEach(item => {
                    container.insertBefore(item, loadMore);
                });

                // Update next page URL
                if (data.next_page) {
                    loadMore.setAttribute('data-next-page', data.next_page);
                } else {
                    loadMore.remove();
                }

                // Re-initialize components for new content
                this.initLazyLoading();
                this.initAnimations();
            }
        } catch (error) {
            console.error('Load more error:', error);
        } finally {
            loadMore.classList.remove('loading');
            loadMore.textContent = 'Load More';
        }
    }

    // Accessibility
    initAccessibility()
    {
        // Skip to main content link
        this.createSkipLink();

        // Focus management
        this.initFocusManagement();

        // ARIA live regions
        this.createLiveRegions();
    }

    createSkipLink()
    {
        if (!document.querySelector('.skip-link')) {
            const skipLink = document.createElement('a');
            skipLink.href = '#main';
            skipLink.className = 'skip-link sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary-600 text-white px-4 py-2 rounded z-50';
            skipLink.textContent = 'Skip to main content';
            document.body.insertBefore(skipLink, document.body.firstChild);
        }
    }

    initFocusManagement()
    {
        // Trap focus in modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                const modal = document.querySelector('.modal.show');
                if (modal) {
                    this.trapFocus(e, modal);
                }
            }
        });
    }

    trapFocus(e, container)
    {
        const focusableElements = container.querySelectorAll(
            'a[href], button, textarea, input[type="text"], input[type="radio"], input[type="checkbox"], select'
        );

        const firstFocusable = focusableElements[0];
        const lastFocusable = focusableElements[focusableElements.length - 1];

        if (e.shiftKey) {
            if (document.activeElement === firstFocusable) {
                lastFocusable.focus();
                e.preventDefault();
            }
        } else {
            if (document.activeElement === lastFocusable) {
                firstFocusable.focus();
                e.preventDefault();
            }
        }
    }

    createLiveRegions()
    {
        if (!document.querySelector('#live-region')) {
            const liveRegion = document.createElement('div');
            liveRegion.id = 'live-region';
            liveRegion.setAttribute('aria-live', 'polite');
            liveRegion.setAttribute('aria-atomic', 'true');
            liveRegion.className = 'sr-only';
            document.body.appendChild(liveRegion);
        }
    }

    announceToScreenReader(message)
    {
        const liveRegion = document.querySelector('#live-region');
        if (liveRegion) {
            liveRegion.textContent = message;
            setTimeout(() => {
                liveRegion.textContent = '';
            }, 1000);
        }
    }

    // Performance Optimizations
    initPerformanceOptimizations()
    {
        // Preload critical resources
        this.preloadCriticalResources();

        // Image optimization
        this.optimizeImages();

        // Memory cleanup
        this.setupMemoryCleanup();
    }

    preloadCriticalResources()
    {
        const criticalImages = document.querySelectorAll('img[data-preload]');
        criticalImages.forEach(img => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = img.src || img.getAttribute('data-src');
            document.head.appendChild(link);
        });
    }

    optimizeImages()
    {
        const images = document.querySelectorAll('img');

        images.forEach(img => {
            // Add loading="lazy" if not already present
            if (!img.hasAttribute('loading')) {
                img.loading = 'lazy';
            }

            // Add proper alt text if missing
            if (!img.hasAttribute('alt')) {
                img.alt = '';
            }
        });
    }

    setupMemoryCleanup()
    {
        // Clean up event listeners on page unload
        window.addEventListener('beforeunload', () => {
            this.cleanup();
        });
    }

    cleanup()
    {
        // Remove event listeners
        // Clear timeouts and intervals
        // Clean up observers
    }

    // Service Worker
    initServiceWorker()
    {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('SW registered:', registration);
                })
                .catch(error => {
                    console.log('SW registration failed:', error);
                });
        }
    }

    // Utility Functions
    debounce(func, wait)
    {
        let timeout;
        return function executedFunction(...args)
        {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    throttle(func, limit)
    {
        let inThrottle;
        return function () {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    hideLoader()
    {
        const loader = document.querySelector('#app-loader');
        if (loader) {
            loader.classList.add('hidden');
            setTimeout(() => {
                loader.remove();
            }, 500);
        }
    }

    handleKeyboardNavigation(e)
    {
        // Handle keyboard shortcuts
        if (e.ctrlKey || e.metaKey) {
            switch (e.key) {
                case 'k':
                    e.preventDefault();
                    const searchInput = document.querySelector('[data-search]');
                    if (searchInput) {
                        searchInput.focus();
                    }
                    break;
            }
        }

        // Handle escape key
        if (e.key === 'Escape') {
            this.closeAllDropdowns();
            this.closeMobileNav();
        }
    }

    handleFocusIn(e)
    {
        // Add focus styles
        e.target.classList.add('focus-visible');
    }

    handleFocusOut(e)
    {
        // Remove focus styles
        e.target.classList.remove('focus-visible');
    }

    handleResponsiveChanges()
    {
        // Handle responsive breakpoint changes
        const isMobile = window.innerWidth < 768;

        if (isMobile) {
            this.closeMobileNav();
        }
    }

    updateViewportHeight()
    {
        // Update CSS custom property for mobile viewport height
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }

    updateScrollProgress()
    {
        // Update scroll progress indicator
        const scrollProgress = document.querySelector('[data-scroll-progress]');
        if (scrollProgress) {
            const scrollPercent = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
            scrollProgress.style.width = `${Math.min(scrollPercent, 100)} % `;
        }
    }

    handleStickyElements()
    {
        // Handle sticky element behavior
        const stickyElements = document.querySelectorAll('[data-sticky]');

        stickyElements.forEach(element => {
            const rect = element.getBoundingClientRect();
            if (rect.top <= 0) {
                element.classList.add('is-stuck');
            } else {
                element.classList.remove('is-stuck');
            }
        });
    }

    initPerformanceMetrics()
    {
        // Track performance metrics
        if ('performance' in window) {
            window.addEventListener('load', () => {
                const perfData = performance.getEntriesByType('navigation')[0];
                console.log('Page load time:', perfData.loadEventEnd - perfData.loadEventStart);
            });
        }
    }

    initTooltips()
    {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');

        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', (e) => this.showTooltip(e));
            element.addEventListener('mouseleave', (e) => this.hideTooltip(e));
            element.addEventListener('focus', (e) => this.showTooltip(e));
            element.addEventListener('blur', (e) => this.hideTooltip(e));
        });
    }

    showTooltip(e)
    {
        const element = e.target;
        const tooltipText = element.getAttribute('data-tooltip');

        if (!tooltipText) {
            return;
        }

        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip absolute bg-gray-900 text-white text-sm px-2 py-1 rounded shadow-lg z-50';
        tooltip.textContent = tooltipText;
        tooltip.id = 'tooltip-' + Date.now();

        document.body.appendChild(tooltip);

        // Position tooltip
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';

        element.setAttribute('aria-describedby', tooltip.id);
    }

    hideTooltip(e)
    {
        const element = e.target;
        const tooltipId = element.getAttribute('aria-describedby');

        if (tooltipId) {
            const tooltip = document.getElementById(tooltipId);
            if (tooltip) {
                tooltip.remove();
            }
            element.removeAttribute('aria-describedby');
        }
    }

    initModals()
    {
        const modalTriggers = document.querySelectorAll('[data-modal-trigger]');

        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const modalId = trigger.getAttribute('data-modal-trigger');
                this.openModal(modalId);
            });
        });

        // Close modal buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-modal-close]')) {
                this.closeModal(e.target.closest('.modal'));
            }
        });

        // Close on background click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-backdrop')) {
                this.closeModal(e.target.querySelector('.modal'));
            }
        });
    }

    openModal(modalId)
    {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';

            // Focus first focusable element
            const firstFocusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (firstFocusable) {
                firstFocusable.focus();
            }
        }
    }

    closeModal(modal)
    {
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }
}

// Initialize the application
const app = new ECommerceApp();

// Export for global access
window.ECommerceApp = app;

// Additional utility functions for global use
window.showNotification = (message, type, duration) => app.showNotification(message, type, duration);
window.addToCart = (productId, quantity) => app.addToCart({ target: { closest: () => ({ getAttribute: (attr) => attr === 'data-product-id' ? productId : quantity }) } });
window.toggleWishlist = (productId) => app.toggleWishlist({ target: { closest: () => ({ getAttribute: () => productId, classList: { contains: () => false, add: () => { }, remove: () => { } } }) } });

