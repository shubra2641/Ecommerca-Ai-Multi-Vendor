/**
 * Vendor Order Details JavaScript
 * Handles interactive functionality for order details page
 */

(function() {
    'use strict';

    // DOM Elements
    const modals = document.querySelectorAll('.modal');
    const modalTriggers = document.querySelectorAll('[data-modal-target]');
    const modalCloses = document.querySelectorAll('.modal-close, [data-modal-close]');
    const body = document.body;

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeModals();
        initializeTooltips();
        initializeAnimations();
        initializeImageLazyLoading();
        initializeKeyboardNavigation();
    });

    /**
     * Initialize modal functionality
     */
    function initializeModals() {
        // Open modal triggers
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('data-modal-target');
                const modal = document.getElementById(targetId);
                if (modal) {
                    openModal(modal);
                }
            });
        });

        // Close modal triggers
        modalCloses.forEach(closeBtn => {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const modal = this.closest('.modal');
                if (modal) {
                    closeModal(modal);
                }
            });
        });

        // Close modal on backdrop click
        modals.forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this);
                }
            });
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal.show');
                if (openModal) {
                    closeModal(openModal);
                }
            }
        });
    }

    /**
     * Open modal
     * @param {HTMLElement} modal
     */
    function openModal(modal) {
        modal.classList.add('show');
        modal.style.display = 'flex';
        body.style.overflow = 'hidden';
        
        // Focus management
        const firstFocusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (firstFocusable) {
            firstFocusable.focus();
        }

        // Trap focus within modal
        trapFocus(modal);
    }

    /**
     * Close modal
     * @param {HTMLElement} modal
     */
    function closeModal(modal) {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
        body.style.overflow = '';
        
        // Return focus to trigger element
        const trigger = document.querySelector(`[data-modal-target="${modal.id}"]`);
        if (trigger) {
            trigger.focus();
        }
    }

    /**
     * Trap focus within modal
     * @param {HTMLElement} modal
     */
    function trapFocus(modal) {
        const focusableElements = modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        modal.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
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
            }
        });
    }

    /**
     * Initialize tooltips
     */
    function initializeTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', showTooltip);
            element.addEventListener('mouseleave', hideTooltip);
            element.addEventListener('focus', showTooltip);
            element.addEventListener('blur', hideTooltip);
        });
    }

    /**
     * Show tooltip
     * @param {Event} e
     */
    function showTooltip(e) {
        const element = e.target;
        const tooltipText = element.getAttribute('data-tooltip');
        
        if (!tooltipText) return;

        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = tooltipText;
        tooltip.style.cssText = `
            position: absolute;
            background: #1e293b;
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            z-index: 1000;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s ease;
        `;

        document.body.appendChild(tooltip);

        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';

        setTimeout(() => {
            tooltip.style.opacity = '1';
        }, 10);

        element._tooltip = tooltip;
    }

    /**
     * Hide tooltip
     * @param {Event} e
     */
    function hideTooltip(e) {
        const element = e.target;
        if (element._tooltip) {
            element._tooltip.style.opacity = '0';
            setTimeout(() => {
                if (element._tooltip && element._tooltip.parentNode) {
                    element._tooltip.parentNode.removeChild(element._tooltip);
                }
                element._tooltip = null;
            }, 200);
        }
    }

    /**
     * Initialize animations
     */
    function initializeAnimations() {
        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe cards for animation
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.classList.add('animate-prepare');
            observer.observe(card);
        });

        // Animation styles are now in CSS file to comply with CSP
    }

    /**
     * Initialize image lazy loading
     */
    function initializeImageLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');
        
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

        images.forEach(img => {
            img.classList.add('lazy');
            imageObserver.observe(img);
        });
    }

    /**
     * Initialize keyboard navigation
     */
    function initializeKeyboardNavigation() {
        // Skip to content link
        const skipLink = document.createElement('a');
        skipLink.href = '#main-content';
        skipLink.className = 'skip-link';
        skipLink.textContent = 'تخطي إلى المحتوى الرئيسي';
        document.body.insertBefore(skipLink, document.body.firstChild);

        // Add main content id if not exists
        const mainContent = document.querySelector('.content-grid') || document.querySelector('main');
        if (mainContent && !mainContent.id) {
            mainContent.id = 'main-content';
        }

        // Enhanced keyboard navigation for buttons
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    }

    /**
     * Utility function to show loading state
     * @param {HTMLElement} element
     */
    function showLoading(element) {
        element.classList.add('loading');
        element.setAttribute('aria-busy', 'true');
    }

    /**
     * Utility function to hide loading state
     * @param {HTMLElement} element
     */
    function hideLoading(element) {
        element.classList.remove('loading');
        element.removeAttribute('aria-busy');
    }

    /**
     * Utility function to show notification
     * @param {string} message
     * @param {string} type
     */
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1001;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
            word-wrap: break-word;
        `;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);

        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Export utilities to global scope for external use
    window.VendorOrderDetails = {
        showLoading,
        hideLoading,
        showNotification,
        openModal: (modalId) => {
            const modal = document.getElementById(modalId);
            if (modal) openModal(modal);
        },
        closeModal: (modalId) => {
            const modal = document.getElementById(modalId);
            if (modal) closeModal(modal);
        }
    };

})();