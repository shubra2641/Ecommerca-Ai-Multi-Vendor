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
            } catch {
                // console.error('Error applying coupon');
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

        const sliderElements = getSliderElements(slider);
        if (!sliderElements) { return; }

        const sliderState = createSliderState(sliderElements);
        setupSliderNavigation(sliderElements, sliderState);
        setupAutoPlay(slider, sliderState);
    }

    function initQuantitySelector() {
        const qtyDisplay = doc.getElementById('qtyDisplay');
        const qtyInput = doc.getElementById('qtyInputSide');
        const increaseBtn = doc.querySelector('.qty-increase');
        const trashBtn = doc.querySelector('.qty-trash');

        if (qtyDisplay && qtyInput && increaseBtn && trashBtn) {
            var currentQty = 1;
            var maxStock = parseInt(qtyInput.getAttribute('max')) || 999;

            function updateQuantity(newQty) {
                currentQty = Math.max(1, Math.min(newQty, maxStock));
                qtyDisplay.textContent = currentQty;
                qtyInput.value = currentQty;
            }

            increaseBtn.addEventListener('click', function () {
                updateQuantity(currentQty + 1);
            });

            trashBtn.addEventListener('click', function () {
                updateQuantity(currentQty - 1);
            });
        }
    }

    function initQuantitySelector() {
        const qtyDisplay = doc.getElementById('qtyDisplay');
        const qtyInput = doc.getElementById('qtyInputSide');
        const increaseBtn = doc.querySelector('.qty-increase');
        const trashBtn = doc.querySelector('.qty-trash');

        if (qtyDisplay && qtyInput && increaseBtn && trashBtn) {
            var currentQty = 1;
            var maxStock = parseInt(qtyInput.getAttribute('max')) || 999;

            function updateQuantity(newQty) {
                currentQty = Math.max(1, Math.min(newQty, maxStock));
                qtyDisplay.textContent = currentQty;
                qtyInput.value = currentQty;
            }

            increaseBtn.addEventListener('click', function () {
                updateQuantity(currentQty + 1);
            });

            trashBtn.addEventListener('click', function () {
                updateQuantity(currentQty - 1);
            });
        }
    }

    function getSliderElements(slider) {
        const track = slider.querySelector('[data-hero-slider-track]');
        const prevBtn = slider.querySelector('[data-hero-prev]');
        const nextBtn = slider.querySelector('[data-hero-next]');
        const dots = slider.querySelector('[data-hero-dots]');
        const slides = slider.querySelectorAll('.hero-slide');

        if (!track || slides.length <= 1) { return null; }

        return {
            track,
            prevBtn,
            nextBtn,
            dots,
            slides
        };
    }

    function createSliderState(elements) {
        const state = {
            currentSlide: 0,
            totalSlides: elements.slides.length
        };

        // Show navigation
        if (elements.prevBtn) { elements.prevBtn.hidden = false; }
        if (elements.nextBtn) { elements.nextBtn.hidden = false; }
        if (elements.dots) { elements.dots.hidden = false; }

        return state;
    }

    function setupSliderNavigation(elements, state) {
        createDots(elements.dots, elements.slides.length, state);
        setupEventListeners(elements, state);
    }

    function createDots(dots, totalSlides, state) {
        if (!dots) { return; }

        for (let i = 0; i < totalSlides; i++) {
            const dot = doc.createElement('button');
            dot.type = 'button';
            dot.className = i === 0 ? 'active' : '';
            dot.addEventListener('click', () => goToSlide(i, state));
            dots.appendChild(dot);
        }
    }

    function setupEventListeners(elements, state) {
        if (elements.nextBtn) {
            elements.nextBtn.addEventListener('click', () => nextSlide(state));
        }
        if (elements.prevBtn) {
            elements.prevBtn.addEventListener('click', () => prevSlide(state));
        }
    }

    function goToSlide(index, state) {
        state.currentSlide = index;
        const track = document.querySelector('[data-hero-slider-track]');
        if (track) {
            track.scrollLeft = index * track.offsetWidth;
        }
        updateDots(state);
    }

    function nextSlide(state) {
        state.currentSlide = (state.currentSlide + 1) % state.totalSlides;
        goToSlide(state.currentSlide, state);
    }

    function prevSlide(state) {
        state.currentSlide = (state.currentSlide - 1 + state.totalSlides) % state.totalSlides;
        goToSlide(state.currentSlide, state);
    }

    function updateDots(state) {
        const dots = document.querySelector('[data-hero-dots]');
        if (!dots) { return; }

        const dotElements = dots.querySelectorAll('button');
        dotElements.forEach((dot, index) => {
            dot.classList.toggle('active', index === state.currentSlide);
        });
    }

    function setupAutoPlay(slider, state) {
        const AUTO_PLAY_INTERVAL = 5000;
        let autoPlay = setInterval(() => nextSlide(state), AUTO_PLAY_INTERVAL);

        slider.addEventListener('mouseenter', () => clearInterval(autoPlay));
        slider.addEventListener('mouseleave', () => {
            autoPlay = setInterval(() => nextSlide(state), AUTO_PLAY_INTERVAL);
        });
    }

    function initProductVariations() {
        const variationCard = doc.getElementById('variationGridCard');
        if (!variationCard) {
            return;
        }

        const variationsData = variationCard.dataset.variations;
        const currencySymbol = variationCard.dataset.currency;

        if (!variationsData || !currencySymbol) {
            return;
        }

        const variations = JSON.parse(variationsData);

        function updatePrice(selectedAttrs) {
            const priceElement = doc.getElementById('productPrice');
            const priceMaxElement = doc.getElementById('productPriceMax');
            const priceRangeSep = doc.querySelector('.price-range-sep');

            if (!selectedAttrs || Object.keys(selectedAttrs).length === 0) {
                // Show range for variable products
                if (priceMaxElement && priceRangeSep) {
                    priceElement.style.display = 'inline';
                    priceRangeSep.style.display = 'inline';
                    priceMaxElement.style.display = 'inline';
                }
                return;
            }

            // Find matching variation
            const matchingVariation = variations.find(v => {
                const attrData = v.attribute_data || {};
                return Object.keys(selectedAttrs).every(attr => attrData[attr] === selectedAttrs[attr]);
            });

            if (matchingVariation) {
                const displayPrice = matchingVariation.effective_price;

                // Update price display to single price
                if (priceElement) {
                    priceElement.textContent = currencySymbol + ' ' + displayPrice.toFixed(2);
                }
                if (priceMaxElement) {
                    priceMaxElement.style.display = 'none';
                }
                if (priceRangeSep) {
                    priceRangeSep.style.display = 'none';
                }

                // Update hidden price input
                const priceInput = doc.getElementById('selectedPrice');
                if (priceInput) {
                    priceInput.value = displayPrice;
                }

                // Update variation ID
                const variationInput = doc.getElementById('selectedVariationId');
                if (variationInput) {
                    variationInput.value = matchingVariation.id;
                }

                // Update sale badge if applicable
                const saleBadge = doc.getElementById('globalSaleBadge');
                if (saleBadge) {
                    if (matchingVariation.sale_price && matchingVariation.sale_price < matchingVariation.price) {
                        const discountPercent = Math.round(((matchingVariation.price - matchingVariation.sale_price) / matchingVariation.price) * 100);
                        saleBadge.textContent = discountPercent + '% Off';
                        saleBadge.style.display = 'inline-block';
                    } else {
                        saleBadge.style.display = 'none';
                    }
                }
            } else {
                // No matching variation, show range
                if (priceMaxElement && priceRangeSep) {
                    priceElement.style.display = 'inline';
                    priceRangeSep.style.display = 'inline';
                    priceMaxElement.style.display = 'inline';
                }
            }
        }

        // Listen for attribute changes
        doc.querySelectorAll('.attr-radio').forEach(radio => {
            radio.addEventListener('change', function () {
                const selectedAttrs = {};
                doc.querySelectorAll('.attr-radio:checked').forEach(checked => {
                    const attrName = checked.name.replace('attr_', '');
                    selectedAttrs[attrName] = checked.value;
                });
                updatePrice(selectedAttrs);
            });
        });

        // No initial update, start with range displayed
    }


    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initDropdowns();
            initCurrencySwitch();
            initCompareBadge();
            initLoader();
            initHeroSlider();
            initQuantitySelector();
            initProductVariations();
        });
    } else {
        initDropdowns();
        initCurrencySwitch();
        initCompareBadge();
        initLoader();
        initHeroSlider();
        initQuantitySelector();
        initProductVariations();
    }
}());


