// Extracted notification poll interval assignment
(function () {
    if (typeof window !== 'undefined') {
      // Value may be injected server-side via data attribute if needed
        var el = document.querySelector('[data-notifications-poll]');
        if (!window.NOTIFICATIONS_POLL_INTERVAL_MS) {
            var attr = el ? el.getAttribute('data-notifications-poll') : null;
            if (attr) {
                window.NOTIFICATIONS_POLL_INTERVAL_MS = parseInt(attr,10) || 30000; }
        }
    }
})();
