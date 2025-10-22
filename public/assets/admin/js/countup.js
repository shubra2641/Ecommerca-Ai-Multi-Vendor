// Simple count-up animation
(function () {
    function animate(el) {
        if (el.dataset.counted) return;

        const target = parseFloat(el.dataset.target || '0');
        const prefix = el.dataset.prefix || '';
        const suffix = el.dataset.suffix || '';
        const duration = parseInt(el.dataset.duration || '1000', 10);

        let start = 0;
        const increment = target / (duration / 16);

        const timer = setInterval(() => {
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
        document.querySelectorAll('[data-countup]').forEach(animate);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
