'use strict';
// Fallback loader for Bootstrap & Chart.js (replaces inline IIFE in admin layout)
(function () {
    function injectCSS(href)
    {
        var l = document.createElement('link'); l.rel = 'stylesheet'; l.href = href; document.head.appendChild(l); }
    function injectJS(src,id)
    {
        if (id && document.getElementById(id)) {
            return;
        } var s = document.createElement('script'); if (id) {
            s.id = id;
        } s.src = src; document.head.appendChild(s); }
    function ensure()
    {
 /* All vendor assets self-hosted now; fallback disabled for strict CSP */ }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ensure); } else {
        ensure();
        }
})();
