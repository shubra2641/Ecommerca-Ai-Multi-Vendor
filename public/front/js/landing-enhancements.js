/**
 * Landing Page Progressive Enhancement
 * Provides enhanced functionality while maintaining graceful degradation
 */

(function () {
    'use strict';

    // Check if JavaScript is enabled and add class to body
    document.documentElement.classList.add('js-enabled');
    document.documentElement.classList.remove('no-js');

    // Utility functions
    const utils = {
        // Debounce function for performance
        debounce: function (func, wait) {
            let timeout;
            return function executedFunction(...args)
            {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        // Check if element is in viewport
        isInViewport: function (element) {
            const rect = element.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        },

        // Smooth scroll to element
        smoothScrollTo: function (target) {
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    };

    // Animation Controller
    const AnimationController = {
        init: function () {
            this.setupIntersectionObserver();
            this.handleReducedMotion();
        },

        setupIntersectionObserver: function () {
            // Only run if IntersectionObserver is supported
            if (!('IntersectionObserver' in window)) {
                // Fallback: show all animations immediately
                document.querySelectorAll('[class*="animate-"]').forEach(el => {
                    el.style.opacity = '1';
                    el.style.transform = 'none';
                });
                return;
            }

            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe all animated elements
            document.querySelectorAll('[class*="animate-"]').forEach(el => {
                observer.observe(el);
            });
        },

        handleReducedMotion: function () {
            // Respect user's motion preferences
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                document.documentElement.classList.add('reduced-motion');
            }
        }
    };

    // Enhanced Navigation
    const Navigation = {
        init: function () {
            this.setupSmoothScrolling();
            this.setupActiveNavigation();
        },

        setupSmoothScrolling: function () {
            // Enhanced smooth scrolling for anchor links
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[href^="#"]');
                if (link && link.getAttribute('href') !== '#') {
                    e.preventDefault();
                    const targetId = link.getAttribute('href').substring(1);
                    const target = document.getElementById(targetId);

                    if (target) {
                        utils.smoothScrollTo(target);

                        // Update URL without jumping
                        if (history.pushState) {
                            history.pushState(null, null, `#${targetId}`);
                        }
                    }
                }
            });
        },

        setupActiveNavigation: function () {
            // Highlight active section in navigation
            const sections = document.querySelectorAll('section[id]');
            if (sections.length === 0) {
                return;
            }

            const updateActiveNav = utils.debounce(() => {
                let current = '';
                sections.forEach(section => {
                    const sectionTop = section.offsetTop - 100;
                    if (window.pageYOffset >= sectionTop) {
                        current = section.getAttribute('id');
                    }
                });

                // Update navigation links
                document.querySelectorAll('a[href^="#"]').forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${current}`) {
                        link.classList.add('active');
                    }
                });
            }, 100);

            window.addEventListener('scroll', updateActiveNav);
        }
    };

    // Enhanced Cards
    const CardEnhancements = {
        init: function () {
            this.setupHoverEffects();
            this.setupKeyboardNavigation();
            this.setupLoadingStates();
        },

        setupHoverEffects: function () {
            // Enhanced hover effects for cards
            const cards = document.querySelectorAll('.feature-card, .category-card, .blog-card');

            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-8px)';
                });

                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0)';
                });
            });
        },

        setupKeyboardNavigation: function () {
            // Enhanced keyboard navigation for cards
            const cards = document.querySelectorAll('.feature-card, .category-card, .blog-card');

            cards.forEach(card => {
                const link = card.querySelector('a');
                if (link) {
                    card.setAttribute('tabindex', '0');
                    card.setAttribute('role', 'button');

                    card.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            link.click();
                        }
                    });
                }
            });
        },

        setupLoadingStates: function () {
            // Progressive image loading
            const images = document.querySelectorAll('img[loading="lazy"]');

            images.forEach(img => {
                img.addEventListener('load', () => {
                    img.classList.add('loaded');
                });

                img.addEventListener('error', () => {
                    img.classList.add('error');
                    // Fallback to placeholder if image fails to load
                    const placeholder = img.parentNode.querySelector('.category-placeholder, .blog-placeholder');
                    if (placeholder) {
                        placeholder.style.display = 'flex';
                        img.style.display = 'none';
                    }
                });
            });
        }
    };

    // Performance Optimizations
    const Performance = {
        init: function () {
            this.setupLazyLoading();
            this.setupPreloading();
            this.optimizeAnimations();
        },

        setupLazyLoading: function () {
            // Enhanced lazy loading for images
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                            }
                            imageObserver.unobserve(img);
                        }
                    });
                });

                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }
        },

        setupPreloading: function () {
            // Preload critical resources
            const criticalLinks = document.querySelectorAll('a[href]:not([href^="#"])');
            const preloadedUrls = new Set();

            criticalLinks.forEach(link => {
                link.addEventListener('mouseenter', () => {
                    const url = link.href;
                    if (!preloadedUrls.has(url)) {
                        const linkEl = document.createElement('link');
                        linkEl.rel = 'prefetch';
                        linkEl.href = url;
                        document.head.appendChild(linkEl);
                        preloadedUrls.add(url);
                    }
                });
            });
        },

        optimizeAnimations: function () {
            // Use requestAnimationFrame for smooth animations
            let ticking = false;

            const updateAnimations = () => {
                // Update any ongoing animations here
                ticking = false;
            };

            const requestTick = () => {
                if (!ticking) {
                    requestAnimationFrame(updateAnimations);
                    ticking = true;
                }
            };

            window.addEventListener('scroll', requestTick);
        }
    };

    // Accessibility Enhancements
    const Accessibility = {
        init: function () {
            this.setupFocusManagement();
            this.setupAriaLabels();
            this.setupKeyboardShortcuts();
        },

        setupFocusManagement: function () {
            // Enhanced focus management
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    document.body.classList.add('keyboard-navigation');
                }
            });

            document.addEventListener('mousedown', () => {
                document.body.classList.remove('keyboard-navigation');
            });
        },

        setupAriaLabels: function () {
            // Dynamic ARIA labels for better screen reader support
            const buttons = document.querySelectorAll('.btn, .category-link, .blog-link');

            buttons.forEach(button => {
                if (!button.getAttribute('aria-label')) {
                    const text = button.textContent.trim();
                    if (text) {
                        button.setAttribute('aria-label', text);
                    }
                }
            });
        },

        setupKeyboardShortcuts: function () {
            // Keyboard shortcuts for better navigation
            document.addEventListener('keydown', (e) => {
                // Alt + H: Go to hero section
                if (e.altKey && e.key === 'h') {
                    e.preventDefault();
                    const hero = document.querySelector('.hero-section');
                    if (hero) {
                        utils.smoothScrollTo(hero);
                    }
                }

                // Alt + F: Go to features section
                if (e.altKey && e.key === 'f') {
                    e.preventDefault();
                    const features = document.querySelector('#features');
                    if (features) {
                        utils.smoothScrollTo(features);
                    }
                }
            });
        }
    };

    // Error Handling
    const ErrorHandler = {
        init: function () {
            this.setupGlobalErrorHandling();
        },

        setupGlobalErrorHandling: function () {
            window.addEventListener('error', (e) => {
                console.warn('Landing page enhancement error:', e.error);
                // Graceful degradation - don't break the page
            });

            window.addEventListener('unhandledrejection', (e) => {
                console.warn('Landing page promise rejection:', e.reason);
                e.preventDefault();
            });
        }
    };

    // Initialize all enhancements when DOM is ready
    const init = () => {
        try {
            AnimationController.init();
            Navigation.init();
            CardEnhancements.init();
            Performance.init();
            Accessibility.init();
            ErrorHandler.init();

            // Mark as initialized
            document.documentElement.classList.add('landing-enhanced');

            console.log('Landing page enhancements initialized successfully');
        } catch (error) {
            console.warn('Error initializing landing page enhancements:', error);
            // Ensure the page still works without enhancements
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose utilities for other scripts if needed
    window.LandingEnhancements = {
        utils: utils,
        reinit: init
    };

})();

