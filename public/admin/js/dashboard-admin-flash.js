'use strict';
// Flash messages initialization extracted from inline assignment
(function () {
    function parse()
    {
        const t = document.getElementById('dashboard-admin-flash'); if (!t) {
            return }; try {
                return JSON.parse(t.innerHTML.trim() || '{}');} catch (e) {
                    return {};}};
    function init()
    {
        const f = parse(); if (!Object.keys(f).length) {
            return;
        } Object.keys(f).forEach(k => { const v = f[k]; if (!v) {
                return;
        } if (window.notify) {
            if (k === 'success') {
                window.notify.success(v); } else if (k === 'error') {
                  window.notify.error(v); } else if (k === 'warning') {
                    window.notify.warning(v); } else if (window.notify.info) {
                      window.notify.info(v);
                    } } }); }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init); } else {
        init();
        }
})();
