/**
 * Simple Service Worker for PWA
 * Ultra lightweight and reliable
 */

const CACHE_NAME = 'ecommerce-store-nocache-v2';
const OFFLINE_URL = '/offline.html';

// Only cache the offline fallback page; everything else is network-first (no SW cache)
const ESSENTIAL_FILES = [OFFLINE_URL];

// Install Service Worker
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(ESSENTIAL_FILES);
            })
            .then(() => {
                return self.skipWaiting();
            })
            .catch(() => {
                // Ignore cache errors
            })
    );
});

// Activate Service Worker
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames
                        .filter(cacheName => cacheName !== CACHE_NAME)
                        .map(cacheName => {
                            return caches.delete(cacheName);
                        })
                );
            })
            .then(() => {
                return self.clients.claim();
            })
    );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
    // Only handle GET requests from same-origin
    if (event.request.method !== 'GET') return;
    if (!event.request.url.startsWith(self.location.origin)) return;

    event.respondWith((async () => {
        try {
            // Always prefer fresh network response and bypass HTTP cache
            return await fetch(event.request, { cache: 'no-store' });
        } catch (err) {
            // If offline and it's a navigation request, show offline page
            if (event.request.mode === 'navigate') {
                const cachedOffline = await caches.match(OFFLINE_URL);
                if (cachedOffline) return cachedOffline;
            }
            return new Response('Offline', { status: 503 });
        }
    })());
});

// Handle messages from main thread
self.addEventListener('message', (event) => {
    const { type } = event.data || {};

    switch (type) {
        case 'SKIP_WAITING':
            self.skipWaiting();
            break;
        case 'PURGE_CACHES':
            // Delete ALL caches and notify clients when done
            event.waitUntil(
                caches.keys()
                    .then(keys => Promise.all(keys.map(k => caches.delete(k))))
                    .then(async () => {
                        const clients = await self.clients.matchAll();
                        clients.forEach(c => c.postMessage({ type: 'PURGE_DONE' }));
                    })
            );
            break;
        case 'UNREGISTER':
            // Unregister this service worker
            event.waitUntil(
                self.registration.unregister().then(async (unregistered) => {
                    const clients = await self.clients.matchAll();
                    clients.forEach(c => c.postMessage({ type: 'UNREGISTERED', ok: unregistered }));
                })
            );
            break;
    }
});