// Catalog interactions: wishlist toggle, quick add to cart, compare toggle
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]') ?.content;
    function toast(msg, type = 'info')
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
    async function post(url, data)
    {
        const res = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, body: JSON.stringify(data) });
        let json = null; try {
            json = await res.json(); } catch (e) {
            }
            if (!res.ok) {
                throw json ||  message: 'Error' }; return json;
    }
    document.addEventListener('click', async e => {
        const favBtn = e.target.closest('.fav-btn[data-product]');
        if (favBtn) {
            e.preventDefault();
            const pid = favBtn.dataset.product;
            favBtn.disabled = true;
            try {
                const r = await post('/wishlist/toggle', { product_id: pid });
                favBtn.textContent = r.state === 'added' ? '❤' : '♡';
                favBtn.classList.toggle('active', r.state === 'added');
                const wbadge = document.querySelector('[data-wishlist-count]'); if (wbadge) {
                    wbadge.textContent = r.count ?  ? wbadge.textContent; }
                toast(r.state === 'added' ? 'Added to wishlist' : 'Removed from wishlist', 'success');
            } catch (err) {
                toast('Wishlist error', 'error'); }
            favBtn.disabled = false;
        }
        const quickBtn = e.target.closest('.cart-quick[data-product]');
        if (quickBtn) {
            e.preventDefault();
            const pid = quickBtn.dataset.product;
            quickBtn.disabled = true;
            try {
                const r = await post('/cart/add', { product_id: pid, qty: 1 });
                toast(window.__tFn ? window.__tFn('added_to_cart', 'Added to cart') : 'Added to cart', 'success');
                // update cart badge if present
                const badge = document.querySelector('.act-cart .badge');
                if (badge && r && r.count) {
                    badge.textContent = r.count; }
            } catch (err) {
                toast('Cart error', 'error'); }
            quickBtn.disabled = false;
        }
        const compareBtn = e.target.closest('.compare-btn[data-product]');
        if (compareBtn) {
            e.preventDefault();
            const pid = compareBtn.dataset.product;
            compareBtn.disabled = true;
            try {
                const r = await post('/compare/toggle', { product_id: pid });
                compareBtn.classList.toggle('is-active', r.state === 'added');
                toast(r.state === 'added' ? 'Added to compare' : 'Removed from compare', 'info');
                window.dispatchEvent(new CustomEvent('compare:update', { detail: { count: r.count } }));
            } catch (err) {
                toast('Compare error', 'error'); }
            compareBtn.disabled = false;
        }
        const notifyBtn = e.target.closest('.notify-btn[data-product]');
        if (notifyBtn) {
            e.preventDefault();
            // prefer to open the central modal via custom event so UI is consistent
            const event = new CustomEvent('notify:open', { detail: { triggerElement: notifyBtn } });
            document.dispatchEvent(event);
            return;
        }
    });

    // price range dual slider
    function initPriceRange()
    {
        const min = document.getElementById('prMin');
        const max = document.getElementById('prMax');
        const minVal = document.getElementById('prMinVal');
        const maxVal = document.getElementById('prMaxVal');
        const minH = document.getElementById('prMinHidden');
        const maxH = document.getElementById('prMaxHidden');
        if (!min || !max) {
            return;
        }
        function clamp()
        {
            let a = Number(min.value); let b = Number(max.value);
            if (a > b) {
                [a, b] = [b, a]; min.value = a; max.value = b; }
            minVal.textContent = a; maxVal.textContent = b; minH.value = a; maxH.value = b;
        }
        min.addEventListener('input', clamp); max.addEventListener('input', clamp); clamp();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPriceRange); } else {
        initPriceRange();
        }
})();

