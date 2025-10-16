document.addEventListener('DOMContentLoaded', function () {
    try {
        const mainImg = document.getElementById('productMainImage');
        if (!mainImg) {
            return;
        }
        const thumbs = document.querySelectorAll('.thumbnail-gallery .thumbnail');
        thumbs.forEach(btn => {
            btn.addEventListener('click', function (e) {
                const imgUrl = this.getAttribute('data-image');
                if (!imgUrl) {
                    return;
                }
                // update main image src
                mainImg.setAttribute('src', imgUrl);
                // manage active class
                thumbs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    } catch (err) {
        console && console.warn('product-show.js error', err);
    }
    // Rely on server-side flash + notifications.js (admin-style) to show messages after reload
});

// Unified notification helper (prefer window.notify then legacy alias / alert)
function productNotify(message, type = 'info')
{
    if (window.notify && window.notify[type]) {
        return window.notify[type](message);
    }
    if (window.notify && window.notify.info) {
        return window.notify.info(message);
    }
    if (window.showToast) {
        return window.showToast(message, type);
    }
    try {
        alert(message); } catch (_) {
        }
}

// Quantity pill handlers
document.addEventListener('DOMContentLoaded', function () {
    try {
        const qtyInput = document.getElementById('qtyInputSide');
        const qtyDisplay = document.getElementById('qtyDisplay');
        const incBtn = document.querySelector('.qty-increase');
        const trashBtn = document.querySelector('.qty-trash');
        const form = document.querySelector('.add-to-cart-form');
        const buyNowBtn = document.getElementById('buyNowBtn');

        if (qtyInput && qtyDisplay) {
            const setQty = (n) => {
                const val = Math.max(1, parseInt(n) || 1);
                qtyInput.value = val;
                qtyDisplay.textContent = val;
            }
            // initialize
            setQty(qtyInput.value || 1);

            if (incBtn) {
                incBtn.addEventListener('click', () => {
                    setQty(parseInt(qtyInput.value || 1) + 1);
                });
            }
            if (trashBtn) {
                trashBtn.addEventListener('click', () => {
                    // reset to 1
                    setQty(1);
                });
            }
        }

        // Prevent submit if variable product and no variation selected
        function canSubmit()
        {
            const varInput = document.getElementById('selectedVariationId');
            // If no variation field exists, it's a simple product => allow
            if (!varInput) {
                return true;
            }
            if (varInput.value === '') {
                return false;
            }
            return true;
        }
        // rely on centralized notifications (public/front/js/notifications.js) for displaying messages (window.notify or window.showToast fallback)

        if (buyNowBtn && form) {
            buyNowBtn.addEventListener('click', function (e) {
                if (!canSubmit()) {
                    productNotify(window.__tFn('select_options_first', 'Please select product options first.'), 'error');
                    return;
                }
                const buyFlag = document.getElementById('buyNowFlag');
                if (buyFlag) {
                    buyFlag.value = '1';
                }
                form.submit();
            });
        }
        if (form) {
            form.addEventListener('submit', function (e) {
                const btn = form.querySelector('.btn-buy');
                if (!canSubmit()) {
                    e.preventDefault();
                    productNotify(window.__tFn('select_options_first', 'Please select product options first.'), 'error');
                    if (btn) {
                        btn.classList.remove('loading');
                        btn.disabled = false;
                    }
                    return;
                }
                // Show loading state and perform AJAX POST to add to cart
                if (btn) {
                    btn.classList.add('loading');
                    btn.disabled = true;
                }
                e.preventDefault();
                try {
                    const action = form.getAttribute('action') || window.location.href;
                    const method = (form.getAttribute('method') || 'POST').toUpperCase();
                    const fd = new FormData(form);
                    // Ensure variation id present for variable products
                    const varInput = document.getElementById('selectedVariationId');
                    if (varInput && varInput.value) {
                        fd.set('variation_id', varInput.value);
                    }
                    // send X-Requested-With to hint server to return JSON
                    fetch(action, {
                        method: method,
                        body: fd,
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    }).then(response => {
                        const ct = response.headers.get('content-type') || '';
                        if (ct.indexOf('application/json') !== -1) {
                            return response.json();
                        }
                        return response.text().then(txt => ({ html: txt, status: response.status }));
                    }).then(data => {
                        // normalize response shapes: support { success: bool, cart_count } and { status:'ok', count }
                        const isSuccess = (data && (data.success === true || data.status === 'ok' || data.status === 'OK' || data.status === 'ok'));
                        const msg = (data && (data.message || data.msg || (isSuccess ? 'Added to cart' : null))) || (data && data.html ? 'Added to cart' : null);
                        const cartCount = data && (data.cart_count ?  ? data.count ?  ? data.count);
                        if (!isSuccess && data && data.success === false) {
                            productNotify(data.message || window.__tFn('failed_add_to_cart', 'Failed to add to cart'), 'error');
                        } else {
                            productNotify(msg || window.__tFn('added_to_cart', 'Added to cart'), isSuccess ? 'success' : 'info');
                            // Update mini cart badge if helper exists (front.js exposes updateCartCount)
                            try {
                                if (window.ECommerceApp && typeof window.ECommerceApp.updateCartCount === 'function' && cartCount !== undefined) {
                                    window.ECommerceApp.updateCartCount(cartCount);
                                } else if (typeof updateCartCount === 'function' && cartCount !== undefined) {
                                    updateCartCount(cartCount);
                                } else if (window.updateCartCount && cartCount !== undefined) {
                                    window.updateCartCount(cartCount);
                                } else if (window.App && typeof window.App.updateCartCount === 'function' && cartCount !== undefined) {
                                    window.App.updateCartCount(cartCount);
                                }
                            } catch (cartErr) {
                                console.warn('cart count update fail', cartErr); }
                            // Do not reload the page; keep UX snappy. Server-side flash still emitted for full navigations.
                        }
                    }).catch(err => {
                        console && console.warn('Add to cart error', err);
                        productNotify(window.__tFn('failed_add_to_cart', 'Failed to add to cart'), 'error');
                    }).finally(() => {
                        if (btn) {
                            btn.classList.remove('loading');
                            btn.disabled = false;
                        }
                    });
                } catch (err) {
                    console && console.warn('Add to cart exception', err);
                    productNotify(window.__tFn('failed_add_to_cart', 'Failed to add to cart'), 'error');
                    if (btn) {
                        btn.classList.remove('loading'); btn.disabled = false; }
                }
            });
        }
    } catch (e) {
        console && console.warn('qty handlers', e) }
});

// SKU copy and tags expand handlers
document.addEventListener('DOMContentLoaded', function () {
    try {
        const copyBtn = document.getElementById('copySkuBtn');
        const skuVal = document.getElementById('skuValue');
        if (copyBtn && skuVal) {
            copyBtn.addEventListener('click', function () {
                const text = skuVal.textContent.trim();
                if (!text) {
                    return;
                }
                navigator.clipboard ?.writeText(text).then(() => {
                    productNotify(window.__tFn('sku_copied', 'SKU copied'), 'success');
                }).catch(() => { productNotify(window.__tFn('failed_copy', 'Failed to copy'), 'error'); });
            });
        }

        const moreBtn = document.getElementById('showMoreTags');
        const moreHidden = document.getElementById('tagMoreHidden');
        if (moreBtn && moreHidden) {
            moreBtn.addEventListener('click', function () {
                const expanded = moreBtn.getAttribute('aria-expanded') === 'true';
                moreBtn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                moreHidden.style.display = expanded ? 'none' : 'block';
                if (expanded) {
                    const remaining = moreBtn.getAttribute('data-more');
                    moreBtn.textContent = '+' + remaining + ' more';
                } else {
                    moreBtn.textContent = 'Show less';
                }
            });
        }
    } catch (err) {
        console.warn('sku/tags handler', err); }
});

// Delivery ETA countdown logic
document.addEventListener('DOMContentLoaded', function () {
    const bar = document.getElementById('deliveryEtaBar');
    if (!bar) {
        return;
    }
    const hasDiscount = bar.getAttribute('data-has-discount') === '1';
    if (!hasDiscount) {
        // hide / collapse if no discount or flash logic
        bar.style.display = 'none';
        return;
    }
    const cdEl = document.getElementById('etaCountdown');
    const dateEl = document.getElementById('etaDate');
    const prog = document.getElementById('etaProgress');
    // Assumptions: Order cutoff 5 hours from now, delivery date = today + 3 business days
    // (Could be replaced by server-provided config via data-* attributes)
    const now = new Date();
    const cutoff = new Date(now.getTime() + 5 * 60 * 60 * 1000); // 5 hours from now
    // Compute delivery date skipping weekends
    function addBusinessDays(date, days)
    {
        const result = new Date(date);
        let added = 0;
        while (added < days) {
            result.setDate(result.getDate() + 1);
            const day = result.getDay();
            if (day !== 0 && day !== 6) {
                added++; }
        }
        return result;
    }
    const deliveryDate = addBusinessDays(now, 3);
    if (dateEl) {
        const opts = { weekday: 'short', month: 'short', day: 'numeric' };
        dateEl.textContent = deliveryDate.toLocaleDateString(undefined, opts);
    }
    const totalMs = cutoff - now;
    function tick()
    {
        const now2 = new Date();
        const remain = cutoff - now2;
        if (remain <= 0) {
            if (cdEl) {
                cdEl.textContent = '00:00:00';
            }
            if (prog) {
                prog.style.width = '100%';
            }
            clearInterval(intv);
            return;
        }
        const h = Math.floor(remain / 3600000);
        const m = Math.floor((remain % 3600000) / 60000);
        const s = Math.floor((remain % 60000) / 1000);
        if (cdEl) {
            cdEl.textContent = `$String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        if (prog) {
            const pct = ((totalMs - remain) / totalMs) * 100;
            prog.style.width = pct.toFixed(2) + '%';
        }
    }
    tick();
    const intv = setInterval(tick, 1000);
});

// Listen for custom events from variation logic to update top stock badge
document.addEventListener('variationStockUpdate', function (e) {
    try {
        const detail = e.detail || {}; // { stock, status }
        const badge = document.getElementById('topStockBadge');
        if (!badge) {
            return;
        }
        const classes = ['low-stock', 'mid-stock', 'high-stock', 'out-stock'];
        badge.classList.remove(...classes);
        let cls = 'high-stock';
        if (detail.stock === 0) {
            cls = 'out-stock';
        } else if (typeof detail.stock === 'number') {
            if (detail.stock <= 5) {
                cls = 'low-stock';
            } else if (detail.stock <= 20) {
                cls = 'mid-stock';
            }
        }
        badge.classList.add(cls);
        // build display text
        let label = '';
        if (detail.stock === 0) {
            label = 'Out of stock';
        } else if (typeof detail.stock === 'number') {
            label = 'In stock (' + detail.stock + ')';
        } else {
            label = 'In stock';
        }
        // add level suffix (Low/Mid/High)
        const level = detail.level || (typeof detail.stock === 'number' ? (detail.stock <= 5 ? 'Low' : (detail.stock <= 20 ? 'Mid' : 'High')) : '');
        badge.textContent = label + (level ? (' • ' + level + ' stock') : '');
        badge.setAttribute('aria-live', 'polite');
    } catch (err) {
        console.warn('variationStockUpdate handler error', err); }
});

