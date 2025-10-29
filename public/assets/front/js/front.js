/**
 * Front-end JavaScript - Secure and Optimized
 * Handles UI interactions, form validations, and dynamic content loading
 */

(function () {
    'use strict';

    // Utility functions
    const $ = (selector) => document.querySelector(selector);
    const $$ = (selector) => Array.from(document.querySelectorAll(selector));

    // Safe DOM manipulation to prevent XSS
    const createElement = (tag, attributes = {}, textContent = '') => {
        const element = document.createElement(tag);
        Object.keys(attributes).forEach(key => {
            if (key === 'className') {
                element.className = attributes[key];
            } else if (key === 'textContent') {
                element.textContent = attributes[key];
            } else {
                element.setAttribute(key, attributes[key]);
            }
        });
        if (textContent) {
            element.textContent = textContent;
        }
        return element;
    };

    // Safe fetch with validation
    const safeFetch = async (url, options = {}) => {
        try {
            // Validate URL - only allow relative URLs or same origin
            if (typeof url !== 'string') {
                throw new Error('Invalid URL');
            }

            // Allow only relative URLs (starting with /) or same origin URLs
            if (!url.startsWith('/')) {
                const urlObj = new URL(url, window.location.origin);
                if (urlObj.origin !== window.location.origin) {
                    throw new Error('Unauthorized URL - only same origin allowed');
                }
            }

            const response = await fetch(url, options);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            }

            return response;
        } catch (error) {
            console.error('Fetch error:', error);
            return null;
        }
    };

    // CSRF token helper
    const getCsrfToken = () => {
        const token = $('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    };

    // Dropdown functionality
    const DropdownManager = {
        init() {
            this.bindEvents();
        },

        bindEvents() {
            document.addEventListener('click', (e) => {
                const trigger = e.target.closest('.dropdown-trigger');
                if (trigger) {
                    e.preventDefault();
                    this.toggleDropdown(trigger);
                } else if (!e.target.closest('[data-dropdown]')) {
                    this.closeAllDropdowns();
                }
            });
        },

        toggleDropdown(trigger) {
            const wrapper = trigger.closest('[data-dropdown]');
            if (!wrapper) return;

            const isOpen = wrapper.classList.toggle('open');
            trigger.setAttribute('aria-expanded', isOpen);

            if (isOpen) {
                this.closeOtherDropdowns(wrapper);
                this.focusFirstElement(wrapper);
            }
        },

        closeAllDropdowns() {
            $$('[data-dropdown].open').forEach(dropdown => {
                dropdown.classList.remove('open');
                const trigger = dropdown.querySelector('.dropdown-trigger');
                if (trigger) trigger.setAttribute('aria-expanded', 'false');
            });
        },

        closeOtherDropdowns(except) {
            $$('[data-dropdown].open').forEach(dropdown => {
                if (dropdown !== except) {
                    dropdown.classList.remove('open');
                    const trigger = dropdown.querySelector('.dropdown-trigger');
                    if (trigger) trigger.setAttribute('aria-expanded', 'false');
                }
            });
        },

        focusFirstElement(wrapper) {
            const panel = wrapper.querySelector('.dropdown-panel');
            if (panel) {
                const focusable = panel.querySelector('button, a, input, [tabindex]');
                if (focusable && focusable.focus) {
                    focusable.focus();
                }
            }
        }
    };

    // Currency switcher
    const CurrencySwitcher = {
        init() {
            document.addEventListener('click', (e) => {
                const button = e.target.closest('.currency-chip');
                if (button) {
                    this.switchCurrency(button);
                }
            });
        },

        async switchCurrency(button) {
            const code = button.dataset.currency;
            if (!code) {return;}

            try {
                const response = await safeFetch('/currency/switch', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({ code })
                });

                if (response) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Currency switch failed:', error);
            }
        }
    };

    // Compare badge
    const CompareBadge = {
        init() {
            const badge = $('[data-compare_count]');
            if (!badge) return;

            window.addEventListener('compare:update', (e) => {
                const count = e.detail?.count || 0;
                badge.textContent = count;
                badge.classList.toggle('show', count > 0);
            });
        }
    };

    // Loader
    const Loader = {
        init() {
            const loader = $('#app-loader');
            if (!loader) {return;}

            const hideLoader = () => {
                loader.classList.add('hidden');
                loader.setAttribute('aria-hidden', 'true');
            };

            if (document.readyState === 'complete') {
                hideLoader();
            } else {
                window.addEventListener('load', hideLoader);
                setTimeout(hideLoader, 3000);
            }
        }
    };

    // Quantity selector
    const QuantitySelector = {
        init() {
            const qtyDisplay = $('#qtyDisplay');
            const qtyInput = $('#qtyInputSide');
            const increaseBtn = $('.qty-increase');
            const decreaseBtn = $('.qty-trash');

            if (!(qtyDisplay && qtyInput && increaseBtn && decreaseBtn)) return;

            this.qtyDisplay = qtyDisplay;
            this.qtyInput = qtyInput;
            this.maxStock = parseInt(qtyInput.getAttribute('max')) || 999;
            this.currentQty = 1;

            this.bindEvents();
            this.updateDisplay();
        },

        bindEvents() {
            $('.qty-increase').addEventListener('click', () => this.increase());
            $('.qty-trash').addEventListener('click', () => this.decrease());
        },

        increase() {
            this.setQuantity(this.currentQty + 1);
        },

        decrease() {
            this.setQuantity(this.currentQty - 1);
        },

        setQuantity(newQty) {
            this.currentQty = Math.max(1, Math.min(newQty, this.maxStock));
            this.updateDisplay();
        },

        updateDisplay() {
            this.qtyDisplay.textContent = this.currentQty;
            this.qtyInput.value = this.currentQty;
        }
    };

    // Product variations
    const ProductVariations = {
        init() {
            const variationCard = $('#variationGridCard');
            if (!variationCard) return;

            this.variationsData = variationCard.dataset.variations;
            this.currencySymbol = variationCard.dataset.currency;

            if (!this.variationsData || !this.currencySymbol) return;

            try {
                this.variations = JSON.parse(this.variationsData);
            } catch (error) {
                console.error('Invalid variations data:', error);
                return;
            }

            this.applyColorSwatches();
            this.bindEvents();
            this.updateAvailableOptions({});
        },

        applyColorSwatches() {
            $$('.option-btn.color[data-swatch], .option-btn.color-swatch[data-swatch]').forEach(btn => {
                const swatch = btn.dataset.swatch;
                if (swatch) {
                    btn.style.setProperty('background-color', swatch, 'important');
                }
            });
        },

        bindEvents() {
            $$('.attr-radio').forEach(radio => {
                radio.addEventListener('change', () => this.onVariationChange());
            });
        },

        onVariationChange() {
            const selectedAttrs = {};
            $$('.attr-radio:checked').forEach(checked => {
                const attrName = checked.name.replace('attr_', '');
                selectedAttrs[attrName] = checked.value;
            });

            this.updateAvailableOptions(selectedAttrs);
            this.updatePrice(selectedAttrs);
        },

        updateAvailableOptions(selectedAttrs) {
            $$('.variation-attr-block').forEach(block => {
                const attrName = block.dataset.attr;
                const radios = block.querySelectorAll('.attr-radio');

                radios.forEach(radio => {
                    const value = radio.value;
                    const label = radio.nextElementSibling;
                    const testAttrs = { ...selectedAttrs, [attrName]: value };

                    const hasMatch = this.variations.some(v => {
                        if (v.active === false) return false;
                        const attrData = v.attribute_data || {};
                        return Object.keys(testAttrs).every(attr =>
                            attrData[attr] === testAttrs[attr]
                        );
                    });

                    radio.disabled = !hasMatch;
                    if (label) {
                        label.classList.toggle('disabled', !hasMatch);
                        label.style.opacity = hasMatch ? '1' : '0.4';
                        label.style.cursor = hasMatch ? 'pointer' : 'not-allowed';
                    }
                });
            });
        },

        updatePrice(selectedAttrs) {
            const priceElement = $('#productPrice');
            const priceMaxElement = $('#productPriceMax');
            const priceRangeSep = $('.price-range-sep');

            if (!selectedAttrs || Object.keys(selectedAttrs).length === 0) {
                this.showPriceRange(priceElement, priceMaxElement, priceRangeSep);
                return;
            }

            const matchingVariation = this.findMatchingVariation(selectedAttrs);
            if (matchingVariation) {
                this.updatePriceDisplay(matchingVariation);
                this.updateStockStatus(matchingVariation);
            } else {
                this.showPriceRange(priceElement, priceMaxElement, priceRangeSep);
                this.updateStockStatus(null);
            }
        },

        findMatchingVariation(selectedAttrs) {
            return this.variations.find(v => {
                const attrData = v.attribute_data || {};
                return Object.keys(selectedAttrs).every(attr =>
                    attrData[attr] === selectedAttrs[attr]
                );
            });
        },

        showPriceRange(priceElement, priceMaxElement, priceRangeSep) {
            if (priceElement) priceElement.style.display = 'inline';
            if (priceRangeSep) priceRangeSep.style.display = 'inline';
            if (priceMaxElement) priceMaxElement.style.display = 'inline';
        },

        updatePriceDisplay(variation) {
            const priceElement = $('#productPrice');
            const priceMaxElement = $('#productPriceMax');
            const priceRangeSep = $('.price-range-sep');

            if (priceElement) {
                priceElement.textContent = this.currencySymbol + ' ' + variation.effective_price.toFixed(2);
            }
            if (priceMaxElement) priceMaxElement.style.display = 'none';
            if (priceRangeSep) priceRangeSep.style.display = 'none';

            // Update hidden inputs
            const priceInput = $('#selectedPrice');
            if (priceInput) priceInput.value = variation.effective_price;

            const variationInput = $('#selectedVariationId');
            if (variationInput) variationInput.value = variation.id;

            // Update sale badge
            this.updateSaleBadge(variation);
        },

        updateSaleBadge(variation) {
            const saleBadge = $('#globalSaleBadge');
            if (!saleBadge) return;

            if (variation.sale_price && variation.sale_price < variation.price) {
                const discountPercent = Math.round(((variation.price - variation.sale_price) / variation.price) * 100);
                saleBadge.textContent = discountPercent + '% Off';
                saleBadge.style.display = 'inline-block';
            } else {
                saleBadge.style.display = 'none';
            }
        },

        updateStockStatus(variation) {
            const stockBadge = $('#topStockBadge');
            const stockStatus = $('.stock-status');
            const addToCartBtn = $('.btn-buy');
            const qtyInput = $('#qtyInputSide');

            if (!variation) return;

            const availableStock = variation.stock_qty - (variation.reserved_qty || 0);

            this.updateStockBadge(stockBadge, availableStock, variation.manage_stock);
            this.updateStockText(stockStatus, availableStock, variation.manage_stock);
            this.updateAddToCartButton(addToCartBtn, availableStock, variation.manage_stock, qtyInput);
        },

        updateStockBadge(badge, availableStock, manageStock) {
            if (!badge) return;

            badge.classList.remove('high-stock', 'low-stock', 'out-stock', 'badge-info');

            if (!manageStock) {
                badge.classList.add('badge-info');
                badge.textContent = badge.dataset.unlimited || 'Unlimited';
            } else if (availableStock <= 0) {
                badge.classList.add('out-stock');
                badge.textContent = badge.dataset.outOfStock || 'Out of Stock';
            } else if (availableStock <= 5) {
                badge.classList.add('low-stock');
                badge.textContent = badge.dataset.lowStock || 'Low Stock';
            } else {
                badge.classList.add('high-stock');
                badge.textContent = badge.dataset.inStock || 'In Stock';
            }
        },

        updateStockText(statusElement, availableStock, manageStock) {
            if (!statusElement) return;

            if (!manageStock) {
                statusElement.textContent = statusElement.dataset.inStock || 'In stock';
            } else if (availableStock <= 0) {
                statusElement.textContent = statusElement.dataset.outOfStock || 'Out of stock';
            } else {
                statusElement.textContent = availableStock + ' ' + (statusElement.dataset.inStockText || 'in stock');
            }
        },

        updateAddToCartButton(button, availableStock, manageStock, qtyInput) {
            if (!button) return;

            const canAddToCart = !manageStock || availableStock > 0;

            button.disabled = !canAddToCart;
            button.classList.toggle('btn-out-of-stock', !canAddToCart);
            button.textContent = canAddToCart
                ? (button.dataset.addText || 'ADD TO CART')
                : (button.dataset.outOfStockText || 'OUT OF STOCK');

            if (qtyInput) {
                qtyInput.max = manageStock ? availableStock : 999;
                qtyInput.disabled = !canAddToCart;
            }
        }
    };

    // Safe select manipulation
    const SelectUtils = {
        clearSelect(select, placeholder) {
            if (!select) return;

            while (select.firstChild) {
                select.removeChild(select.firstChild);
            }

            const option = createElement('option', { value: '' }, placeholder);
            select.appendChild(option);
        },

        populateSelect(select, options, selectedValue = null) {
            if (!select) return;

            const firstOption = select.querySelector('option');
            const firstText = firstOption ? firstOption.textContent : '';

            this.clearSelect(select, firstText);

            (options || []).forEach(option => {
                const opt = createElement('option', { value: option.id }, option.name);
                if (selectedValue && option.id == selectedValue) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });
        }
    };

    // Shared location loader
    const LocationLoader = {
        async loadGovernorates(selectElement, countryId) {
            try {
                const data = await safeFetch(`/api/locations/governorates?country=${countryId}`);
                SelectUtils.populateSelect(selectElement, data?.data || []);
            } catch (error) {
                console.error('Error loading governorates:', error);
            }
        },

        async loadCities(selectElement, governorateId) {
            try {
                const data = await safeFetch(`/api/locations/cities?governorate=${governorateId}`);
                SelectUtils.populateSelect(selectElement, data?.data || []);
            } catch (error) {
                console.error('Error loading cities:', error);
            }
        }
    };

    // Checkout shipping
    const CheckoutShipping = {
        init() {
            const checkoutRoot = $('#checkout-root');
            if (!checkoutRoot) return;

            this.translations = {
                selectGovernorate: checkoutRoot.dataset.selectGovernorate || 'Select Governorate',
                selectCity: checkoutRoot.dataset.selectCity || 'Select City',
                selectShippingCompany: checkoutRoot.dataset.selectShippingCompany || 'Select Shipping Company'
            };

            this.currencySymbol = checkoutRoot.dataset.currencySymbol;
            this.baseTotal = parseFloat(checkoutRoot.dataset.baseTotal || '0') || 0;

            this.elements = {
                countrySelect: $('#country-select'),
                governorateSelect: $('#governorate-select'),
                citySelect: $('#city-select'),
                shippingZoneSelect: $('#shipping-zone-select'),
                shippingCostDisplay: $('#shipping-cost-display'),
                shippingCompanyDisplay: $('#shipping-company-display'),
                shippingCostInput: $('#shipping-cost-input'),
                shippingDaysInput: $('#shipping-days-input'),
                shippingDaysDiv: $('#shipping-days'),
                shippingDaysDisplay: $('#shipping-days-display'),
                shippingInfo: $('#shipping-info'),
                hiddenShippingZoneId: $('#input-shipping-zone-id'),
                hiddenShippingPrice: $('#input-shipping-price'),
                hiddenShippingDays: $('#shipping-days-input')
            };

            this.bindEvents();
            this.initializeShipping();
        },

        bindEvents() {
            const { countrySelect, governorateSelect, citySelect, shippingZoneSelect } = this.elements;

            if (countrySelect) {
                countrySelect.addEventListener('change', () => this.onCountryChange());
            }
            if (governorateSelect) {
                governorateSelect.addEventListener('change', () => this.onGovernorateChange());
            }
            if (citySelect) {
                citySelect.addEventListener('change', () => this.onCityChange());
            }
            if (shippingZoneSelect) {
                shippingZoneSelect.addEventListener('change', () => this.onShippingZoneChange());
            }
        },

        async onCountryChange() {
            const countryId = this.elements.countrySelect.value;
            await this.loadGovernorates(countryId);
        },

        async onGovernorateChange() {
            const governorateId = this.elements.governorateSelect.value;
            await this.loadCities(governorateId);
        },

        async onCityChange() {
            const countryId = this.elements.countrySelect.value;
            const governorateId = this.elements.governorateSelect.value;
            const cityId = this.elements.citySelect.value;

            if (cityId) {
                await this.loadShippingOptions(countryId, governorateId, cityId);
            } else {
                await this.loadShippingOptions(countryId, governorateId, null);
            }
        },

        async onShippingZoneChange() {
            const selectedZoneId = this.elements.shippingZoneSelect.value;
            if (!selectedZoneId) {
                this.hideShippingInfo();
                return;
            }

            const params = new URLSearchParams({
                country: this.elements.countrySelect.value,
                all: '1'
            });

            if (this.elements.governorateSelect.value) {
                params.append('governorate', this.elements.governorateSelect.value);
            }
            if (this.elements.citySelect.value) {
                params.append('city', this.elements.citySelect.value);
            }

            try {
                const data = await safeFetch(`/api/new-shipping/quote?${params}`);
                if (data && data.data) {
                    const selectedOption = data.data.find(item => item.zone_id == selectedZoneId);
                    if (selectedOption) {
                        this.updateShippingDisplay(selectedOption);
                    }
                }
            } catch (error) {
                console.error('Error updating shipping display:', error);
            }
        },

        async loadGovernorates(countryId) {
            if (!countryId) {
                this.clearShippingSelects();
                return;
            }

            await LocationLoader.loadGovernorates(this.elements.governorateSelect, countryId);
            SelectUtils.clearSelect(this.elements.citySelect, this.translations.selectCity);
            SelectUtils.clearSelect(this.elements.shippingZoneSelect, this.translations.selectShippingCompany);
            this.hideShippingInfo();
        },

        async loadCities(governorateId) {
            if (!governorateId) {
                this.clearShippingSelects();
                return;
            }

            const data = await safeFetch(`/api/locations/cities?governorate=${governorateId}`);
            if (data && data.data && data.data.length > 0) {
                await LocationLoader.loadCities(this.elements.citySelect, governorateId);
                SelectUtils.clearSelect(this.elements.shippingZoneSelect, this.translations.selectShippingCompany);
                this.hideShippingInfo();
            } else {
                SelectUtils.clearSelect(this.elements.citySelect, this.translations.selectCity);
                await this.loadShippingOptions(
                    this.elements.countrySelect.value,
                    governorateId,
                    null
                );
            }
        },

        async loadShippingOptions(countryId, governorateId = null, cityId = null) {
            if (!countryId) {
                this.hideShippingInfo();
                return;
            }

            const params = new URLSearchParams({ country: countryId });
            if (governorateId) params.append('governorate', governorateId);
            if (cityId) params.append('city', cityId);

            const data = await safeFetch(`/api/locations/shipping?${params}`);
            if (!data || !data.data || data.data.length === 0) {
                this.clearShippingSelects();
                return;
            }

            const options = data.data.map(item => ({
                id: item.zone_id,
                name: `${item.company_name} - ${this.currencySymbol}${parseFloat(item.price).toFixed(2)}`
            }));

            SelectUtils.populateSelect(this.elements.shippingZoneSelect, options);

            if (data.data.length === 1) {
                this.elements.shippingZoneSelect.value = data.data[0].zone_id;
                this.updateShippingDisplay(data.data[0]);
            } else {
                this.hideShippingInfo();
            }
        },

        clearShippingSelects() {
            SelectUtils.clearSelect(this.elements.citySelect, this.translations.selectCity);
            SelectUtils.clearSelect(this.elements.shippingZoneSelect, this.translations.selectShippingCompany);
            this.hideShippingInfo();
        },

        updateShippingDisplay(shippingOption) {
            if (!shippingOption) {
                this.hideShippingInfo();
                return;
            }

            const { shippingCostDisplay, shippingCompanyDisplay, shippingCostInput,
                    shippingDaysInput, shippingDaysDiv, shippingDaysDisplay, shippingInfo,
                    hiddenShippingZoneId, hiddenShippingPrice, hiddenShippingDays } = this.elements;

            if (shippingCostDisplay) {
                shippingCostDisplay.textContent = `${this.currencySymbol}${parseFloat(shippingOption.price).toFixed(2)}`;
            }
            if (shippingCompanyDisplay) {
                shippingCompanyDisplay.textContent = shippingOption.zone_name;
            }
            if (shippingCostInput) {
                shippingCostInput.value = shippingOption.price;
            }
            if (shippingDaysInput) {
                shippingDaysInput.value = shippingOption.estimated_days || '';
            }

            if (shippingOption.estimated_days) {
                if (shippingDaysDisplay) {
                    shippingDaysDisplay.textContent = shippingOption.estimated_days;
                }
                if (shippingDaysDiv) {
                    shippingDaysDiv.classList.remove('hidden');
                }
            } else if (shippingDaysDiv) {
                shippingDaysDiv.classList.add('hidden');
            }

            if (shippingInfo) {
                shippingInfo.classList.remove('hidden');
            }
            if (hiddenShippingZoneId) {
                hiddenShippingZoneId.value = shippingOption.zone_id;
            }
            if (hiddenShippingPrice) {
                hiddenShippingPrice.value = shippingOption.price;
            }
            if (hiddenShippingDays) {
                hiddenShippingDays.value = shippingOption.estimated_days || '';
            }

            this.updateOrderSummary(shippingOption.price);
        },

        hideShippingInfo() {
            const { shippingInfo, shippingCostInput, shippingDaysInput,
                    hiddenShippingZoneId, hiddenShippingPrice, hiddenShippingDays } = this.elements;

            if (shippingInfo) shippingInfo.classList.add('hidden');
            if (shippingCostInput) shippingCostInput.value = '';
            if (shippingDaysInput) shippingDaysInput.value = '';
            if (hiddenShippingZoneId) hiddenShippingZoneId.value = '';
            if (hiddenShippingPrice) {hiddenShippingPrice.value = '';}
            if (hiddenShippingDays) hiddenShippingDays.value = '';

            this.updateOrderSummary(0);
        },

        updateOrderSummary(shippingCost) {
            const shippingAmount = $('.shipping-amount');
            if (shippingAmount) {
                shippingAmount.textContent = shippingCost > 0
                    ? `${this.currencySymbol}${parseFloat(shippingCost).toFixed(2)}`
                    : '-';
            }

            const newTotal = this.baseTotal + parseFloat(shippingCost || 0);
            const orderTotal = $('.order-total');
            if (orderTotal) {
                orderTotal.textContent = `${this.currencySymbol}${newTotal.toFixed(2)}`;
            }
        },

        initializeShipping() {
            if (this.elements.countrySelect && this.elements.countrySelect.value) {
                this.loadGovernorates(this.elements.countrySelect.value);
            }
        }
    };

    // Initialize all modules when DOM is ready
    function initializeApp() {
        DropdownManager.init();
        CurrencySwitcher.init();
        CompareBadge.init();
        Loader.init();
        QuantitySelector.init();
        ProductVariations.init();
        CheckoutShipping.init();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeApp);
    } else {
        initializeApp();
    }

})();


