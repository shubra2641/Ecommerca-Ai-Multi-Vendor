/**
 * Simple Service Worker for E-Commerce PWA
 * Optimized for performance, security, and simplicity
 */

const STATIC_CACHE = 'static-v2.0.0';
const DYNAMIC_CACHE = 'dynamic-v2.0.0';

// Essential files to cache
const ESSENTIAL_FILES = [
    '/',
    '/offline.html',
    '/manifest.json'
];

// Install event - cache essential files
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => cache.addAll(ESSENTIAL_FILES))
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then(cacheNames =>
                Promise.all(
                    cacheNames
                        .filter(name => name !== STATIC_CACHE && name !== DYNAMIC_CACHE)
                        .map(name => caches.delete(name))
                )
            )
            .then(() => self.clients.claim())
    );
});

// Fetch event - handle all requests
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Skip non-GET requests and non-HTTP requests
    if (request.method !== 'GET' || !request.url.startsWith('http')) {
        return;
    }

    // Determine cache strategy based on request type
    if (isStaticFile(request)) {
        event.respondWith(cacheFirst(request, STATIC_CACHE));
    } else if (isImageFile(request)) {
        event.respondWith(cacheFirst(request, DYNAMIC_CACHE));
    } else if (isAPIRequest(request)) {
        event.respondWith(networkFirst(request, DYNAMIC_CACHE));
    } else {
        event.respondWith(networkFirst(request, DYNAMIC_CACHE));
    }
});

// Cache First Strategy - for static files
async function cacheFirst(request, cacheName) {
    try {
        const cache = await caches.open(cacheName);
        const cachedResponse = await cache.match(request);

        if (cachedResponse) {
            return cachedResponse;
        }

        const networkResponse = await fetch(request);

        if (networkResponse.ok) {
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch (error) {
        return handleOffline(request);
    }
}

// Network First Strategy - for dynamic content
async function networkFirst(request, cacheName) {
    try {
        const networkResponse = await fetch(request);

        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch (error) {
        const cache = await caches.open(cacheName);
        const cachedResponse = await cache.match(request);

        if (cachedResponse) {
            return cachedResponse;
        }

        return handleOffline(request);
    }
}

// Handle offline requests
async function handleOffline(request) {
    if (request.mode === 'navigate') {
        const cache = await caches.open(STATIC_CACHE);
        return cache.match('/offline.html') || new Response('Offline', { status: 503 });
    }

    return new Response('Offline', { status: 503 });
}

// Helper functions
function isStaticFile(request) {
    const url = new URL(request.url);
    return url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.woff') ||
        url.pathname.endsWith('.woff2');
}

function isImageFile(request) {
    const url = new URL(request.url);
    return url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.jpeg') ||
        url.pathname.endsWith('.svg') ||
        url.pathname.endsWith('.gif') ||
        url.pathname.endsWith('.webp');
}

function isAPIRequest(request) {
    const url = new URL(request.url);
    return url.pathname.includes('/api/') ||
        url.pathname.includes('/search') ||
        url.pathname.includes('/products') ||
        url.pathname.includes('/categories');
}

// Background sync for offline actions
self.addEventListener('sync', (event) => {
    if (event.tag === 'cart-sync') {
        event.waitUntil(syncOfflineData('cart'));
    } else if (event.tag === 'wishlist-sync') {
        event.waitUntil(syncOfflineData('wishlist'));
    }
});

// Sync offline data when back online
async function syncOfflineData(type) {
    try {
        const data = await getStoredData(`pending-${type}-actions`);

        if (data && data.length > 0) {
            for (const action of data) {
                await fetch(action.url, {
                    method: action.method,
                    headers: action.headers,
                    body: action.body
                });
            }

            await clearStoredData(`pending-${type}-actions`);
            notifyClients(`${type.toUpperCase()}_SYNCED`, `${type} data synchronized`);
        }
    } catch (error) {
        // Silent error handling
    }
}

// IndexedDB helpers
async function getStoredData(key) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('ECommerceOffline', 1);

        request.onerror = () => reject(request.error);
        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction(['offline-data'], 'readonly');
            const store = transaction.objectStore('offline-data');
            const getRequest = store.get(key);

            getRequest.onsuccess = () => resolve(getRequest.result?.data || null);
            getRequest.onerror = () => reject(getRequest.error);
        };

        request.onupgradeneeded = () => {
            const db = request.result;
            if (!db.objectStoreNames.contains('offline-data')) {
                db.createObjectStore('offline-data', { keyPath: 'key' });
            }
        };
    });
}

async function clearStoredData(key) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('ECommerceOffline', 1);

        request.onerror = () => reject(request.error);
        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction(['offline-data'], 'readwrite');
            const store = transaction.objectStore('offline-data');
            const deleteRequest = store.delete(key);

            deleteRequest.onsuccess = () => resolve();
            deleteRequest.onerror = () => reject(deleteRequest.error);
        };
    });
}

// Notify all clients
async function notifyClients(type, message) {
    const clients = await self.clients.matchAll();
    clients.forEach(client => {
        client.postMessage({ type, message });
    });
}

// Push notifications
self.addEventListener('push', (event) => {
    const options = {
        title: 'E-Commerce Store',
        body: 'You have a new notification',
        icon: '/manifest.json',
        badge: '/manifest.json',
        tag: 'general',
        requireInteraction: false
    };

    if (event.data) {
        try {
            const data = event.data.json();
            Object.assign(options, data);
        } catch (error) {
            // Silent error handling
        }
    }

    event.waitUntil(
        self.registration.showNotification(options.title, options)
    );
});

// Notification click handling
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    if (event.action === 'view') {
        event.waitUntil(
            self.clients.openWindow(event.notification.data?.url || '/')
        );
    } else {
        event.waitUntil(
            self.clients.matchAll({ type: 'window' })
                .then(clientList => {
                    for (const client of clientList) {
                        if (client.url === '/' && 'focus' in client) {
                            return client.focus();
                        }
                    }
                    return self.clients.openWindow('/');
                })
        );
    }
});

// Message handling
self.addEventListener('message', (event) => {
    if (event.data?.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// Error handling
self.addEventListener('error', (event) => {
    // Silent error handling
});

self.addEventListener('unhandledrejection', (event) => {
    event.preventDefault();
});