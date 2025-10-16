// Front toast system (clean build)
// Provides window.showToast(message, type='info', timeout=4000) and window.toastsReady
(function () {
    'use strict';
    var READY_EVENT = 'toasts:ready';
    var isLocal = (location.hostname === 'localhost' || location.hostname === '127.0.0.1');
    function log()
    {
        if (isLocal && window.console && console.debug) {
            try {
                console.debug.apply(console, ['[toasts]'].concat([].slice.call(arguments))); } catch (e) {
                } } }

    function ensureContainer()
    {
        var c = document.querySelector('.toast-stack');
        if (!c) {
            c = document.createElement('div');
            c.className = 'toast-stack';
            if (document.body) {
                document.body.appendChild(c); } else {
                document.addEventListener('DOMContentLoaded', function () {
                                document.body.appendChild(c); });
                }
        }
        return c;
    }

    function showToast(message, type, timeout)
    {
        if (!message) {
            return;
        }
        type = type || 'info';
        timeout = typeof timeout === 'number' ? timeout : 4000;
        var stack = ensureContainer();
        var el = document.createElement('div');
        el.className = 'toast toast-' + type;
        el.setAttribute('role', 'status');
        el.textContent = message;
        stack.appendChild(el);
        requestAnimationFrame(function () {
            el.classList.add('visible'); });
        setTimeout(function () {
            el.classList.add('hide');
            el.addEventListener('transitionend', function () {
                try {
                    el.remove(); } catch (e) {
                    } });
        }, timeout);
    }

    function hydrateSpans()
    {
        try {
            var nodes = document.querySelectorAll('[data-flash-msg]');
            [].forEach.call(nodes, function (n) {
                var msg = n.getAttribute('data-flash-msg');
                var t = n.getAttribute('data-flash-type') || 'info';
                if (msg) {
                    showToast(msg, t);
                }
                try {
                    n.remove(); } catch (e) {
                    }
            });
        } catch (e) {
            log('hydrate error', e); }
    }

    function consumeQueue()
    {
        try {
            window.__flash_queue = window.__flash_queue || [];
            if (window.__flash_queue.length) {
                log('consume queue', window.__flash_queue.length);
                while (window.__flash_queue.length) {
                    var item = window.__flash_queue.shift();
                    if (item && item.message) {
                        showToast(item.message, item.type || 'info');
                    }
                }
            }
        } catch (e) {
            log('queue error', e); }
    }

    function init()
    {
        try {
            ensureContainer();
            hydrateSpans();
            consumeQueue();
            window.toastsReady = true;
            try {
                window.dispatchEvent(new CustomEvent(READY_EVENT)); } catch (e) {
                }
                log('ready');
        } catch (e) {
            log('init error', e); }
    }

    // public API
    window.showToast = window.showToast || showToast; // don't override inline fallback if already present
    window.toastsReady = false;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init); } else {
        setTimeout(init, 0);
        }
})();

