// Header dropdown & currency interactions
(function () {
    const doc = document;
    const $ = (s) => doc.querySelector(s);
    const $$ = (s) => Array.from(doc.querySelectorAll(s));

    async function fetchJson(url) {
        try {
            const res = await fetch(url);
            return await res.json();
        } catch (err) {
            console.error('fetchJson error', url, err);
            return { data: [] };
        }
    }

    function closeAll(except) {
        $$('[data-dropdown].open').forEach(d => { if (d !== except) d.classList.remove('open'); });
    }

    function initDropdowns() {
        $$('[data-dropdown]').forEach(wrapper => {
            const trigger = wrapper.querySelector('.dropdown-trigger');
            const panel = wrapper.querySelector('.dropdown-panel');
            if (!trigger || !panel) return;

            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const isOpen = wrapper.classList.toggle('open');
                trigger.setAttribute('aria-expanded', isOpen);
                if (isOpen) {
                    closeAll(wrapper);
                    panel.querySelector('button, a, input, [tabindex]')?.focus?.();
                }
            });
        });

        doc.addEventListener('click', (e) => {
            if (!e.target.closest('[data-dropdown]')) closeAll();
        });
    }

    function initCurrencySwitch() {
        doc.addEventListener('click', async (e) => {
            const btn = e.target.closest('.currency-chip');
            if (!btn) return;
            const code = btn.dataset.currency; if (!code) return;
            try {
                const res = await fetch('/currency/switch', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]')?.content || '' },
                    body: JSON.stringify({ code })
                });
                if (res.ok) location.reload();
            } catch (err) {
                console.error('Currency switch failed', err);
            }
        });
    }

    function initCompareBadge() {
        const badge = $('[data-compare_count]');
        if (!badge) return;
        window.addEventListener('compare:update', (e) => {
            const c = e.detail && typeof e.detail.count === 'number' ? e.detail.count : 0;
            badge.textContent = c; badge.classList.toggle('show', c > 0);
        });
    }

    function initLoader() {
        const loader = $('#app-loader'); if (!loader) return;
        const hideLoader = () => { loader.classList.add('hidden'); loader.setAttribute('aria-hidden', 'true'); };
        if (document.readyState === 'complete') hideLoader(); else { window.addEventListener('load', hideLoader); setTimeout(hideLoader, 3000); }
    }

    // --- Hero slider ---
    function getSliderElements(slider) {
        const track = slider.querySelector('[data-hero-slider-track]');
        const prevBtn = slider.querySelector('[data-hero-prev]');
        const nextBtn = slider.querySelector('[data-hero-next]');
        const dots = slider.querySelector('[data-hero-dots]');
        const slides = slider.querySelectorAll('.hero-slide');
        if (!track || slides.length <= 1) return null;
        return { track, prevBtn, nextBtn, dots, slides };
    }

    function createSliderState(elements) {
        const state = { currentSlide: 0, totalSlides: elements.slides.length };
        if (elements.prevBtn) elements.prevBtn.hidden = false;
        if (elements.nextBtn) elements.nextBtn.hidden = false;
        if (elements.dots) elements.dots.hidden = false;
        return state;
    }

    function createDots(dots, totalSlides, state) {
        if (!dots) return;
        for (let i = 0; i < totalSlides; i++) {
            const dot = doc.createElement('button'); dot.type = 'button'; dot.className = i === 0 ? 'active' : ''; dot.addEventListener('click', () => goToSlide(i, state)); dots.appendChild(dot);
        }
    }

    function setupEventListeners(elements, state) {
        if (elements.nextBtn) elements.nextBtn.addEventListener('click', () => nextSlide(state));
        if (elements.prevBtn) elements.prevBtn.addEventListener('click', () => prevSlide(state));
    }

    function goToSlide(index, state) { state.currentSlide = index; const track = $('[data-hero-slider-track]'); if (track) track.scrollLeft = index * track.offsetWidth; updateDots(state); }
    function nextSlide(state) { state.currentSlide = (state.currentSlide + 1) % state.totalSlides; goToSlide(state.currentSlide, state); }
    function prevSlide(state) { state.currentSlide = (state.currentSlide - 1 + state.totalSlides) % state.totalSlides; goToSlide(state.currentSlide, state); }

    function updateDots(state) { const dots = $('[data-hero-dots]'); if (!dots) return; const dotElements = dots.querySelectorAll('button'); dotElements.forEach((dot, idx) => dot.classList.toggle('active', idx === state.currentSlide)); }

    function setupAutoPlay(slider, state) { const AUTO_PLAY_INTERVAL = 5000; let autoPlay = setInterval(() => nextSlide(state), AUTO_PLAY_INTERVAL); slider.addEventListener('mouseenter', () => clearInterval(autoPlay)); slider.addEventListener('mouseleave', () => { autoPlay = setInterval(() => nextSlide(state), AUTO_PLAY_INTERVAL); }); }

    function initHeroSlider() { const slider = $('.hero-slider'); if (!slider) return; const elements = getSliderElements(slider); if (!elements) return; const state = createSliderState(elements); createDots(elements.dots, elements.slides.length, state); setupEventListeners(elements, state); setupAutoPlay(slider, state); }

    // --- Quantity selector (single) ---
    function initQuantitySelector() {
        const qtyDisplay = $('#qtyDisplay'); const qtyInput = $('#qtyInputSide'); const increaseBtn = doc.querySelector('.qty-increase'); const trashBtn = doc.querySelector('.qty-trash');
        if (!(qtyDisplay && qtyInput && increaseBtn && trashBtn)) return;
        let currentQty = 1; const maxStock = parseInt(qtyInput.getAttribute('max')) || 999;
        const updateQuantity = (newQty) => { currentQty = Math.max(1, Math.min(newQty, maxStock)); qtyDisplay.textContent = currentQty; qtyInput.value = currentQty; };
        increaseBtn.addEventListener('click', () => updateQuantity(currentQty + 1));
        trashBtn.addEventListener('click', () => updateQuantity(currentQty - 1));
    }

    // --- Product variations ---
    function initProductVariations() {
        const variationCard = $('#variationGridCard'); if (!variationCard) return;
        const variationsData = variationCard.dataset.variations; const currencySymbol = variationCard.dataset.currency; if (!variationsData || !currencySymbol) return;
        const variations = JSON.parse(variationsData);
        const applyColorSwatches = () => $$('.option-btn.color[data-swatch], .option-btn.color-swatch[data-swatch]').forEach(btn => { const swatch = btn.dataset.swatch; if (swatch) btn.style.setProperty('background-color', swatch, 'important'); });
        applyColorSwatches();

        function updateAvailableOptions(selectedAttrs) {
            $$('.variation-attr-block').forEach(block => {
                const attrName = block.dataset.attr; const radios = block.querySelectorAll('.attr-radio');
                radios.forEach(radio => {
                    const value = radio.value; const label = radio.nextElementSibling; const testAttrs = { ...selectedAttrs, [attrName]: value };
                    const hasMatch = variations.some(v => { const attrData = v.attribute_data || {}; if (v.active === false) return false; return Object.keys(testAttrs).every(attr => attrData[attr] === testAttrs[attr]); });
                    radio.disabled = !hasMatch; if (label) { label.classList.toggle('disabled', !hasMatch); label.style.opacity = hasMatch ? '1' : '0.4'; label.style.cursor = hasMatch ? 'pointer' : 'not-allowed'; }
                });
            });
        }

        function updatePrice(selectedAttrs) {
            const priceElement = $('#productPrice'); const priceMaxElement = $('#productPriceMax'); const priceRangeSep = $('.price-range-sep');
            if (!selectedAttrs || Object.keys(selectedAttrs).length === 0) { if (priceElement && priceMaxElement && priceRangeSep) { priceElement.style.display = 'inline'; priceRangeSep.style.display = 'inline'; priceMaxElement.style.display = 'inline'; } return; }
            const matchingVariation = variations.find(v => { const attrData = v.attribute_data || {}; return Object.keys(selectedAttrs).every(attr => attrData[attr] === selectedAttrs[attr]); });
            if (matchingVariation) {
                const displayPrice = matchingVariation.effective_price; if (priceElement) priceElement.textContent = currencySymbol + ' ' + displayPrice.toFixed(2); if (priceMaxElement) priceMaxElement.style.display = 'none'; if (priceRangeSep) priceRangeSep.style.display = 'none';
                const priceInput = $('#selectedPrice'); if (priceInput) priceInput.value = displayPrice; const variationInput = $('#selectedVariationId'); if (variationInput) variationInput.value = matchingVariation.id;
                const saleBadge = $('#globalSaleBadge'); if (saleBadge) { if (matchingVariation.sale_price && matchingVariation.sale_price < matchingVariation.price) { const discountPercent = Math.round(((matchingVariation.price - matchingVariation.sale_price) / matchingVariation.price) * 100); saleBadge.textContent = discountPercent + '% Off'; saleBadge.style.display = 'inline-block'; } else { saleBadge.style.display = 'none'; } }
                updateStockStatus(matchingVariation);
            } else { if (priceElement && priceMaxElement && priceRangeSep) { priceElement.style.display = 'inline'; priceRangeSep.style.display = 'inline'; priceMaxElement.style.display = 'inline'; } updateStockStatus(null); }
        }

        function updateStockStatus(variation) {
            const stockBadge = $('#topStockBadge'); const stockStatus = $('.stock-status'); const addToCartBtn = $('.btn-buy'); const qtyInput = $('#qtyInputSide'); if (!variation) return; const availableStock = variation.stock_qty - (variation.reserved_qty || 0);
            if (stockBadge) { stockBadge.classList.remove('high-stock', 'low-stock', 'out-stock', 'badge-info'); if (!variation.manage_stock) { stockBadge.classList.add('badge-info'); stockBadge.textContent = stockBadge.dataset.unlimited || 'Unlimited'; } else if (availableStock <= 0) { stockBadge.classList.add('out-stock'); stockBadge.textContent = stockBadge.dataset.outOfStock || 'Out of Stock'; } else if (availableStock <= 5) { stockBadge.classList.add('low-stock'); stockBadge.textContent = stockBadge.dataset.lowStock || 'Low Stock'; } else { stockBadge.classList.add('high-stock'); stockBadge.textContent = stockBadge.dataset.inStock || 'In Stock'; } }
            if (stockStatus) { if (!variation.manage_stock) stockStatus.textContent = stockStatus.dataset.inStock || 'In stock'; else if (availableStock <= 0) stockStatus.textContent = stockStatus.dataset.outOfStock || 'Out of stock'; else stockStatus.textContent = availableStock + ' ' + (stockStatus.dataset.inStockText || 'in stock'); }
            if (addToCartBtn) { if (!variation.manage_stock || availableStock > 0) { addToCartBtn.disabled = false; addToCartBtn.classList.remove('btn-out-of-stock'); addToCartBtn.textContent = addToCartBtn.dataset.addText || 'ADD TO CART'; if (qtyInput) { qtyInput.max = variation.manage_stock ? availableStock : 999; qtyInput.disabled = false; } } else { addToCartBtn.disabled = true; addToCartBtn.classList.add('btn-out-of-stock'); addToCartBtn.textContent = addToCartBtn.dataset.outOfStockText || 'OUT OF STOCK'; if (qtyInput) qtyInput.disabled = true; } }
        }

        $$('.attr-radio').forEach(radio => radio.addEventListener('change', function () { const selectedAttrs = {}; $$('.attr-radio:checked').forEach(checked => { const attrName = checked.name.replace('attr_', ''); selectedAttrs[attrName] = checked.value; }); updateAvailableOptions(selectedAttrs); updatePrice(selectedAttrs); }));
        updateAvailableOptions({});
    }

    // --- Shipping / Checkout ---
    function clearSelect(select, placeholder) { if (!select) return; select.innerHTML = `<option value="">${placeholder}</option>`; }
    function populateSelect(select, options, selectedValue = null) { if (!select) return; const firstText = select.querySelector('option')?.textContent || ''; select.innerHTML = `<option value="">${firstText}</option>`; (options || []).forEach(option => { const opt = doc.createElement('option'); opt.value = option.id; opt.textContent = option.name; if (selectedValue && option.id == selectedValue) opt.selected = true; select.appendChild(opt); }); }

    function initCheckoutShipping() {
        const checkoutRoot = $('#checkout-root'); const translations = { selectGovernorate: checkoutRoot?.dataset.selectGovernorate || 'Select Governorate', selectCity: checkoutRoot?.dataset.selectCity || 'Select City', selectShippingCompany: checkoutRoot?.dataset.selectShippingCompany || 'Select Shipping Company' };
        const currencySymbol = checkoutRoot?.dataset.currencySymbol; const baseTotal = parseFloat(checkoutRoot?.dataset.baseTotal || '0') || 0;
        const countrySelect = $('#country-select'); const governorateSelect = $('#governorate-select'); const citySelect = $('#city-select'); const shippingZoneSelect = $('#shipping-zone-select'); const shippingCostDisplay = $('#shipping-cost-display'); const shippingCompanyDisplay = $('#shipping-company-display'); const shippingCostInput = $('#shipping-cost-input'); const shippingDaysInput = $('#shipping-days-input'); const shippingDaysDiv = $('#shipping-days'); const shippingDaysDisplay = $('#shipping-days-display'); const shippingInfo = $('#shipping-info'); const hiddenShippingZoneId = $('#input-shipping-zone-id'); const hiddenShippingPrice = $('#input-shipping-price'); const hiddenShippingDays = $('#shipping-days-input');
        if (!countrySelect || !governorateSelect || !citySelect) return;

        async function loadGovernorates(countryId) { if (!countryId) { clearSelect(governorateSelect, translations.selectGovernorate); clearSelect(citySelect, translations.selectCity); clearSelect(shippingZoneSelect, translations.selectShippingCompany); hideShippingInfo(); return; } const data = await fetchJson(`/api/locations/governorates?country=${countryId}`); populateSelect(governorateSelect, data.data || []); clearSelect(citySelect, translations.selectCity); clearSelect(shippingZoneSelect, translations.selectShippingCompany); hideShippingInfo(); }

        async function loadCities(governorateId) { if (!governorateId) { clearSelect(citySelect, translations.selectCity); clearSelect(shippingZoneSelect, translations.selectShippingCompany); hideShippingInfo(); return; } const data = await fetchJson(`/api/locations/cities?governorate=${governorateId}`); if ((data.data || []).length > 0) { populateSelect(citySelect, data.data); clearSelect(shippingZoneSelect, translations.selectShippingCompany); hideShippingInfo(); } else { clearSelect(citySelect, translations.selectCity); await loadShippingOptions(countrySelect.value, governorateId, null); } }

        async function loadShippingOptions(countryId, governorateId = null, cityId = null) { if (!countryId) { hideShippingInfo(); return; } const params = new URLSearchParams({ country: countryId }); if (governorateId) params.append('governorate', governorateId); if (cityId) params.append('city', cityId); const data = await fetchJson(`/api/locations/shipping?${params}`); if (!data.data || data.data.length === 0) { clearSelect(shippingZoneSelect, translations.selectShippingCompany); hideShippingInfo(); return; } populateSelect(shippingZoneSelect, (data.data || []).map(item => ({ id: item.zone_id, name: `${item.company_name} - ${currencySymbol}${parseFloat(item.price).toFixed(2)}` }))); if (data.data.length === 1) { shippingZoneSelect.value = data.data[0].zone_id; updateShippingDisplay(data.data[0]); } else hideShippingInfo(); }

        function updateShippingDisplay(shippingOption) { if (!shippingOption) { hideShippingInfo(); return; } if (shippingCostDisplay) shippingCostDisplay.textContent = `${currencySymbol}${parseFloat(shippingOption.price).toFixed(2)}`; if (shippingCompanyDisplay) shippingCompanyDisplay.textContent = shippingOption.zone_name; if (shippingCostInput) shippingCostInput.value = shippingOption.price; if (shippingDaysInput) shippingDaysInput.value = shippingOption.estimated_days || ''; if (shippingOption.estimated_days) { if (shippingDaysDisplay) shippingDaysDisplay.textContent = shippingOption.estimated_days; if (shippingDaysDiv) shippingDaysDiv.classList.remove('hidden'); } else if (shippingDaysDiv) shippingDaysDiv.classList.add('hidden'); if (shippingInfo) shippingInfo.classList.remove('hidden'); if (hiddenShippingZoneId) hiddenShippingZoneId.value = shippingOption.zone_id; if (hiddenShippingPrice) hiddenShippingPrice.value = shippingOption.price; if (hiddenShippingDays) hiddenShippingDays.value = shippingOption.estimated_days || ''; updateOrderSummary(shippingOption.price); }

        function hideShippingInfo() { if (shippingInfo) shippingInfo.classList.add('hidden'); if (shippingCostInput) shippingCostInput.value = ''; if (shippingDaysInput) shippingDaysInput.value = ''; if (hiddenShippingZoneId) hiddenShippingZoneId.value = ''; if (hiddenShippingPrice) hiddenShippingPrice.value = ''; if (hiddenShippingDays) hiddenShippingDays.value = ''; updateOrderSummary(0); }

        function updateOrderSummary(shippingCost) { const shippingAmount = $('.shipping-amount'); if (shippingAmount) shippingAmount.textContent = shippingCost > 0 ? `${currencySymbol}${parseFloat(shippingCost).toFixed(2)}` : '-'; const newTotal = baseTotal + parseFloat(shippingCost || 0); const orderTotal = $('.order-total'); if (orderTotal) orderTotal.textContent = `${currencySymbol}${newTotal.toFixed(2)}`; }

        countrySelect.addEventListener('change', function () { loadGovernorates(this.value); }); governorateSelect.addEventListener('change', function () { loadCities(this.value); }); citySelect.addEventListener('change', function () { if (this.value) loadShippingOptions(countrySelect.value, governorateSelect.value, this.value); else loadShippingOptions(countrySelect.value, governorateSelect.value, null); });

        shippingZoneSelect.addEventListener('change', function () { const selectedZoneId = this.value; if (!selectedZoneId) { hideShippingInfo(); return; } const params = new URLSearchParams({ country: countrySelect.value }); if (governorateSelect.value) params.append('governorate', governorateSelect.value); if (citySelect.value) params.append('city', citySelect.value); params.append('all', '1'); fetch(`/api/new-shipping/quote?${params}`).then(r => r.json()).then(data => { const selectedOption = data.data.find(item => item.zone_id == selectedZoneId); if (selectedOption) updateShippingDisplay(selectedOption); }).catch(err => console.error('Error updating shipping display:', err)); });

        if (countrySelect.value) loadGovernorates(countrySelect.value);
    }

    // --- Address selection ---
    function initAddressSelection() {
        const addressRadios = $$('input[name="selected_address"]'); const selectedAddressIdHidden = $('#selected-address-id'); const customerNameInput = $('#customer_name'); const customerEmailInput = $('#customer_email'); const customerPhoneInput = $('#customer_phone'); const customerAddressInput = $('#customer_address'); const countrySelect = $('#country-select'); const governorateSelect = $('#governorate-select'); const citySelect = $('#city-select'); const translations = { selectGovernorate: 'Select Governorate', selectCity: 'Select City', selectShippingCompany: 'Select Shipping Company' };

        async function loadGovernoratesAsync(countryId) { if (!countryId) { if (governorateSelect) clearSelect(governorateSelect, translations.selectGovernorate); if (citySelect) clearSelect(citySelect, translations.selectCity); const shippingZoneSelect = $('#shipping-zone-select'); if (shippingZoneSelect) clearSelect(shippingZoneSelect, translations.selectShippingCompany); hideShippingInfo(); return; } try { const data = await fetchJson(`/api/locations/governorates?country=${countryId}`); if (governorateSelect) populateSelect(governorateSelect, data.data); if (citySelect) clearSelect(citySelect, translations.selectCity); const shippingZoneSelect = $('#shipping-zone-select'); if (shippingZoneSelect) clearSelect(shippingZoneSelect, translations.selectShippingCompany); hideShippingInfo(); } catch (err) { console.error('Error loading governorates:', err); } }

        async function loadCitiesAsync(governorateId) { if (!governorateId) { if (citySelect) clearSelect(citySelect, translations.selectCity); const shippingZoneSelect = $('#shipping-zone-select'); if (shippingZoneSelect) clearSelect(shippingZoneSelect, translations.selectShippingCompany); hideShippingInfo(); return; } try { const data = await fetchJson(`/api/locations/cities?governorate=${governorateId}`); if (data.data.length > 0) { if (citySelect) populateSelect(citySelect, data.data); const shippingZoneSelect = $('#shipping-zone-select'); if (shippingZoneSelect) clearSelect(shippingZoneSelect, translations.selectShippingCompany); hideShippingInfo(); } else { if (citySelect) clearSelect(citySelect, translations.selectCity); } } catch (err) { console.error('Error loading cities:', err); } }

        async function loadShippingOptionsAsync(countryId, governorateId = null, cityId = null) { if (!countryId) { hideShippingInfo(); return; } const params = new URLSearchParams({ country: countryId }); if (governorateId) params.append('governorate', governorateId); if (cityId) params.append('city', cityId); params.append('all', '1'); try { const data = await fetchJson(`/api/new-shipping/quote?${params}`); if (!data.data || data.data.length === 0) { const shippingZoneSelect = $('#shipping-zone-select'); if (shippingZoneSelect) clearSelect(shippingZoneSelect, translations.selectShippingCompany); hideShippingInfo(); return; } const shippingZoneSelect = $('#shipping-zone-select'); if (shippingZoneSelect) populateSelect(shippingZoneSelect, data.data.map(item => ({ id: item.zone_id, name: `${item.zone_name} - ${currencySymbol}${parseFloat(item.price).toFixed(2)}` }))); } catch (err) { console.error('Error loading shipping options:', err); } }

        function hideShippingInfo() { const shippingInfo = $('#shipping-info'); const shippingCostInput = $('#shipping-cost-input'); const shippingDaysInput = $('#shipping-days-input'); const hiddenShippingZoneId = $('#input-shipping-zone-id'); const hiddenShippingPrice = $('#input-shipping-price'); const hiddenShippingDays = $('#shipping-days-input'); if (shippingInfo) shippingInfo.classList.add('hidden'); if (shippingCostInput) shippingCostInput.value = ''; if (shippingDaysInput) shippingDaysInput.value = ''; if (hiddenShippingZoneId) hiddenShippingZoneId.value = ''; if (hiddenShippingPrice) hiddenShippingPrice.value = ''; if (hiddenShippingDays) hiddenShippingDays.value = ''; }

        async function selectAddress(radio) { const label = radio.closest('label'); const addrId = label?.dataset?.addrId; const country = label?.dataset?.country; const governorate = label?.dataset?.governorate; const city = label?.dataset?.city; const line1 = label?.dataset?.line1; const phone = label?.dataset?.phone; const name = label?.querySelector('.address-name')?.textContent || ''; if (selectedAddressIdHidden) selectedAddressIdHidden.value = addrId; if (customerNameInput) customerNameInput.value = name; if (customerEmailInput) customerEmailInput.value = doc.querySelector('meta[name="user-email"]')?.content || ''; if (customerPhoneInput) customerPhoneInput.value = phone; if (customerAddressInput) customerAddressInput.value = line1; if (countrySelect) { countrySelect.value = country; await loadGovernoratesAsync(country); } if (governorateSelect) { governorateSelect.value = governorate; await loadCitiesAsync(governorate); } if (citySelect) { citySelect.value = city; if (citySelect.value) await loadShippingOptionsAsync(countrySelect.value, governorateSelect.value, citySelect.value); else await loadShippingOptionsAsync(countrySelect.value, governorateSelect.value, null); } }

        addressRadios.forEach(radio => radio.addEventListener('change', function () { if (this.checked) selectAddress(this); }));
        const initialChecked = $('input[name="selected_address"]:checked'); if (initialChecked) selectAddress(initialChecked);
    }

    // Initialize all
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initDropdowns(); initCurrencySwitch(); initCompareBadge(); initLoader(); initHeroSlider(); initQuantitySelector(); initProductVariations(); initCheckoutShipping(); initAddressSelection();
        });
    } else {
        initDropdowns(); initCurrencySwitch(); initCompareBadge(); initLoader(); initHeroSlider(); initQuantitySelector(); initProductVariations(); initCheckoutShipping(); initAddressSelection();
    }
}());


