/**
 * Simple Admin JavaScript - Ultra Lightweight
 * Only essential functionality
 */

// Simple Admin Panel
window.AdminPanel = {

    // Initialize everything
    init() {
        this.initSidebar();
        this.initDropdowns();
        this.initConfirmations();
    },

    // Sidebar toggle
    initSidebar() {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('mobileMenuToggle');
        const overlay = document.querySelector('.sidebar-overlay');

        if (!sidebar || !toggle) return;

        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
        });

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }
    },

    // Dropdowns
    initDropdowns() {
        document.addEventListener('click', (e) => {
            const toggle = e.target.closest('.dropdown-toggle');
            if (!toggle) return;

            e.preventDefault();
            const dropdown = toggle.closest('.dropdown');
            if (!dropdown) return;

            // Close all other dropdowns
            document.querySelectorAll('.dropdown.show').forEach(d => {
                if (d !== dropdown) d.classList.remove('show');
            });

            // Toggle current dropdown
            dropdown.classList.toggle('show');
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown.show').forEach(d => {
                    d.classList.remove('show');
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
    }
};

// Start when page loads
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => AdminPanel.init());
} else {
    AdminPanel.init();
}
