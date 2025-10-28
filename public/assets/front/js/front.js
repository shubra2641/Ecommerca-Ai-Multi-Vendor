/**
 * Simple Front-end JavaScript - Ultra Lightweight
 * Only essential functionality
 */

(function () {
    'use strict';

    // Simple utility functions
    const $ = (selector) => document.querySelector(selector);
    const $$ = (selector) => Array.from(document.querySelectorAll(selector));

    // Simple Admin Panel
    window.FrontApp = {

        // Initialize everything
        init() {
            this.initDropdowns();
            this.initConfirmations();
            this.initQuantitySelector();
            this.initLoader();
        },

        // Dropdowns
        initDropdowns() {
            document.addEventListener('click', (e) => {
                const toggle = e.target.closest('.dropdown-trigger');
                if (!toggle) return;

                e.preventDefault();
                const dropdown = toggle.closest('[data-dropdown]');
                if (!dropdown) return;

                // Close all other dropdowns
                $$('[data-dropdown].open').forEach(d => {
                    if (d !== dropdown) d.classList.remove('open');
                });

                // Toggle current dropdown
                dropdown.classList.toggle('open');
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('[data-dropdown]')) {
                    $$('[data-dropdown].open').forEach(d => {
                        d.classList.remove('open');
                    });
                }
            });
        },

        // Confirmations
        initConfirmations() {
            // Form confirmations
            document.querySelectorAll('form.js-confirm, form.js-confirm-delete').forEach(form => {
                form.addEventListener('submit', (e) => {
                    const msg = form.dataset.confirm || 'Are you sure?';
                    if (!confirm(msg)) e.preventDefault();
                });
            });

            // Link/button confirmations
            document.querySelectorAll('[data-confirm]').forEach(element => {
                element.addEventListener('click', (e) => {
                    const msg = element.getAttribute('data-confirm');
                    if (!confirm(msg)) e.preventDefault();
                });
            });
        },

        // Quantity selector
        initQuantitySelector() {
            const qtyDisplay = $('#qtyDisplay');
            const qtyInput = $('#qtyInputSide');
            const increaseBtn = $('.qty-increase');
            const decreaseBtn = $('.qty-trash');

            if (!(qtyDisplay && qtyInput && increaseBtn && decreaseBtn)) return;

            let currentQty = 1;
            const maxStock = parseInt(qtyInput.getAttribute('max')) || 999;

            const updateDisplay = () => {
                qtyDisplay.textContent = currentQty;
                qtyInput.value = currentQty;
            };

            const setQuantity = (newQty) => {
                currentQty = Math.max(1, Math.min(newQty, maxStock));
                updateDisplay();
            };

            increaseBtn.addEventListener('click', () => setQuantity(currentQty + 1));
            decreaseBtn.addEventListener('click', () => setQuantity(currentQty - 1));
        },

        // Loader
        initLoader() {
            const loader = $('#app-loader');
            if (!loader) return;

            const hideLoader = () => {
                loader.classList.add('hidden');
                loader.setAttribute('aria-hidden', 'true');
            };

            if (document.readyState === 'complete') {
                hideLoader();
            } else {
                window.addEventListener('load', hideLoader);
                setTimeout(hideLoader, 3000);
            }
        }
    };

    // Start when page loads
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => FrontApp.init());
    } else {
        FrontApp.init();
    }

})();
