// header-inline.js
// Wishlist fetch interception + currency switching logic (extracted from inline scripts)
(function () {
  // Safe number helper
    function safeNum(n)
    {
        return Number(n) || 0; }
  // Intercept fetch for wishlist count updates
    const _fetch = window.fetch;
    window.fetch = function () {
        return _fetch.apply(this, arguments).then(function (resp) {
            try {
                const url = (arguments[0] || '').toString();
                if (url.indexOf('wishlist') !== -1) {
                    resp.clone().json().then(function (json) {
                        if (json && typeof json.count !== 'undefined') {
                            document.querySelectorAll('[data-wishlist-count]').forEach(n => { n.textContent = safeNum(json.count); });
                        }
                    }).catch(() => {});
                }
            } catch (e) {
            }
            return resp;
        });
    };

  // Currency switching
    const cfg = document.getElementById('currency-config');
    if (!cfg) {
        return;
    }
    try {
        window.appCurrencySymbol = window.appCurrencySymbol || JSON.parse(cfg.dataset.symbol || '"$"');
    } catch (e) {
        window.appCurrencySymbol = window.appCurrencySymbol || '$'; }
    try {
        window.defaultCurrency = window.defaultCurrency || JSON.parse(cfg.dataset.default || 'null');
    } catch (e) {
        window.defaultCurrency = window.defaultCurrency || null; }

    function postJSON(url,data)
    {
        return fetch(url,{ method:'POST', headers: (function () {
            const base = {'Content-Type':'application/json'}; const t = document.querySelector('meta[name=csrf-token]'); if (t) {
                base['X-CSRF-TOKEN'] = t.getAttribute('content');
            } return base; })(), body:JSON.stringify(data || {}) }).then(r => r.json());
    }
    function parseNumber(str)
    {
        if (!str && str !== 0) {
            return 0;
        } return Number(String(str).replace(/[^0-9.\-]/g,'')) || 0; }
    function formatNumber(num,dec)
    {
        return Number(num).toLocaleString(undefined,{minimumFractionDigits:dec,maximumFractionDigits:dec}); }
    function applyRateToNode(node,rate,dec)
    {
        const n = parseNumber(node.textContent || node.innerText || ''); const converted = n * rate; node.textContent = (window.appCurrencySymbol || '$') + ' ' + formatNumber(converted,dec || 2); }
    function applyRateToSelector(sel,rate,dec)
    {
        document.querySelectorAll(sel).forEach(n => applyRateToNode(n,rate,dec)); }

    document.querySelectorAll('.currency-chip').forEach(btn => {
        btn.addEventListener('click', function () {
            const code = this.dataset.currency; if (!code) {
                return;
            }
            postJSON('/currency/switch',{code:code}).then(function (resp) {
                if (resp && resp.status === 'ok' && resp.currency) {
                    const cur = resp.currency;
                    const prevRate = window.__app_prev_rate || (window.defaultCurrency ? (window.defaultCurrency.exchange_rate || 1) : 1);
                    const newRate = cur.exchange_rate || 1;
                    let multiplier = 1; try {
                        multiplier = Number(newRate) / Number(prevRate);} catch (e) {
                                    multiplier = 1; }
                        window.appCurrencySymbol = cur.symbol || window.appCurrencySymbol;
                        window.__app_prev_rate = newRate;
                        applyRateToSelector('.product-price .price-current', multiplier, 0);
                        applyRateToSelector('.product-price .price-sale', multiplier, 0);
                        applyRateToSelector('.product-price .price-original', multiplier, 0);
                        applyRateToSelector('.cart-item-card .price-current', multiplier, 2);
                        applyRateToSelector('[data-cart-line-price]', multiplier, 2);
                        applyRateToSelector('[data-cart-line-total]', multiplier, 2);
                        applyRateToSelector('.order-summary .subtotal-amount', multiplier, 2);
                        applyRateToSelector('.order-summary .discount-amount', multiplier, 2);
                        applyRateToSelector('.order-summary .total-amount', multiplier, 2);
                        applyRateToSelector('.coupon-discount-value', multiplier, 2);
                        const subtotalNode = document.querySelector('.order-summary .subtotal-amount');
                        if (subtotalNode) {
                            const displayedVal = parseNumber(subtotalNode.textContent || subtotalNode.innerText);
                            const cf = document.querySelector('form[data-coupon-form]');
                            if (cf) {
                                let hidden = cf.querySelector('input[name="displayed_total"]'); if (!hidden) {
                                              hidden = document.createElement('input'); hidden.type = 'hidden'; hidden.name = 'displayed_total'; cf.appendChild(hidden);} hidden.value = displayedVal; }
                        }
                } else {
                    alert('Failed to switch currency'); }
            }).catch(() => alert('Failed to switch currency'));
        });
    });
})();

