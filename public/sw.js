/**
 * Service Worker for E-Commerce PWA
 * Provides offline functionality, caching, and performance optimization
 */

const CACHE_NAME = 'ecommerce-v1.0.0';
const STATIC_CACHE = 'static-v1.0.0';
const DYNAMIC_CACHE = 'dynamic-v1.0.0';
const IMAGE_CACHE = 'images-v1.0.0';

// Files to cache immediately
const STATIC_FILES = [
    '/',
    '/front/css/front.css',
    '/front/js/front-lite.js',
    '/manifest.json',
    '/offline.html'
];

// API endpoints to cache
const API_CACHE_PATTERNS = [
    /\/api\//,
    /\/search/,
    /\/products/,
    /\/categories/
];

// Image patterns to cache
const IMAGE_PATTERNS = [
    /\.(?:png|jpg|jpeg|svg|gif|webp)$/i
];

// Install event - cache static files
self.addEventListener('install', (event) => {
    console.log('Service Worker installing...');

    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => {
                console.log('Caching static files');
                return cache.addAll(STATIC_FILES);
            })
            .then(() => {
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('Failed to cache static files:', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('Service Worker activating...');

    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== STATIC_CACHE &&
                            cacheName !== DYNAMIC_CACHE &&
                            cacheName !== IMAGE_CACHE) {
                            console.log('Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                return self.clients.claim();
            })
    );
});

// Fetch event - handle requests with caching strategies
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Skip chrome-extension and other non-http requests
    if (!request.url.startsWith('http')) {
        return;
    }

    // Handle different types of requests
    if (isStaticFile(request)) {
        event.respondWith(cacheFirst(request, STATIC_CACHE));
    } else if (isImageRequest(request)) {
        event.respondWith(cacheFirst(request, IMAGE_CACHE));
    } else if (isAPIRequest(request)) {
        event.respondWith(networkFirst(request, DYNAMIC_CACHE));
    } else {
        event.respondWith(staleWhileRevalidate(request, DYNAMIC_CACHE));
    }
});

// Cache strategies

// Cache First - for static files
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
        console.error('Cache first strategy failed:', error);

        // Return offline page for navigation requests
        if (request.mode === 'navigate') {
            const cache = await caches.open(STATIC_CACHE);
            return cache.match('/offline.html');
        }

        throw error;
    }
}

// Network First - for API requests
async function networkFirst(request, cacheName) {
    try {
        const networkResponse = await fetch(request);

        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch (error) {
        console.log('Network failed, trying cache:', error);

        const cache = await caches.open(cacheName);
        const cachedResponse = await cache.match(request);

        if (cachedResponse) {
            return cachedResponse;
        }

        throw error;
    }
}

// Stale While Revalidate - for dynamic content
async function staleWhileRevalidate(request, cacheName) {
    const cache = await caches.open(cacheName);
    const cachedResponse = await cache.match(request);

    const fetchPromise = fetch(request)
        .then((networkResponse) => {
            if (networkResponse.ok) {
                cache.put(request, networkResponse.clone());
            }
            return networkResponse;
        })
        .catch((error) => {
            console.error('Network request failed:', error);
            return cachedResponse;
        });

    return cachedResponse || fetchPromise;
}

// Helper functions

function isStaticFile(request) {
    const url = new URL(request.url);
    return STATIC_FILES.some(file => url.pathname === file) ||
        url.pathname.includes('/front/css/') ||
        url.pathname.includes('/front/js/') ||
        url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.js');
}

function isImageRequest(request) {
    return IMAGE_PATTERNS.some(pattern => pattern.test(request.url));
}

function isAPIRequest(request) {
    return API_CACHE_PATTERNS.some(pattern => pattern.test(request.url));
}

// Background sync for offline actions
self.addEventListener('sync', (event) => {
    console.log('Background sync triggered:', event.tag);

    if (event.tag === 'cart-sync') {
        event.waitUntil(syncCartData());
    } else if (event.tag === 'wishlist-sync') {
        event.waitUntil(syncWishlistData());
    }
});

// Sync cart data when back online
async function syncCartData() {
    try {
        const cartData = await getStoredData('pending-cart-actions');

        if (cartData && cartData.length > 0) {
            for (const action of cartData) {
                await fetch(action.url, {
                    method: action.method,
                    headers: action.headers,
                    body: action.body
                });
            }

            // Clear pending actions
            await clearStoredData('pending-cart-actions');

            // Notify all clients
            const clients = await self.clients.matchAll();
            clients.forEach(client => {
                client.postMessage({
                    type: 'CART_SYNCED',
                    message: 'Cart data synchronized'
                });
            });
        }
    } catch (error) {
        console.error('Cart sync failed:', error);
    }
}

// Sync wishlist data when back online
async function syncWishlistData() {
    try {
        const wishlistData = await getStoredData('pending-wishlist-actions');

        if (wishlistData && wishlistData.length > 0) {
            for (const action of wishlistData) {
                await fetch(action.url, {
                    method: action.method,
                    headers: action.headers,
                    body: action.body
                });
            }

            // Clear pending actions
            await clearStoredData('pending-wishlist-actions');

            // Notify all clients
            const clients = await self.clients.matchAll();
            clients.forEach(client => {
                client.postMessage({
                    type: 'WISHLIST_SYNCED',
                    message: 'Wishlist data synchronized'
                });
            });
        }
    } catch (error) {
        console.error('Wishlist sync failed:', error);
    }
}

// IndexedDB helpers for storing offline data
async function getStoredData(key) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('ECommerceOffline', 1);

        request.onerror = () => reject(request.error);

        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction(['offline-data'], 'readonly');
            const store = transaction.objectStore('offline-data');
            const getRequest = store.get(key);

            getRequest.onsuccess = () => {
                resolve(getRequest.result ? getRequest.result.data : null);
            };

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

// Push notification handling
self.addEventListener('push', (event) => {
    console.log('Push notification received:', event);

    let notificationData = {
        title: 'E-Commerce Store',
        body: 'You have a new notification',
        icon: '/front/images/icon-192x192.png',
        badge: '/front/images/badge-72x72.png',
        tag: 'general',
        requireInteraction: false,
        actions: [
            {
                action: 'view',
                title: 'View',
                icon: '/front/images/view-icon.png'
            },
            {
                action: 'dismiss',
                title: 'Dismiss',
                icon: '/front/images/dismiss-icon.png'
            }
        ]
    };

    if (event.data) {
        try {
            const data = event.data.json();
            notificationData = { ...notificationData, ...data };
        } catch (error) {
            console.error('Failed to parse push data:', error);
        }
    }

    event.waitUntil(
        self.registration.showNotification(notificationData.title, notificationData)
    );
});

// Notification click handling
self.addEventListener('notificationclick', (event) => {
    console.log('Notification clicked:', event);

    event.notification.close();

    if (event.action === 'view') {
        event.waitUntil(
            clients.openWindow(event.notification.data?.url || '/')
        );
    } else if (event.action === 'dismiss') {
        // Just close the notification
        return;
    } else {
        // Default action - open the app
        event.waitUntil(
            clients.matchAll({ type: 'window' })
                .then((clientList) => {
                    // If app is already open, focus it
                    for (const client of clientList) {
                        if (client.url === '/' && 'focus' in client) {
                            return client.focus();
                        }
                    }

                    // Otherwise open new window
                    if (clients.openWindow) {
                        return clients.openWindow('/');
                    }
                })
        );
    }
});

// Message handling from main thread
self.addEventListener('message', (event) => {
    console.log('Service Worker received message:', event.data);

    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    } else if (event.data && event.data.type === 'CACHE_URLS') {
        event.waitUntil(
            cacheUrls(event.data.urls)
        );
    }
});

// Cache specific URLs on demand
async function cacheUrls(urls) {
    try {
        const cache = await caches.open(DYNAMIC_CACHE);
        await cache.addAll(urls);
        console.log('URLs cached successfully:', urls);
    } catch (error) {
        console.error('Failed to cache URLs:', error);
    }
}

// Periodic background sync for cache cleanup
self.addEventListener('periodicsync', (event) => {
    if (event.tag === 'cache-cleanup') {
        event.waitUntil(cleanupOldCaches());
    }
});

// Clean up old cache entries
async function cleanupOldCaches() {
    try {
        const cacheNames = await caches.keys();

        for (const cacheName of cacheNames) {
            const cache = await caches.open(cacheName);
            const requests = await cache.keys();

            // Remove entries older than 7 days
            const oneWeekAgo = Date.now() - (7 * 24 * 60 * 60 * 1000);

            for (const request of requests) {
                const response = await cache.match(request);
                const dateHeader = response.headers.get('date');

                if (dateHeader) {
                    const responseDate = new Date(dateHeader).getTime();
                    if (responseDate < oneWeekAgo) {
                        await cache.delete(request);
                    }
                }
            }
        }

        console.log('Cache cleanup completed');
    } catch (error) {
        console.error('Cache cleanup failed:', error);
    }
}

// Error handling
self.addEventListener('error', (event) => {
    console.error('Service Worker error:', event.error);
});

self.addEventListener('unhandledrejection', (event) => {
    console.error('Service Worker unhandled rejection:', event.reason);
    event.preventDefault();
});

console.log('Service Worker loaded successfully');