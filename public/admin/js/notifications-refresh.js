'use strict';
// Triggers admin notifications refresh if body has data-refresh-admin-notifications attribute.
(function () {
    function attempt()
    {
        if (window.refreshAdminNotifications && typeof window.refreshAdminNotifications === 'function') {
            try {
                window.refreshAdminNotifications(); } catch (e) {
                          /* ignore */ }
        } else {
            setTimeout(attempt,250);
        }
    }
    function init()
    {
        if (document.body.hasAttribute('data-refresh-admin-notifications')) {
            attempt();
        } }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init); } else {
        init();
        }
})();
