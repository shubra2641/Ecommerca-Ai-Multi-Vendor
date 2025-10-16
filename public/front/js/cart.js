/* Cart page behaviors: coupon apply via AJAX and graceful fallback */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        try {
            // Unified notify helper
            function cartNotify(msg, type)
            {
                if (!msg) {
                    return;
                }
                type = type || 'info';
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
                    alert(msg); } catch (_) {
                    }
            }
            // Coupon AJAX handling
            var form = document.querySelector('form[data-coupon-form]');
            if (form) {
                var messageBox = document.createElement('div');
                messageBox.className = 'cart-coupon-msg';
                form.parentNode.insertBefore(messageBox, form);

                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var fd = new FormData(form);
                    if (!fd.has('displayed_total')) {
                        var subtotalNode = document.querySelector('.subtotal-amount');
                        if (subtotalNode) {
                            fd.set('displayed_total', subtotalNode.textContent.replace(/[^0-9.\-]/g, '')); }
                    }

                    fetch(form.action, {
                        method: form.method || 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') },
                        body: fd
                    }).then(function (resp) {
                        var ct = resp.headers.get('content-type') || '';
                        if (ct.indexOf('application/json') !== -1) {
                            return resp.json();
                        }
                        return resp.text().then(function () {
                            return { status: 'ok', message: 'Applied' }; });
                    }).then(function (data) {
                        if (!data) {
                            return;
                        }
                        if (data.status && data.status === 'ok') {
                            var successMsg = data.message || window.__tFn('coupon_applied', 'Coupon applied');
                            messageBox.innerHTML = '<div class="coupon-success">' + successMsg + '</div>';
                            cartNotify(successMsg, 'success');
                            var subtotalNode = document.querySelector('.subtotal-amount');
                            var totalNode = document.querySelector('.total-amount');
                            var discountNode = document.querySelector('.discount-amount');
                            if (subtotalNode && typeof data.displayTotal !== 'undefined') {
                                subtotalNode.textContent = (data.currency_symbol || '') + ' ' + Number(data.displayTotal).toFixed(2);
                            }
                            if (totalNode && typeof data.discountedTotal !== 'undefined') {
                                totalNode.textContent = (data.currency_symbol || '') + ' ' + Number(data.discountedTotal).toFixed(2);
                            }
                            if (discountNode && typeof data.discount !== 'undefined') {
                                discountNode.textContent = (data.discount > 0 ? ('- ' + (data.currency_symbol || '') + ' ' + Number(data.discount).toFixed(2)) : (data.currency_symbol || '') + ' ' + Number(0).toFixed(2));
                            }
                            // After updating totals, reload the page automatically so server-rendered UI (coupon block, banners)
                            // is fully in-sync. Delay briefly to let the user see the success message.
                            try {
                                setTimeout(function () {
                                    window.location.reload(); }, 900);
                            } catch (e) {
/* ignore */ }
                        } else {
                            var failMsg = (data && data.message) || window.__tFn('failed_apply_coupon', 'Failed to apply coupon');
                            messageBox.innerHTML = '<div class="coupon-error">' + failMsg + '</div>';
                            cartNotify(failMsg, 'error');
                        }
                    }).catch(function (err) {
                        console.error('Failed to apply coupon', err);
                        messageBox.innerHTML = '<div class="coupon-error">' + window.__tFn('failed_apply_coupon', 'Failed to apply coupon') + '</div>';
                        cartNotify(window.__tFn('failed_apply_coupon', 'Failed to apply coupon'), 'error');
                    });
                });
            }

            // Attach AJAX handlers to Remove and Move to Wishlist buttons for better UX
            document.querySelectorAll('.cart-item .action-buttons form').forEach(function (f) {
                var isRemove = !!f.querySelector('input[name=cart_key]');
                var isMove = !!f.querySelector('input[name=product_id]');
                if (!isRemove && !isMove) {
                    return;
                }
                f.addEventListener('submit', function (e) {
                    if (!window.fetch) {
                        return; // fallback to normal submit
                    }
                    e.preventDefault();
                    var fd = new FormData(f);
                    fetch(f.action, { method: f.method || 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') }, body: fd })
                        .then(function (r) {
                            var ct = r.headers.get('content-type') || '';
                            if (ct.indexOf('application/json') !== -1) {
                                return r.json();
                            }
                            return { status: 'ok' };
                        })
                        .then(function (resp) {
                            if (resp && resp.status === 'ok') {
                                var item = f.closest('.cart-item'); if (item) {
                                    item.remove();
                                }
                                if (resp.displayTotal) {
                                    var subtotalNode = document.querySelector('.subtotal-amount');
                                    if (subtotalNode) {
                                        subtotalNode.textContent = (resp.currency_symbol || '') + ' ' + Number(resp.displayTotal).toFixed(2);
                                    }
                                }
                                // Show appropriate toast
                                if (isRemove) {
                                    cartNotify((resp && resp.message) || window.__tFn('removed_from_cart', 'Removed from cart'), 'success');
                                } else if (isMove) {
                                    cartNotify((resp && resp.message) || window.__tFn('moved_to_wishlist', 'Moved to wishlist'), 'info');
                                }
                                // If cart becomes empty, optionally reload to show empty state
                                if (!document.querySelector('.cart-item')) {
                                    setTimeout(function () {
                                        try {
                                            window.location.reload(); } catch (_) {
                                            } }, 400);
                                }
                            } else {
                                cartNotify((resp && resp.message) || 'Action failed', 'error');
                            }
                        }).catch(function () {
                            cartNotify('Action failed', 'error'); try {
                                location.reload(); } catch (_) {
                                } });
                });
            });

            // Make summary sticky on larger screens
            var summary = document.querySelector('.checkout-right .summary-box');
            if (summary && window.matchMedia && window.matchMedia('(min-width: 992px)').matches) {
                summary.style.position = 'sticky';
                summary.style.top = '90px';
            }
        } catch (ex) {
            console.error(ex);
        }
    });
})();

