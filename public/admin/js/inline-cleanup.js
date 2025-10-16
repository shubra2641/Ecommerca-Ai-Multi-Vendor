// Centralized handlers replacing inline JS (confirm dialogs, font preview, reset, refresh info)
(function () {
    function handlePageRefresh(btn)
    {
        if (btn.classList.contains('js-refresh-system')) {
          // Legacy system refresh (AJAX hint)
            if (window.fetch) {
                fetch(location.href, {headers:{'X-Refresh-System':'1'}}).catch(() => {}); }
        } else {
          // Simple full page reload
            location.reload();
        }
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-refresh-system, .js-refresh-page');
        if (btn) {
            e.preventDefault();
            handlePageRefresh(btn);
            return;
        }
        const reset = e.target.closest('.js-reset-form');
        if (reset) {
            const form = reset.closest('form');
            if (form) {
                form.reset(); }
            return;
        }
        const exportLink = e.target.closest('.js-export');
        if (exportLink) {
            e.preventDefault();
            const type = exportLink.getAttribute('data-export-type');
            const report = exportLink.getAttribute('data-report');
            if (type && report) {
                const base = exportLink.getAttribute('data-export-url') || (window.__REPORT_EXPORT_ROUTE__ || '{{ route ? }}');
              // Fallback to server-provided route via data attribute when available
                let url = exportLink.dataset.url;
                if (!url) {
                    const route = exportLink.dataset.route || (typeof window.reportExportRoute === 'string' ? window.reportExportRoute : null);
                    if (route) {
                        url = route + '?type=' + encodeURIComponent(type) + '&report=' + encodeURIComponent(report);
                    } else {
                  // Build from current location if no dedicated route variable
                        url = (exportLink.getAttribute('data-base') || '/admin/reports/export') + '?type=' + encodeURIComponent(type) + '&report=' + encodeURIComponent(report);
                    }
                }
                window.open(url, '_blank');
            }
            return;
        }
    });

    document.addEventListener('change', function (e) {
        const sel = e.target.closest('.js-preview-font');
        if (sel) {
            const val = sel.value;
            const preview = document.getElementById('fontPreview');
            if (preview) {
                preview.style.fontFamily = `'${val}', var(--font - family - primary)`;
                const label = preview.querySelector('[data-sample="label"]');
                if (label) {
                    label.textContent = val + ' - ' + label.textContent.replace(/^.* - /,''); }
            }
        }
        const perPage = e.target.closest('.js-per-page-select');
        if (perPage) {
            const prefix = perPage.getAttribute('data-url-prefix') || '';
            const suffix = perPage.getAttribute('data-url-suffix') || '';
            const value = perPage.value;
            if (prefix) {
                const url = prefix + encodeURIComponent(value) + suffix;
                window.location.href = url;
            }
        }
        const autoForm = e.target.closest('.js-auto-submit');
        if (autoForm) {
            const form = autoForm.tagName === 'FORM' ? autoForm : autoForm.closest('form');
            if (form) {
                form.submit(); }
        }
    });

    document.addEventListener('submit', function (e) {
        const f = e.target.closest('.js-confirm');
        if (f) {
            const msg = f.getAttribute('data-confirm') || 'Are you sure?';
            if (!window.confirm(msg)) {
                e.preventDefault(); }
        }
    });

  // Simple alert buttons
    document.addEventListener('click', function (e) {
        const alertBtn = e.target.closest('.js-alert');
        if (alertBtn) {
            const msg = alertBtn.getAttribute('data-alert');
            if (msg) {
                window.alert(msg); }
        }
    });
})();
