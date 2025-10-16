// Simple toast system
(function () {
    'use strict';

    // create a container element we will reuse for toasts
    function createContainer()
    {
        var c = document.querySelector('.toast-stack');
        if (c) {
            return c;
        }
        c = document.createElement('div');
        c.className = 'toast-stack';
        // try to append now if body exists; otherwise append on DOMContentLoaded
        if (document.body) {
            document.body.appendChild(c);
        } else {
            document.addEventListener('DOMContentLoaded', function () {
                        document.body.appendChild(c); });
        }
        return c;
    }

    // ensure container exists and return it
    function ensureContainer()
    {
        var c = document.querySelector('.toast-stack');
        if (!c) {
            return createContainer();
        }
        return c;
    }

    // show a toast message
    function showToast(message, type, timeout)
    {
        if (!message) {
            return;
        }
        type = type || 'info';
        timeout = typeof timeout === 'number' ? timeout : 4000;
        var container = ensureContainer();
        var el = document.createElement('div');
        el.className = 'toast toast-' + type;
        el.setAttribute('role', 'status');
        el.textContent = message;
        container.appendChild(el);
        // entrance
        requestAnimationFrame(function () {
            el.classList.add('visible'); });
        // auto remove
        setTimeout(function () {
            el.classList.add('hide');
            el.addEventListener('transitionend', function () {
                try {
                    el.remove(); } catch (e) {
                    } });
        }, timeout);
    }

    // hydrate any server-rendered flash nodes
    function hydrateFlashes()
    {
        try {
            var nodes = document.querySelectorAll('[data-flash-msg]');
            Array.prototype.forEach.call(nodes, function (n) {
                var msg = n.getAttribute('data-flash-msg');
                var type = n.getAttribute('data-flash-type') || 'info';
                if (msg) {
                    showToast(msg, type);
                }
                try {
                    n.parentNode && n.parentNode.removeChild(n); } catch (e) {
                    }
            });
        } catch (e) {
/* ignore */ }
    }

    // expose and initialize
    window.toastsReady = false;
    window.showToast = showToast;

    // small debug helper (only when running on localhost)
    var isLocalhost = (location.hostname === 'localhost' || location.hostname === '127.0.0.1');
    function debugLog()
    {
        if (isLocalhost && window.console && console.debug) {
            try {
                console.debug.apply(console, ['[toasts]'].concat(Array.prototype.slice.call(arguments))); } catch (e) {
                } } }
    debugLog('init');

    function initialize()
    {
        try {
            ensureContainer();
            hydrateFlashes();

            // consume inline queue placed by server-side blade
            try {
                window.__flash_queue = window.__flash_queue || [];
                if (Array.isArray(window.__flash_queue) && window.__flash_queue.length) {
                    debugLog('consuming inline flash queue', window.__flash_queue.length);
                    while (window.__flash_queue.length) {
                        var item = window.__flash_queue.shift();
                        if (item && item.message) {
                            showToast(item.message, item.type || 'info');
                        }
                    }
                }
            } catch (e) {
                debugLog('queue consume failed', e); }

            window.toastsReady = true;
            try {
                window.dispatchEvent(new CustomEvent('toasts:ready')); } catch (e) {
                /* ignore */ }
        } catch (e) {
            debugLog('initialize error', e);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        setTimeout(initialize, 0);
    }

})();

