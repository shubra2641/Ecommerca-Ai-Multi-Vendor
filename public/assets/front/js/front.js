// Header dropdown & currency interactions
(function () {
    const doc = document;
    function closeAll(except) {
        doc.querySelectorAll('[data-dropdown].open').forEach(d => {
            if (d !== except) {
                d.classList.remove('open');
            }
        });
    }
    function initDropdowns() {
        doc.querySelectorAll('[data-dropdown]').forEach(wrapper => {
            const trigger = wrapper.querySelector('.dropdown-trigger');
            const panel = wrapper.querySelector('.dropdown-panel');
            if (!trigger || !panel) {
                return;
            }
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const open = wrapper.classList.toggle('open');
                trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (open) {
                    closeAll(wrapper); panel.querySelector('button, a, input, [tabindex]')?.focus?.();
                }
            });
            // keyboard navigation / escape
            wrapper.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    wrapper.classList.remove('open'); trigger.setAttribute('aria-expanded', 'false'); trigger.focus();
                }
            });
        });
        doc.addEventListener('click', (e) => {
            if (!e.target.closest('[data-dropdown]')) {
                closeAll();
            }
        });
    }
    function initCurrencySwitch() {
        doc.addEventListener('click', async (e) => {
            const btn = e.target.closest('.currency-chip');
            if (!btn) {
                return;
            }
            const code = btn.dataset.currency; if (!code) {
                return;
            }
            try {
                const res = await fetch('/currency/switch', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ code }) });
                if (res.ok) {
                    location.reload();
                }
            } catch (err) {
                console.warn('Currency switch failed', err);
            }
        });
    }
    // Simple compare badge updater placeholder (other scripts can dispatch window.dispatchEvent(new CustomEvent('compare:update',{detail:{count:n}})))
    function initCompareBadge() {
        const badge = doc.querySelector('[data-compare-count]');
        if (!badge) {
            return;
        }
        window.addEventListener('compare:update', (e) => {
            const c = e.detail && typeof e.detail.count === 'number' ? e.detail.count : 0;
            badge.textContent = c;
            if (c > 0) {
                badge.classList.add('show');
            } else {
                badge.classList.remove('show');
            }
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => { initDropdowns(); initCurrencySwitch(); initCompareBadge(); });
    } else {
        initDropdowns(); initCurrencySwitch(); initCompareBadge();
    }
})();


