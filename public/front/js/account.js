/* Account area interactions (extracted from blade inline scripts) */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        // progress bars
        document.querySelectorAll('.progress-bar span[data-progress], .mini-progress span[data-progress]').forEach(function (el) {
            var v = el.getAttribute('data-progress');
            if (v !== null) {
                el.style.width = v + '%'; }
        });
    });

    // Address creation dynamic selects (new address form)
    function fetchJSON(url, params)
    {
        var q = new URLSearchParams(params || {}).toString();
        return fetch(url + (q ? ('?' + q) : ''), { headers : { 'X-Requested-With' : 'XMLHttpRequest' } }).then(r => r.json());
    }

    function initNewAddressForm()
    {
        var countryEl = document.getElementById('new_country');
        if (!countryEl) {
            return; // only on addresses page
        }
        var govEl = document.getElementById('new_governorate');
        var cityEl = document.getElementById('new_city');
        var OLD_COUNTRY = countryEl.dataset.old || countryEl.getAttribute('data-old') || '';
        var OLD_GOV = govEl ? (govEl.dataset.old || '') : '';
        var OLD_CITY = cityEl ? (cityEl.dataset.old || '') : '';

        function setLoading(el, text)
        {
            if (!el) {
                return;
            } el.disabled = true; el.innerHTML = '<option value="">' + text + '</option>'; }
        function option(t, v)
        {
            return '<option value="' + v + '">' + t + '</option>'; }

        function populateGovernorates(countryId, selected)
        {
            if (!govEl) {
                return Promise.resolve();
            }
            setLoading(govEl, govEl.getAttribute('data-loading-text') || 'Loading...');
            return fetchJSON('/api/locations/governorates', { country: countryId }).then(json => {
                var items = json.data || []; govEl.innerHTML = option(govEl.dataset.placeholder || 'Select governorate', '') + items.map(i => option(i.name, i.id)).join('');
                govEl.disabled = false; if (selected) {
                    govEl.value = selected; }
            }).catch(() => { govEl.disabled = false; });
        }
        function populateCities(govId, selected)
        {
            if (!cityEl) {
                return Promise.resolve();
            }
            setLoading(cityEl, cityEl.getAttribute('data-loading-text') || 'Loading...');
            return fetchJSON('/api/locations/cities', { governorate: govId }).then(json => {
                var items = json.data || []; cityEl.innerHTML = option(cityEl.dataset.placeholder || 'Select city', '') + items.map(i => option(i.name, i.id)).join('');
                cityEl.disabled = false; if (selected) {
                    cityEl.value = selected; }
            }).catch(() => { cityEl.disabled = false; });
        }

        countryEl.addEventListener('change', function () {
            var c = this.value; if (!c) {
                if (govEl) {
                    govEl.innerHTML = option('Select governorate', ''); govEl.disabled = true; }
                if (cityEl) {
                    cityEl.innerHTML = option('Select city', ''); cityEl.disabled = true; }
                return;
            }
            populateGovernorates(c);
        });

        if (OLD_COUNTRY) {
            populateGovernorates(OLD_COUNTRY, OLD_GOV).then(() => { if (OLD_GOV) {
                    populateCities(OLD_GOV, OLD_CITY); } });
        }
        if (govEl) {
            govEl.addEventListener('change', function () {
                var g = this.value; if (!g) {
                    if (cityEl) {
                        cityEl.innerHTML = option('Select city', ''); cityEl.disabled = true; } return; } populateCities(g); });
        }
    }

    function initEditAddressModal()
    {
        var modal = document.getElementById('addressEditModal');
        if (!modal) {
            return; // only on addresses page
        }
        var form = document.getElementById('addressEditForm');
        function jsonFetch(u, p)
        {
            var q = new URLSearchParams(p || {}).toString(); return fetch(u + (q ? ('?' + q) : ''), { headers : { 'X-Requested-With' : 'XMLHttpRequest' } }).then(r => r.json()); }
        function open()
        {
            modal.style.display = 'block'; }
        function close()
        {
            modal.style.display = 'none'; }

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-action="edit-address"]');
            if (btn) {
                var id = btn.dataset.addressId;
                var target = btn.closest('.address-card');
                var data = target && target.hasAttribute('data-address') ? JSON.parse(target.getAttribute('data-address')) : null;
                if (!data) {
                    return alert('Address not found'); }
                form.action = '/account/addresses/' + data.id; form.querySelector('#modal_address_id').value = data.id;
                // map title/name/phone
                var titleEl = form.querySelector('#modal_label'); if (titleEl) {
                    titleEl.value = data.title || data.label || '';
                }
                var phoneEl = form.querySelector('#modal_phone'); if (phoneEl) {
                    phoneEl.value = data.phone || '';
                }
                var nameEl = form.querySelector('#modal_name'); if (nameEl) {
                    nameEl.value = data.name || '';
                }
                ['line1', 'line2', 'postal_code'].forEach(function (k) {
                    var el = form.querySelector('#modal_' + k); if (el) {
                        el.value = data[k] || '';
                    } });
                var countrySel = form.querySelector('#modal_country'); var govSel = form.querySelector('#modal_governorate'); var citySel = form.querySelector('#modal_city');
                countrySel.value = data.country_id || '';
                if (data.country_id) {
                    jsonFetch('/api/locations/governorates', { country: data.country_id }).then(function (json) {
                        govSel.innerHTML = '<option value="">Select governorate</option>' + (json.data || []).map(i => '<option value="' + i.id + '">' + i.name + '</option>').join('');
                        govSel.disabled = false; govSel.value = data.governorate_id || '';
                        if (data.governorate_id) {
                            jsonFetch('/api/locations/cities', { governorate: data.governorate_id }).then(function (json2) {
                                citySel.innerHTML = '<option value="">Select city</option>' + (json2.data || []).map(i => '<option value="' + i.id + '">' + i.name + '</option>').join('');
                                citySel.disabled = false; citySel.value = data.city_id || '';
                            });
                        }
                    });
                }
                open();
            }
            if (e.target.closest('[data-action="close-modal"]')) {
                close(); }
        });

        form.querySelector('#modal_governorate').addEventListener('change', function () {
            var g = this.value; var city = form.querySelector('#modal_city'); if (!g) {
                city.innerHTML = '<option value="">Select city</option>'; city.disabled = true; return; }
            city.innerHTML = '<option>Loading...</option>';
            jsonFetch('/api/locations/cities', { governorate: g }).then(function (json) {
                city.innerHTML = '<option value="">Select city</option>' + (json.data || []).map(i => '<option value="' + i.id + '">' + i.name + '</option>').join('');
                city.disabled = false;
            }).catch(function (err) {
                console.error('Failed to load cities', err); city.disabled = false; });
        });

        form.querySelector('#modal_country').addEventListener('change', function () {
            var c = this.value; var gov = form.querySelector('#modal_governorate'); var city = form.querySelector('#modal_city');
            if (!c) {
                gov.innerHTML = '<option value="">Select governorate</option>'; gov.disabled = true; city.innerHTML = '<option value="">Select city</option>'; city.disabled = true; return; }
            gov.innerHTML = '<option>Loading...</option>';
            jsonFetch('/api/locations/governorates', { country: c }).then(function (json) {
                gov.innerHTML = '<option value="">Select governorate</option>' + (json.data || []).map(i => '<option value="' + i.id + '">' + i.name + '</option>').join('');
                gov.disabled = false;
            }).catch(function (err) {
                console.error('Failed to load governorates', err); gov.disabled = false; });
        });
        // Ensure the form action has id before submit (fallback in case open handler didn't set action)
        form.addEventListener('submit', function (e) {
            try {
                var act = (form.getAttribute('action') || '').trim();
                var idEl = form.querySelector('#modal_address_id');
                var id = idEl ? idEl.value : null;
                if (id && (!act || act.endsWith('/account/addresses') || /\/account\/addresses\/?$/.test(act))) {
                    form.action = '/account/addresses/' + id;
                }
            } catch (ex) {
                console.error('modal submit fallback error', ex);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initNewAddressForm();
        initEditAddressModal();
        // Inline-form quick actions (Make default) - submit via fetch to avoid full page navigation when possible
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || !form.classList || !form.classList.contains('inline-form')) {
                return;
            }
            // only handle simple 'is_default' toggles here
            var isDefaultInput = form.querySelector('input[name="is_default"]');
            if (!isDefaultInput) {
                return; // let other inline forms behave normally
            }
            e.preventDefault();
            var url = form.getAttribute('action');
            var method = (form.querySelector('input[name="_method"]') && form.querySelector('input[name="_method"]').value) || form.getAttribute('method') || 'POST';
            var tokenEl = form.querySelector('input[name="_token"]');
            var token = tokenEl ? tokenEl.value : null;
            var body = new URLSearchParams();
            // include _method for Laravel method spoofing
            if (method && method.toUpperCase() !== 'POST') {
                body.append('_method', method); }
            if (token) {
                body.append('_token', token);
            }
            body.append('is_default', isDefaultInput.value || '1');

            fetch(url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString(),
                credentials: 'same-origin'
            }).then(function (res) {
                if (res.ok) {
                    // reload to reflect change (server will update ordering); keep this simple and robust
                    window.location.reload();
                } else {
                    return res.text().then(function (txt) {
                        throw new Error('Failed to update address: ' + res.status); });
                }
            }).catch(function (err) {
                console.error(err); alert('Failed to update address. See console for details.'); });
        });
    });
})();

