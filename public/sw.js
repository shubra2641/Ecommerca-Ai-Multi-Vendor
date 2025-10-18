/**
 * Simple and reliable Service Worker
 * For PWA - E-commerce Store
 */

const CACHE_NAME = 'ecommerce-store-v1';
const OFFLINE_URL = '/offline.html';

// Essential files for caching
const ESSENTIAL_FILES = [
    '/',
    '/offline.html',
    '/manifest.json',
    '/assets/front/css/front.css',
    '/assets/front/js/front-lite.js'
];

// Install Service Worker
self.addEventListener('install', (event) => {
    console.log('[SW] Installing Service Worker');

    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('[SW] Caching essential files');
                return cache.addAll(ESSENTIAL_FILES);
            })
            .then(() => {
                console.log('[SW] Service Worker installed successfully');
                return self.skipWaiting();
            })
            .catch(error => {
                console.error('[SW] Installation error:', error);
            })
    );
});

// Activate Service Worker
self.addEventListener('activate', (event) => {
    console.log('[SW] Activating Service Worker');

    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames
                        .filter(cacheName => cacheName !== CACHE_NAME)
                        .map(cacheName => {
                            console.log('[SW] Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        })
                );
            })
            .then(() => {
                console.log('[SW] Service Worker activated');
                return self.clients.claim();
            })
    );
});

// Handle requests
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Skip non-HTTP/HTTPS requests
    if (!request.url.startsWith('http')) {
        return;
    }

    event.respondWith(
        handleRequest(request)
    );
});

// Handle requests
async function handleRequest(request) {
    const url = new URL(request.url);

    try {
        // Network First strategy for pages
        if (request.mode === 'navigate' || request.headers.get('accept')?.includes('text/html')) {
            return await networkFirst(request);
        }

        // Cache First strategy for static files
        if (isStaticFile(request)) {
            return await cacheFirst(request);
        }

        // Network First strategy for API
        if (url.pathname.startsWith('/api/')) {
            return await networkFirst(request);
        }

        // Cache First strategy for images
        if (isImageFile(request)) {
            return await cacheFirst(request);
        }

        // Default: Network First
        return await networkFirst(request);

    } catch (error) {
        console.error('[SW] Error handling request:', error);
        return await handleOffline(request);
    }
}

// Cache First strategy
async function cacheFirst(request) {
    const cache = await caches.open(CACHE_NAME);
    const cachedResponse = await cache.match(request);

    if (cachedResponse) {
        console.log('[SW] Returning from cache:', request.url);
        return cachedResponse;
    }

    try {
        const networkResponse = await fetch(request);

        if (networkResponse.ok) {
            cache.put(request, networkResponse.clone());
            console.log('[SW] Caching new resource:', request.url);
        }

        return networkResponse;
    } catch (error) {
        console.log('[SW] Network failed:', request.url);
        return await handleOffline(request);
    }
}

// Network First strategy
async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);

        if (networkResponse.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
            console.log('[SW] Updating cache:', request.url);
        }

        return networkResponse;
    } catch (error) {
        console.log('[SW] Network failed, checking cache:', request.url);

        const cache = await caches.open(CACHE_NAME);
        const cachedResponse = await cache.match(request);

        if (cachedResponse) {
            return cachedResponse;
        }

        return await handleOffline(request);
    }
}

// Handle offline state
async function handleOffline(request) {
    if (request.mode === 'navigate') {
        const cache = await caches.open(CACHE_NAME);
        const offlinePage = await cache.match(OFFLINE_URL);

        if (offlinePage) {
            return offlinePage;
        }

        return new Response(
            `
      <!DOCTYPE html>
      <html lang="en">
      <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Offline - E-Commerce Store</title>
        <style>
          body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            padding: 50px; 
            background: #f5f5f5;
          }
          .offline-message {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 400px;
            margin: 0 auto;
          }
          .retry-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
          }
        </style>
      </head>
      <body>
        <div class="offline-message">
          <h2>You're Offline</h2>
          <p>Please check your internet connection and try again</p>
          <button class="retry-btn" onclick="window.location.reload()">Try Again</button>
        </div>
      </body>
      </html>
      `,
            {
                status: 503,
                headers: { 'Content-Type': 'text/html; charset=utf-8' }
            }
        );
    }

    return new Response('Offline', { status: 503 });
}

// Check file type
function isStaticFile(request) {
    const url = new URL(request.url);
    return url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.woff') ||
        url.pathname.endsWith('.woff2') ||
        url.pathname.endsWith('.ttf');
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

// Handle messages
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// Handle errors
self.addEventListener('error', (event) => {
    console.error('[SW] Service Worker error:', event.error);
});

self.addEventListener('unhandledrejection', (event) => {
    console.error('[SW] Unhandled rejection:', event.reason);
    event.preventDefault();
});

console.log('[SW] Service Worker loaded');