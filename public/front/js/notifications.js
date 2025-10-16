/* Front Notification / Flash Toast System (mirrors admin style)
 * Provides window.notify.{success,error,warning,info} and consumes window.__frontFlash
 * Also listens for CustomEvent('app:notify', {detail:{type,message,timeout}})
 */
(function () {
    function createNotice(message, type = 'info', timeout = 5000)
    {
        if (!message) {
            return;
        }
        var rootId = 'flash-messages-root';
        var root = document.getElementById(rootId);
        if (!root) {
            root = document.createElement('div');
            root.id = rootId;
            root.className = 'position-fixed';
            root.style.cssText = 'top:20px;right:20px;z-index:1100;pointer-events:none;display:flex;flex-direction:column;gap:10px;';
            document.body.appendChild(root);
        }
        var colors = { success: 'success', error: 'danger', warning: 'warning', info: 'info' };
        var icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
        var el = document.createElement('div');
        el.className = 'alert alert-' + (colors[type] || 'info') + ' shadow-sm d-flex align-items-start gap-2 fade show flash-toast';
        el.style.minWidth = '300px';
        el.style.pointerEvents = 'auto';
        el.innerHTML = '\n            <div><i class="fas ' + (icons[type] || icons.info) + ' fa-lg mt-1"></i></div>\n            <div class="flex-fill">' + message + '</div>\n            <button type="button" class="btn-close mt-1" aria-label="Close"></button>\n        ';
        root.appendChild(el);
        function remove()
        {
            if (el) {
                el.classList.remove('show'); setTimeout(function () {
                    try {
                        el.remove(); } catch (e) {
                        } }, 250); } }
        el.querySelector('.btn-close').addEventListener('click', remove);
        if (timeout) {
            setTimeout(remove, timeout); }
    }
    window.notify = {
        success: (m, t) => createNotice(m, 'success', t),
        error: (m, t) => createNotice(m, 'error', t),
        warning: (m, t) => createNotice(m, 'warning', t),
        info: (m, t) => createNotice(m, 'info', t)
    };
    if (window.__frontFlash) {
        ['success', 'error', 'warning', 'info'].forEach(function (k) {
            if (window.__frontFlash[k]) {
                createNotice(window.__frontFlash[k], k);
            } });
    }
    // Backwards compatibility: consume any legacy __flash_queue entries {message,type}
    if (Array.isArray(window.__flash_queue) && window.__flash_queue.length) {
        try {
            while (window.__flash_queue.length) {
                var item = window.__flash_queue.shift();
                if (!item) {
                    continue;
                }
                if (typeof item === 'string') {
                    createNotice(item, 'info');
                } else {
                    createNotice(item.message || item.msg, item.type || 'info');
                }
            }
        } catch (e) {
/* ignore */ }
    }
    // Provide showToast alias expected by older scripts
    window.showToast = function (msg, type, timeout) {
        createNotice(msg, type, timeout); };
    document.addEventListener('app:notify', function (e) {
        var d = e.detail || {}; createNotice(d.message, d.type, d.timeout);
    });
})();

