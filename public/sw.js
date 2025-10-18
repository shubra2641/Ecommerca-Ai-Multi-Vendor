/**
 * Simple Service Worker for E-Commerce PWA
 * Optimized for performance, security, and simplicity
 */

const CACHE_NAME = 'ecommerce-v2.0.0';
const STATIC_CACHE = 'static-v2.0.0';
const DYNAMIC_CACHE = 'dynamic-v2.0.0';

// Essential files to cache
const ESSENTIAL_FILES = [
    '/',
    '/offline.html',
    '/manifest.json'
];

// Cache strategies
const CACHE_STRATEGIES = {
    static: ['css', 'js', 'woff', 'woff2'],
    images: ['png', 'jpg', 'jpeg', 'svg', 'gif', 'webp'],
    api: ['/api/', '/search', '/products', '/categories']
};

// Install event - cache essential files
self.addEventListener('install', (event) => {
    console.log('Service Worker installing...');

    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => cache.addAll(ESSENTIAL_FILES))
            .then(() => self.skipWaiting())
            .catch(error => console.error('Install failed:', error))
    );
});

// Activate event - clean old caches
self.addEventListener('activate', (event) => {
    console.log('Service Worker activating...');

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
    const url = new URL(request.url);

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
        console.error('Cache first failed:', error);
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
        console.log('Network failed, trying cache...');

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
    return CACHE_STRATEGIES.static.some(ext => url.pathname.endsWith('.' + ext));
}

function isImageFile(request) {
    const url = new URL(request.url);
    return CACHE_STRATEGIES.images.some(ext => url.pathname.endsWith('.' + ext));
}

function isAPIRequest(request) {
    const url = new URL(request.url);
    return CACHE_STRATEGIES.api.some(pattern => url.pathname.includes(pattern));
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
        console.error(`${type} sync failed:`, error);
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
            console.error('Failed to parse push data:', error);
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
            clients.openWindow(event.notification.data?.url || '/')
        );
    } else {
        event.waitUntil(
            clients.matchAll({ type: 'window' })
                .then(clientList => {
                    for (const client of clientList) {
                        if (client.url === '/' && 'focus' in client) {
                            return client.focus();
                        }
                    }
                    return clients.openWindow('/');
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
    console.error('Service Worker error:', event.error);
});

self.addEventListener('unhandledrejection', (event) => {
    console.error('Service Worker unhandled rejection:', event.reason);
    event.preventDefault();
});

console.log('Service Worker loaded successfully');