/**
 * Simple PWA Registration Script
 * Handles service worker registration and PWA features
 */

(function () {
    'use strict';

    const PWA_CONFIG = {
        serviceWorkerPath: '/sw.js',
        manifestPath: '/manifest.json',
        enableNotifications: true,
        enableOfflineSupport: true
    };

    // Check if service workers are supported
    if (!('serviceWorker' in navigator)) {
        return;
    }

    // Check if we're in a secure context
    if (!window.isSecureContext && location.hostname !== 'localhost') {
        // console.warn('PWA requires HTTPS in production');
    }

    // Initialize PWA when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPWA);
    } else {
        initPWA();
    }

    function initPWA() {
        // Register service worker
        registerServiceWorker();

        // Setup offline detection
        setupOfflineDetection();

        // Setup install prompt
        setupInstallPrompt();

        // Setup notifications
        if (PWA_CONFIG.enableNotifications) {
            setupNotifications();
        }
    }

    // Register service worker
    async function registerServiceWorker() {
        try {
            const registration = await navigator.serviceWorker.register(PWA_CONFIG.serviceWorkerPath);


            // Handle service worker updates
            registration.addEventListener('updatefound', () => {
                const newWorker = registration.installing;

                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        // New service worker is available
                        showUpdateNotification();
                    }
                });
            });

            // Listen for messages from service worker
            navigator.serviceWorker.addEventListener('message', handleServiceWorkerMessage);
        } catch {
            // console.error('Failed to register service worker');
        }
    }

    // Handle service worker messages
    function handleServiceWorkerMessage(event) {
        const { type } = event.data || {};

        switch (type) {
            case 'CACHE_UPDATED':
                // Handle cache update if needed
                break;
            case 'OFFLINE_ACTION_QUEUED':
                break;
        default:
        }
    }

    // Setup offline detection
    function setupOfflineDetection() {
        function updateOnlineStatus() {
            const isOnline = navigator.onLine;
            document.body.classList.toggle('offline', !isOnline);

            if (isOnline) {
                // Sync any offline data
                syncOfflineData();
            } else {
                // Handle offline state
                // console.log('App is offline');
            }
        }

        // Listen for online/offline events
        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);

        // Initial check
        updateOnlineStatus();
    }

    // Setup install prompt
    function setupInstallPrompt() {
        let deferredPrompt;

        window.addEventListener('beforeinstallprompt', (event) => {
            // Prevent the mini-infobar from appearing on mobile
            event.preventDefault();

            // Stash the event so it can be triggered later
            deferredPrompt = event;

            // Show install button or banner
            showInstallButton(deferredPrompt);
        });

        window.addEventListener('appinstalled', () => {
            hideInstallButton();
        });
    }

    // Show install button
    function showInstallButton(deferredPrompt) {
        // Create install button if it doesn't exist
        let installButton = document.getElementById('pwa-install-button');

        if (!installButton) {
            installButton = document.createElement('button');
            installButton.id = 'pwa-install-button';
            installButton.textContent = 'Install App';
            installButton.className = 'pwa-install-btn';

            // Add styles
            installButton.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: #3b82f6;
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
                z-index: 1000;
                transition: all 0.3s ease;
            `;

            document.body.appendChild(installButton);
        }

        installButton.style.display = 'block';

        installButton.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                await deferredPrompt.userChoice;
                // console.log('Install prompt outcome');
                // Clear the deferred prompt
                window.deferredPrompt = null;
                hideInstallButton();
            }
        });
    }

    // Hide install button
    function hideInstallButton() {
        const installButton = document.getElementById('pwa-install-button');
        if (installButton) {
            installButton.style.display = 'none';
        }
    }

    // Setup notifications
    function setupNotifications() {
        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(() => {
                // console.log('Notification permission');
            });
        }
    }

    // Show update notification
    function showUpdateNotification() {
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            const updateButton = document.createElement('button');
            updateButton.textContent = 'Update Available - Click to Update';
            updateButton.className = 'pwa-update-btn';
            updateButton.style.cssText = `
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: #10b981;
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
                z-index: 1000;
            `;

            document.body.appendChild(updateButton);

            updateButton.addEventListener('click', () => {
                navigator.serviceWorker.controller.postMessage({ type: 'SKIP_WAITING' });
                window.location.reload();
            });
        }
    }

    // Sync offline data
    function syncOfflineData() {
        try {
            // Send message to service worker to sync offline data
            if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                navigator.serviceWorker.controller.postMessage({ type: 'SYNC_OFFLINE_DATA' });
            }
        } catch {
            // console.error('Failed to sync offline data');
        }
    }

    // Utility function to check if app is installed
    function isAppInstalled() {
        return window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone === true;
    }

    // Expose PWA utilities to global scope
    window.PWA = {
        isInstalled: isAppInstalled,
        syncOfflineData,
        showInstallButton,
        hideInstallButton
    };
}());