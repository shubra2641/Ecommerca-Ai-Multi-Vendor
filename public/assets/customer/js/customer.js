/* Customer JavaScript - Consolidated JS for Customer/Frontend Interface
 * This file contains all JavaScript functionality for the customer and frontend interface
 * Following rules: Progressive enhancement, no inline JS, unified structure
 */

(function() {
    'use strict';

    // Customer namespace
    window.CustomerPanel = window.CustomerPanel || {};

    // Initialize customer functionality
    CustomerPanel.init = function() {
        this.initNavigation();
        this.initSearch();
        this.initCart();
        this.initProducts();
        this.initWishlist();
        this.initAccount();
        this.initForms();
        this.initNotifications();
    };

    // Navigation functionality
    CustomerPanel.initNavigation = function() {
        // Mobile menu toggle
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenu = document.querySelector('.customer-mobile-menu');

        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('active');
                menuToggle.classList.toggle('active');
            });
        }

        // Sticky header
        const header = document.querySelector('.customer-header');
        if (header) {
            let lastScrollTop = 0;
            window.addEventListener('scroll', function() {
                let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                if (scrollTop > lastScrollTop && scrollTop > 100) {
                    header.style.transform = 'translateY(-100%)';
                } else {
                    header.style.transform = 'translateY(0)';
                }
                lastScrollTop = scrollTop;
            });
        }
    };

    // Search functionality
    CustomerPanel.initSearch = function() {
        const searchInput = document.querySelector('.customer-search-input');
        const searchResults = document.querySelector('.customer-search-results');
        let searchTimeout;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length >= 2) {
                    searchTimeout = setTimeout(function() {
                        CustomerPanel.performSearch(query);
                    }, 300);
                } else {
                    CustomerPanel.hideSearchResults();
                }
            });

            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.customer-search')) {
                    CustomerPanel.hideSearchResults();
                }
            });
        }
    };

    // Perform search
    CustomerPanel.performSearch = function(query) {
        fetch('/search', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ query: query })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            CustomerPanel.displaySearchResults(data.results);
        })
        .catch(function(error) {
            console.error('Search error:', error);
        });
    };

    // Display search results
    CustomerPanel.displaySearchResults = function(results) {
        const searchResults = document.querySelector('.customer-search-results');
        if (!searchResults) return;

        if (results.length === 0) {
            searchResults.innerHTML = '<div class="search-no-results">No results found</div>';
        } else {
            const resultsHTML = results.map(function(result) {
                return `
                    <a href="${result.url}" class="search-result-item">
                        <div class="search-result-image">
                            <img src="${result.image}" alt="${result.name}">
                        </div>
                        <div class="search-result-info">
                            <div class="search-result-name">${result.name}</div>
                            <div class="search-result-price">${result.price}</div>
                        </div>
                    </a>
                `;
            }).join('');
            
            searchResults.innerHTML = resultsHTML;
        }
        
        searchResults.style.display = 'block';
    };

    // Hide search results
    CustomerPanel.hideSearchResults = function() {
        const searchResults = document.querySelector('.customer-search-results');
        if (searchResults) {
            searchResults.style.display = 'none';
        }
    };

    // Cart functionality
    CustomerPanel.initCart = function() {
        // Add to cart buttons
        document.addEventListener('click', function(e) {
            if (e.target.matches('.customer-add-to-cart')) {
                e.preventDefault();
                const productId = e.target.dataset.productId;
                const quantity = e.target.dataset.quantity || 1;
                CustomerPanel.addToCart(productId, quantity, e.target);
            }

            if (e.target.matches('.cart-item-remove')) {
                e.preventDefault();
                const itemId = e.target.dataset.itemId;
                CustomerPanel.removeFromCart(itemId);
            }

            if (e.target.matches('.cart-item-quantity-update')) {
                const itemId = e.target.dataset.itemId;
                const quantity = e.target.value;
                CustomerPanel.updateCartQuantity(itemId, quantity);
            }
        });

        // Cart dropdown
        const cartBtn = document.querySelector('.customer-cart-btn');
        const cartDropdown = document.querySelector('.customer-cart-dropdown');

        if (cartBtn && cartDropdown) {
            cartBtn.addEventListener('click', function() {
                cartDropdown.classList.toggle('active');
                CustomerPanel.refreshCartDropdown();
            });
        }

        // Update cart badge on page load
        CustomerPanel.updateCartBadge();
    };

    // Add product to cart
    CustomerPanel.addToCart = function(productId, quantity, button) {
        const originalText = button.textContent;
        button.textContent = 'Adding...';
        button.disabled = true;

        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                CustomerPanel.showNotification('Product added to cart!', 'success');
                CustomerPanel.updateCartBadge();
                button.textContent = 'Added!';
                
                setTimeout(function() {
                    button.textContent = originalText;
                    button.disabled = false;
                }, 2000);
            } else {
                CustomerPanel.showNotification(data.message || 'Failed to add to cart', 'error');
                button.textContent = originalText;
                button.disabled = false;
            }
        })
        .catch(function(error) {
            CustomerPanel.showNotification('Error adding to cart', 'error');
            button.textContent = originalText;
            button.disabled = false;
        });
    };

    // Remove from cart
    CustomerPanel.removeFromCart = function(itemId) {
        fetch('/cart/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ item_id: itemId })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                CustomerPanel.showNotification('Item removed from cart', 'success');
                CustomerPanel.updateCartBadge();
                CustomerPanel.refreshCartPage();
            } else {
                CustomerPanel.showNotification(data.message || 'Failed to remove item', 'error');
            }
        })
        .catch(function(error) {
            CustomerPanel.showNotification('Error removing item', 'error');
        });
    };

    // Update cart quantity
    CustomerPanel.updateCartQuantity = function(itemId, quantity) {
        if (quantity < 1) {
            CustomerPanel.removeFromCart(itemId);
            return;
        }

        fetch('/cart/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                item_id: itemId,
                quantity: quantity
            })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                CustomerPanel.updateCartBadge();
                CustomerPanel.refreshCartPage();
            } else {
                CustomerPanel.showNotification(data.message || 'Failed to update quantity', 'error');
            }
        })
        .catch(function(error) {
            CustomerPanel.showNotification('Error updating quantity', 'error');
        });
    };

    // Update cart badge
    CustomerPanel.updateCartBadge = function() {
        fetch('/cart/count')
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            const badge = document.querySelector('.customer-cart-badge');
            if (badge) {
                badge.textContent = data.count;
                badge.style.display = data.count > 0 ? 'block' : 'none';
            }
        })
        .catch(function(error) {
            console.error('Failed to update cart badge:', error);
        });
    };

    // Product functionality
    CustomerPanel.initProducts = function() {
        this.initProductFilters();
        this.initProductGallery();
        this.initProductVariations();
    };

    // Product filters
    CustomerPanel.initProductFilters = function() {
        const filterBtns = document.querySelectorAll('.customer-filter-btn');
        
        filterBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Update active state
                filterBtns.forEach(function(b) { b.classList.remove('active'); });
                this.classList.add('active');
                
                const category = this.dataset.category;
                CustomerPanel.filterProducts(category);
            });
        });

        // Price range filter
        const priceRange = document.querySelector('.price-range-slider');
        if (priceRange) {
            priceRange.addEventListener('input', function() {
                CustomerPanel.filterByPrice(this.value);
            });
        }

        // Sort options
        const sortSelect = document.querySelector('.product-sort-select');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                CustomerPanel.sortProducts(this.value);
            });
        }
    };

    // Filter products by category
    CustomerPanel.filterProducts = function(category) {
        const products = document.querySelectorAll('.customer-product-card');
        
        products.forEach(function(product) {
            const productCategory = product.dataset.category;
            const shouldShow = !category || category === 'all' || productCategory === category;
            
            if (shouldShow) {
                product.style.display = 'block';
                product.style.animation = 'fadeIn 0.3s ease';
            } else {
                product.style.display = 'none';
            }
        });
    };

    // Filter by price
    CustomerPanel.filterByPrice = function(maxPrice) {
        const products = document.querySelectorAll('.customer-product-card');
        
        products.forEach(function(product) {
            const productPrice = parseFloat(product.dataset.price);
            const shouldShow = productPrice <= maxPrice;
            
            product.style.display = shouldShow ? 'block' : 'none';
        });
        
        // Update price display
        const priceDisplay = document.querySelector('.price-range-display');
        if (priceDisplay) {
            priceDisplay.textContent = '$0 - $' + maxPrice;
        }
    };

    // Sort products
    CustomerPanel.sortProducts = function(sortBy) {
        const container = document.querySelector('.customer-product-grid');
        const products = Array.from(container.querySelectorAll('.customer-product-card'));
        
        products.sort(function(a, b) {
            switch (sortBy) {
                case 'price-low':
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                case 'price-high':
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                case 'name':
                    return a.dataset.name.localeCompare(b.dataset.name);
                case 'newest':
                    return new Date(b.dataset.created) - new Date(a.dataset.created);
                default:
                    return 0;
            }
        });
        
        // Reorder DOM elements
        products.forEach(function(product) {
            container.appendChild(product);
        });
    };

    // Product gallery
    CustomerPanel.initProductGallery = function() {
        const thumbnails = document.querySelectorAll('.product-thumbnail');
        const mainImage = document.querySelector('.product-main-image');
        
        thumbnails.forEach(function(thumb) {
            thumb.addEventListener('click', function() {
                if (mainImage) {
                    mainImage.src = this.dataset.fullImage;
                }
                
                thumbnails.forEach(function(t) { t.classList.remove('active'); });
                this.classList.add('active');
            });
        });

        // Image zoom
        if (mainImage) {
            mainImage.addEventListener('click', function() {
                CustomerPanel.openImageModal(this.src);
            });
        }
    };

    // Open image modal
    CustomerPanel.openImageModal = function(imageSrc) {
        const modal = document.createElement('div');
        modal.className = 'image-modal';
        modal.innerHTML = `
            <div class="image-modal-content">
                <img src="${imageSrc}" alt="Product Image">
                <button class="image-modal-close">&times;</button>
            </div>
        `;
        
        document.body.appendChild(modal);
        document.body.classList.add('modal-open');
        
        // Close modal events
        modal.addEventListener('click', function(e) {
            if (e.target === modal || e.target.matches('.image-modal-close')) {
                document.body.removeChild(modal);
                document.body.classList.remove('modal-open');
            }
        });
    };

    // Wishlist functionality
    CustomerPanel.initWishlist = function() {
        document.addEventListener('click', function(e) {
            if (e.target.matches('.customer-wishlist-btn')) {
                e.preventDefault();
                const productId = e.target.dataset.productId;
                CustomerPanel.toggleWishlist(productId, e.target);
            }
        });
    };

    // Toggle wishlist
    CustomerPanel.toggleWishlist = function(productId, button) {
        const isActive = button.classList.contains('active');
        const action = isActive ? 'remove' : 'add';
        
        fetch('/wishlist/' + action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                button.classList.toggle('active');
                const message = isActive ? 'Removed from wishlist' : 'Added to wishlist';
                CustomerPanel.showNotification(message, 'success');
            } else {
                CustomerPanel.showNotification(data.message || 'Wishlist action failed', 'error');
            }
        })
        .catch(function(error) {
            CustomerPanel.showNotification('Error updating wishlist', 'error');
        });
    };

    // Account functionality
    CustomerPanel.initAccount = function() {
        this.initAddressManagement();
        this.initOrderTracking();
        this.initProfileUpdate();
    };

    // Address management
    CustomerPanel.initAddressManagement = function() {
        const addAddressBtn = document.querySelector('.add-address-btn');
        const addressForms = document.querySelectorAll('.address-form');
        
        if (addAddressBtn) {
            addAddressBtn.addEventListener('click', function() {
                CustomerPanel.showAddressForm();
            });
        }

        addressForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                CustomerPanel.saveAddress(this);
            });
        });
    };

    // Save address
    CustomerPanel.saveAddress = function(form) {
        const formData = new FormData(form);
        
        fetch('/account/addresses', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                CustomerPanel.showNotification('Address saved successfully', 'success');
                location.reload();
            } else {
                CustomerPanel.showNotification(data.message || 'Failed to save address', 'error');
            }
        })
        .catch(function(error) {
            CustomerPanel.showNotification('Error saving address', 'error');
        });
    };

    // Order tracking
    CustomerPanel.initOrderTracking = function() {
        const trackingInputs = document.querySelectorAll('.order-tracking-input');
        
        trackingInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                if (this.value.length >= 8) {
                    CustomerPanel.trackOrder(this.value);
                }
            });
        });
    };

    // Track order
    CustomerPanel.trackOrder = function(orderNumber) {
        fetch('/orders/track/' + orderNumber)
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            CustomerPanel.displayOrderTracking(data);
        })
        .catch(function(error) {
            CustomerPanel.showNotification('Order not found', 'error');
        });
    };

    // Display order tracking
    CustomerPanel.displayOrderTracking = function(orderData) {
        const trackingContainer = document.querySelector('.order-tracking-results');
        if (trackingContainer) {
            trackingContainer.innerHTML = `
                <div class="tracking-timeline">
                    ${orderData.timeline.map(function(event) {
                        return `
                            <div class="timeline-event ${event.status}">
                                <div class="timeline-date">${event.date}</div>
                                <div class="timeline-description">${event.description}</div>
                            </div>
                        `;
                    }).join('')}
                </div>
            `;
            trackingContainer.style.display = 'block';
        }
    };

    // Form enhancements
    CustomerPanel.initForms = function() {
        const forms = document.querySelectorAll('.customer-form');
        
        forms.forEach(function(form) {
            // Real-time validation
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(function(input) {
                input.addEventListener('blur', function() {
                    CustomerPanel.validateField(this);
                });
                
                input.addEventListener('input', function() {
                    CustomerPanel.clearFieldError(this);
                });
            });
            
            // Form submission
            form.addEventListener('submit', function(e) {
                if (!CustomerPanel.validateForm(this)) {
                    e.preventDefault();
                }
            });
        });
    };

    // Validate field
    CustomerPanel.validateField = function(field) {
        let isValid = true;
        
        if (field.required && !field.value.trim()) {
            CustomerPanel.showFieldError(field, 'This field is required');
            isValid = false;
        } else if (field.type === 'email' && field.value && !CustomerPanel.isValidEmail(field.value)) {
            CustomerPanel.showFieldError(field, 'Please enter a valid email address');
            isValid = false;
        } else if (field.type === 'tel' && field.value && !CustomerPanel.isValidPhone(field.value)) {
            CustomerPanel.showFieldError(field, 'Please enter a valid phone number');
            isValid = false;
        }
        
        return isValid;
    };

    // Validate form
    CustomerPanel.validateForm = function(form) {
        const fields = form.querySelectorAll('input, textarea, select');
        let isValid = true;
        
        fields.forEach(function(field) {
            if (!CustomerPanel.validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    };

    // Show field error
    CustomerPanel.showFieldError = function(field, message) {
        CustomerPanel.clearFieldError(field);
        
        field.classList.add('error');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    };

    // Clear field error
    CustomerPanel.clearFieldError = function(field) {
        field.classList.remove('error');
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    };

    // Email validation
    CustomerPanel.isValidEmail = function(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    };

    // Phone validation
    CustomerPanel.isValidPhone = function(phone) {
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        return phoneRegex.test(phone.replace(/\s/g, ''));
    };

    // Notification system
    CustomerPanel.initNotifications = function() {
        // Auto-hide notifications
        const notifications = document.querySelectorAll('.customer-notification');
        notifications.forEach(function(notification) {
            setTimeout(function() {
                CustomerPanel.hideNotification(notification);
            }, 5000);
        });
    };

    // Show notification
    CustomerPanel.showNotification = function(message, type) {
        type = type || 'info';
        
        const notification = document.createElement('div');
        notification.className = 'customer-notification customer-notification-' + type;
        notification.innerHTML = `
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        `;
        
        document.body.appendChild(notification);
        
        // Close button
        notification.querySelector('.notification-close').addEventListener('click', function() {
            CustomerPanel.hideNotification(notification);
        });
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            CustomerPanel.hideNotification(notification);
        }, 5000);
    };

    // Hide notification
    CustomerPanel.hideNotification = function(notification) {
        if (notification) {
            notification.style.opacity = '0';
            setTimeout(function() {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }
    };

    // Refresh cart page
    CustomerPanel.refreshCartPage = function() {
        if (window.location.pathname.includes('/cart')) {
            location.reload();
        }
    };

    // Refresh cart dropdown
    CustomerPanel.refreshCartDropdown = function() {
        fetch('/cart/dropdown')
        .then(function(response) {
            return response.text();
        })
        .then(function(html) {
            const dropdown = document.querySelector('.customer-cart-dropdown');
            if (dropdown) {
                dropdown.innerHTML = html;
            }
        })
        .catch(function(error) {
            console.error('Failed to refresh cart dropdown:', error);
        });
    };

    // Initialize when DOM is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', CustomerPanel.init);
    } else {
        CustomerPanel.init();
    }

})();