// Extracted performance dashboard refresh logic
(function () {
    function attach(id, url)
    {
        var btn = document.getElementById(id);
        if (!btn) {
            return;
        }
        function refresh()
        {
            fetch(url)
            .then(r => r.json())
            .then(json => {
                if (!json.success) {
                    return;
                }
                Object.entries(json.data).forEach(function (entry) {
                    var metric = entry[0]; var row = entry[1];
                    ['sum','count','avg_time_ms'].forEach(function (f) {
                        var el = document.querySelector('[data-metric="' + metric + '"][data-field="' + f + '"]');
                        if (el) {
                            el.textContent = row[f];
                        }
                    });
                });
            });
        }
        btn.addEventListener('click', refresh);
    }
  // Admin
    attach('refreshBtn', document.documentElement.getAttribute('data-admin-perf-url'));
  // Vendor
    attach('refreshBtn', document.documentElement.getAttribute('data-vendor-perf-url'));
})();
