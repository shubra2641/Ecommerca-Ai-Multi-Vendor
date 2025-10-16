/* Basic service worker for Laravel CMS */
const CACHE_VERSION = 'v3';
const CORE_CACHE = `core - ${CACHE_VERSION}`;
const RUNTIME_CACHE = `runtime - ${CACHE_VERSION}`;
const API_CACHE = `api - ${CACHE_VERSION}`;
const BG_SYNC_QUEUE = 'bg-sync-queue-v1';

const CORE_ASSETS = [
    '/',
    '/offline',
    '/manifest.webmanifest',
    '/front/css/front.css',
    '/front/js/front-lite.js',
    '/front/js/pwa.js'
];

/*
 * Safely put a request/response into a cache.
 * Some browsers/extensions produce requests with non-http(s) schemes
 * (for example chrome-extension://). cache.put will throw for those.
 */
function safeCachePut(cache, request, response)
{
    try {
        const url = new URL(request.url);
        if (url.protocol === 'http:' || url.protocol === 'https:') {
            return cache.put(request, response);
        }
    } catch (e) {
        // If request isn't a valid URL or unsupported, ignore caching.
    }
    return Promise.resolve();
}

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CORE_CACHE).then(cache => cache.addAll(CORE_ASSETS)).then(self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then(keys => Promise.all(keys.filter(k => ![CORE_CACHE, RUNTIME_CACHE, API_CACHE].includes(k)).map(k => caches.delete(k)))).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') {
        return;
    }
    const url = new URL(req.url);
    // HTML navigation: network-first with offline fallback
    if (req.mode === 'navigate' || (req.headers.get('accept') || '').includes('text/html')) {
        event.respondWith((async() => {
            try {
                const fresh = await fetch(req);
                const cache = await caches.open(RUNTIME_CACHE);
                await safeCachePut(cache, req, fresh.clone());
                return fresh;
            } catch (e) {
                const cached = await caches.match(req);
                return cached || caches.match('/offline') || new Response('Offline', { status: 503 });
            }
        })());
        return;
    }
    // Images: stale-while-revalidate
    if (req.destination === 'image') {
        event.respondWith((async() => {
            const cache = await caches.open(RUNTIME_CACHE);
            const cached = await cache.match(req);
            const fetchPromise = fetch(req).then(res => { safeCachePut(cache, req, res.clone()); return res; }).catch(() => cached);
            return cached || fetchPromise;
        })());
        return;
    }
    // CSS/JS/font: cache-first then network
    if (['style', 'script', 'font'].includes(req.destination) || CORE_ASSETS.includes(url.pathname)) {
        event.respondWith(caches.match(req).then(cached => cached || fetch(req).then(res => { const copy = res.clone(); caches.open(RUNTIME_CACHE).then(c => safeCachePut(c, req, copy)); return res; })));
        return;
    }
    // Generic JSON API GET requests: network-first then cache (by path w/out query)
    if (url.pathname.startsWith('/api/') && req.headers.get('accept') ? .includes('application/json')) {
        event.respondWith((async() => {
            const cache = await caches.open(API_CACHE);
            const cacheKey = new Request(url.origin + url.pathname);
            try {
                const fresh = await fetch(req);
                if (fresh.ok) {
                    await safeCachePut(cache, cacheKey, fresh.clone());
                }
                return fresh;
            } catch (e) {
                const cached = await cache.match(cacheKey);
                if (cached) {
                    return cached;
                }
                return new Response(JSON.stringify({ offline: true }), { status: 503, headers: { 'Content-Type': 'application/json' } });
            }
        })());
        return;
    }

    // API (media list) with query params: network-first fallback to cache by stripped URL (legacy special-case)
    if (url.pathname.endsWith('/pages/media/list')) {
        event.respondWith((async() => {
            const cacheKey = new Request(url.origin + url.pathname);
            try {
                const fresh = await fetch(req); const cache = await caches.open(RUNTIME_CACHE); await safeCachePut(cache, cacheKey, fresh.clone()); return fresh; } catch (e) {
                return caches.match(cacheKey); }
        })());
    }
});
// Background Sync: queue failed POST requests (simple strategy for JSON APIs)
// Background-sync queue: restrict to allowlist of lightweight endpoints
const BG_SYNC_ALLOWLIST = [
    '/api/wishlist',
    '/api/notify',
    '/api/stock-notify',
    '/api/push/subscribe' // subscription can be queued if offline
];
self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method === 'POST' && BG_SYNC_ALLOWLIST.some(path => req.url.includes(path))) {
        event.respondWith((async() => {
            try {
                return await fetch(req.clone());
            } catch (e) {
                const body = await req.clone().text();
                const db = await openQueue();
                const tx = db.transaction('queue', 'readwrite');
                const headersObj = {};
                req.headers.forEach((v,k) => { headersObj[k] = v; });
                tx.objectStore('queue').add({ id: Date.now() + Math.random(), url: req.url, body, headers: headersObj, ts: Date.now() });
                // rely on oncomplete later
                registerSync();
                return new Response(JSON.stringify({ queued: true, offline: true }), { status: 202, headers: { 'Content-Type': 'application/json' } });
            }
        })());
    }
});

async function openQueue()
{
    return await new Promise((resolve, reject) => {
        const req = indexedDB.open('pwa-bg-sync', 1);
        req.onupgradeneeded = () => {
            const db = req.result;
            if (!db.objectStoreNames.contains('queue')) {
                db.createObjectStore('queue', { keyPath: 'id' });
            }
        };
        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}

function registerSync()
{
    if ('sync' in self.registration) {
        self.registration.sync.register('bg-sync-submit').catch(() => {});
    }
}

self.addEventListener('sync', (event) => {
    if (event.tag === 'bg-sync-submit') {
        event.waitUntil(flushQueue());
    }
});

async function flushQueue()
{
    const db = await openQueue();
    await new Promise((resolve) => {
        const tx = db.transaction('queue','readwrite');
        const store = tx.objectStore('queue');
        const getAllReq = store.getAll();
        getAllReq.onsuccess = async() => {
            const all = getAllReq.result || [];
            for (const job of all) {
                try {
                    await fetch(job.url, { method: 'POST', headers: job.headers || {}, body: job.body }); await store.delete(job.id); } catch (e) {
                    /* keep */ }
            }
        };
        tx.oncomplete = () => resolve();
        tx.onerror = () => resolve();
    });
}


// Allow pages to trigger an immediate SW activation
self.addEventListener('message', (event) => {
    if (event.data === 'skipWaiting') {
        self.skipWaiting();
    }
});

// Placeholder push handler (extend to display notifications when backend sends them)
self.addEventListener('push', (event) => {
    if (!event.data) {
        return;
    }
    const data = (() => { try {
            return event.data.json(); } catch { return { title: 'Notification', body: event.data.text() }; } })();
    event.waitUntil(self.registration.showNotification(data.title || 'Update', {
        body: data.body || '',
        icon: '/icons/icon-192.png',
        data: data.url ? { url : data.url } : undefined,
    }));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = event.notification.data ? .url || '/';
    event.waitUntil(clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientsArr => {
        const existing = clientsArr.find(w => w.url.includes(location.origin));
        if (existing) {
            existing.focus(); existing.postMessage({ navigate: targetUrl }); return; }
        clients.openWindow(targetUrl);
    }));
});
