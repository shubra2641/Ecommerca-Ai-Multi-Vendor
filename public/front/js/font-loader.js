/**
 * Font Loader JavaScript
 * Handles dynamic loading and application of Google Fonts
 */

(function () {
    'use strict';

    // Font families that support Arabic
    const ARABIC_FONTS = [
        'Noto Sans Arabic', 'Cairo', 'Tajawal', 'Almarai', 'Amiri',
        'Scheherazade New', 'Markazi Text', 'Reem Kufi', 'IBM Plex Sans Arabic',
        'Changa', 'El Messiri', 'Harmattan', 'Lateef', 'Aref Ruqaa',
        'Katibeh', 'Lalezar', 'Mirza'
    ];

    // Latin fonts
    const LATIN_FONTS = [
        'Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat',
        'Source Sans Pro', 'Oswald', 'Raleway', 'PT Sans', 'Lora',
        'Nunito', 'Poppins', 'Playfair Display', 'Merriweather', 'Ubuntu',
        'Crimson Text', 'Work Sans', 'Fira Sans', 'Noto Sans', 'Dancing Script',
        'Roboto Slab', 'Source Serif Pro', 'Libre Baskerville', 'Quicksand',
        'Rubik', 'Barlow', 'DM Sans', 'Manrope', 'Space Grotesk', 'Plus Jakarta Sans'
    ];

    /**
     * Load Google Font dynamically
     * @param {string} fontFamily - The font family name
     */
    function loadGoogleFont(fontFamily)
    {
        if (!fontFamily || fontFamily === 'Inter') {
            return; // Inter is already loaded by default
        }

        // Respect allow flag (CSP environments should set meta allow-google-fonts=1)
        try {
            const meta = document.querySelector('meta[name="allow-google-fonts"]');
            if (meta && meta.getAttribute('content') !== '1') {
                // Do not attempt external Google Fonts when CSP restricts styles
                applyFontToBody(fontFamily);
                return;
            }
        } catch (e) {
            // fallthrough
        }

        // Check if font is already loaded
        const existingLink = document.querySelector(`link[href *= "${fontFamily.replace(' ', '+')}"]`);
        if (existingLink) {
            return;
        }

    // Create link element for Google Fonts
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = `https://fonts.googleapis.com / css2 ? family = ${fontFamily.replace(' ', '+')} : wght@300;400;500;600;700 & display = swap`;

        // Add to document head
        document.head.appendChild(link);

        // Wait for font to load before applying
        link.onload = function () {
            applyFontToBody(fontFamily);
        };

        // Fallback in case onload doesn't fire
        setTimeout(() => {
            applyFontToBody(fontFamily);
        }, 1000);
    }

    /**
     * Apply font to body and all elements
     * @param {string} fontFamily - The font family name
     */
    function applyFontToBody(fontFamily)
    {
        // Apply to body
        document.body.style.fontFamily = `'${fontFamily}', sans - serif`;

        // Apply to common elements
        const elements = document.querySelectorAll('h1, h2, h3, h4, h5, h6, p, span, div, a, button, input, textarea, select, label');
        elements.forEach(element => {
            if (!element.style.fontFamily) {
                element.style.fontFamily = `'${fontFamily}', sans - serif`;
            }
        });

        // Trigger font change event
        const event = new CustomEvent('fontChanged', {
            detail: { fontFamily: fontFamily }
        });
        document.dispatchEvent(event);
    }

    /**
     * Initialize font loader
     */
    function initFontLoader()
    {
        // Get font from meta tag or data attribute
        const fontMeta = document.querySelector('meta[name="selected-font"]');
        const selectedFont = fontMeta ? fontMeta.getAttribute('content') : null;

        if (selectedFont) {
            loadGoogleFont(selectedFont);
        }

        // Listen for font changes (for admin settings)
        document.addEventListener('fontPreview', function (e) {
            if (e.detail && e.detail.fontFamily) {
                loadGoogleFont(e.detail.fontFamily);
            }
        });
    }

    /**
     * Preview font function for settings page
     * @param {string} fontFamily - The font family to preview
     */
    window.previewFont = function (fontFamily) {
        loadGoogleFont(fontFamily);

        // Dispatch preview event
        const event = new CustomEvent('fontPreview', {
            detail: { fontFamily: fontFamily }
        });
        document.dispatchEvent(event);
    };

    /**
     * Reset font to default
     */
    window.resetFont = function () {
        document.body.style.fontFamily = '';
        const elements = document.querySelectorAll('[style*="font-family"]');
        elements.forEach(element => {
            element.style.fontFamily = '';
        });
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFontLoader);
    } else {
        initFontLoader();
    }

})();

