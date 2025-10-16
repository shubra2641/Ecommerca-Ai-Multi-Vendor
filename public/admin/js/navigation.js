/**
 * Navigation JavaScript
 * Handles navigation interactions and form submissions
 */

// Initialize navigation functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    initializeNavigation();
});

/**
 * Initialize navigation functionality
 */
function initializeNavigation()
{
    initializeFormSubmissions();
    initializeDropdowns();
    initializeMobileMenu();
}

/**
 * Initialize form submission handlers
 */
function initializeFormSubmissions()
{
    // Handle logout form submissions
    const formSubmitLinks = document.querySelectorAll('[data-action="submit-form"]');

    formSubmitLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            const target = this.getAttribute('data-form-target');
            let form;

            if (target === 'closest') {
                form = this.closest('form');
            } else if (target) {
                form = document.querySelector(target);
            }

            if (form) {
                // Add loading state
                this.classList.add('loading');

                // Submit the form
                form.submit();
            } else {
                console.error('Form not found for submission');
            }
        });
    });
}

/**
 * Initialize dropdown functionality
 */
function initializeDropdowns()
{
    const dropdownToggles = document.querySelectorAll('[data-toggle="dropdown"]');

    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function (event) {
            event.preventDefault();

            const targetId = this.getAttribute('data-target');
            const dropdown = document.querySelector(targetId);

            if (dropdown) {
                dropdown.classList.toggle('show');

                // Close other dropdowns
                const otherDropdowns = document.querySelectorAll('.dropdown-menu.show');
                otherDropdowns.forEach(other => {
                    if (other !== dropdown) {
                        other.classList.remove('show');
                    }
                });
            }
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.dropdown')) {
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });
}

/**
 * Initialize mobile menu functionality
 */
function initializeMobileMenu()
{
    const mobileMenuToggle = document.querySelector('[data-toggle="mobile-menu"]');
    const mobileMenu = document.querySelector('#mobile-menu');

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function () {
            mobileMenu.classList.toggle('show');

            // Update aria attributes
            const isExpanded = mobileMenu.classList.contains('show');
            this.setAttribute('aria-expanded', isExpanded);
        });
    }
}

/**
 * Show notification message
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, error, warning, info)
 * @param {number} duration - Duration in milliseconds (default: 5000)
 */
function showNotification(message, type = 'info', duration = 5000)
{
    if (window.AdminPanel && typeof window.AdminPanel.showNotification === 'function') {
        window.AdminPanel.showNotification(message, type);
    } else {
        // Fallback - simple alert
        alert(message);
    }
}

/**
 * Handle navigation loading states
 */
function setNavigationLoading(element, loading = true)
{
    if (loading) {
        element.classList.add('loading');
        element.setAttribute('disabled', 'disabled');
    } else {
        element.classList.remove('loading');
        element.removeAttribute('disabled');
    }
}

/**
 * Confirm action before proceeding
 * @param {string} message - Confirmation message
 * @param {Function} callback - Function to call if confirmed
 */
function confirmAction(message, callback)
{
    if (confirm(message)) {
        callback();
    }
}

// Export functions for global use
window.navigationUtils = {
    showNotification,
    setNavigationLoading,
    confirmAction
};