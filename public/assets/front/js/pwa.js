/**
 * Simple PWA Registration Script
 * Ultra lightweight - only essential functionality
 */

(function () {
    'use strict';

    // Check if service workers are supported
    if (!('serviceWorker' in navigator)) {
        return;
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

        // Setup install prompt
        setupInstallPrompt();
    }

    // Register service worker
    async function registerServiceWorker() {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js');

            // Handle service worker updates
            registration.addEventListener('updatefound', () => {
                const newWorker = registration.installing;

                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        showUpdateNotification();
                    }
                });
            });
        } catch (error) {
            // Service worker registration failed
        }
    }

    // Setup install prompt
    function setupInstallPrompt() {
        let deferredPrompt;

        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            deferredPrompt = event;
            showInstallButton(deferredPrompt);
        });

        window.addEventListener('appinstalled', () => {
            hideInstallButton();
        });
    }

    // Show install button
    function showInstallButton(deferredPrompt) {
        let installButton = document.getElementById('pwa-install-button');

        if (!installButton) {
            installButton = document.createElement('button');
            installButton.id = 'pwa-install-button';
            installButton.textContent = 'Install App';
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

    // Show update notification
    function showUpdateNotification() {
        const updateButton = document.createElement('button');
        updateButton.textContent = 'Update Available - Click to Update';
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

    // Expose PWA utilities
    window.PWA = {
        showInstallButton,
        hideInstallButton
    };
})();