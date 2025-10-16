/**
 * Font Manager JavaScript
 * Handles Google Fonts loading and font preview functionality
 * Follows PSR-12 coding standards and includes XSS protection
 */

(function () {
    'use strict';

    // Available Google Fonts with their weights (including Arabic support)
    const GOOGLE_FONTS = {
        // Latin Fonts
        'Inter': '300,400,500,600,700',
        'Roboto': '300,400,500,700',
        'Open Sans': '300,400,600,700',
        'Lato': '300,400,700',
        'Montserrat': '300,400,500,600,700',
        'Source Sans Pro': '300,400,600,700',
        'Oswald': '300,400,500,600,700',
        'Raleway': '300,400,500,600,700',
        'PT Sans': '400,700',
        'Lora': '400,500,600,700',
        'Nunito': '300,400,600,700',
        'Poppins': '300,400,500,600,700',
        'Playfair Display': '400,500,600,700',
        'Merriweather': '300,400,700',
        'Ubuntu': '300,400,500,700',
        'Crimson Text': '400,600,700',
        'Work Sans': '300,400,500,600,700',
        'Fira Sans': '300,400,500,600,700',
        'Noto Sans': '300,400,500,600,700',
        'Dancing Script': '400,500,600,700',
        // Additional Latin Fonts
        'Roboto Slab': '300,400,500,600,700',
        'Source Serif Pro': '300,400,600,700',
        'Libre Baskerville': '400,700',
        'Quicksand': '300,400,500,600,700',
        'Rubik': '300,400,500,600,700',
        'Barlow': '300,400,500,600,700',
        'DM Sans': '400,500,700',
        'Manrope': '300,400,500,600,700',
        'Space Grotesk': '300,400,500,600,700',
        'Plus Jakarta Sans': '300,400,500,600,700',
        // Arabic Fonts
        'Noto Sans Arabic': '300,400,500,600,700',
        'Cairo': '300,400,500,600,700',
        'Tajawal': '300,400,500,700',
        'Almarai': '300,400,700',
        'Amiri': '400,700',
        'Scheherazade New': '400,700',
        'Markazi Text': '400,500,600,700',
        'Reem Kufi': '400,500,600,700',
        'IBM Plex Sans Arabic': '300,400,500,600,700',
        'Changa': '300,400,500,600,700',
        'El Messiri': '400,500,600,700',
        'Harmattan': '400,700',
        'Lateef': '400,700',
        'Aref Ruqaa': '400,700',
        'Katibeh': '400',
        'Lalezar': '400',
        'Mirza': '400,500,600,700'
    };

    // Loaded fonts cache to prevent duplicate loading
    const loadedFonts = new Set();

    /**
     * Sanitize font name to prevent XSS attacks
     * @param {string} fontName - The font name to sanitize
     * @returns {string} - Sanitized font name
     */
    function sanitizeFontName(fontName)
    {
        if (typeof fontName !== 'string') {
            return 'Inter';
        }

        // Only allow alphanumeric characters, spaces, and common font name characters
        const sanitized = fontName.replace(/[^a-zA-Z0-9\s\+\-]/g, '');

        // Check if it's in our allowed fonts list
        return GOOGLE_FONTS.hasOwnProperty(sanitized) ? sanitized : 'Inter';
    }

    /**
     * Load Google Font dynamically
     * @param {string} fontFamily - The font family name
     */
    function loadGoogleFont(fontFamily)
    {
        // Disabled: external Google Fonts blocked by CSP. Keeping interface no-op.
        return;
    }

    /**
     * Apply font to preview element
     * @param {string} fontFamily - The font family name
     */
    function applyFontPreview(fontFamily)
    {
        const sanitizedFont = sanitizeFontName(fontFamily);
        const previewElement = document.getElementById('fontPreview');
        const previewContainer = document.querySelector('.font-preview-container');
        if (!previewElement || !previewContainer) {
            return;
        }

        previewContainer.style.display = 'block';

        // Lock container height first time to avoid layout jump
        if (!previewContainer.dataset.lockedHeight) {
            const rect = previewContainer.getBoundingClientRect();
            previewContainer.style.minHeight = rect.height + 'px';
            previewContainer.dataset.lockedHeight = '1';
        }

        const fontStack = sanitizedFont === 'Inter'
            ? 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
            : `"${sanitizedFont}", -apple - system, BlinkMacSystemFont, "Segoe UI", Roboto, sans - serif`;

        previewElement.style.fontFamily = fontStack;

        // Update sample text to reflect font name
        const sampleLines = previewElement.querySelectorAll('[data-sample]');
        if (sampleLines.length) {
            sampleLines.forEach(el => {
                if (el.dataset.sample === 'label') {
                    el.textContent = sanitizedFont + ' - ' + window.transFontPreviewTitle || 'Font Preview';
                }
            });
        }

        previewElement.classList.add('font-loading');
        setTimeout(() => previewElement.classList.remove('font-loading'), 350);
    }

    /**
     * Apply font to entire website
     * @param {string} fontFamily - The font family name
     */
    function applyFontToWebsite(fontFamily)
    {
        // Only called initially to show current saved font, not on every preview change before save.
        const sanitizedFont = sanitizeFontName(fontFamily);
        const root = document.documentElement;
        const fontStack = sanitizedFont === 'Inter'
            ? 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
            : `"${sanitizedFont}", -apple - system, BlinkMacSystemFont, "Segoe UI", Roboto, sans - serif`;
        // Set the CSS variable (used by local-fonts.css) and a data attribute on body
        // so the same CSP-safe mechanism used by the front-end is reused in admin.
        root.style.setProperty('--font-family-primary', fontStack);
        try {
            document.body.setAttribute('data-font-active', sanitizedFont);
        } catch (e) {
            // Fallback: if setting data attribute fails for some reason, keep variable only.
            if (window && window.console) {
                console.warn('[FontManager] failed to set data-font-active', e);
            }
        }
    }

    /**
     * Initialize font manager
     */
    function initializeFontManager()
    {
        const fontSelect = document.getElementById('font_family');

        if (!fontSelect) {
            return;
        }

        const currentFont = fontSelect.value || 'Inter';
    // External fonts disabled; only apply if local Inter/Cairo
        applyFontToWebsite(currentFont);
        applyFontPreview(currentFont);   // preview shows it

        // Handle font selection change
        fontSelect.addEventListener('change', function () {
            const selectedFont = this.value;
            // Apply preview in the preview pane
            applyFontPreview(selectedFont);
            // Also apply to the entire admin UI for a live preview (does not persist until save)
            applyFontToWebsite(selectedFont);
            // Show notification when font changes
            if (typeof showNotification === 'function') {
                showNotification('Font preview updated. Save settings to apply permanently.', 'info');
            }
        });
    }

    /**
     * Load font from localStorage on page load
     */
    function loadSavedFont()
    {
 /* localStorage persistence disabled for clearer preview separation */ }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initializeFontManager();
            loadSavedFont();
        });
    } else {
        initializeFontManager();
        loadSavedFont();
    }

    // Export functions for global access
    window.FontManager = {
        loadGoogleFont: loadGoogleFont,
        applyFontPreview: applyFontPreview,
        applyFontToWebsite: applyFontToWebsite,
        sanitizeFontName: sanitizeFontName
    };

})();