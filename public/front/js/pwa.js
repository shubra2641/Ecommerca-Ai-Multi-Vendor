// PWA registration + diagnostics
(function () {
    const LOG_PREFIX = '[PWA]';
    function headCheck(url)
    {
        if (!url) {
            return;
        }
        fetch(url, { method: 'HEAD', cache: 'no-store' })
            .then(r => { if (!r.ok) {
                    console.warn(LOG_PREFIX, 'Missing or 404:', url);
            } })
            .catch(() => console.warn(LOG_PREFIX, 'Fetch fail:', url));
    }

    // Skip registration on common local dev hosts to avoid SSL/certificate errors
    const host = location.hostname;
    if (host === 'localhost' || host === '127.0.0.1' || host.endsWith('.local')) {
        console.info(LOG_PREFIX, 'Skipping SW registration on local host:', host);
        return;
    }

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            // Only try to register on secure contexts (https) and when not local
            if (location.protocol !== 'https:') {
                console.info(LOG_PREFIX, 'Not a secure context, skipping SW registration');
                return;
            }

            navigator.serviceWorker.register('/service-worker.js')
                .then(async reg => {
                    console.log(LOG_PREFIX, 'SW registered', reg.scope);
                    // Attempt push subscription if permission granted already
                    if ('Notification' in window && Notification.permission === 'granted' && reg.pushManager) {
                        try {
                            await ensurePushSubscription(reg); } catch (e) {
                            console.warn(LOG_PREFIX,'Push sub fail',e);} }
                })
                .catch(e => console.warn(LOG_PREFIX, 'SW registration failed', e));

            const base = document.querySelector('meta[name=app-base]')?.content || '';
            const manifestLink = document.querySelector('link[rel=manifest]');
            const manifestHref = manifestLink ? manifestLink.getAttribute('href') : (base.replace(/\/$/, '') + '/manifest.webmanifest');
            headCheck(manifestHref);
        });
    } else {
        console.warn(LOG_PREFIX, 'Service workers not supported');
    }
    async function ensurePushSubscription(reg)
    {
        const existing = await reg.pushManager.getSubscription();
        if (existing) {
            sendSubscription(existing); return existing; }
        if (Notification.permission !== 'granted') {
            return null;
        }
        // NOTE: Replace with real VAPID public key if configured
        const vapid = document.querySelector('meta[name="vapid-public-key"]')?.content;
        const sub = await reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: vapid ? urlB64ToUint8Array(vapid) : undefined });
        sendSubscription(sub);
        return sub;
    }
    function sendSubscription(sub)
    {
        fetch('/api/push/subscribe', { method: 'POST', headers: { 'Content-Type': 'application/json','X-CSRF-TOKEN': getCsrf() }, body: JSON.stringify(sub) }).catch(() => {});
    }
    function urlB64ToUint8Array(base64String)
    {
        if (!base64String) {
            return undefined;
        }
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const raw = atob(base64);
        const arr = new Uint8Array(raw.length);
        for (let i = 0; i < raw.length; i++) {
            arr[i] = raw.charCodeAt(i);
        }
        return arr;
    }
    function getCsrf()
    {
        return document.querySelector('meta[name="csrf-token"]')?.content || ''; }
})();


