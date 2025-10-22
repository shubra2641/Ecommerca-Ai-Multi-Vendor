// Simple count-up animation
(function () {
    'use strict';

    function animate(el) {
        if (!el || el.dataset.counted) return;

        const target = parseFloat(el.dataset.target || '0');
        const prefix = el.dataset.prefix || '';
        const suffix = el.dataset.suffix || '';
        const duration = parseInt(el.dataset.duration || '1000', 10);

        if (isNaN(target) || isNaN(duration) || duration <= 0) {
            return;
        }

        let start = 0;
        const increment = target / (duration / 16);

        const timer = setInterval(function () {
            start += increment;
            if (start >= target) {
                start = target;
                clearInterval(timer);
                el.dataset.counted = '1';
            }
            el.textContent = prefix + Math.floor(start) + suffix;
        }, 16);
    }

    function init() {
        const elements = document.querySelectorAll('[data-countup]');
        elements.forEach(function (el) {
            animate(el);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
