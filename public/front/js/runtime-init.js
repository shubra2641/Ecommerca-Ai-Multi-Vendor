// runtime-init.js
// Initializes flash messages and localization map without inline script (CSP compliant)
(function () {
    const root = document.getElementById('flash-messages-root');
    if (!root) {
        return;
    }
    const ds = root.dataset;
    function parseVal(v) {
        try {
            return JSON.parse(v);
        } catch (e) {
            return v;
        }
    }
    window.__frontFlash = {
        success: ds.flashSuccess ? parseVal(ds.flashSuccess) : null,
        error: ds.flashError ? parseVal(ds.flashError) : null,
        warning: ds.flashWarning ? parseVal(ds.flashWarning) : null,
        info: ds.flashInfo ? parseVal(ds.flashInfo) : null
    };
    const tpl = document.getElementById('l10n-data');
    if (tpl) {
        try {
            window.__t = JSON.parse(tpl.textContent.trim());
        } catch (e) {
            window.__t = {};
        }
    }
    window.__t = window.__t || {};
    window.__tFn = function (k, f) {
        return (window.__t && Object.prototype.hasOwnProperty.call(window.__t, k)) ? window.__t[k] : (f || k);
    };

    // Delegated behavior for small inline handlers migrated out of templates
    document.addEventListener('DOMContentLoaded', function () {
        // auto-submit forms with data-auto-submit
        try {
            document.querySelectorAll('form[data-auto-submit]')?.forEach(function (f) {
                // submit deferred to allow any other init scripts
                setTimeout(function () { try { f.submit(); } catch (e) { /* ignore */ } }, 50);
            });
        } catch (e) { /* ignore */ }

        // click delegation for data-action handlers
        document.body.addEventListener('click', function (ev) {
            var el = ev.target;
            while (el && el !== document.body) {
                if (el.dataset && el.dataset.action === 'reload') {
                    ev.preventDefault();
                    window.location.reload();
                    return;
                }
                el = el.parentNode;
            }
        });

        // auto-refresh attribute (milliseconds) on body or container elements
        try {
            var auto = document.body.dataset.autoRefresh || document.querySelector('[data-auto-refresh]')?.dataset?.autoRefresh;
            if (auto) {
                var ms = parseInt(auto, 10) || 10000;
                setTimeout(function () { window.location.reload(); }, ms);
            }
        } catch (e) { /* ignore */ }

        // Payment method interactions: show/hide payment-details and enable proceed button
        try {
            var paymentRadios = document.querySelectorAll('.payment-radio');
            var proceedBtn = document.getElementById('proceedBtn');
            function updatePaymentUI() {
                var selected = document.querySelector('.payment-radio:checked');
                document.querySelectorAll('.payment-details').forEach(function (el) {
                    if (!selected) {
                        el.classList.add('envato-hidden');
                        el.classList.remove('show');
                        return;
                    }
                    var container = selected.closest('.payment-method');
                    if (container && container.contains(el)) {
                        el.classList.remove('envato-hidden');
                        el.classList.add('show');
                    } else {
                        el.classList.add('envato-hidden');
                        el.classList.remove('show');
                    }
                });
                if (proceedBtn) {
                    proceedBtn.disabled = !selected;
                }
            }
            if (paymentRadios && paymentRadios.length) {
                paymentRadios.forEach(function (r) {
                    r.addEventListener('change', updatePaymentUI);
                });
                // initialize
                updatePaymentUI();
            }
        } catch (e) { /* ignore */ }

        // Apply widths for any progress-bar elements that carry data-progress (migrated from inline style)
        try {
            document.querySelectorAll('.progress-bar[data-progress]').forEach(function (pb) {
                var v = pb.getAttribute('data-progress');
                if (v !== null) {
                    var n = parseFloat(v);
                    if (!isNaN(n)) {
                        pb.style.width = Math.max(0, Math.min(100, n)) + '%';
                        pb.setAttribute('aria-valuenow', n);
                    }
                }
            });
        } catch (e) { /* ignore */ }
    });
})();

