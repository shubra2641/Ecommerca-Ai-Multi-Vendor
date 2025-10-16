// Guard against double inclusion
if (window.__notifyInit) {
/* already initialized */ } else {
    window.__notifyInit = true;
}

document.addEventListener('DOMContentLoaded', function () {
    // Show server flash if present (no inline JS in Blade)
    try {
        const serverFlashEl = document.getElementById('serverFlash');
        if (serverFlashEl && serverFlashEl.dataset.flash) {
            const msg = serverFlashEl.dataset.flash;
            if (msg) {
                if (window.notify && window.notify.info) {
                    window.notify.info(msg); } else if (window.showToast) {
                    window.showToast(msg, 'info'); } else {
                        try {
                                        alert(msg); } catch (_) {
                                        }
                    }
            }
        }
    } catch (e) {
/* ignore */ }
    // Notify button handler - open modal and submit
    const notifyBtn = document.getElementById('notifyBtn');
    const notifyModalEl = document.getElementById('notifyModal');
    const notifyEmail = document.getElementById('notifyEmail');
    const notifyPhone = document.getElementById('notifyPhone');
    const notifyPhoneError = document.getElementById('notifyPhoneError');
    const notifySubmit = document.getElementById('notifySubmit');
    let iti = null;
    // Initialize intl-tel-input if available
    if (window.intlTelInput && notifyPhone) {
        iti = window.intlTelInput(notifyPhone, { initialCountry: 'auto', utilsScript: '/vendor/intl-tel-input/utils.js', geoIpLookup: function (callback) {
            callback('eg'); } });
    }
    function showToast(text, type = 'info')
    {
        if (window.notify && window.notify[type]) {
            return window.notify[type](text);
        }
        if (window.notify && window.notify.info) {
            return window.notify.info(text);
        }
        if (window.showToast) {
            return window.showToast(text, type);
        }
        try {
            alert(text); } catch (e) {
            }
    }
    let notifyModal = null;
    if (notifyModalEl) {
        notifyModal = new bootstrap.Modal(notifyModalEl);
    }

    function validatePhoneIntl()
    {
        if (!iti) {
            return true; // fallback to basic
        }
        return iti.isValidNumber();
    }

    // Helper to extract product id from any notify trigger
    function getProductId(el)
    {
        if (!el) {
            return null;
        }
        return el.dataset.productId || el.getAttribute('data-product-id') || el.dataset.product || el.getAttribute('data-product') || null;
    }
    // support opening modal from any .notify-btn (cards or detail). Keep track of current trigger
    let currentNotifyTrigger = null;
    async function openNotifyModalFor(trigger)
    {
        if (!notifyModalEl) {
            return false;
        }
        currentNotifyTrigger = trigger || null;
        const dataEmail = (trigger && (trigger.dataset.email || trigger.getAttribute('data-email'))) || (window.userEmail || '');
        const dataPhone = (trigger && (trigger.dataset.phone || trigger.getAttribute('data-phone'))) || '';
        if (notifyEmail) {
            notifyEmail.value = dataEmail || '';
        }
        if (notifyPhone) {
            notifyPhone.value = dataPhone || '';
        }
        if (iti && dataPhone) {
            iti.setNumber(dataPhone);
        }
        if (notifyPhoneError) {
            notifyPhoneError.style.display = 'none';
        }
        notifyModal.show();
        return true;
    }

    // delegate clicks on any notify button to open modal
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.notify-btn');
        if (!btn) {
            return;
        }
        e.preventDefault();
        // open modal if present, otherwise fallback prompt
        if (notifyModalEl) {
            openNotifyModalFor(btn);
        } else {
            // Prompt fallback
            let email = btn.dataset.email || window.userEmail || '';
            let phone = btn.dataset.phone || '';
            if (!email && !phone) {
                const choice = window.prompt('Enter email or phone to be notified:');
                if (!choice) {
                    return;
                }
                if (choice.includes('@')) {
                    email = choice; } else {
                    phone = choice;
                    }
            }
            const productId = btn.dataset.product || btn.getAttribute('data-product');
            fetch('/notify/product', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ product_id: productId, email: email || null, phone: phone || null, type: 'back_in_stock' }) })
                .then(r => r.json()).then(data => {
                    if (data && (data.ok || data.status === 'ok')) {
                        showToast(window.__tFn('subscription_saved', 'Subscription saved'), 'success');
                        btn.classList.add('subscribed');
                        btn.querySelector('.notify-label')?.classList.add('d-none');
                        btn.querySelector('.notify-subscribed')?.classList.remove('d-none');
                    } else {
                        showToast(data.message || window.__tFn('network_error', 'Network error'), 'error');
                    }
                }).catch(() => showToast(window.__tFn('network_error', 'Network error'), 'error'));
        }
    });

    // support programmatic open via custom event
    document.addEventListener('notify:open', function (ev) {
        const d = ev.detail || {};
        // if detail contains a DOM element reference, use it; otherwise pass null and prefill inputs
        let trigger = d.triggerElement || null;
        if (!trigger && d.productId) {
            // create a synthetic trigger object with dataset
            trigger = document.createElement('button');
            trigger.dataset.productId = d.productId;
            if (d.email) {
                trigger.dataset.email = d.email;
            }
            if (d.phone) {
                trigger.dataset.phone = d.phone;
            }
        }
        openNotifyModalFor(trigger);
    });

    // handle submit for modal using currentNotifyTrigger to get product id
    if (notifySubmit) {
        notifySubmit.addEventListener('click', async function () {
            const productId = getProductId(currentNotifyTrigger) || getProductId(notifyBtn);
            const email = notifyEmail ?.value ?.trim() || null;
            let phone = notifyPhone ?.value ?.trim() || null;
            if (iti && phone) {
                phone = iti.getNumber();
            }
            if (!email && !phone) {
                if (notifyPhoneError) {
                    notifyPhoneError.textContent = 'Please provide an email or phone'; notifyPhoneError.style.display = 'block'; }
                return;
            }
            if (phone && iti && !validatePhoneIntl()) {
                if (notifyPhoneError) {
                    notifyPhoneError.textContent = 'Invalid phone format'; notifyPhoneError.style.display = 'block'; }
                return;
            }
            notifySubmit.disabled = true;
            try {
                const res = await fetch('/notify/product', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '' }, body : JSON.stringify({ product_id : productId, email, phone, type : 'back_in_stock' }) });
                const data = await res.json();
                if (data && (data.ok || data.status === 'ok')) {
                    showToast(window.__tFn('subscription_saved', 'Subscription saved'), 'success');
                    // toggle subscribed state in UI for both product page and card
                    document.querySelectorAll('.notify-btn[data-product]') ?.forEach(function (b) {
                        if ((b.dataset.product || b.getAttribute('data-product')) == productId) {
                            b.classList.add('subscribed'); b.querySelector('.notify-label') ?.classList.add('d-none'); b.querySelector('.notify-subscribed') ?.classList.remove('d-none'); } });
                    if (currentNotifyTrigger) {
                        currentNotifyTrigger.classList.add('subscribed'); currentNotifyTrigger.querySelector('.notify-label') ?.classList.add('d-none'); currentNotifyTrigger.querySelector('.notify-subscribed') ?.classList.remove('d-none'); }
                    if (notifyBtn) {
                        notifyBtn.classList.add('subscribed'); notifyBtn.querySelector('.notify-label') ?.classList.add('d-none'); notifyBtn.querySelector('.notify-subscribed') ?.classList.remove('d-none'); }
                    if (notifyModal) {
                        notifyModal.hide();
                    }
                } else {
                    showToast(data.message || window.__tFn('network_error', 'Network error'), 'error');
                }
            } catch (e) {
                showToast(window.__tFn('network_error', 'Network error'), 'error'); }
            finally { notifySubmit.disabled = false; }
        });
    }

    // Immediately check subscription status for detail page button (if exists)
    (async function () {
        try {
            if (!notifyBtn) {
                return;
            }
            const productId = getProductId(notifyBtn);
            if (!productId) {
                return;
            }
            const params = new URLSearchParams({ product_id: productId, type: 'back_in_stock' });
            if (window.userEmail) {
                params.set('email', window.userEmail);
            }
            const res = await fetch('/notify/check?' + params.toString());
            const data = await res.json();
            if (data && data.subscribed) {
                notifyBtn.classList.add('subscribed');
                notifyBtn.querySelector('.notify-label') ?.classList.add('d-none');
                notifyBtn.querySelector('.notify-subscribed') ?.classList.remove('d-none');
            }
        } catch (err) {
            console.warn('notify detail check failed', err); }
    })();

    // Buy Now button wiring: set flag and submit parent form
    const buyNowBtn = document.getElementById('buyNowBtn');
    if (buyNowBtn) {
        buyNowBtn.addEventListener('click', function () {
            const form = buyNowBtn.closest('form');
            if (!form) {
                return;
            }
            const variationField = form.querySelector('#selectedVariationId');
            if (variationField && !variationField.value) {
                showToast(window.__tFn('please_select_required_options', 'Please select required options first'), 'error');
                return;
            }
            const buyNowFlag = form.querySelector('#buyNowFlag');
            if (buyNowFlag) {
                buyNowFlag.value = '1';
            }
            form.submit();
        });
    }
});

