/**
 * Flash Messages as Toast Notifications
 * Handles flash messages from Laravel sessions and displays them as toast notifications
 */

/* eslint-env browser */
(function () {
    'use strict';

    const FlashManager = {
        init() {
            this.initFlashMessages();
        },

        initFlashMessages() {
            const flashElements = [
                {
                    id: 'flash-success',
                    type: 'success'
                },
                {
                    id: 'flash-info',
                    type: 'info'
                },
                {
                    id: 'flash-warning',
                    type: 'warning'
                },
                {
                    id: 'flash-error',
                    type: 'error'
                }
            ];

            flashElements.forEach(({ id, type }) => {
                const element = document.getElementById(id);
                if (element) {
                    this.showToast(element.getAttribute('data-message'), type);
                    element.remove();
                }
            });

            // Handle validation errors
            const errorsEl = document.getElementById('flash-errors');
            if (errorsEl) {
                try {
                    const errors = JSON.parse(errorsEl.getAttribute('data-errors'));
                    errors.forEach(error => this.showToast(error, 'error'));
                } catch {
                    // Silent fail for JSON parsing
                }
                errorsEl.remove();
            }
        },

        showToast(message, type = 'info') {
            const container = this.getOrCreateContainer();
            const toast = this.createToast(message, type);

            container.appendChild(toast);
            this.animateToast(toast);
            this.setupToastRemoval(toast);
        },

        getOrCreateContainer() {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'toast-stack';
                container.id = 'toast-container';
                document.body.appendChild(container);
            }
            return container;
        },

        createToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.textContent = message;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'polite');
            toast.style.display = 'block';
            return toast;
        },

        animateToast(toast) {
            const ANIMATION_DELAY = 50;
            const FALLBACK_DELAY = 100;
            setTimeout(() => {
                toast.classList.add('in');
                // Force reflow
                const _ = toast.offsetHeight;
                // Use the variable to avoid unused warning
                if (_) {
                    // Force reflow completed
                }

                // Fallback for visibility
                setTimeout(() => {
                    if (!toast.classList.contains('in')) {
                        toast.style.opacity = '1';
                        toast.style.transform = 'translateY(0)';
                    }
                }, FALLBACK_DELAY);
            }, ANIMATION_DELAY);
        },

        setupToastRemoval(toast) {
            // Auto remove after 5 seconds
            const AUTO_REMOVE_DELAY = 5000;
            setTimeout(() => {
                this.removeToast(toast);
            }, AUTO_REMOVE_DELAY);

            // Click to close
            toast.addEventListener('click', () => this.removeToast(toast));
        },

        removeToast(toast) {
            toast.classList.add('out');
            const REMOVAL_DELAY = 350;
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, REMOVAL_DELAY);
        }
    };

    function initFlash() {
        const INIT_DELAY = 100;
        setTimeout(() => FlashManager.init(), INIT_DELAY);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFlash);
    } else {
        initFlash();
    }

    window.FlashManager = FlashManager;
}());