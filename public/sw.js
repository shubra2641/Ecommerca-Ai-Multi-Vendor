/**
 * Simple and reliable Service Worker
 * For PWA - E-commerce Store
 */
/* global caches, URL, Response */

const CACHE_NAME = 'ecommerce-store-v1';
const OFFLINE_URL = '/offline.html';

// Essential files for caching
const ESSENTIAL_FILES = [
    '/',
    '/offline.html',
    '/manifest.json',
    '/assets/front/css/front.css',
    '/assets/front/js/front.js'
];

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
    } catch {
        return handleOffline(request);
    }
}

// Cache First strategy
async function cacheFirst(request) {
    const cache = await caches.open(CACHE_NAME);
    const cachedResponse = await cache.match(request);

    if (cachedResponse) {
        return cachedResponse;
    }

    try {
        const networkResponse = await fetch(request);

        if (networkResponse.ok) {
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch {
        return handleOffline(request);
    }
}

// Network First strategy
async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);

        if (networkResponse.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch {
        const cache = await caches.open(CACHE_NAME);
        const cachedResponse = await cache.match(request);

        if (cachedResponse) {
            return cachedResponse;
        }

        return handleOffline(request);
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
self.addEventListener('error', () => {
    // Handle service worker errors
});

self.addEventListener('unhandledrejection', (event) => {
    event.preventDefault();
});
