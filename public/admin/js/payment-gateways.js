(function () {
    const root = document.getElementById('pgRoot');
    if (!root) {
        return;
    }
    const gatewaysData = JSON.parse(root.getAttribute('data-gateways') || '[]');
    const updateBase = root.getAttribute('data-update-base');
    const confirmText = root.getAttribute('data-confirm-text') || 'Are you sure?';
    const failedText = root.getAttribute('data-failed-text') || 'Failed to save';

    const sel = document.getElementById('gatewaySelect');
    const editor = document.getElementById('gatewayEditor');
    const form = document.getElementById('gatewayEditForm');

    function showDriver(driver)
    {
        document.querySelectorAll('#ge_driver_fields .driver-block').forEach(function (el) {
            if (el.getAttribute('data-driver') === driver) {
                el.classList.remove('d-none'); } else {
                el.classList.add('d-none');
                }
        });
    }

    function populate(item)
    {
        document.getElementById('ge_name').value = item.name || '';
        document.getElementById('ge_slug').value = item.slug || '';
        document.getElementById('ge_enabled').checked = !!item.enabled;
        showDriver(item.driver);
        if (item.driver === 'stripe') {
            document.getElementById('ge_stripe_publishable').value = item.stripe_publishable_key || '';
            document.getElementById('ge_stripe_secret').value = item.stripe_secret_key ? '********' : '';
            document.getElementById('ge_stripe_webhook_secret').value = item.stripe_webhook_secret ? '********' : '';
            document.getElementById('ge_stripe_mode').value = item.stripe_mode || 'test';
        }
        if (item.driver === 'offline') {
            document.getElementById('ge_transfer_instructions').value = item.transfer_instructions || '';
            document.getElementById('ge_requires_transfer_image').checked = !!item.requires_transfer_image;
        }
    }

    sel.addEventListener('change', function () {
        const id = this.value;
        if (!id) {
            editor.classList.add('d-none'); return; }
        const item = gatewaysData.find(g => String(g.id) === String(id));
        if (!item) {
            editor.classList.add('d-none'); return; }
        editor.classList.remove('d-none');
        populate(item);
        form.action = updateBase + '/' + id;
    });

    document.getElementById('ge_cancel').addEventListener('click', function () {
        sel.value = ''; sel.dispatchEvent(new Event('change')); });

    document.getElementById('ge_save').addEventListener('click', function (e) {
        e.preventDefault();
        const fd = new FormData(form);
        fetch(form.action, { method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}, body: fd })
        .then(r => { if (r.status === 422) {
                return r.json().then(j => Promise.reject(j));
        } return r.text(); })
        .then(() => { location.reload(); })
        .catch(err => { alert(failedText + ': ' + (err.message || JSON.stringify(err))); });
    });

  // generic delete confirm for this page
    document.querySelectorAll('form.js-confirm-delete').forEach(function (f) {
        f.addEventListener('submit', function (e) {
            if (!confirm(f.getAttribute('data-confirm') || confirmText)) {
                  e.preventDefault(); } });
    });
})();