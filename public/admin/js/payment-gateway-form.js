'use strict';
// Handles driver field toggling & dynamic custom key/value rows for payment gateway form (extracted from inline)
(function () {
    function refreshDriverFields()
    {
        var driverEl = document.getElementById('driver');
        if (!driverEl) {
            return;
        } var driver = driverEl.value;
      // use class toggling instead of style.display because .envato-hidden uses !important
        document.querySelectorAll('.driver-fields').forEach(function (el) {
            el.classList.add('envato-hidden'); });
        if (driver) {
            var target = document.getElementById('driver-' + driver);
            if (target) {
                target.classList.remove('envato-hidden');
            } else {
              // If no specific driver fields found, try dynamic config
                if (typeof window.renderDynamicDriverConfig === 'function') {
                    window.renderDynamicDriverConfig(driver);
                }
            }
        }
    }
    function bindCustomRows()
    {
        var addBtn = document.getElementById('add-custom'); var container = document.getElementById('custom-rows'); if (!addBtn || !container) {
            return;
        }
        function bindRemove(root)
        {
            root.querySelectorAll('.remove-custom').forEach(function (btn) {
                       btn.onclick = function () {
                        this.closest('.custom-row') ? .remove(); }; }); }
        addBtn.addEventListener('click', function () {
            var div = document.createElement('div'); div.className = 'row g-2 mb-2 custom-row';
            div.innerHTML = '\n  <div class="col-md-5"><input name="custom_keys[]" class="form-control" placeholder="Key" /></div>\n  <div class="col-md-6"><input name="custom_values[]" class="form-control" placeholder="Value" /></div>\n  <div class="col-md-1"><button type="button" class="btn btn-sm btn-danger remove-custom">&times;</button></div>';
            container.appendChild(div); bindRemove(div);
        });
        bindRemove(document);
    }
    function init()
    {
        refreshDriverFields(); var driverEl = document.getElementById('driver'); if (driverEl) {
            driverEl.addEventListener('change', refreshDriverFields); }
        bindCustomRows();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init); } else {
        init();
        }
})();

// Bind PayPal test webhook button (moved from inline script to satisfy CSP)
(function () {
    function bindTestWebhook()
    {
        var btn = document.getElementById('testWebhookBtn'); if (!btn) {
            return;
        }
        btn.addEventListener('click', function () {
            btn.disabled = true;
            var result = document.getElementById('testWebhookResult');
            if (result) {
                result.textContent = 'Testing...';
            }
            var url = btn.dataset.testWebhookUrl;
            if (!url) {
                if (result) {
                    result.textContent = 'Missing test URL';
                }
                btn.disabled = false; return;
            }
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            }).then(function (r) {
                return r.json(); }).then(function (j) {
                              btn.disabled = false;
                    if (j && j.ok) {
                        if (result) {
                            result.textContent = 'OK';
                        }
                    } else {
                        if (result) {
                                 result.textContent = (j && (j.message || j.error)) || JSON.stringify(j);
                        }
                    }
                }).catch(function (e) {
                    btn.disabled = false;
                    if (result) {
                        result.textContent = e && e.message || 'Error';
                    }
                });
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindTestWebhook); } else {
        bindTestWebhook();
        }
})();

// Dynamic config rendering for  drivers
(function () {
    window.renderDynamicDriverConfig = function (driver) {
        var container = document.getElementById('dynamic-config-fields');
        if (!container) {
            return;
        }
        container.innerHTML = '';

        if (!driver) {
            return;
        }

      // Check if this is a built-in gateway with custom fields
        if (['stripe', 'offline', 'paymob', 'fawry', 'myfatoorah', 'thawani'].indexOf(driver) !== -1) {
            container.innerHTML = '<div class="text-success"><i class="fas fa-check-circle"></i> تم تحميل الحقول المخصصة لبوابة ' + driver + '</div>';
            return;
        }

        var driverEl = document.getElementById('driver');
        var baseUrl = driverEl ? .dataset ? .configBaseUrl || '/admin/payment-gateways-management/config-fields';
        var url = baseUrl + '/' + encodeURIComponent(driver);

        var existingConfig = {};
        try {
            existingConfig = JSON.parse(driverEl ? .dataset ? .existingConfig || '{}'); } catch (e) {
                    existingConfig = {}; }

            fetch(url, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            }).then(function (r) {
                return r.json(); }).then(function (json) {
                    if (!json || !json.success) {
                        return;
                    }
                    var keys = json.config_keys || [];
                    if (!keys.length) {
                        container.innerHTML = '<div class="text-muted">No configuration fields required for this gateway.</div>';
                        return;
                    }
                    var html = '<div class="row g-3">';
                    keys.forEach(function (key) {
                        var id = 'cfg_' + key.replace(/[^a-z0-9_]/gi, '_');
                        var value = existingConfig[key] ?  ? '';
                        html += '<div class="col-md-6">\n' +
                        '<label class="form-label">' + key.replace(/_/g, ' ') + '</label>\n' +
                        '<input name="' + key + '" id="' + id + '" class="form-control" value="' + (value + '').replace(/"/g, '&quot;') + '" required />\n' +
                        '</div>';
                    });
                          html += '</div>';
                          container.innerHTML = html;
                }).catch(function (e) {
                    container.innerHTML = '<div class="text-danger">Failed to load configuration fields</div>';
                    console.error(e);
                });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
                var driverEl = document.getElementById('driver'); if (driverEl && typeof window.renderDynamicDriverConfig === 'function') {
                window.renderDynamicDriverConfig(driverEl.value); }
        }); } else {
        var driverEl = document.getElementById('driver'); if (driverEl && typeof window.renderDynamicDriverConfig === 'function') {
            window.renderDynamicDriverConfig(driverEl.value); } }
})();
