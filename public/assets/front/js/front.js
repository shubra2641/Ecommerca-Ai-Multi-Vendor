// Header dropdown & currency interactions
(function () {
    const doc = document;
    function closeAll(except) {
        doc.querySelectorAll('[data-dropdown].open').forEach(d => {
            if (d !== except) {
                d.classList.remove('open');
            }
        });
    }
    function initDropdowns() {
        doc.querySelectorAll('[data-dropdown]').forEach(wrapper => {
            const trigger = wrapper.querySelector('.dropdown-trigger');
            const panel = wrapper.querySelector('.dropdown-panel');
            if (!trigger || !panel) {
                return;
            }
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const open = wrapper.classList.toggle('open');
                trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (open) {
                    closeAll(wrapper); panel.querySelector('button, a, input, [tabindex]')?.focus?.();
                }
            });
            // keyboard navigation / escape
            wrapper.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    wrapper.classList.remove('open'); trigger.setAttribute('aria-expanded', 'false'); trigger.focus();
                }
            });
        });
        doc.addEventListener('click', (e) => {
            if (!e.target.closest('[data-dropdown]')) {
                closeAll();
            }
        });
    }
    function initCurrencySwitch() {
        doc.addEventListener('click', async (e) => {
            const btn = e.target.closest('.currency-chip');
            if (!btn) {
                return;
            }
            const code = btn.dataset.currency; if (!code) {
                return;
            }
            try {
                const res = await fetch('/currency/switch', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ code })
                });
                if (res.ok) {
                    location.reload();
                }
            } catch (err) {
                console.error('Error applying coupon:', err);
            }
        });
    }
    // Simple compare badge updater placeholder (other scripts can dispatch window.dispatchEvent(new CustomEvent('compare:update',{detail:{count:n}})))
    function initCompareBadge() {
        const badge = doc.querySelector('[data-compare-count]');
        if (!badge) {
            return;
        }
        window.addEventListener('compare:update', (e) => {
            const c = e.detail && typeof e.detail.count === 'number' ? e.detail.count : 0;
            badge.textContent = c;
            if (c > 0) {
                badge.classList.add('show');
            } else {
                badge.classList.remove('show');
            }
        });
    }
    function initLoader() {
        const loader = doc.getElementById('app-loader');
        if (!loader) {
            return;
        }

        // Hide loader when page is fully loaded
        function hideLoader() {
            loader.classList.add('hidden');
            loader.setAttribute('aria-hidden', 'true');
        }

        // Hide loader immediately if page is already loaded
        if (document.readyState === 'complete') {
            hideLoader();
        } else {
            // Hide loader when page finishes loading
            window.addEventListener('load', hideLoader);

            // Fallback: hide loader after 3 seconds maximum
            const LOADER_TIMEOUT = 3000;
            setTimeout(hideLoader, LOADER_TIMEOUT);
        }
    }

    function initHeroSlider() {
        const slider = doc.querySelector('.hero-slider');
        if (!slider) { return; }

        const track = slider.querySelector('[data-hero-slider-track]');
        const prevBtn = slider.querySelector('[data-hero-prev]');
        const nextBtn = slider.querySelector('[data-hero-next]');
        const dots = slider.querySelector('[data-hero-dots]');
        const slides = slider.querySelectorAll('.hero-slide');

        if (!track || slides.length <= 1) { return; }

        let currentSlide = 0;
        const totalSlides = slides.length;

        // Show navigation
        if (prevBtn) { prevBtn.hidden = false; }
        if (nextBtn) { nextBtn.hidden = false; }
        if (dots) { dots.hidden = false; }

        // Create dots
        if (dots) {
            for (let i = 0; i < totalSlides; i++) {
                const dot = doc.createElement('button');
                dot.type = 'button';
                dot.className = i === 0 ? 'active' : '';
                dot.addEventListener('click', () => goToSlide(i));
                dots.appendChild(dot);
            }
        }

        function goToSlide(index) {
            currentSlide = index;
            track.scrollLeft = index * track.offsetWidth;
            updateDots();
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            goToSlide(currentSlide);
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            goToSlide(currentSlide);
        }

        function updateDots() {
            if (dots) {
                const dotElements = dots.querySelectorAll('button');
                dotElements.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentSlide);
                });
            }
        }

        // Event listeners
        if (nextBtn) { nextBtn.addEventListener('click', nextSlide); }
        if (prevBtn) { prevBtn.addEventListener('click', prevSlide); }

        // Auto-play
        const AUTO_PLAY_INTERVAL = 5000;
        let autoPlay = setInterval(nextSlide, AUTO_PLAY_INTERVAL);

        slider.addEventListener('mouseenter', () => clearInterval(autoPlay));
        slider.addEventListener('mouseleave', () => {
            autoPlay = setInterval(nextSlide, AUTO_PLAY_INTERVAL);
        });
    }


    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initDropdowns();
            initCurrencySwitch();
            initCompareBadge();
            initLoader();
            initHeroSlider();
        });
    } else {
        initDropdowns();
        initCurrencySwitch();
        initCompareBadge();
        initLoader();
        initHeroSlider();
    }
}());


