// Lightweight count-up animation shared by admin & vendor dashboards
// Elements: add data-countup, data-target (number), optional: data-decimals, data-prefix, data-suffix, data-duration(ms)
(function() {
    function formatNumber(value, decimals, locale, prefix, suffix) {
        const opts = decimals > 0 ? {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        } : { maximumFractionDigits: 0 };
        try {
            return (prefix || '') + new Intl.NumberFormat(locale || 'en', opts).format(value) + (suffix || '');
        } catch {
            return (prefix || '') + value.toFixed(decimals) + (suffix || '');
        }
    }
    function animate(el) {
        if (el.dataset.counted) {
            return; // prevent double
        }
        const target = parseFloat(el.dataset.target || '0');
        const decimals = parseInt(el.dataset.decimals || '0', 10);
        const prefix = el.dataset.prefix || '';
        const suffix = el.dataset.suffix || '';
        const duration = parseInt(el.dataset.duration || '1200', 10);
        const startTime = performance.now();
        const startVal = 0;
        const locale = document.documentElement.getAttribute('lang') || 'en';
        const EASE_HALF = 0.5;
        const EASE_MULTIPLIER = 4;
        const EASE_POWER = 3;
        const EASE_DIVISOR = 2;
        const EASE_NEGATIVE_MULTIPLIER = -2;
        const EASE_OFFSET = 2;
        const ease = t => t < EASE_HALF ? EASE_MULTIPLIER * t * t * t : 1 - Math.pow(EASE_NEGATIVE_MULTIPLIER * t + EASE_OFFSET, EASE_POWER) / EASE_DIVISOR; // cubic in-out
        function step(now) {
            const p = Math.min(1, (now - startTime) / duration);
            const val = startVal + (target - startVal) * ease(p);
            el.textContent = formatNumber(val, decimals, locale, prefix, suffix);
            if (p < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = formatNumber(target, decimals, locale, prefix, suffix); el.dataset.counted = '1';
            }
        }
        requestAnimationFrame(step);
    }
    function init() {
        const els = document.querySelectorAll('[data-countup]');
        if (!('IntersectionObserver' in window)) {
            els.forEach(animate); return;
        }
        const io = new IntersectionObserver(entries => {
            entries.forEach(ent => {
                if (ent.isIntersecting) {
                    animate(ent.target); io.unobserve(ent.target);
                }
            });
        }, { threshold: 0.3 });
        els.forEach(el => io.observe(el));
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
