/**
 * Font Loader JavaScript
 * Handles dynamic loading and application of Local Fonts (CSP Compliant)
 */

(function () {
    'use strict';

    // Font families that support Arabic
    const ARABIC_FONTS = [
        'Cairo', 'Noto Sans Arabic', 'Tajawal', 'Almarai', 'Amiri',
        'Scheherazade New', 'Markazi Text', 'Reem Kufi', 'IBM Plex Sans Arabic',
        'Changa', 'El Messiri', 'Harmattan', 'Lateef', 'Aref Ruqaa',
        'Katibeh', 'Lalezar', 'Mirza'
    ];

    // Latin fonts
    const LATIN_FONTS = [
        'Inter', 'Roboto', 'Poppins', 'Open Sans', 'Lato', 'Montserrat',
        'Source Sans Pro', 'Oswald', 'Raleway', 'PT Sans', 'Lora',
        'Nunito', 'Playfair Display', 'Merriweather', 'Ubuntu',
        'Crimson Text', 'Work Sans', 'Fira Sans', 'Noto Sans', 'Dancing Script',
        'Roboto Slab', 'Source Serif Pro', 'Libre Baskerville', 'Quicksand',
        'Rubik', 'Barlow', 'DM Sans', 'Manrope', 'Space Grotesk', 'Plus Jakarta Sans'
    ];

    /**
     * Load Local Font dynamically (CSP compliant)
     * @param {string} fontFamily - The font family name
     * @param {string} fontPath - Path to the font file
     * @param {string} fontWeight - Font weight range or specific weight
     * @param {string} fontStyle - Font style (normal, italic)
     */
    function loadLocalFont(fontFamily, fontPath, fontWeight = 'normal', fontStyle = 'normal') {
        // CSP-safe: rely on predeclared @font-face in CSS; optionally add preload one time per (family,weight)
        if (!fontFamily || !fontPath) {
            return;
        }
        const key = `preload-${fontFamily}-${fontWeight}`;
        if (!document.querySelector(`link[data-font="${key}"]`)) {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'font';
            link.type = 'font/woff2';
            link.href = fontPath;
            link.setAttribute('data-font', key);
            link.crossOrigin = 'anonymous';
            document.head.appendChild(link);
        }
    }

    /**
     * Apply font to body element
     * @param {string} fontFamily - The font family to apply
     */
    function applyFontToBody(fontFamily) {
        if (!fontFamily) {
            return;
        }

        // Get current font stack from body
        const currentFontFamily = getComputedStyle(document.body).fontFamily;

        // Check if font is already applied
        if (currentFontFamily.includes(fontFamily)) {
            return;
        }

        // Apply font with fallbacks
        let fontStack;
        if (ARABIC_FONTS.includes(fontFamily)) {
            fontStack = `"${fontFamily}", "Cairo", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif`;
        } else {
            fontStack = `"${fontFamily}", "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif`;
        }

        // Avoid inline style (CSP) -> use data attribute consumed by CSS
        document.body.setAttribute('data-font-active', fontFamily);
    }

    /**
     * Detect if text contains Arabic characters
     * @param {string} text - Text to analyze
     * @returns {boolean} - True if contains Arabic
     */
    function containsArabic(text) {
        const arabicRegex = /[\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFF]/;
        return arabicRegex.test(text);
    }

    /**
     * Get appropriate font for content
     * @param {string} content - Content to analyze
     * @returns {string} - Recommended font family
     */
    function getAppropriateFont(content) {
        if (containsArabic(content)) {
            return 'Cairo'; // Default Arabic font
        }
        return 'Inter'; // Default Latin font
    }

    /**
     * Initialize font loading
     */
    function initializeFonts() {
        // Preload
        const BASE_WEIGHTS = { 'Inter': 400, 'Cairo': 400, 'Poppins': 400 };
        Object.entries(BASE_WEIGHTS).forEach(([fam, w]) => {
            const path = `/fonts/${fam}/${fam}-${familyFileSuffix(fam, w)}.woff2`;
            // Check that file exists by attempting to fetch its HEAD (silent fail)
            try {
                fetch(path, { method: 'HEAD', mode: 'same-origin', credentials: 'same-origin' })
                    .then(resp => {
                        if (resp.ok) {
                            loadLocalFont(fam, path, String(w));
                        }
                    })
                    .catch(() => { });
            } catch (e) {
                /* ignore */
            }
        });

        const metaSelected = document.querySelector('meta[name="selected-font"]');
        const savedFont = metaSelected ? metaSelected.content : null;
        const SUPPORTED = ['Inter', 'Cairo', 'Roboto', 'Poppins'];
        let effective;
        if (savedFont) {
            effective = SUPPORTED.includes(savedFont)
                ? savedFont
                : (ARABIC_FONTS.includes(savedFont) ? 'Cairo' : 'Inter');
            if (effective !== savedFont && window && window.console) {
                console.warn('[FontLoader] Saved font "' + savedFont + '" not locally available. Fallback -> ' + effective);
            }
        } else {
            const pageContent = document.body.textContent || document.body.innerText || '';
            effective = getAppropriateFont(pageContent);
        }
        applyFontToBody(effective);
        setupLazyWeightLoading();
    }

    // Map (family, weight) to existing file naming pattern
    function familyFileSuffix(family, weight) {
        switch (family) {
            case 'Inter': return `latin-${weight}`;
            case 'Cairo': return `arabic-${weight}`;
            case 'Roboto': return `${weight}`;
            case 'Poppins': return `${weight}`;
            default: return `${weight}`;
        }
    }

    // Lazy loading utilities
    const loadedWeightKeys = new Set();
    function ensureFontWeightLoaded(family, weight) {
        if (!family || weight <= 400) {
            return; // الوزن 400 مُحمَّل مسبقاً
        }
        const key = family + ':' + weight;
        if (loadedWeightKeys.has(key)) {
            return;
        }
        loadedWeightKeys.add(key);
        loadLocalFont(family, `/fonts/${family}/${family}-${familyFileSuffix(family, weight)}.woff2`, String(weight));
    }
    function normalizeWeight(val) {
        if (!val) {
            return 400;
        }
        if (val === 'bold') {
            return 700;
        }
        const n = parseInt(val, 10); if (isNaN(n)) {
            return 400;
        } return n;
    }
    function scanForHeavyWeights(root) {
        const activeFamily = document.body.getAttribute('data-font-active') || 'Inter';
        (root.querySelectorAll ? root.querySelectorAll('h1,h2,h3,strong,b,[class*="fw-"],[style*="font-weight"],.fw-semibold,.fw-bold') : [])
            .forEach(el => {
                const w = normalizeWeight(getComputedStyle(el).fontWeight);
                if (w >= 500) { // نبدأ من 500 ثم قد نصل إلى 600 أو 700
                    const target = w >= 700 ? 700 : (w >= 600 ? 600 : 500);
                    ensureFontWeightLoaded(activeFamily, target);
                }
            });
    }
    function setupLazyWeightLoading() {
        // فحص أولي
        scanForHeavyWeights(document);
        // مراقبة تقاطعية للعناوين و العناصر المحتملة
        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        scanForHeavyWeights(e.target);
                    }
                });
            }, { threshold: 0 });
            document.querySelectorAll('h1,h2,h3,strong,b').forEach(el => io.observe(el));
        }
    }

    // مراقبة المحتوى الديناميكي (تستدعي فحص الأوزان أيضاً)
    function handleDynamicContent() {
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            const content = node.textContent || node.innerText || '';
                            if (content.trim()) {
                                if (containsArabic(content) && !node.style.fontFamily.includes('Cairo')) {
                                    node.setAttribute('data-font-fragment', 'cairo');
                                }
                                scanForHeavyWeights(node);
                            }
                        }
                    });
                }
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    /**
     * Initialize when DOM is ready
     */
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                initializeFonts();
                handleDynamicContent();
            });
        } else {
            initializeFonts();
            handleDynamicContent();
        }
    }

    // Start initialization
    init();

    // Expose functions for external use
    window.FontLoader = {
        loadLocalFont,
        applyFontToBody,
        containsArabic,
        getAppropriateFont
    };

})();