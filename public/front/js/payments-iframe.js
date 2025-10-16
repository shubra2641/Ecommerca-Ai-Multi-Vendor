// payments-iframe.js
(function () {
    'use strict';
    function initProviderIframe(redirectUrl, fallback) {
        try {
            var iframe = document.getElementById('provider-iframe');
            if (!iframe) return;
            var loaded = false;
            iframe.addEventListener('load', function () { loaded = true; });
            setTimeout(function () {
                if (!loaded) {
                    try { window.open(fallback, '_blank'); } catch (e) { window.location.href = fallback; }
                }
            }, 5000);
        } catch (e) {
            console.error('payments-iframe init error', e);
        }
    }

    // Auto-init if DOM has the iframe
    document.addEventListener('DOMContentLoaded', function () {
        var iframe = document.getElementById('provider-iframe');
        if (!iframe) return;
        // Fallback URL is provided in data attribute on body or window
        var fallback = window.__PAYMENT_FALLBACK_URL__ || (iframe.getAttribute('data-fallback') || null);
        initProviderIframe(iframe.src || null, fallback);
    });
})();

