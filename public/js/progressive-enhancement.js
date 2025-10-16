/**
 * Progressive Enhancement JavaScript
 * Handles form interactions and user confirmations without inline JavaScript
 * Provides graceful degradation when JavaScript is disabled
 */

(function () {
    'use strict';

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init()
    {
        initRemoveForms();
        initLanguageSwitcher();
        initLogoutForm();
        initAddressDeletion();
    }

    /**
     * Handle cart item removal with confirmation
     * Graceful degradation: Forms still work without JavaScript
     */
    function initRemoveForms()
    {
        const removeForms = document.querySelectorAll('.remove-form');
        removeForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                const confirmMessage = this.getAttribute('data-confirm') || 'Remove item?';
                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                }
            });
        });
    }

    /**
     * Handle language switcher auto-submission
     * Graceful degradation: Manual submit button appears when JS disabled
     */
    function initLanguageSwitcher()
    {
        const langSelects = document.querySelectorAll('select[name="lang"]');
        langSelects.forEach(select => {
            // Hide manual submit button when JS is available
            const submitBtn = select.parentNode.querySelector('.lang-submit-btn');
            if (submitBtn) {
                submitBtn.style.display = 'none';
            }

            select.addEventListener('change', function () {
                this.form.submit();
            });
        });
    }

    /**
     * Handle logout form submission
     * Graceful degradation: Regular form submission works without JS
     */
    function initLogoutForm()
    {
        const logoutLinks = document.querySelectorAll('[data-logout]');
        logoutLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const formId = this.getAttribute('data-logout');
                const form = document.getElementById(formId);
                if (form) {
                    form.submit();
                }
            });
        });
    }

    /**
     * Handle address deletion with confirmation
     * Graceful degradation: Forms work without JavaScript
     */
    function initAddressDeletion()
    {
        const deleteForms = document.querySelectorAll('.delete-address-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                const confirmMessage = this.getAttribute('data-confirm') || 'Delete address?';
                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                }
            });
        });
    }

    /**
     * Utility function to show loading state
     */
    function showLoading(element)
    {
        if (element) {
            element.classList.add('loading');
            const originalText = element.textContent;
            element.setAttribute('data-original-text', originalText);
            element.textContent = 'Loading...';
            element.disabled = true;
        }
    }

    /**
     * Utility function to hide loading state
     */
    function hideLoading(element)
    {
        if (element) {
            element.classList.remove('loading');
            const originalText = element.getAttribute('data-original-text');
            if (originalText) {
                element.textContent = originalText;
                element.removeAttribute('data-original-text');
            }
            element.disabled = false;
        }
    }

    // Export functions for external use
    window.ProgressiveEnhancement = {
        showLoading: showLoading,
        hideLoading: hideLoading
    };

})();

/**
 * CSS for loading states and progressive enhancement
 * This ensures visual feedback even when JavaScript is disabled
 */
if (!document.getElementById('progressive-enhancement-styles')) {
    const style = document.createElement('style');
    style.id = 'progressive-enhancement-styles';
    style.textContent = `
        .loading {
            opacity: 0.6;
            pointer - events: none;
    }

        .lang - submit - btn {
            margin - left: 8px;
            padding: 4px 8px;
            background: #3b82f6;
            color: white;
            border: none;
            border - radius: 4px;
            cursor: pointer;
    }

        .lang - submit - btn:hover {
            background: #2563eb;
    }

        /* Hide submit button when JavaScript is available */
        .js .lang - submit - btn {
            display: none !important;
    }

        /* Show loading indicator */
        .loading::after {
            content: '';
            display: inline - block;
            width: 12px;
            height: 12px;
            margin - left: 8px;
            border: 2px solid #f3f3f3;
            border - top: 2px solid #3b82f6;
            border - radius: 50 % ;
            animation: spin 1s linear infinite;
    }

        @keyframes spin {
            0 % { transform: rotate(0deg); }
            100 % { transform: rotate(360deg); }
    }
    `;
    document.head.appendChild(style);
}

// Add 'js' class to body to indicate JavaScript is available
document.documentElement.classList.add('js');