/* ===== Unified Admin Panel JavaScript ===== */

class AdminPanel {
    constructor() {
        this.init();
    }

    init() {
        if (window.ADMIN_DEBUG) {
            console.log('AdminPanel initializing');
        }

        this.setupCSRF();
        this.setupSidebar();

        setTimeout(() => {
            this.setupDropdowns();
        }, 200);

        this.setupBootstrapComponents();
        this.setupLanguageSwitcher();
        this.setupNotifications();
        this.setupResponsive();
        this.setupMobileNavigation();
        this.initializeTableActions();
        this.initializeTooltips();
        this.initializeBootstrapDropdowns();

        // Initialize DataTables if any exist
        document.querySelectorAll('.data-table').forEach(table => {
            this.initializeDataTable(table);
        });

        if (window.ADMIN_DEBUG) {
            console.log('Admin Panel initialized');
        }
    }

    // CSRF Token Setup
    setupCSRF() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.Laravel = { csrfToken: token.content };

            // Setup AJAX headers
            if (window.axios) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
            }

            // Wait for jQuery to be fully loaded
            if (typeof window.jQuery !== 'undefined' && window.jQuery.ajaxSetup) {
                window.jQuery.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': token.content
                    }
                });
            } else if (typeof window.jQuery !== 'undefined') {
                // Fallback for jQuery versions without ajaxSetup or when ajaxSetup is not available
                window.jQuery(document).ajaxSend(function (event, xhr, settings) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', token.content);
                });
            } else {
                // jQuery not loaded yet, try again after a short delay
                setTimeout(() => {
                    if (typeof window.jQuery !== 'undefined' && window.jQuery.ajaxSetup) {
                        window.jQuery.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': token.content
                            }
                        });
                    }
                }, 100);
            }
        }
    }

    // Sidebar functionality
    setupSidebar() {
        const toggleBtn = document.getElementById('sidebarToggle');
        const mobileToggleBtn = document.getElementById('mobileMenuToggle');
        const sidebar = document.querySelector('.modern-sidebar') || document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const mainContent = document.querySelector('.main-content');

        // Desktop sidebar toggle
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                this.saveSidebarState();
                this.updateToggleButtonVisibility();
                console.log('Sidebar toggled:', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
            });
        }

        // Mobile sidebar toggle
        if (mobileToggleBtn && sidebar) {
            mobileToggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
                if (overlay) {
                    overlay.classList.toggle('active');
                }
                console.log('Mobile sidebar toggled');
            });
        }

        // Overlay click to close mobile sidebar
        if (overlay && sidebar) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                console.log('Mobile sidebar closed via overlay');
            });
        }

        // Add desktop toggle button when sidebar is collapsed
        this.createDesktopToggleButton();

        // Restore sidebar state
        this.restoreSidebarState();
        this.updateToggleButtonVisibility();
    }

    // Dropdown functionality
    setupDropdowns() {
        if (window.ADMIN_DEBUG) {
            console.log('Setting up dropdowns...');
        }

        const dropdownContainers = document.querySelectorAll('.nav-dropdown');
        if (window.ADMIN_DEBUG) {
            console.log('Found dropdown containers:', dropdownContainers.length);
        }

        dropdownContainers.forEach((container, index) => {
            const toggle = container.querySelector('.nav-dropdown-toggle');
            const menu = container.querySelector('.nav-dropdown-menu');

            if (!toggle || !menu) {
                if (window.ADMIN_DEBUG) {
                    console.warn(`Dropdown ${index} missing toggle or menu`);
                }
                return;
            }

            if (window.ADMIN_DEBUG) {
                console.log(`Setting up dropdown ${index}:`, {
                    toggle: toggle.textContent.trim(),
                    menuItems: menu.children.length
                });
            }

            const handleClick = (e) => {
                e.preventDefault();
                e.stopPropagation();

                if (window.ADMIN_DEBUG) {
                    console.log('Dropdown clicked:', toggle.textContent.trim());
                }

                // إغلاق جميع القوائم الأخرى
                dropdownContainers.forEach(otherContainer => {
                    if (otherContainer !== container && otherContainer.classList.contains('active')) {
                        otherContainer.classList.remove('active');
                        if (window.ADMIN_DEBUG) {
                            console.log('Closed other dropdown');
                        }
                    }
                });

                // تبديل حالة القائمة الحالية
                const isActive = container.classList.contains('active');
                if (isActive) {
                    container.classList.remove('active');
                    if (window.ADMIN_DEBUG) {
                        console.log('Dropdown closed');
                    }
                } else {
                    container.classList.add('active');
                    if (window.ADMIN_DEBUG) {
                        console.log('Dropdown opened');
                    }

                    // التأكد من أن القائمة مرئية
                    setTimeout(() => {
                        const computedStyle = window.getComputedStyle(menu);
                        if (window.ADMIN_DEBUG) {
                            console.log('Menu computed styles:', {
                                maxHeight: computedStyle.maxHeight,
                                opacity: computedStyle.opacity,
                                visibility: computedStyle.visibility,
                                transform: computedStyle.transform
                            });
                        }
                    }, 100);
                }
            };

            toggle.addEventListener('click', handleClick);

            // إضافة دعم لوحة المفاتيح
            toggle.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    handleClick(e);
                }
            });

            // تأكد من أن العنصر قابل للتركيز
            if (!toggle.hasAttribute('tabindex')) {
                toggle.setAttribute('tabindex', '0');
            }
        });

        // إغلاق القوائم عند النقر خارجها
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.nav-dropdown')) {
                dropdownContainers.forEach(container => {
                    if (container.classList.contains('active')) {
                        container.classList.remove('active');
                        if (window.ADMIN_DEBUG) {
                            console.log('Closed dropdown due to outside click:', container);
                        }
                    }
                });
            }
        });

        if (window.ADMIN_DEBUG) {
            console.log('Dropdowns setup completed');
        }
    }

    // Bootstrap Components Setup
    setupBootstrapComponents() {
        // Initialize Bootstrap tooltips
        if (window.bootstrap && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Initialize Bootstrap popovers
        if (window.bootstrap && bootstrap.Popover) {
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        }

        // Initialize Bootstrap dropdowns
        if (window.bootstrap && bootstrap.Dropdown) {
            const dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
            dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        }
    }

    // Language Switcher
    setupLanguageSwitcher() {
        const languageLinks = document.querySelectorAll('[data-action="switch-language"]');

        languageLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                const formId = this.getAttribute('data-form-id');
                const form = document.getElementById(formId);

                if (form) {
                    form.submit();
                    console.log('Language switch form submitted:', formId);
                }
            });
        });
    }

    // Notifications
    setupNotifications() {
        // Auto-hide alerts after 5 seconds
        document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
            setTimeout(() => {
                this.fadeOutAlert(alert);
            }, 5000);
        });

        // Close button functionality
        document.querySelectorAll('.alert .btn-close').forEach(button => {
            button.addEventListener('click', (e) => {
                const alert = e.target.closest('.alert');
                if (alert) {
                    this.fadeOutAlert(alert);
                }
            });
        });
    }

    // Responsive Design
    setupResponsive() {
        // Close mobile menu on resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');

                if (sidebar) {
                    sidebar.classList.remove('mobile-open');
                }
                if (overlay) {
                    overlay.classList.remove('active');
                }
            }
            // Update toggle button visibility on resize
            this.updateToggleButtonVisibility();
        });

        // Mobile menu close on outside click
        if (window.innerWidth <= 768) {
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.sidebar') && !e.target.closest('#mobileMenuToggle')) {
                    const sidebar = document.getElementById('sidebar');
                    const overlay = document.getElementById('sidebarOverlay');

                    if (sidebar && sidebar.classList.contains('mobile-open')) {
                        sidebar.classList.remove('mobile-open');
                        if (overlay) {
                            overlay.classList.remove('active');
                        }
                    }
                }
            });
        }

        // Setup swipe gestures for mobile sidebar
        this.setupSwipeGestures();
    }

    // Setup swipe gestures for mobile sidebar
    setupSwipeGestures() {
        let startX = 0;
        let startY = 0;
        let currentX = 0;
        let currentY = 0;
        let isDragging = false;
        const threshold = 50; // Minimum distance for swipe
        const restraint = 100; // Maximum distance perpendicular to swipe direction
        const allowedTime = 300; // Maximum time allowed to travel that distance
        let startTime = 0;

        const sidebar = document.querySelector('.modern-sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const mainContent = document.querySelector('.main-content');

        if (!sidebar || window.innerWidth > 768) {
            return;
        }

        // Touch start
        const handleTouchStart = (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            startTime = new Date().getTime();
            isDragging = true;
        };

        // Touch move
        const handleTouchMove = (e) => {
            if (!isDragging) {
                return;
            }

            currentX = e.touches[0].clientX;
            currentY = e.touches[0].clientY;

            // Prevent default scrolling when swiping horizontally
            if (Math.abs(currentX - startX) > Math.abs(currentY - startY)) {
                e.preventDefault();
            }
        };

        // Touch end
        const handleTouchEnd = (e) => {
            if (!isDragging) {
                return;
            }

            isDragging = false;
            const elapsedTime = new Date().getTime() - startTime;
            const distanceX = currentX - startX;
            const distanceY = Math.abs(currentY - startY);

            // Check if swipe meets criteria
            if (elapsedTime <= allowedTime && Math.abs(distanceX) >= threshold && distanceY <= restraint) {
                // Swipe right to open sidebar
                if (distanceX > 0 && startX < 50 && !sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.add('mobile-open');
                    if (overlay) {
                        overlay.classList.add('active');
                    }
                    console.log('Sidebar opened via swipe');
                }
                // Swipe left to close sidebar
                else if (distanceX < 0 && sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('mobile-open');
                    if (overlay) {
                        overlay.classList.remove('active');
                    }
                    console.log('Sidebar closed via swipe');
                }
            }
        };

        // Add event listeners
        document.addEventListener('touchstart', handleTouchStart, { passive: false });
        document.addEventListener('touchmove', handleTouchMove, { passive: false });
        document.addEventListener('touchend', handleTouchEnd, { passive: true });

        // Cleanup on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                document.removeEventListener('touchstart', handleTouchStart);
                document.removeEventListener('touchmove', handleTouchMove);
                document.removeEventListener('touchend', handleTouchEnd);
            }
        });
    }

    setupMobileNavigation() {
        // Setup hamburger menu
        const hamburger = document.querySelector('.hamburger');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.mobile-overlay');
        const body = document.body;

        if (hamburger && sidebar) {
            hamburger.addEventListener('click', () => {
                this.toggleSidebar();
            });
        }

        // Setup overlay click to close
        if (overlay) {
            overlay.addEventListener('click', () => {
                this.closeSidebar();
            });
        }

        // Setup mobile bottom navigation
        this.setupBottomNavigation();

        // Setup mobile navigation animations
        this.setupNavigationAnimations();

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                this.closeSidebar();
                hamburger?.classList.remove('active');
            }
        });
    }

    setupBottomNavigation() {
        const bottomNav = document.querySelector('.mobile-bottom-nav');
        if (!bottomNav) {
            return;
        }

        // Show bottom navigation on mobile
        if (window.innerWidth < 768) {
            setTimeout(() => {
                bottomNav.classList.add('show');
            }, 500);
        }

        // Handle bottom nav item clicks
        const navItems = bottomNav.querySelectorAll('.mobile-bottom-nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', (e) => {
                // Remove active class from all items
                navItems.forEach(navItem => navItem.classList.remove('active'));
                // Add active class to clicked item
                item.classList.add('active');

                // Add ripple effect
                this.addRippleEffect(item, e);
            });
        });
    }

    setupNavigationAnimations() {
        const sidebar = document.querySelector('.sidebar');
        if (!sidebar) {
            return;
        }

        // Add stagger animation to nav links when sidebar opens
        const navLinks = sidebar.querySelectorAll('.nav-link');

        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (sidebar.classList.contains('active')) {
                        // Reset animations
                        navLinks.forEach(link => {
                            link.style.opacity = '0';
                            link.style.transform = 'translateX(-20px)';
                        });

                        // Trigger stagger animation
                        navLinks.forEach((link, index) => {
                            setTimeout(() => {
                                link.style.transition = 'all 0.3s ease';
                                link.style.opacity = '1';
                                link.style.transform = 'translateX(0)';
                            }, (index + 1) * 50);
                        });
                    }
                }
            });
        });

        observer.observe(sidebar, { attributes: true });
    }

    toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const hamburger = document.querySelector('.hamburger');
        const overlay = document.querySelector('.mobile-overlay');
        const body = document.body;

        if (!sidebar) {
            return;
        }

        const isActive = sidebar.classList.contains('active');

        if (isActive) {
            this.closeSidebar();
        } else {
            this.openSidebar();
        }
    }

    openSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const hamburger = document.querySelector('.hamburger');
        const overlay = document.querySelector('.mobile-overlay');
        const body = document.body;

        if (!sidebar) {
            return;
        }

        sidebar.classList.add('active', 'entering');
        hamburger?.classList.add('active');
        overlay?.classList.add('active');
        body.style.overflow = 'hidden';

        // Remove entering class after animation
        setTimeout(() => {
            sidebar.classList.remove('entering');
        }, 300);
    }

    closeSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const hamburger = document.querySelector('.hamburger');
        const overlay = document.querySelector('.mobile-overlay');
        const body = document.body;

        if (!sidebar) {
            return;
        }

        sidebar.classList.add('leaving');
        hamburger?.classList.remove('active');
        overlay?.classList.remove('active');
        body.style.overflow = '';

        // Remove classes after animation
        setTimeout(() => {
            sidebar.classList.remove('active', 'leaving');
        }, 300);
    }

    addRippleEffect(element, event) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s ease-out;
        pointer-events: none;
        `;

        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);

        // Add ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;

        if (!document.querySelector('style[data-ripple]')) {
            style.setAttribute('data-ripple', 'true');
            document.head.appendChild(style);
        }

        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    // Utility Methods
    saveSidebarState() {
        const sidebar = document.querySelector('.modern-sidebar') || document.getElementById('sidebar');
        if (sidebar) {
            localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
        }
    }

    restoreSidebarState() {
        const sidebar = document.querySelector('.modern-sidebar') || document.getElementById('sidebar');
        const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';

        if (sidebar && isCollapsed) {
            sidebar.classList.add('collapsed');
        }
    }

    createDesktopToggleButton() {
        // Create desktop toggle button for when sidebar is collapsed
        if (!document.getElementById('desktopToggleBtn')) {
            const toggleBtn = document.createElement('button');
            toggleBtn.id = 'desktopToggleBtn';
            toggleBtn.className = 'desktop-toggle-btn';
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            toggleBtn.style.cssText = `
            position: fixed;
            top: 20px;
            left: 20px;
                z - index: 1001;
            background: var(--primary - color, #4f46e5);
            color: white;
            border: none;
                border - radius: 8px;
            width: 40px;
            height: 40px;
            display: none;
                align - items: center;
                justify - content: center;
            cursor: pointer;
                box - shadow: 0 4px 6px - 1px rgb(0 0 0 / 0.1);
            transition: all 0.2s ease;
            `;

            toggleBtn.addEventListener('click', () => {
                const sidebar = document.querySelector('.modern-sidebar') || document.getElementById('sidebar');
                if (sidebar) {
                    sidebar.classList.remove('collapsed');
                    this.saveSidebarState();
                    this.updateToggleButtonVisibility();
                }
            });

            document.body.appendChild(toggleBtn);
        }
    }

    updateToggleButtonVisibility() {
        const sidebar = document.querySelector('.modern-sidebar') || document.getElementById('sidebar');
        const desktopToggleBtn = document.getElementById('desktopToggleBtn');
        const isCollapsed = sidebar && sidebar.classList.contains('collapsed');
        const isMobile = window.innerWidth <= 768;

        if (desktopToggleBtn) {
            // Show desktop toggle button only on desktop when sidebar is collapsed
            if (isCollapsed && !isMobile) {
                desktopToggleBtn.style.display = 'flex';
            } else {
                desktopToggleBtn.style.display = 'none';
            }
        }
    }

    fadeOutAlert(alert) {
        alert.style.transition = 'opacity 0.3s ease';
        alert.style.opacity = '0';

        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 300);
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert - ${type} alert - dismissible fade show notification - toast`;
        notification.innerHTML = `
            ${message}
            < button type = "button" class = "btn-close" data - bs - dismiss = "alert" > < / button >
        `;

        const container = document.querySelector('.notification-container') || document.body;
        container.appendChild(notification);

        setTimeout(() => {
            if (notification.parentNode) {
                this.fadeOutAlert(notification);
            }
        }, 5000);
    }

    // Static method to get instance
    static getInstance() {
        if (!window.adminPanelInstance) {
            window.adminPanelInstance = new AdminPanel();
        }
        return window.adminPanelInstance;
    }

    // Additional methods for currency management
    toggleCurrencyStatus(currencyId, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';

        fetch(` / admin / currencies / ${currencyId} / toggle - status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: newStatus })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    this.showNotification(data.message || (typeof __tFn === 'function' ? __tFn('currency.status.update_error') : 'Error updating currency status'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showNotification((typeof __tFn === 'function' ? __tFn('currency.status.update_error') : 'Error updating currency status'), 'error');
            });
    }

    setDefaultCurrency(currencyId) {
        if (!confirm((typeof __tFn === 'function' ? __tFn('currency.set_default.confirm') : 'Are you sure to set this currency as default?'))) {
            return;
        }

        fetch(` / admin / currencies / ${currencyId} / set - default`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showNotification(data.message || (typeof __tFn === 'function' ? __tFn('currency.set_default.success') : 'Default currency set successfully'), 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showNotification(data.message || (typeof __tFn === 'function' ? __tFn('currency.set_default.error') : 'Error setting default currency'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showNotification((typeof __tFn === 'function' ? __tFn('currency.set_default.error') : 'Error setting default currency'), 'error');
            });
    }

    initializeDataTable(table) {
        if (window.jQuery && window.jQuery.fn.DataTable) {
            window.jQuery(table).DataTable({
                responsive: true,
                language: {
                    url: '/admin/js/datatables-ar.json'
                },
                pageLength: 25,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: -1 }
                ]
            });
        }
    }

    setButtonLoading(button, loading = true) {
        if (loading) {
            button.disabled = true;
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + (typeof __tFn === 'function' ? __tFn('common.processing') : 'Processing...');
        } else {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText || button.innerHTML;
        }
    }

    showAlert(message, type = 'info', duration = 5000) {
        const alertContainer = this.createAlertContainer();

        const alert = document.createElement('div');
        alert.className = `alert alert - ${type} alert - dismissible fade show`;
        alert.innerHTML = `
            ${message}
            < button type = "button" class = "btn-close" data - bs - dismiss = "alert" > < / button >
        `;

        alertContainer.appendChild(alert);

        if (duration > 0) {
            setTimeout(() => {
                this.fadeOutAlert(alert);
            }, duration);
        }
    }

    createAlertContainer() {
        let container = document.querySelector('.alert-container');
        if (!container) {
            container = document.createElement('div');
            const isRtl = (document.documentElement && document.documentElement.getAttribute('dir') === 'rtl') || document.body.classList.contains('rtl');
            const posClass = isRtl ? 'start-0' : 'end-0';
            container.className = 'alert-container position-fixed top-0 ' + posClass + ' p-3';
            document.body.appendChild(container);
        }
        return container;
    }

    initializeTableActions() {
        document.querySelectorAll('[data-action="delete"]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const url = button.getAttribute('data-url');
                if (url && confirm((typeof __tFn === 'function' ? __tFn('common.delete_confirm') : 'Are you sure you want to delete?'))) {
                    this.performDeleteAction(url);
                }
            });
        });
    }

    performDeleteAction(url) {
        fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showNotification(data.message || (typeof __tFn === 'function' ? __tFn('common.delete_success') : 'Deleted successfully'), 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showNotification(data.message || (typeof __tFn === 'function' ? __tFn('common.delete_error') : 'Error deleting item'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showNotification((typeof __tFn === 'function' ? __tFn('common.delete_error') : 'Error deleting item'), 'error');
            });
    }

    initializeTooltips() {
        if (window.bootstrap && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }

    initializeBootstrapDropdowns() {
        // Initialize Bootstrap dropdowns specifically for user menu and other Bootstrap dropdowns
        if (window.bootstrap && bootstrap.Dropdown) {
            const dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]:not(.nav-dropdown-toggle)'));
            dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
            console.log('Bootstrap dropdowns initialized:', dropdownElementList.length);
        }
    }

    validateForm(form) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    makeAjaxRequest(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        };

        return fetch(url, { ...defaultOptions, ...options })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            });
    }

    hideLoadingScreen() {
        const loadingScreen = document.getElementById('loading-screen');
        if (loadingScreen) {
            setTimeout(() => {
                loadingScreen.style.opacity = '0';
                setTimeout(() => {
                    loadingScreen.style.display = 'none';
                }, 300);
            }, 500);
        }
    }

    // Fallback initialization for when AdminPanel class is not available
    static initializeFallback() {
        console.log('🔄 AdminPanel fallback initialization...');

        // Initialize Bootstrap dropdowns
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            const headerDropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');
            headerDropdowns.forEach(dropdown => {
                new bootstrap.Dropdown(dropdown);
            });
            console.log('✅ Bootstrap dropdowns initialized:', headerDropdowns.length);
        }

        // Hide loading screen
        const loadingScreen = document.getElementById('loading-screen');
        if (loadingScreen) {
            setTimeout(() => {
                loadingScreen.style.opacity = '0';
                setTimeout(() => {
                    loadingScreen.style.display = 'none';
                }, 300);
            }, 500);
        }

        console.log('✅ Fallback initialization complete');
    }
}

// --- Payment Gateways Management handlers ---
(function () {
    function ajaxPost(url, data) {
        // prefer fetch, fall back to jQuery
        const headers = { 'Content-Type': 'application/json' };
        if (window.Laravel && window.Laravel.csrfToken) headers['X-CSRF-TOKEN'] = window.Laravel.csrfToken;

        if (window.fetch) {
            return fetch(url, { method: 'POST', headers: headers, body: JSON.stringify(data || {}) }).then(r => r.json());
        }

        if (window.jQuery && window.jQuery.ajax) {
            return new Promise((resolve, reject) => {
                window.jQuery.ajax({ url: url, method: 'POST', data: data || {}, dataType: 'json', headers: headers, success: resolve, error: (xhr) => reject(xhr) });
            });
        }

        return Promise.reject(new Error('No fetch or jQuery available'));
    }

    function rootData() {
        const el = document.getElementById('pgMgmtRoot');
        if (!el) return {};
        return {
            syncUrl: el.getAttribute('data-sync-url'),
            testBase: el.getAttribute('data-test-base'),
            toggleBase: el.getAttribute('data-toggle-base'),
        };
    }

    // Note: gateway-related actions are handled in the consolidated pgMgmt block later in this file.
    // This placeholder prevents duplicate handlers from conflicting.
    document.addEventListener('click', function () { });

    // Run test button inside modal
    document.addEventListener('click', function (e) {
        if (e.target && e.target.id === 'runTestGateway') {
            const form = document.getElementById('testGatewayForm');
            if (!form) return;
            const gateway = form.dataset.gateway;
            const amount = form.querySelector('input[name="amount"]').value;
            const root = rootData();
            if (!gateway) return alert('Gateway missing');
            const url = (root.testBase || '') + '/' + encodeURIComponent(gateway) + '/test-connection';
            ajaxPost(url, { amount: amount })
                .then(res => { alert(res && res.success ? 'Test Successful' : 'Test Failed'); if (window.bootstrap && bootstrap.Modal) { const m = bootstrap.Modal.getInstance(document.getElementById('testGatewayModal')); if (m) m.hide(); } })
                .catch(err => { console.error(err); alert('Test failed'); });
        }
    });

})();

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    // Silence verbose logs unless ADMIN_DEBUG

    // Initialize AdminPanel if available
    if (typeof AdminPanel !== 'undefined') {
        if (window.ADMIN_DEBUG) {
            console.log('AdminPanel class found, initializing...');
        }
        const adminPanel = AdminPanel.getInstance();

        // Make instance globally available
        window.adminPanel = adminPanel;

        // Initialize page-specific functionality
        const currentPage = document.body.getAttribute('data-page');
        if (currentPage) {
            if (window.ADMIN_DEBUG) {
                console.log('Current page:', currentPage);
            }

            // Page-specific initialization can be added here
            switch (currentPage) {
                case 'currencies':
                    if (window.ADMIN_DEBUG) {
                        console.log('Initializing currencies page functionality');
                    }
                    break;
                case 'users':
                    if (window.ADMIN_DEBUG) {
                        console.log('Initializing users page functionality');
                    }
                    break;
                default:
                    if (window.ADMIN_DEBUG) {
                        console.log('No specific initialization for this page');
                    }
            }
        }

        if (window.ADMIN_DEBUG) {
            console.log('AdminPanel fully initialized and ready');
        }
    } else {
        if (window.ADMIN_DEBUG) {
            console.warn('AdminPanel class not found, using fallback...');
        }
        AdminPanel.initializeFallback();
    }
});

// Backup loading screen handler
window.addEventListener('load', function () {
    const loadingScreen = document.getElementById('loading-screen');
    if (loadingScreen && loadingScreen.style.display !== 'none') {
        loadingScreen.style.opacity = '0';
        setTimeout(() => {
            loadingScreen.style.display = 'none';
        }, 300);
    }
});

// Global helper functions for backward compatibility
window.showNotification = (message, type = 'info') => {
    AdminPanel.getInstance().showNotification(message, type);
};

window.toggleCurrencyStatus = (currencyId, currentStatus) => {
    AdminPanel.getInstance().toggleCurrencyStatus(currencyId, currentStatus);
};

window.setDefaultCurrency = (currencyId) => {
    AdminPanel.getInstance().setDefaultCurrency(currencyId);
};

window.setButtonLoading = (button, loading) => {
    AdminPanel.getInstance().setButtonLoading(button, loading);
};

window.showAlert = (message, type, duration) => {
    AdminPanel.getInstance().showAlert(message, type, duration);
};

window.validateForm = (form) => {
    return AdminPanel.getInstance().validateForm(form);
};

window.makeAjaxRequest = (url, options) => {
    return AdminPanel.getInstance().makeAjaxRequest(url, options);
};

window.initializeDataTable = (table) => {
    AdminPanel.getInstance().initializeDataTable(table);
};

window.initializeTooltips = () => {
    AdminPanel.getInstance().initializeTooltips();
};

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdminPanel;
}

// Make available globally
window.AdminPanel = AdminPanel;

// --- Payment Gateways Management behaviors (moved from Blade) ---
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const root = document.getElementById('pgMgmtRoot');
        if (!root) return;

        const gatewaysMap = JSON.parse(root.getAttribute('data-gateways') || '{}');
        const syncUrl = root.getAttribute('data-sync-url');
        const testBase = root.getAttribute('data-test-base');
        const toggleBase = root.getAttribute('data-toggle-base');

        function ajaxPost(url, data, okCb, errCb) {
            const headers = { 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content };
            if (window.jQuery) {
                window.jQuery.ajax({ url: url, method: 'POST', data: data || {}, headers: headers, success: okCb, error: function (xhr) { let body = xhr.responseText || ''; try { body = JSON.parse(body); } catch (e) { }; (errCb || console.error)(body || xhr.statusText || 'Error'); } });
                return;
            }

            fetch(url, { method: 'POST', headers: Object.assign({ 'Content-Type': 'application/json' }, headers), body: JSON.stringify(data || {}) })
                .then(r => {
                    const contentType = r.headers.get('content-type') || '';
                    if (!r.ok) {
                        // try to parse json error body
                        if (contentType.indexOf('application/json') !== -1) {
                            return r.json().then(j => Promise.reject(j));
                        }
                        return r.text().then(t => Promise.reject({ message: t || r.statusText }));
                    }
                    if (contentType.indexOf('application/json') !== -1) return r.json();
                    return r.text().then(t => ({ success: true, text: t }));
                })
                .then(okCb)
                .catch(err => { (errCb || console.error)(err); });
        }

        function getGatewayIdByName(n) {
            if (!n) return null;
            if (gatewaysMap[n]) return gatewaysMap[n];
            // try case-insensitive
            const key = Object.keys(gatewaysMap).find(k => k && k.toLowerCase() === (n || '').toLowerCase());
            if (key) return gatewaysMap[key];
            // maybe n is already an id
            if (/^\d+$/.test(n)) return n;
            return null;
        }

        document.addEventListener('click', function (e) {
            const t = e.target.closest('[data-action]');
            if (!t) return;
            const act = t.getAttribute('data-action');

            if (act === 'sync-gateways') {
                ajaxPost(syncUrl, {}, function (r) { if (r.success) { toastr.success(r.message); location.reload(); } else { toastr.error(r.message); } }, function () { toastr.error(root.getAttribute('data-translate-sync-failed') || 'Failed to sync'); });
            }

            if (act === 'test-gateway') {
                const currentGateway = t.getAttribute('data-gateway');
                const modal = document.getElementById('testGatewayModal');
                if (window.jQuery) jQuery(modal).modal('show');
                const results = document.getElementById('testResults'); if (results) results.style.display = 'none';
                modal && modal.setAttribute('data-selected-gateway', currentGateway);
            }

            if (act === 'run-gateway-test') {
                const modal = document.getElementById('testGatewayModal');
                const currentGateway = modal && modal.getAttribute('data-selected-gateway');
                const gid = getGatewayIdByName(currentGateway);
                const amtEl = document.getElementById('testAmount');
                const amt = amtEl ? amtEl.value : '10.00';
                const results = document.getElementById('testResults');
                if (!gid) { toastr.error(root.getAttribute('data-translate-gateway-not-found') || 'Gateway not found'); return; }
                if (results) results.innerHTML = `<div class="text-center"><i class="fas fa-spinner fa-spin"></i> ${root.getAttribute('data-translate-testing')}</div>`, results.style.display = 'block';
                ajaxPost(`${testBase}/${gid}/test-connection`, { test_amount: amt }, function (response) {
                    try {
                        if (response && response.success) {
                            if (results) results.innerHTML = `<div class="alert alert-success"><h6><i class="fas fa-check-circle"></i> ${root.getAttribute('data-translate-test-success')}</h6><p><strong>Gateway:</strong> ${response.gateway || gid}</p><p><strong>Response Time:</strong> ${response.response_time || 'n/a'}ms</p><p><strong>Status:</strong> ${response.config_status || 'OK'}</p></div>`;
                        } else {
                            const msg = response && (response.message || response.error || JSON.stringify(response)) || 'Failed';
                            if (results) results.innerHTML = `<div class="alert alert-danger"><h6><i class="fas fa-exclamation-triangle"></i> ${root.getAttribute('data-translate-test-failed')}</h6><p>${msg}</p></div>`;
                        }
                    } catch (ex) {
                        if (results) results.innerHTML = `<div class="alert alert-danger"><h6><i class="fas fa-exclamation-triangle"></i> ${root.getAttribute('data-translate-test-failed')}</h6><p>An unexpected error occurred</p></div>`;
                        console.error(ex);
                    }
                }, function (err) { if (results) results.innerHTML = `<div class="alert alert-danger"><h6><i class="fas fa-exclamation-triangle"></i> ${root.getAttribute('data-translate-test-failed')}</h6><p>${(err && err.message) || 'An error occurred during testing'}</p></div>`; });
            }

            if (act === 'view-analytics') {
                const id = getGatewayIdByName(t.getAttribute('data-gateway')); if (id) window.open(`${testBase}/${id}/analytics`, '_blank');
            }

            if (act === 'toggle-gateway') {
                const gid = t.getAttribute('data-id');
                ajaxPost(`${toggleBase}/${gid}/toggle`, {}, function (resp) { try { if (resp && resp.success) { toastr.success(resp.message || 'Gateway status updated'); location.reload(); } else { toastr.error(resp && resp.message ? resp.message : 'Failed to update gateway status'); } } catch (ex) { console.error(ex); toastr.error('Failed to update gateway status'); } }, function (err) { console.error(err); toastr.error((err && err.message) || 'Failed to update gateway status'); });
            }

            if (act === 'test-all-gateways') { toastr.info('Testing all gateways...'); }
            if (act === 'generate-report') { toastr.info('Generating report...'); }
            if (act === 'view-logs') { window.open('/admin/logs', '_blank'); }
            if (act === 'refresh-performance-data') { location.reload(); }
            if (act === 'export-performance-report') { toastr.info('Exporting performance report...'); }
            if (act === 'view-transaction') { const id = t.getAttribute('data-id'); if (id) window.open(`/admin/payments/${id}`, '_blank'); }
        });

        if (window.jQuery && jQuery.fn.DataTable) {
            jQuery('#performanceTable').DataTable({ pageLength: 10, ordering: true, searching: false, info: false, paging: false });
            jQuery('#transactionsTable').DataTable({ pageLength: 10, ordering: true, searching: true });
        }
    });
})();