(function () {
    // small module logger: enable by setting data-config.debug = true on #checkout-root
    const CheckoutLogger = (function () {
        const root = document && document.getElementById && document.getElementById('checkout-root');
        let cfg = null;
        try { cfg = root && root.dataset && root.dataset.config ? JSON.parse(root.dataset.config) : null } catch (e) { cfg = null }
        const enabled = !!(cfg && cfg.debug);
        return {
            log: function () { if (enabled) console.log.apply(console, arguments); },
            warn: function () { if (enabled) console.warn.apply(console, arguments); },
            error: function () { if (enabled) console.error.apply(console, arguments); }
        };
    })();

    CheckoutLogger.log('Checkout.js script loaded!');

    function $(sel) { return document.querySelector(sel) }
    function $id(id) { return document.getElementById(id) }

    document.addEventListener('DOMContentLoaded', function () {
        try {
            CheckoutLogger.log('DOMContentLoaded fired!');
            // read config from DOM node #checkout-root instead of a global
            let domConfig = null;
            try {
                const root = document.getElementById('checkout-root');
                domConfig = root && root.dataset && root.dataset.config ? JSON.parse(root.dataset.config) : {};
            } catch (e) { domConfig = {}; }
            window.checkoutConfig = window.checkoutConfig || domConfig;
            CheckoutLogger.log('checkoutConfig loaded from DOM:', window.checkoutConfig);
            const country = $id('country-select');
            const gov = $id('governorate-select');
            const city = $id('city-select');
            const quoteBox = $id('shipping-info');
            const shippingAmountEl = document.querySelector('.shipping-amount');
            const orderTotalEl = document.querySelector('.order-total');
            const inputZone = $id('input-shipping-zone-id');
            const inputPrice = $id('input-shipping-price');
            const matchLevel = $id('shipping-match-level');

            // Determine base subtotal safely: prefer server-provided numeric, else compute from DOM listing
            let baseTotal = NaN;
            try {
                baseTotal = Number(window.checkoutConfig && window.checkoutConfig.baseTotal);
            } catch (e) { baseTotal = NaN }
            if (!Number.isFinite(baseTotal)) {
                // fallback: sum product lines in summary (skip shipping/total rows)
                try {
                    baseTotal = 0;
                    const lines = document.querySelectorAll('.summary-lines .summary-line');
                    lines.forEach(li => {
                        // skip shipping line by text
                        const label = li.querySelector('span')?.textContent || '';
                        if (/shipping/i.test(label)) return;
                        // find last span (value)
                        const spans = li.querySelectorAll('span');
                        if (spans.length < 2) return;
                        const txt = spans[spans.length - 1].textContent || '';
                        const num = parseFloat(txt.replace(/[^0-9.-]+/g, ''));
                        if (Number.isFinite(num)) baseTotal += num;
                    });
                } catch (e) { baseTotal = 0 }
            }

            // Get currency symbol from data attribute
            const currencySymbol = document.getElementById('checkout-root')?.dataset?.currencySymbol || '$';

            function setTotals(shippingPrice) {
                if (!shippingAmountEl) return;
                // Normalize price to a finite number. Treat non-finite as unknown.
                const parsed = (shippingPrice === null || typeof shippingPrice === 'undefined' || shippingPrice === '') ? NaN : Number(shippingPrice);
                const price = Number.isFinite(parsed) ? parsed : 0;
                if (!Number.isFinite(parsed)) {
                    // unknown / not selected
                    shippingAmountEl.textContent = '-';
                } else if (price === 0) {
                    // explicit free
                    shippingAmountEl.textContent = 'Free';
                } else {
                    shippingAmountEl.textContent = currencySymbol + price.toFixed(2);
                }
                if (orderTotalEl) orderTotalEl.textContent = currencySymbol + (baseTotal + price).toFixed(2);
            }

            // helpers to show/hide without writing inline styles (CSP-safe)
            function showEl(el) { if (!el) return; el.classList.remove('envato-hidden'); el.classList.add('d-block-init'); }
            function hideEl(el) { if (!el) return; el.classList.add('envato-hidden'); el.classList.remove('d-block-init'); }

            async function loadGovs() {
                CheckoutLogger.log('loadGovs called, country value:', country ? country.value : 'no country element');
                if (!gov || !country) return;
                hideEl(gov); hideEl(city); gov.innerHTML = ''; city.innerHTML = '';
                if (!country.value) { setTotals(null); if (quoteBox) quoteBox.innerHTML = ''; if (matchLevel) hideEl(matchLevel); return; }
                try {
                    CheckoutLogger.log('Fetching governorates for country:', country.value);
                    const res = await fetch('/api/locations/governorates?country=' + country.value);
                    if (!res.ok) {
                        const text = await res.text().catch(() => '{unable to read body}');
                        CheckoutLogger.error('Failed to fetch governorates:', res.status, res.statusText, text);
                    } else {
                        const data = await res.json();
                        CheckoutLogger.log('Governorates response:', data);
                        const list = data.data || [];
                        if (list.length) {
                            gov.innerHTML = '<option value="">' + (window.checkoutConfig?.labels?.selectGovernorate || 'Select Governorate') + '</option>' + list.map(g => `<option value="${g.id}">${g.name}</option>`).join('');
                            showEl(gov);
                            CheckoutLogger.log('Governorate select shown with', list.length, 'options');
                        } else {
                            CheckoutLogger.warn('No governorates found for country', country.value, 'response data:', data);
                        }
                    }
                } catch (e) { CheckoutLogger.error('Error loading governorates:', e) }
                if (!_suppressAutoQuote) fetchQuote();
            }

            async function loadCities() {
                if (!city || !gov) return;
                hideEl(city); city.innerHTML = '';
                if (!gov.value) { fetchQuote(); return; }
                try {
                    const res = await fetch('/api/locations/cities?governorate=' + gov.value);
                    if (!res.ok) {
                        const text = await res.text().catch(() => '{unable to read body}');
                        CheckoutLogger.error('Failed to fetch cities:', res.status, res.statusText, text);
                    } else {
                        const data = await res.json(); const list = data.data || [];
                        if (list.length) {
                            city.innerHTML = '<option value="">' + (window.checkoutConfig?.labels?.selectCity || 'Select City') + '</option>' + list.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                            showEl(city);
                            CheckoutLogger.log('City select shown with', list.length, 'options');
                        } else {
                            CheckoutLogger.warn('No cities found for governorate', gov.value, 'response data:', data);
                        }
                    }
                } catch (e) { CheckoutLogger.error('Error loading cities:', e) }
                if (!_suppressAutoQuote) fetchQuote();
            }

            // sequence token to avoid race conditions between multiple parallel quote requests
            let _currentQuoteRequest = 0;
            // when true, loadGovs/loadCities will not trigger their automatic fetchQuote()
            let _suppressAutoQuote = false;
            async function fetchQuote() {
                if (!country || !country.value) {
                    setTotals(null);
                    if (quoteBox) quoteBox.innerHTML = '';
                    if (matchLevel) hideEl(matchLevel);
                    if (inputZone) inputZone.value = '';
                    if (inputPrice) inputPrice.value = '';
                    return;
                }
                const thisReq = ++_currentQuoteRequest;
                if (quoteBox) quoteBox.innerHTML = '<div class="small text-muted">Calculating shipping...</div>';
                try {
                    const params = new URLSearchParams();
                    params.append('country', country.value);
                    if (gov && gov.value) params.append('governorate', gov.value);
                    if (city && city.value) params.append('city', city.value);
                    // request all matching shipping options so user can choose a preferred group
                    params.append('all', '1');
                    const url = '/api/new-shipping/quote?' + params.toString();
                    console.debug('[checkout] requesting shipping quote ->', url);
                    const res = await fetch(url);
                    // if a newer request was started while awaiting, drop this response
                    if (thisReq !== _currentQuoteRequest) {
                        CheckoutLogger.log('Dropping stale fetchQuote response (outdated request)');
                        return;
                    }
                    if (!res.ok) {
                        const text = await res.text().catch(() => '{unable to read body}');
                        CheckoutLogger.error('Failed to fetch shipping quote:', res.status, res.statusText, text);
                        if (quoteBox) quoteBox.innerHTML = '<div class="alert alert-danger small">Failed to get quote</div>';
                        setTotals(null);
                        if (inputZone) inputZone.value = '';
                        if (inputPrice) inputPrice.value = '';
                        if (matchLevel) hideEl(matchLevel);
                        return;
                    }
                    const data = await res.json();
                    if (thisReq !== _currentQuoteRequest) { CheckoutLogger.log('Dropping stale fetchQuote JSON response'); return; }
                    console.debug('[checkout] shipping quote response:', data);
                    CheckoutLogger.log('Shipping quote response:', data);
                    const options = Array.isArray(data.data) ? data.data : [];
                    console.debug('[checkout] options length:', options.length, 'zones:', options.map(o => o.zone_id));
                    if (!options.length) {
                        if (quoteBox) quoteBox.innerHTML = '<div class="alert alert-info small">No shipping rule found for this location</div>';
                        setTotals(null);
                        if (inputZone) inputZone.value = '';
                        if (inputPrice) inputPrice.value = '';
                        if (matchLevel) hideEl(matchLevel);
                        return;
                    }

                    // render options as radio buttons
                    if (quoteBox) {
                        quoteBox.innerHTML = '<div class="shipping-options small">' + options.map((o, idx) => {
                            const priceLabel = o.price !== null ? '$' + parseFloat(o.price).toFixed(2) : 'Free';
                            const eta = o.estimated_days ? ` • ${o.estimated_days} days` : '';
                            // default select first option
                            return `<label class="shipping-option shipping-option-row"><input type="radio" name="_shipping_option" data-zone="${o.zone_id}" data-price="${o.price}" ${idx === 0 ? 'checked' : ''}> <strong>${o.zone_name}</strong> - ${priceLabel}${eta} <span class="small text-muted"> (${o.level})</span></label>`;
                        }).join('') + '</div>';

                        // bind change listeners
                        const radios = quoteBox.querySelectorAll('input[name="_shipping_option"]');
                        radios.forEach(r => r.addEventListener('change', function () {
                            const z = this.dataset.zone; const p = this.dataset.price;
                            if (inputZone) inputZone.value = z || '';
                            const parsed = (p === '' || p === null || typeof p === 'undefined') ? NaN : Number(p);
                            if (inputPrice) inputPrice.value = Number.isFinite(parsed) ? parsed.toFixed(2) : '';
                            setTotals(p);
                        }));

                        // set inputs to first option by default
                        const first = quoteBox.querySelector('input[name="_shipping_option"]:checked');
                        if (first) {
                            if (inputZone) inputZone.value = first.dataset.zone || '';
                            const fp = first.dataset.price;
                            const parsed = (fp === '' || fp === null || typeof fp === 'undefined') ? NaN : Number(fp);
                            if (inputPrice) inputPrice.value = Number.isFinite(parsed) ? parsed.toFixed(2) : '';
                            setTotals(fp);
                        }
                    }
                } catch (e) {
                    CheckoutLogger.error('Error fetching quote:', e);
                    if (quoteBox) quoteBox.innerHTML = '<div class="alert alert-danger small">Failed to get quote</div>';
                    setTotals(null);
                    if (inputZone) inputZone.value = '';
                    if (inputPrice) inputPrice.value = '';
                    if (matchLevel) hideEl(matchLevel);
                }
            }

            // Robust quote fetch that accepts explicit params (useful when selecting a saved address
            // to avoid race conditions while selects are being populated). country/governorate/city
            // may be empty strings which will be interpreted server-side as no filter.
            async function fetchQuoteWithParams(countryVal, govVal, cityVal) {
                if (!countryVal) {
                    setTotals(null);
                    if (quoteBox) quoteBox.innerHTML = '';
                    if (matchLevel) hideEl(matchLevel);
                    if (inputZone) inputZone.value = '';
                    if (inputPrice) inputPrice.value = '';
                    return;
                }
                const thisReq = ++_currentQuoteRequest;
                if (quoteBox) quoteBox.innerHTML = '<div class="small text-muted">Calculating shipping...</div>';
                try {
                    const params = new URLSearchParams();
                    params.append('country', countryVal);
                    if (govVal) params.append('governorate', govVal);
                    if (cityVal) params.append('city', cityVal);
                    params.append('all', '1');
                    const url = '/api/new-shipping/quote?' + params.toString();
                    console.debug('[checkout] requesting shipping quote (explicit) ->', url);
                    const res = await fetch(url);
                    if (thisReq !== _currentQuoteRequest) { CheckoutLogger.log('Dropping stale fetchQuoteWithParams response (outdated request)'); return; }
                    if (!res.ok) {
                        const text = await res.text().catch(() => '{unable to read body}');
                        CheckoutLogger.error('Failed to fetch shipping quote (explicit):', res.status, res.statusText, text);
                        if (quoteBox) quoteBox.innerHTML = '<div class="alert alert-danger small">Failed to get quote</div>';
                        setTotals(null);
                        if (inputZone) inputZone.value = '';
                        if (inputPrice) inputPrice.value = '';
                        if (matchLevel) hideEl(matchLevel);
                        return;
                    }
                    const data = await res.json();
                    if (thisReq !== _currentQuoteRequest) { CheckoutLogger.log('Dropping stale fetchQuoteWithParams JSON response'); return; }
                    console.debug('[checkout] shipping quote response (explicit):', data);
                    CheckoutLogger.log('Shipping quote response (explicit):', data);
                    const options = Array.isArray(data.data) ? data.data : [];
                    console.debug('[checkout] options length (explicit):', options.length, 'zones:', options.map(o => o.zone_id));
                    if (!options.length) {
                        if (quoteBox) quoteBox.innerHTML = '<div class="alert alert-info small">No shipping rule found for this location</div>';
                        setTotals(null);
                        if (inputZone) inputZone.value = '';
                        if (inputPrice) inputPrice.value = '';
                        if (matchLevel) hideEl(matchLevel);
                        return;
                    }

                    // reuse same rendering logic as fetchQuote
                    if (quoteBox) {
                        quoteBox.innerHTML = '<div class="shipping-options small">' + options.map((o, idx) => {
                            const priceLabel = o.price !== null ? '$' + parseFloat(o.price).toFixed(2) : 'Free';
                            const eta = o.estimated_days ? ` • ${o.estimated_days} days` : '';
                            return `<label class="shipping-option shipping-option-row"><input type="radio" name="_shipping_option" data-zone="${o.zone_id}" data-price="${o.price}" ${idx === 0 ? 'checked' : ''}> <strong>${o.zone_name}</strong> - ${priceLabel}${eta} <span class="small text-muted"> (${o.level})</span></label>`;
                        }).join('') + '</div>';

                        const radios = quoteBox.querySelectorAll('input[name="_shipping_option"]');
                        radios.forEach(r => r.addEventListener('change', function () {
                            const z = this.dataset.zone; const p = this.dataset.price;
                            if (inputZone) inputZone.value = z || '';
                            const parsed = (p === '' || p === null || typeof p === 'undefined') ? NaN : Number(p);
                            if (inputPrice) inputPrice.value = Number.isFinite(parsed) ? parsed.toFixed(2) : '';
                            setTotals(p);
                        }));

                        const first = quoteBox.querySelector('input[name="_shipping_option"]:checked');
                        if (first) {
                            if (inputZone) inputZone.value = first.dataset.zone || '';
                            const fp = first.dataset.price;
                            const parsed = (fp === '' || fp === null || typeof fp === 'undefined') ? NaN : Number(fp);
                            if (inputPrice) inputPrice.value = Number.isFinite(parsed) ? parsed.toFixed(2) : '';
                            setTotals(fp);
                        }
                    }
                } catch (e) {
                    CheckoutLogger.error('Error fetching explicit quote:', e);
                    if (quoteBox) quoteBox.innerHTML = '<div class="alert alert-danger small">Failed to get quote</div>';
                    setTotals(null);
                    if (inputZone) inputZone.value = '';
                    if (inputPrice) inputPrice.value = '';
                    if (matchLevel) hideEl(matchLevel);
                }
            }

            CheckoutLogger.log('Setting up event listeners...');
            CheckoutLogger.log('Country element:', country);
            CheckoutLogger.log('Governorate element:', gov);
            CheckoutLogger.log('City element:', city);

            // Hidden inputs to sync with visible selects for server fallback
            const hiddenCountry = document.getElementById('input-shipping-country');
            const hiddenGov = document.getElementById('input-shipping-governorate');
            const hiddenCity = document.getElementById('input-shipping-city');

            function syncHiddenLocationInputs() {
                if (hiddenCountry && country) hiddenCountry.value = country.value || '';
                if (hiddenGov && gov) hiddenGov.value = gov.value || '';
                if (hiddenCity && city) hiddenCity.value = city.value || '';
            }

            if (country) country.addEventListener('change', function () { syncHiddenLocationInputs(); loadGovs(); });
            if (gov) gov.addEventListener('change', function () { syncHiddenLocationInputs(); loadCities(); });
            if (city) city.addEventListener('change', function () { syncHiddenLocationInputs(); fetchQuote(); });

            // Transfer image toggling: show file input when selected gateway requires transfer image
            (function setupTransferImageToggle() {
                try {
                    const transferArea = document.getElementById('transfer-image-area');
                    const transferInput = document.getElementById('transfer_image');
                    if (!transferArea) return;
                    // parse once: data-requiring holds array of gateway slugs that require image
                    let requiring = [];
                    try { requiring = JSON.parse(transferArea.getAttribute('data-requiring') || '[]'); } catch (e) { requiring = []; }

                    function updateVisibilityForGateway(slug) {
                        if (!slug) { hideEl(transferArea); if (transferInput) transferInput.required = false; return; }
                        if (Array.isArray(requiring) && requiring.indexOf(slug) !== -1) {
                            showEl(transferArea); if (transferInput) transferInput.required = true;
                        } else { hideEl(transferArea); if (transferInput) transferInput.required = false; }
                    }

                    // attach listeners to gateway radios
                    const gwRadios = document.querySelectorAll('input[name="gateway"]');
                    gwRadios.forEach(r => r.addEventListener('change', function () { updateVisibilityForGateway(this.value); }));

                    // initial state: pick checked radio
                    const checked = document.querySelector('input[name="gateway"]:checked');
                    if (checked) updateVisibilityForGateway(checked.value);
                } catch (e) { CheckoutLogger.error('transfer image toggle init failed', e); }
            })();

            // Address selection: when user picks a saved address, populate the form and set selects then trigger fetchQuote
            const addressesList = document.getElementById('addresses-list');
            const selectedAddressIdInput = document.getElementById('selected-address-id');
            if (addressesList) {
                // helper: apply a selected address label to the form (reused on change and init)
                function selectAddressLabel(label) {
                    if (!label) return;
                    const countryId = label.dataset.country || '';
                    const govId = label.dataset.governorate || '';
                    const cityId = label.dataset.city || '';
                    const line1 = label.dataset.line1 || '';
                    const line2 = label.dataset.line2 || '';
                    const phone = label.dataset.phone || '';
                    // populate inputs
                    if (country) {
                        // set selects for UX, but fetch quote explicitly using dataset values to avoid race conditions
                        country.value = countryId;
                        // attempt to populate gov/city selects for UX but do not rely on them for quote
                        (async () => {
                            // suppress auto-fetch during select population to avoid race with explicit fetch
                            _suppressAutoQuote = true;
                            try {
                                await loadGovs();
                                if (govId && gov) { gov.value = govId; await loadCities(); if (city && cityId) { city.value = cityId; } }
                            } catch (e) { CheckoutLogger.warn('select population failed', e); }
                            // re-enable auto-quote after population
                            _suppressAutoQuote = false;
                        })();
                        // fetch shipping options directly for the selected address (explicit)
                        fetchQuoteWithParams(countryId, govId, cityId);
                    }
                    const nameInput = document.querySelector('input[name="customer_name"]'); if (nameInput && label.dataset.name) nameInput.value = label.dataset.name;
                    const phoneInput = document.querySelector('input[name="customer_phone"]'); if (phoneInput) phoneInput.value = phone;
                    const addrInput = document.querySelector('input[name="customer_address"]'); if (addrInput) addrInput.value = line1 + (line2 ? (', ' + line2) : '');
                    // set hidden selected address id for server to use
                    if (selectedAddressIdInput) selectedAddressIdInput.value = label.dataset.addrId || '';
                    // ensure hidden location inputs reflect chosen address immediately
                    syncHiddenLocationInputs();
                    // visual highlight
                    const all = addressesList.querySelectorAll('label.address-card-selectable');
                    all.forEach(l => l.classList.toggle('selected', l === label));
                }

                // if no addresses, hide addresses list (handled server-side) — this ensures graceful behavior
                addressesList.addEventListener('change', function (ev) {
                    const target = ev.target;
                    // radio inside label; find enclosing label
                    let label = target.closest && target.closest('label.address-card-selectable');
                    if (!label && target.tagName === 'LABEL') label = target;
                    if (!label) return;
                    selectAddressLabel(label);
                });

                // initialize if there is a pre-checked address (default) so it applies on page load
                (function initCheckedAddress() {
                    const pre = addressesList.querySelector('input[name="selected_address"]:checked');
                    if (pre) {
                        const label = pre.closest && pre.closest('label.address-card-selectable');
                        if (label) selectAddressLabel(label);
                    }
                })();
            }

            // initialize from server-provided config
            try {
                const initial = window.checkoutConfig?.initial || {};
                if (country && initial.country) { country.value = initial.country; (async () => { await loadGovs(); if (initial.governorate && gov) { gov.value = initial.governorate; await loadCities(); if (initial.city && city) { city.value = initial.city; fetchQuote(); } else fetchQuote(); } else fetchQuote(); syncHiddenLocationInputs(); })(); }
                else setTotals(null);
            } catch (e) { CheckoutLogger.error(e); setTotals(null); }
            // Handle checkout form submission for PayPal and other gateways
            const checkoutForm = document.querySelector('.checkout-form');
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', function (e) {
                    const selectedGateway = document.querySelector('input[name="gateway"]:checked');
                    const redirectDrivers = ['paypal', 'tap', 'paytabs', 'weaccept', 'payeer'];
                    const gw = selectedGateway ? selectedGateway.value : null;
                    if (gw === 'paypal') {
                        // PayPal support has been removed. Prevent submission and inform the user.
                        e.preventDefault();
                        const submitBtn = checkoutForm.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            const originalText = submitBtn.textContent;
                            submitBtn.disabled = true;
                            submitBtn.textContent = 'Processing...';
                            // restore shortly so the user can pick another method
                            setTimeout(() => { submitBtn.disabled = false; submitBtn.textContent = originalText; }, 800);
                        }
                        alert('The PayPal payment method has been removed. Please choose a different payment method.');
                        return false;
                    }

                    // If gateway is a redirect driver, submit via AJAX expecting JSON { redirect_url }
                    if (gw && redirectDrivers.indexOf(gw) !== -1) {
                        e.preventDefault();
                        const submitBtn = checkoutForm.querySelector('button[type="submit"]');
                        if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Processing...'; }
                        // Build FormData (handles file inputs too)
                        const fd = new FormData(checkoutForm);
                        // Ensure server treats this as AJAX so it responds with JSON redirect_url
                        const headers = new Headers();
                        headers.append('X-Requested-With', 'XMLHttpRequest');
                        headers.append('Accept', 'application/json');

                        fetch(checkoutForm.action, {
                            method: (checkoutForm.method || 'POST').toUpperCase(),
                            body: fd,
                            headers: headers,
                            credentials: 'same-origin',
                        }).then(async (res) => {
                            if (!res.ok) {
                                let txt = await res.text().catch(() => null);
                                console.error('[checkout] init failed', res.status, txt);
                                alert('Payment initialization failed. Please try another method.');
                                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Place Order'; }
                                return;
                            }
                            const json = await res.json().catch(() => null);
                            if (json && json.redirect_url) {
                                try {
                                    const redirect = json.redirect_url;
                                    // Direct to provider by default. If we ever introduce a flag in JSON (e.g., json.use_local_iframe), honor it.
                                    if (json.use_local_iframe && json.payment_id) {
                                        window.location.href = '/payments/iframe/' + encodeURIComponent(json.payment_id);
                                        return;
                                    }
                                } catch (e) { console.warn('iframe host navigation failed, falling back to direct redirect', e); }
                                window.location.href = json.redirect_url;
                                return;
                            }
                            // If server didn't return JSON redirect, fall back to full-page submit
                            checkoutForm.submit();
                        }).catch((err) => {
                            console.error('[checkout] ajax submit error', err);
                            alert('Payment initialization failed (network). Please try again.');
                            if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Place Order'; }
                        });
                        return false;
                    }
                    // For non-redirect drivers, let the form submit normally
                });
            }
        } catch (err) { CheckoutLogger.error('DOMContentLoaded handler failed', err); }
    });
})();

