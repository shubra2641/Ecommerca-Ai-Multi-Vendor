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
        const badge = doc.querySelector('[data-compare_count]');
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

    function initCheckoutShipping() {
        const checkoutRoot = doc.getElementById('checkout-root');
        const translations = {
            selectGovernorate: checkoutRoot?.dataset.selectGovernorate || 'Select Governorate',
            selectCity: checkoutRoot?.dataset.selectCity || 'Select City',
            selectShippingCompany: checkoutRoot?.dataset.selectShippingCompany || 'Select Shipping Company'
        };
        const currencySymbol = checkoutRoot?.dataset.currencySymbol;
        const baseTotal = parseFloat(checkoutRoot?.dataset.baseTotal || '0') || 0;

        // Get DOM elements
        const countrySelect = doc.getElementById('country-select');
        const governorateSelect = doc.getElementById('governorate-select');
        const citySelect = doc.getElementById('city-select');
        const shippingZoneSelect = doc.getElementById('shipping-zone-select');
        const shippingCostDisplay = doc.getElementById('shipping-cost-display');
        const shippingCompanyDisplay = doc.getElementById('shipping-company-display');
        const shippingCostInput = doc.getElementById('shipping-cost-input');
        const shippingDaysInput = doc.getElementById('shipping-days-input');
        const shippingDaysDiv = doc.getElementById('shipping-days');
        const shippingDaysDisplay = doc.getElementById('shipping-days-display');
        const shippingInfo = doc.getElementById('shipping-info');

        // Hidden form inputs for server submission
        const hiddenShippingZoneId = doc.getElementById('input-shipping-zone-id');
        const hiddenShippingPrice = doc.getElementById('input-shipping-price');
        const hiddenShippingDays = doc.getElementById('shipping-days-input');

        if (!countrySelect || !governorateSelect || !citySelect) {
            return;
        }

        function clearSelect(select, placeholder) {
            select.innerHTML = `<option value="">${placeholder}</option>`;
        }

        function populateSelect(select, options, selectedValue = null) {
            select.innerHTML = `<option value="">${select.querySelector('option').textContent}</option>`;
            options.forEach(option => {
                const opt = doc.createElement('option');
                opt.value = option.id;
                opt.textContent = option.name;
                if (selectedValue && option.id == selectedValue) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });
        }

        function loadGovernorates(countryId) {
            if (!countryId) {
                clearSelect(governorateSelect, translations.selectGovernorate || 'Select Governorate');
                clearSelect(citySelect, translations.selectCity || 'Select City');
                clearSelect(shippingZoneSelect, translations.selectShippingCompany || 'Select Shipping Company');
                hideShippingInfo();
                return;
            }

            fetch(`/api/locations/governorates?country=${countryId}`)
                .then(response => response.json())
                .then(data => {
                    populateSelect(governorateSelect, data.data);
                    clearSelect(citySelect, translations.selectCity || 'Select City');
                    clearSelect(shippingZoneSelect, translations.selectShippingCompany || 'Select Shipping Company');
                    hideShippingInfo();
                })
                .catch(error => console.error('Error loading governorates:', error));
        }

        function loadCities(governorateId) {
            if (!governorateId) {
                clearSelect(citySelect, translations.selectCity || 'Select City');
                clearSelect(shippingZoneSelect, translations.selectShippingCompany || 'Select Shipping Company');
                hideShippingInfo();
                return;
            }

            fetch(`/api/locations/cities?governorate=${governorateId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.data.length > 0) {
                        populateSelect(citySelect, data.data);
                        clearSelect(shippingZoneSelect, translations.selectShippingCompany || 'Select Shipping Company');
                        hideShippingInfo();
                    } else {
                        // No cities, load shipping directly for governorate
                        clearSelect(citySelect, translations.selectCity || 'Select City');
                        loadShippingOptions(countrySelect.value, governorateId, null);
                    }
                })
                .catch(error => console.error('Error loading cities:', error));
        }

        function loadShippingOptions(countryId, governorateId = null, cityId = null) {
            if (!countryId) {
                hideShippingInfo();
                return;
            }

            const params = new URLSearchParams({ country: countryId });
            if (governorateId) params.append('governorate', governorateId);
            if (cityId) params.append('city', cityId);

            fetch(`/api/locations/shipping?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.data.length === 0) {
                        clearSelect(shippingZoneSelect, translations.selectShippingCompany || 'Select Shipping Company');
                        hideShippingInfo();
                        return;
                    }

                    populateSelect(shippingZoneSelect, data.data.map(item => ({
                        id: item.zone_id,
                        name: `${item.company_name} - ${currencySymbol}${parseFloat(item.price).toFixed(2)}`
                    })));

                    // Auto-select if only one option
                    if (data.data.length === 1) {
                        shippingZoneSelect.value = data.data[0].zone_id;
                        updateShippingDisplay(data.data[0]);
                    } else {
                        hideShippingInfo();
                    }
                })
                .catch(error => console.error('Error loading shipping options:', error));
        }

        function updateShippingDisplay(shippingOption) {
            if (!shippingOption) {
                hideShippingInfo();
                return;
            }

            shippingCostDisplay.textContent = `${currencySymbol}${parseFloat(shippingOption.price).toFixed(2)}`;
            shippingCompanyDisplay.textContent = shippingOption.zone_name;
            shippingCostInput.value = shippingOption.price;
            shippingDaysInput.value = shippingOption.estimated_days || '';

            if (shippingOption.estimated_days) {
                shippingDaysDisplay.textContent = shippingOption.estimated_days;
                shippingDaysDiv.classList.remove('hidden');
            } else {
                shippingDaysDiv.classList.add('hidden');
            }

            shippingInfo.classList.remove('hidden');

            // Update hidden form inputs for server submission
            if (hiddenShippingZoneId) {
                hiddenShippingZoneId.value = shippingOption.zone_id;
            }
            if (hiddenShippingPrice) {
                hiddenShippingPrice.value = shippingOption.price;
            }
            if (hiddenShippingDays) {
                hiddenShippingDays.value = shippingOption.estimated_days || '';
            }

            // Update order summary
            updateOrderSummary(shippingOption.price);
        }

        function hideShippingInfo() {
            shippingInfo.classList.add('hidden');
            shippingCostInput.value = '';
            shippingDaysInput.value = '';

            // Clear hidden form inputs
            if (hiddenShippingZoneId) hiddenShippingZoneId.value = '';
            if (hiddenShippingPrice) hiddenShippingPrice.value = '';
            if (hiddenShippingDays) hiddenShippingDays.value = '';

            updateOrderSummary(0);
        }

        function updateOrderSummary(shippingCost) {
            const shippingAmount = doc.querySelector('.shipping-amount');
            if (shippingAmount) {
                shippingAmount.textContent = shippingCost > 0 ? `${currencySymbol}${parseFloat(shippingCost).toFixed(2)}` : '-';
            }

            // Update total
            const newTotal = baseTotal + parseFloat(shippingCost || 0);
            const orderTotal = doc.querySelector('.order-total');
            if (orderTotal) {
                orderTotal.textContent = `${currencySymbol}${newTotal.toFixed(2)}`;
            }
        }

        // Event listeners
        countrySelect.addEventListener('change', function () {
            loadGovernorates(this.value);
        });

        governorateSelect.addEventListener('change', function () {
            loadCities(this.value);
        });

        citySelect.addEventListener('change', function () {
            if (this.value) {
                loadShippingOptions(countrySelect.value, governorateSelect.value, this.value);
            } else {
                // If city is cleared, load shipping for governorate
                loadShippingOptions(countrySelect.value, governorateSelect.value, null);
            }
        });

        shippingZoneSelect.addEventListener('change', function () {
            const selectedZoneId = this.value;
            if (!selectedZoneId) {
                hideShippingInfo();
                return;
            }

            // Find the selected shipping option from the current data
            const params = new URLSearchParams({ country: countrySelect.value });
            if (governorateSelect.value) params.append('governorate', governorateSelect.value);
            if (citySelect.value) params.append('city', citySelect.value);
            params.append('all', '1');

            fetch(`/api/new-shipping/quote?${params}`)
                .then(response => response.json())
                .then(data => {
                    const selectedOption = data.data.find(item => item.zone_id == selectedZoneId);
                    if (selectedOption) {
                        updateShippingDisplay(selectedOption);
                    }
                })
                .catch(error => console.error('Error updating shipping display:', error));
        });

        // Initialize with current values if any
        if (countrySelect.value) {
            loadGovernorates(countrySelect.value);
        }
    }

    function initAddressSelection() {
        const addressRadios = doc.querySelectorAll('input[name="selected_address"]');
        const selectedAddressIdHidden = doc.getElementById('selected-address-id');
        const customerNameInput = doc.getElementById('customer_name');
        const customerEmailInput = doc.getElementById('customer_email');
        const customerPhoneInput = doc.getElementById('customer_phone');
        const customerAddressInput = doc.getElementById('customer_address');
        const countrySelect = doc.getElementById('country-select');
        const governorateSelect = doc.getElementById('governorate-select');
        const citySelect = doc.getElementById('city-select');

        // Translations (assuming they are available)
        const translations = {
            selectGovernorate: 'Select Governorate',
            selectCity: 'Select City',
            selectShippingCompany: 'Select Shipping Company'
        };

        const currencySymbol = doc.getElementById('checkout-root')?.dataset.currencySymbol;

        // Helper functions
        function clearSelect(select, placeholder) {
            select.innerHTML = `<option value="">${placeholder}</option>`;
        }

        function populateSelect(select, options, selectedValue = null) {
            select.innerHTML = `<option value="">${select.querySelector('option').textContent}</option>`;
            options.forEach(option => {
                const opt = doc.createElement('option');
                opt.value = option.id;
                opt.textContent = option.name;
                if (selectedValue && option.id == selectedValue) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });
        }

        function hideShippingInfo() {
            const shippingInfo = doc.getElementById('shipping-info');
            const shippingCostInput = doc.getElementById('shipping-cost-input');
            const shippingDaysInput = doc.getElementById('shipping-days-input');
            const hiddenShippingZoneId = doc.getElementById('input-shipping-zone-id');
            const hiddenShippingPrice = doc.getElementById('input-shipping-price');
            const hiddenShippingDays = doc.getElementById('shipping-days-input');

            if (shippingInfo) shippingInfo.classList.add('hidden');
            if (shippingCostInput) shippingCostInput.value = '';
            if (shippingDaysInput) shippingDaysInput.value = '';

            // Clear hidden form inputs
            if (hiddenShippingZoneId) hiddenShippingZoneId.value = '';
            if (hiddenShippingPrice) hiddenShippingPrice.value = '';
            if (hiddenShippingDays) hiddenShippingDays.value = '';
        }

        async function selectAddress(radio) {
            const label = radio.closest('label');
            const addrId = label.dataset.addrId;
            const country = label.dataset.country;
            const governorate = label.dataset.governorate;
            const city = label.dataset.city;
            const line1 = label.dataset.line1;
            const phone = label.dataset.phone;
            const name = label.querySelector('.address-name')?.textContent || '';

            // Set hidden selected_address_id
            if (selectedAddressIdHidden) {
                selectedAddressIdHidden.value = addrId;
            }

            // Populate form fields
            if (customerNameInput) customerNameInput.value = name;
            if (customerEmailInput) customerEmailInput.value = doc.querySelector('meta[name="user-email"]')?.content || '';
            if (customerPhoneInput) customerPhoneInput.value = phone;
            if (customerAddressInput) customerAddressInput.value = line1;

            // Set country and load governorates
            if (countrySelect) {
                countrySelect.value = country;
                await loadGovernoratesAsync(country);
            }

            // Set governorate and load cities
            if (governorateSelect) {
                governorateSelect.value = governorate;
                await loadCitiesAsync(governorate);
            }

            // Set city and load shipping
            if (citySelect) {
                citySelect.value = city;
                if (citySelect.value) {
                    await loadShippingOptionsAsync(countrySelect.value, governorateSelect.value, citySelect.value);
                } else {
                    await loadShippingOptionsAsync(countrySelect.value, governorateSelect.value, null);
                }
            }
        }

        // Async versions of the load functions
        async function loadGovernoratesAsync(countryId) {
            if (!countryId) {
                if (governorateSelect) clearSelect(governorateSelect, translations.selectGovernorate || 'Select Governorate');
                if (citySelect) clearSelect(citySelect, translations.selectCity || 'Select City');
                const shippingZoneSelect = doc.getElementById('shipping-zone-select');
                if (shippingZoneSelect) clearSelect(shippingZoneSelect, translations.selectShippingCompany || 'Select Shipping Company');
                hideShippingInfo();
                return;
            }

            try {
                const response = await fetch(`/api/locations/governorates?country=${countryId}`);
                const data = await response.json();
                if (governorateSelect) populateSelect(governorateSelect, data.data);
                if (citySelect) clearSelect(citySelect, translations.selectCity || 'Select City');
                const shippingZoneSelect = doc.getElementById('shipping-zone-select');
                if (shippingZoneSelect) clearSelect(shippingZoneSelect, translations.selectShippingCompany || 'Select Shipping Company');
                hideShippingInfo();
            } catch (error) {
                console.error('Error loading governorates:', error);
            }
        }

        async function loadCitiesAsync(governorateId) {
            if (!governorateId) {
                if (citySelect) clearSelect(citySelect, translations.selectCity || 'Select City');
                const shippingZoneSelect = doc.getElementById('shipping-zone-select');
                if (shippingZoneSelect) clearSelect(shippingZoneSelect, translations.selectShippingCompany || 'Select Shipping Company');
                hideShippingInfo();
                return;
            }

            try {
                const response = await fetch(`/api/locations/cities?governorate=${governorateId}`);
                const data = await response.json();
                if (data.data.length > 0) {
                    if (citySelect) populateSelect(citySelect, data.data);
                    const shippingZoneSelect = doc.getElementById('shipping-zone-select');
                    if (shippingZoneSelect) clearSelect(shippingZoneSelect, translations.selectShippingCompany || 'Select Shipping Company');
                    hideShippingInfo();
                } else {
                    // No cities, load shipping directly for governorate
                    if (citySelect) clearSelect(citySelect, translations.selectCity || 'Select City');
                }
            } catch (error) {
                console.error('Error loading cities:', error);
            }
        }

        async function loadShippingOptionsAsync(countryId, governorateId = null, cityId = null) {
            if (!countryId) {
                hideShippingInfo();
                return;
            }

            const params = new URLSearchParams({ country: countryId });
            if (governorateId) params.append('governorate', governorateId);
            if (cityId) params.append('city', cityId);
            params.append('all', '1');

            try {
                const response = await fetch(`/api/new-shipping/quote?${params}`);
                const data = await response.json();
                if (data.data.length === 0) {
                    const shippingZoneSelect = doc.getElementById('shipping-zone-select');
                    if (shippingZoneSelect) clearSelect(shippingZoneSelect, translations.selectShippingCompany || 'Select Shipping Company');
                    hideShippingInfo();
                    return;
                }

                const shippingZoneSelect = doc.getElementById('shipping-zone-select');
                if (shippingZoneSelect) {
                    populateSelect(shippingZoneSelect, data.data.map(item => ({
                        id: item.zone_id,
                        name: `${item.zone_name} - ${currencySymbol}${parseFloat(item.price).toFixed(2)}`
                    })));
                }

                // Populate shipping options without auto-selecting
                // Let user choose manually
            } catch (error) {
                console.error('Error loading shipping options:', error);
            }
        }

        function updateShippingDisplay(shippingOption) {
            const shippingCostDisplay = doc.getElementById('shipping-cost-display');
            const shippingCompanyDisplay = doc.getElementById('shipping-company-display');
            const shippingCostInput = doc.getElementById('shipping-cost-input');
            const shippingDaysInput = doc.getElementById('shipping-days-input');
            const shippingDaysDiv = doc.getElementById('shipping-days');
            const shippingDaysDisplay = doc.getElementById('shipping-days-display');
            const shippingInfo = doc.getElementById('shipping-info');
            const hiddenShippingZoneId = doc.getElementById('input-shipping-zone-id');
            const hiddenShippingPrice = doc.getElementById('input-shipping-price');
            const hiddenShippingDays = doc.getElementById('shipping-days-input');

            if (!shippingOption) {
                hideShippingInfo();
                return;
            }

            if (shippingCostDisplay) shippingCostDisplay.textContent = `${currencySymbol}${parseFloat(shippingOption.price).toFixed(2)}`;
            if (shippingCompanyDisplay) shippingCompanyDisplay.textContent = shippingOption.zone_name;
            if (shippingCostInput) shippingCostInput.value = shippingOption.price;
            if (shippingDaysInput) shippingDaysInput.value = shippingOption.estimated_days || '';

            if (shippingOption.estimated_days) {
                if (shippingDaysDisplay) shippingDaysDisplay.textContent = shippingOption.estimated_days;
                if (shippingDaysDiv) shippingDaysDiv.classList.remove('hidden');
            } else {
                if (shippingDaysDiv) shippingDaysDiv.classList.add('hidden');
            }

            if (shippingInfo) shippingInfo.classList.remove('hidden');

            // Update hidden form inputs for server submission
            if (hiddenShippingZoneId) {
                hiddenShippingZoneId.value = shippingOption.zone_id;
            }
            if (hiddenShippingPrice) {
                hiddenShippingPrice.value = shippingOption.price;
            }
            if (hiddenShippingDays) {
                hiddenShippingDays.value = shippingOption.estimated_days || '';
            }
        }

        addressRadios.forEach(radio => {
            radio.addEventListener('change', function () {
                if (this.checked) {
                    selectAddress(this);
                }
            });
        });

        // Handle initial checked address
        const initialChecked = doc.querySelector('input[name="selected_address"]:checked');
        if (initialChecked) {
            selectAddress(initialChecked);
        }
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
            initCheckoutShipping();
            initAddressSelection();
        });
    } else {
        initDropdowns();
        initCurrencySwitch();
        initCompareBadge();
        initLoader();
        initHeroSlider();
        initQuantitySelector();
        initProductVariations();
        initCheckoutShipping();
        initAddressSelection();
    }
}());


