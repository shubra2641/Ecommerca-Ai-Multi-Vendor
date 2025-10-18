// Extracted countdown script (progressive, minimal)
(function () {
    const el = document.getElementById('countdown');
    if (!el) {
        return;
    } const targetAttr = el.getAttribute('data-target'); if (!targetAttr) {
        return;
    }
    const target = new Date(targetAttr).getTime();
    function tick()
    {
        const now = Date.now(); let diff = target - now; if (diff <= 0) {
            el.textContent = el.dataset.labelSoon || 'Soon'; return;}
        const d = Math.floor(diff / 86400000); diff %= 86400000;
        const h = Math.floor(diff / 3600000); diff %= 3600000;
        const m = Math.floor(diff / 60000); diff %= 60000;
        const s = Math.floor(diff / 1000);
        el.textContent = `${d}d ${h}h ${m}m ${s}s`;
        setTimeout(tick,1000);
    }
    tick();
})();

