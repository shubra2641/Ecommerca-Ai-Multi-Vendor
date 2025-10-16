/**
 * Vendor Layout JavaScript
 * Navigation and layout functionality for vendor dashboard
 * Professional, accessible, and performance-optimized
 */

(function(window, document) {
    'use strict';

    // Initialize immediately or wait for VendorApp
    function initializeLayout() {
        // Use VendorApp Utils if available, otherwise create fallback
        const Utils = window.VendorApp?.Utils || {
            $: (selector, context = document) => context.querySelector(selector),
            $$: (selector, context = document) => Array.from(context.querySelectorAll(selector)),
            on: (element, event, handler, options = false) => {
                if (element && typeof element.addEventListener === 'function') {
                    element.addEventListener(event, handler, options);
                }
            },
            addClass: (element, className) => {
                if (element?.classList) element.classList.add(className);
            },
            removeClass: (element, className) => {
                if (element?.classList) element.classList.remove(className);
            },
            toggleClass: (element, className) => {
                if (element?.classList) element.classList.toggle(className);
            }
        };
        
        const NotificationManager = window.VendorApp?.NotificationManager || {
            show: (message, type = 'info') => console.log(`${type}: ${message}`)
        };

        /**
         * Layout Manager
         * Handles sidebar, navigation, and responsive behavior
         */
        const LayoutManager = {
            sidebar: null,
            sidebarOverlay: null,
            mobileMenuToggle: null,
            isMobile: false,
            isTablet: false,
            sidebarOpen: false,

            init: function() {
                this.cacheDOMElements();
                this.detectDeviceType();
                this.bindEvents();
                this.initializeState();
                this.setupScrollIndicator();
                this.setupStickyHeader();
                this.initializeAccessibility();
            },

            cacheDOMElements: function() {
                this.sidebar = Utils.$('.vendor-sidebar');
                this.sidebarOverlay = Utils.$('.sidebar-overlay');
                this.mobileMenuToggle = Utils.$('.mobile-menu-toggle');
                this.header = Utils.$('.vendor-header');
                this.content = Utils.$('.vendor-content');
            },

            detectDeviceType: function() {
                this.isMobile = window.innerWidth < 768;
                this.isTablet = window.innerWidth >= 768 && window.innerWidth < 1024;
                
                document.documentElement.classList.toggle('is-mobile', this.isMobile);
                document.documentElement.classList.toggle('is-tablet', this.isTablet);
                document.documentElement.classList.toggle('is-desktop', !this.isMobile && !this.isTablet);
            },

            bindEvents: function() {
                // Sidebar toggle events
                Utils.on(this.mobileMenuToggle, 'click', this.toggleSidebar.bind(this));
                Utils.on(this.sidebarOverlay, 'click', this.closeSidebar.bind(this));
                
                // Window resize event
                Utils.on(window, 'resize', Utils.debounce(() => {
                    this.detectDeviceType();
                    this.handleResize();
                }, 250));
                
                // Keyboard navigation
                Utils.on(document, 'keydown', this.handleKeyboardNavigation.bind(this));
            },

            toggleSidebar: function() {
                this.sidebarOpen = !this.sidebarOpen;
                this.updateSidebarState();
            },

            closeSidebar: function() {
                this.sidebarOpen = false;
                this.updateSidebarState();
            },

            updateSidebarState: function() {
                if (this.sidebar) {
                    Utils.toggleClass(this.sidebar, 'open');
                }
                if (this.sidebarOverlay) {
                    Utils.toggleClass(this.sidebarOverlay, 'show');
                }
                document.body.classList.toggle('sidebar-open', this.sidebarOpen);
            },

            handleResize: function() {
                if (!this.isMobile && this.sidebarOpen) {
                    this.closeSidebar();
                }
            },

            handleKeyboardNavigation: function(e) {
                if (e.key === 'Escape' && this.sidebarOpen) {
                    this.closeSidebar();
                }
            },

            initializeState: function() {
                this.detectDeviceType();
                if (this.isMobile) {
                    this.closeSidebar();
                }
            },

            setupScrollIndicator: function() {
                // Optional scroll indicator functionality
            },

            setupStickyHeader: function() {
                // Optional sticky header functionality
            },

            initializeAccessibility: function() {
                // Add ARIA attributes and accessibility features
                if (this.sidebar) {
                    this.sidebar.setAttribute('role', 'navigation');
                    this.sidebar.setAttribute('aria-label', 'Main navigation');
                }
            }
        };

        /**
         * Quick Actions Manager
         * Handles quick action buttons and shortcuts
         */
        const QuickActionsManager = {
            init: function() {
                this.bindQuickActions();
            },

            bindQuickActions: function() {
                // Add quick action functionality
                const quickActions = Utils.$$('[data-quick-action]');
                quickActions.forEach(action => {
                    Utils.on(action, 'click', this.handleQuickAction.bind(this));
                });
            },

            handleQuickAction: function(e) {
                const action = e.currentTarget.dataset.quickAction;
                switch (action) {
                    case 'refresh':
                        window.location.reload();
                        break;
                    case 'back':
                        window.history.back();
                        break;
                    default:
                        console.log('Unknown quick action:', action);
                }
            }
        };

        // Initialize managers
        try {
            LayoutManager.init();
            QuickActionsManager.init();

            // Expose layout API
            if (window.VendorApp) {
                window.VendorApp.LayoutManager = LayoutManager;
                window.VendorApp.QuickActionsManager = QuickActionsManager;
            }
            
            console.log('Vendor Layout initialized successfully');
        } catch (error) {
            console.error('Error initializing Vendor Layout:', error);
        }
    }

    // Initialize layout when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeLayout);
    } else {
        initializeLayout();
    }

    // Also listen for VendorApp:ready event if it comes later
    document.addEventListener('VendorApp:ready', function() {
        if (window.VendorApp?.Utils) {
            // Re-initialize with full VendorApp utilities if available
            initializeLayout();
        }
    });

})(window, document);