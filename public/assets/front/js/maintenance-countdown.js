// Extracted countdown script (progressive, minimal)
(function () {
    const el = document.getElementById('countdown');
    if (!el) {
        return;
    } const targetAttr = el.getAttribute('data-target'); if (!targetAttr) {
        return;
    }
    const target = new Date(targetAttr).getTime();
    function tick() {
        const now = Date.now();
        let diff = target - now;
        if (diff <= 0) {
            el.textContent = el.dataset.labelSoon || 'Soon';
            return;
        }

        const MS_PER_DAY = 86400000;
        const MS_PER_HOUR = 3600000;
        const MS_PER_MINUTE = 60000;
        const MS_PER_SECOND = 1000;
        const UPDATE_INTERVAL = 1000;

        const d = Math.floor(diff / MS_PER_DAY);
        diff %= MS_PER_DAY;
        const h = Math.floor(diff / MS_PER_HOUR);
        diff %= MS_PER_HOUR;
        const m = Math.floor(diff / MS_PER_MINUTE);
        diff %= MS_PER_MINUTE;
        const s = Math.floor(diff / MS_PER_SECOND);
        el.textContent = `${d}d ${h}h ${m}m ${s}s`;
        setTimeout(tick, UPDATE_INTERVAL);
    }
    tick();
}());

