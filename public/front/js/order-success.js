(function () {
    // Run only if element with id es-order-success exists
    document.addEventListener('DOMContentLoaded', function () {
        try {
            const banner = document.getElementById('es-order-success');
            if (!banner) {
                return;
            }

            // prevent re-run if page reloaded/back-forward cache: use sessionStorage token per order
            const orderId = banner.dataset.orderId || null;
            const key = orderId ? ('es_order_shown_' + orderId) : 'es_order_shown_generic';
            const alreadyShown = sessionStorage.getItem(key);

            // show hero with animation
            requestAnimationFrame(() => banner.classList.add('visible'));

            // show toast briefly
            const toast = document.getElementById('es-order-toast');
            if (toast) {
                setTimeout(() => toast.classList.add('visible'), 450); setTimeout(() => toast.classList.remove('visible'), 7000); }

            // if not shown before, run confetti and mark shown
            if (!alreadyShown) {
                runConfetti();
                try {
                    sessionStorage.setItem(key, '1'); } catch (e) {
                    }
            }

            // Small accessibility: focus first heading
            const h = banner.querySelector('h3'); if (h && typeof h.focus === 'function') {
                h.setAttribute('tabindex', '-1'), h.focus();
            }

            function runConfetti()
            {
                const layer = document.getElementById('es-confetti-layer');
                if (!layer) {
                    return;
                }
                const colors = ['#fde047', '#fb7185', '#60a5fa', '#34d399', '#f97316', '#a78bfa'];
                const pieces = 36;
                for (let i = 0; i < pieces; i++) {
                    const el = document.createElement('div');
                    el.className = 'es-confetti-piece';
                    const w = 6 + Math.round(Math.random() * 14);
                    const h = 10 + Math.round(Math.random() * 18);
                    el.style.width = w + 'px'; el.style.height = h + 'px';
                    el.style.background = colors[Math.floor(Math.random() * colors.length)];
                    // random spin and left position
                    el.style.left = (Math.random() * 100) + '%';
                    el.style.top = (-20 - Math.random() * 30) + 'px';
                    el.style.transform = 'rotate(' + (Math.random() * 360) + 'deg)';
                    el.style.opacity = String(0.9 + Math.random() * 0.1);
                    el.style.animation = 'es-confetti-fall ' + (1.6 + Math.random() * 1.4) + 's cubic-bezier(.2,.7,.3,1) forwards';
                    layer.appendChild(el);
                    // cleanup
                    setTimeout(() => { try {
                            layer.removeChild(el); } catch (e) {
                            } }, 3500 + Math.random() * 800);
                }
            }

        } catch (e) {
            console.error('order-success init failed', e); }
    });
})();

