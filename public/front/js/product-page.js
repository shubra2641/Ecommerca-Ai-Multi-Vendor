// Product page behaviors: variation handling and product page init
document.addEventListener('DOMContentLoaded', function () {
    if (window.ECommerceApp && typeof window.ECommerceApp.initProductPage === 'function') {
        try {
            window.ECommerceApp.initProductPage(); } catch (e) {
            console.error(e); }
    }

    const variationsEl = document.getElementById('productVariations');
    // If there is no variations element, this is a simple product; exit to avoid disabling its Add button.
    if (!variationsEl) {
        return; // Simple product: do not run variation logic
    }
    const variations = JSON.parse(variationsEl.dataset.json || '[]');
    // read used attributes from the variation grid if present
    const variationGrid = document.getElementById('variationGridCard');
    const usedAttrs = variationGrid ? (JSON.parse(variationGrid.dataset.used || '[]')) : [];
    // attr blocks limited to used attributes only
    const attrBlocks = Array.from(document.querySelectorAll('.variation-attr-block')).filter(b => {
        if (!usedAttrs || !usedAttrs.length) {
            return true; // no restriction
        }
        return usedAttrs.includes(b.dataset.attr);
    });
    // apply swatch colors via data attribute to avoid inline style
    document.querySelectorAll('.attr-option-btn.color[data-swatch]').forEach(btn => {
        const c = btn.getAttribute('data-swatch');
        if (c) {
            btn.style.setProperty('--swatch', c);
        }
    });
    // safe default currency symbol from server
    const __defaultCurrencySymbol = window.appCurrencySymbol || '$';
    const hiddenVar = document.getElementById('selectedVariationId');
    const priceWrap = document.querySelector('.product-pricing');
    const topStockBadge = document.getElementById('topStockBadge');
    const saleBadge = document.getElementById('globalSaleBadge');
    const stockEl = document.querySelector('.stock-status');
    const addBtn = document.querySelector('.purchase-box .btn-buy');
    // Unified notification (prefer window.notify, fallback to showToast, then alert)
    function showToast(msg, type = 'info')
    {
        if (window.notify && window.notify[type]) {
            return window.notify[type](msg);
        }
        if (window.notify && window.notify.info) {
            return window.notify.info(msg);
        }
        if (window.showToast) {
            return window.showToast(msg, type);
        }
        try {
            alert(msg); } catch (e) {
            }
    }

    function normalize(obj)
    {
        const clone = { ...obj };
        return JSON.stringify(Object.keys(clone).sort().reduce((acc, k) => { acc[k] = clone[k]; return acc; }, {}));
    }
    const variationMap = variations.map(v => ({
        id: v.id,
        attrs: v.attribute_data || {},
        price: v.price,
        sale_price: v.sale_price,
        sale_start: v.sale_start,
        sale_end: v.sale_end,
        stock: v.manage_stock ? (v.stock_qty - v.reserved_qty) : null,
        image: v.image
    }));

    function currentSelection()
    {
        const sel = {};
        attrBlocks.forEach(b => {
            const active = b.querySelector('.option-btn.active');
            if (active) {
                sel[b.dataset.attr] = active.dataset.attrValue; }
        });
        return sel;
    }

    function isOnSale(v)
    {
        if (!v.sale_price || v.sale_price >= v.price) {
            return false;
        }
        const now = new Date();
        if (v.sale_start && new Date(v.sale_start) > now) {
            return false;
        }
        if (v.sale_end && new Date(v.sale_end) < now) {
            return false;
        }
        return true;
    }

    // Preload images for smoother swaps
    variationMap.filter(v => v.image).forEach(v => { const i = new Image(); i.src = v.image.startsWith('http') ? v.image : (window.location.origin + '/' + v.image.replace(/^\//, '')); });

    function updateDisabledStates()
    {
        attrBlocks.forEach(block => {
            const attr = block.dataset.attr;
            block.querySelectorAll('.option-btn').forEach(btn => {
                const val = btn.dataset.attrValue;
                const sel = currentSelection();
                delete sel[attr];
                sel[attr] = val;
                const possible = variationMap.some(v => Object.entries(sel).every(([k, vv]) => v.attrs[k] === vv));
                btn.disabled = !possible;
                btn.classList.toggle('disabled', !possible);
            });
        });
    }

    function updateUI()
    {
        const sel = currentSelection();
        const selectedKeysCount = Object.keys(sel).length;
        if (selectedKeysCount === 0) {
            if (hiddenVar) {
                hiddenVar.value = '';
            }
            // no selection yet -> show base price and disable add
            addBtn && addBtn.setAttribute('disabled', 'disabled');
            return;
        }
        // find candidates that match the currently selected attribute values (allow partial selection)
        const candidates = variationMap.filter(v => Object.entries(sel).every(([k, vv]) => v.attrs[k] === vv));
        if (!candidates || candidates.length === 0) {
            if (hiddenVar) {
                hiddenVar.value = '';
            }
            showToast('Option combination unavailable', 'error');
            addBtn && addBtn.setAttribute('disabled', 'disabled');
            return;
        }
        // If exactly one candidate remains, treat it as the selected variation
        const match = (candidates.length === 1) ? candidates[0] : null;
        if (!match) {
            // multiple possible variations remain: show a price range if available, do not enable Add
            if (hiddenVar) {
                hiddenVar.value = '';
            }
            const cs = (window.appCurrencySymbol || __defaultCurrencySymbol);
            const prices = candidates.map(c => isOnSale(c) ? c.sale_price : c.price).filter(p => typeof p !== 'undefined' && p !== null);
            if (prices.length) {
                const minP = Math.min(...prices);
                const maxP = Math.max(...prices);
                if (priceWrap) {
                    if (minP === maxP) {
                        priceWrap.innerHTML = ` < span class = "price-current" > ${cs} ${parseFloat(minP).toFixed(2)} < / span > `;
                    } else {
                        priceWrap.innerHTML = ` < span class = "price-current" > ${cs} ${parseFloat(minP).toFixed(2)} < / span > < span class = "price-range-sep" > - < / span > < span class = "price-current" > ${cs} ${parseFloat(maxP).toFixed(2)} < / span > `;
                    }
                }
            }
            addBtn && addBtn.setAttribute('disabled', 'disabled');
            return;
        }
        if (hiddenVar) {
            hiddenVar.value = match.id;
        }
        if (priceWrap) {
            const cs = (window.appCurrencySymbol || __defaultCurrencySymbol);
            if (isOnSale(match)) {
                // compute discount percent
                const discountPct = Math.round(((match.price - match.sale_price) / match.price) * 100);
                priceWrap.innerHTML = ` < span class = "price-current" > ${cs} ${parseFloat(match.sale_price).toFixed(2)} < / span > < span class = "price-original" > ${cs} ${parseFloat(match.price).toFixed(2)} < / span > `;
                if (saleBadge) {
                    saleBadge.textContent = discountPct + '% Off';
                    saleBadge.style.display = '';
                }
            } else {
                priceWrap.innerHTML = ` < span class = "price-current" > ${cs} ${parseFloat(match.price).toFixed(2)} < / span > `;
                if (saleBadge) {
                    // hide badge if no sale for this variation (only when variable product)
                    saleBadge.style.display = 'none';
                }
            }
        }
        if (match.image) {
            // The main image element has id 'productMainImage' in Blade
            const main = document.getElementById('productMainImage');
            if (main) {
                main.classList.add('image-placeholder');
                const loader = new Image();
                loader.onload = () => { main.src = loader.src; main.classList.remove('image-placeholder'); };
                loader.src = match.image.startsWith('http') ? match.image : (window.location.origin + '/' + match.image.replace(/^\//, ''));
            }
        }
        if (stockEl) {
            // compute level label
            function levelFor(n)
            {
                if (n === 0) {
                    return 'out';
                }
                if (n === null || typeof n === 'undefined') {
                    return 'unknown';
                }
                if (n <= 5) {
                    return 'low';
                }
                if (n <= 20) {
                    return 'mid';
                }
                return 'high';
            }
            if (match.stock === 0) {
                stockEl.textContent = 'Out of stock';
                stockEl.style.color = '#c00';
                addBtn && addBtn.setAttribute('disabled', 'disabled');
                document.dispatchEvent(new CustomEvent('variationStockUpdate', { detail: { stock: 0, level: levelFor(0) } }));
            } else if (match.stock === null || typeof match.stock === 'undefined') {
                stockEl.textContent = 'In stock';
                stockEl.style.color = '#0a7a3a';
                addBtn && addBtn.removeAttribute('disabled');
                document.dispatchEvent(new CustomEvent('variationStockUpdate', { detail: { stock: null, level: levelFor(null) } }));
            } else {
                stockEl.textContent = `${match.stock} in stock`;
                stockEl.style.color = '#0a7a3a';
                addBtn && addBtn.removeAttribute('disabled');
                document.dispatchEvent(new CustomEvent('variationStockUpdate', { detail: { stock: match.stock, level: levelFor(match.stock) } }));
            }
        }
        // update top stock badge (already handled by event) but ensure immediate text if needed
        if (topStockBadge) {
            // Nothing extra; left for future extension
        }
    }

    attrBlocks.forEach(block => {
        block.addEventListener('click', e => {
            const btn = e.target.closest('.option-btn');
            if (!btn) {
                return;
            }
            block.querySelectorAll('.option-btn').forEach(b => b.classList.toggle('active', b === btn));
            block.querySelectorAll('.color-swatch-wrapper').forEach(w => w.classList.remove('active'));
            const wrap = btn.closest('.color-swatch-wrapper'); if (wrap) {
                wrap.classList.add('active');
            }
            updateDisabledStates(); updateUI();
        });
        block.querySelectorAll('.option-btn').forEach(b => {
            b.setAttribute('tabindex', '0');
            b.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault(); b.click(); } });
        });
    });
    updateDisabledStates(); updateUI();

    // Ensure the add-to-cart form requires a selectedVariationId for variable products
    const addToCartForm = document.querySelector('.add-to-cart-form');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function (e) {
            if (variations && variations.length && (!hiddenVar || !hiddenVar.value)) {
                // prevent adding ambiguous selection
                e.preventDefault();
                showToast('Please select options until a unique variation is found', 'error');
                return false;
            }
            return true;
        });
    }
});

