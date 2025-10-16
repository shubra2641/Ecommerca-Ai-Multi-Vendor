/* Global Notification / Flash Toast System
 * Extracted from layout inline script.
 * Provides window.notify.{success,error,warning,info} and consumes window.__flash.
 * Also listens for CustomEvent('app:notify', {detail:{type,message,timeout}})
 */
(function () {
    function createNotice(message, type = 'info', timeout = 5000) {
        if (!message) {
            return;
        }
        const rootId = 'flash-messages-root';
        let root = document.getElementById(rootId);
        if (!root) {
            root = document.createElement('div');
            root.id = rootId;
            root.className = 'position-fixed';
            root.style.cssText = 'top:20px;right:20px;z-index:1100;pointer-events:none;display:flex;flex-direction:column;gap:10px;';
            document.body.appendChild(root);
        }
        const id = 'flash-' + Date.now() + Math.random().toString(36).slice(2);
        const colors = { success: 'success', error: 'danger', warning: 'warning', info: 'info' };
        const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
        const el = document.createElement('div');
        el.className = `alert alert-${colors[type] || 'info'} shadow-sm d-flex align-items-start gap-2 fade show flash-toast`;
        el.id = id;
        el.style.minWidth = '300px';
        el.style.pointerEvents = 'auto';

        // Build children safely to avoid malformed HTML and ensure btn exists
        const iconWrap = document.createElement('div');
        const icon = document.createElement('i');
        icon.className = `fas ${icons[type] || icons.info} fa-lg mt-1`;
        iconWrap.appendChild(icon);

        const msgWrap = document.createElement('div');
        msgWrap.className = 'flex-fill';
        // Insert as text to avoid rendering raw HTML strings
        msgWrap.textContent = message;

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn-close mt-1';
        btn.setAttribute('aria-label', 'Close');

        el.appendChild(iconWrap);
        el.appendChild(msgWrap);
        el.appendChild(btn);

        root.appendChild(el);

        const remove = () => {
            if (el) {
                el.classList.remove('show');
                setTimeout(() => el.remove(), 250);
            }
        };

        // Guard in case something unexpected happens
        if (btn && typeof btn.addEventListener === 'function') {
            btn.addEventListener('click', remove);
        }

        if (timeout) {
            setTimeout(remove, timeout);
        }
    }
    window.notify = {
        success: (m, t) => createNotice(m, 'success', t),
        error: (m, t) => createNotice(m, 'error', t),
        warning: (m, t) => createNotice(m, 'warning', t),
        info: (m, t) => createNotice(m, 'info', t)
    };
    // Consume server flashes
    if (window.__flash) {
        ['success', 'error', 'warning', 'info'].forEach(k => {
            if (window.__flash[k]) {
                createNotice(window.__flash[k], k);
            }
        });
    }
    // Global event dispatcher usage: document.dispatchEvent(new CustomEvent('app:notify',{detail:{type:'success',message:'Saved'}}));
    document.addEventListener('app:notify', e => {
        const { type, message, timeout } = e.detail || {};
        createNotice(message, type, timeout);
    });
})();
