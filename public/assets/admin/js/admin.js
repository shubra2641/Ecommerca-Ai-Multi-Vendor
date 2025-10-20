/**
 * Ultra Simple Admin JavaScript
 * Basic functionality only
 */
(function () {
    'use strict';

    // Simple Admin object
    window.AdminPanel = {};

    // Initialize everything
    AdminPanel.init = function () {
        this.initSidebar();
        this.initDropdowns();
        this.initConfirmations();
    };

    // Simple sidebar toggle
    AdminPanel.initSidebar = function () {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('mobileMenuToggle');
        const overlay = document.querySelector('.sidebar-overlay');

        if (!sidebar || !toggle) return;

        // Toggle sidebar
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
        });

        // Close sidebar when clicking overlay
        if (overlay) {
            overlay.addEventListener('click', function () {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }

        // Close sidebar on mobile when clicking nav items
        document.addEventListener('click', function (e) {
            const navItem = e.target.closest('.nav-item');
            if (navItem && window.innerWidth <= 992) {
                sidebar.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function () {
            if (window.innerWidth > 992) {
                sidebar.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
            }
        });
    };

    // Simple dropdowns
    AdminPanel.initDropdowns = function () {
        // Handle dropdown clicks
        document.addEventListener('click', function (e) {
            const toggle = e.target.closest('.dropdown-toggle');
            if (!toggle) return;

            e.preventDefault();
            e.stopPropagation();

            const dropdown = toggle.closest('.dropdown');
            if (!dropdown) return;

            const isOpen = dropdown.classList.contains('show');

            // Close all other dropdowns
            document.querySelectorAll('.dropdown.show').forEach(function (openDropdown) {
                if (openDropdown !== dropdown) {
                    openDropdown.classList.remove('show');
                    const openToggle = openDropdown.querySelector('.dropdown-toggle');
                    if (openToggle) openToggle.setAttribute('aria-expanded', 'false');
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
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown.show').forEach(function (dropdown) {
                    dropdown.classList.remove('show');
                    const toggle = dropdown.querySelector('.dropdown-toggle');
                    if (toggle) toggle.setAttribute('aria-expanded', 'false');
                });
            }
        });
    };

    // Simple confirmations
    AdminPanel.initConfirmations = function () {
        // Form confirmations
        document.querySelectorAll('form.js-confirm, form.js-confirm-delete').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                const msg = form.dataset.confirm || 'Are you sure?';
                if (!confirm(msg)) {
                    e.preventDefault();
                }
            });
        });

        // Link/button confirmations
        document.querySelectorAll('[data-confirm]').forEach(function (element) {
            element.addEventListener('click', function (e) {
                const msg = element.getAttribute('data-confirm');
                if (!confirm(msg)) {
                    e.preventDefault();
                }
            });
        });
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

})();