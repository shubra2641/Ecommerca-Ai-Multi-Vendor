// Simple flash sale countdown
(function () {
    const el = document.querySelector('[data-flash-countdown]');
    if (!el) {
        return;
    }
    const endAttr = el.getAttribute('data-end');
    if (!endAttr) {
        return;
    }
    const end = new Date(endAttr).getTime();
    if (!end) {
        return;
    }
    const dEl = el.querySelector('[data-d]');
    const hEl = el.querySelector('[data-h]');
    const mEl = el.querySelector('[data-m]');
    const sEl = el.querySelector('[data-s]');
    function pad(n)
    {
        return String(n).padStart(2,'0'); }
    function tick()
    {
        const now = Date.now();
        let diff = Math.floor((end - now) / 1000);
        if (diff <= 0) {
            dEl.textContent = hEl.textContent = mEl.textContent = sEl.textContent = '00';
            el.classList.add('expired');
            el.setAttribute('aria-label', 'Flash sale ended');
            clearInterval(timer); return;
        }
        const d = Math.floor(diff / 86400); diff %= 86400;
        const h = Math.floor(diff / 3600); diff %= 3600;
        const m = Math.floor(diff / 60); const s = diff % 60;
        dEl.textContent = pad(d); hEl.textContent = pad(h); mEl.textContent = pad(m); sEl.textContent = pad(s);
    }
    tick();
    const timer = setInterval(tick,1000);
})();

