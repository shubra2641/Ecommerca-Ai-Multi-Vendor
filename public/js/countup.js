// Simple count-up animation for admin & vendor dashboards
// Usage: add data-countup, data-target (number), optional: data-decimals, data-prefix, data-suffix, data-duration(ms)

(function () {
    'use strict';

    // Format number with locale support
    function formatNumber(value, decimals, prefix, suffix) {
        const options = decimals > 0
            ? { minimumFractionDigits: decimals, maximumFractionDigits: decimals }
            : { maximumFractionDigits: 0 };

        const locale = document.documentElement.getAttribute('lang') || 'en';

        try {
            return prefix + new Intl.NumberFormat(locale, options).format(value) + suffix;
        } catch (e) {
            return prefix + value.toFixed(decimals) + suffix;
        }
    }

    // Easing function for smooth animation
    function easeInOutCubic(t) {
        return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
    }

    // Animate element
    function animateElement(element) {
        if (element.dataset.counted) return;

        const target = parseFloat(element.dataset.target || '0');
        const decimals = parseInt(element.dataset.decimals || '0');
        const prefix = element.dataset.prefix || '';
        const suffix = element.dataset.suffix || '';
        const duration = parseInt(element.dataset.duration || '1200');

        const startTime = performance.now();
        const startValue = 0;

        function updateAnimation(currentTime) {
            const progress = Math.min(1, (currentTime - startTime) / duration);
            const currentValue = startValue + (target - startValue) * easeInOutCubic(progress);

            element.textContent = formatNumber(currentValue, decimals, prefix, suffix);

            if (progress < 1) {
                requestAnimationFrame(updateAnimation);
            } else {
                element.textContent = formatNumber(target, decimals, prefix, suffix);
                element.dataset.counted = '1';
            }
        }

        requestAnimationFrame(updateAnimation);
    }

    // Initialize count-up animations
    function init() {
        const elements = document.querySelectorAll('[data-countup]');

        if (!elements.length) return;

        // If IntersectionObserver is not supported, animate all elements immediately
        if (!('IntersectionObserver' in window)) {
            elements.forEach(animateElement);
            return;
        }

        // Use IntersectionObserver for better performance
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateElement(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        elements.forEach(element => observer.observe(element));
    }

    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
