// Global Auto Refresh Enabler
// Activates all known refresh / auto-update mechanisms across admin pages.
(function () {
    const SAFE_INTERVALS = {
        generic: 120000,      // 2 min default
        dashboard: 120000,    // dashboard stats
        reports: 180000,      // reports heavier
        activity: 15000,      // already handled inside component
        performance: 10000,   // already defined inline
        systemInfo: 300000,   // 5 min
        balance: 60000,       // 1 min (financial balance)
        translations: 300000  // 5 min
    };

    function throttleClick(btn, interval)
    {
      // Auto-triggering removed by policy. Do not dispatch automatic clicks.
      // This function intentionally left blank to prevent repeated refresh triggers.
        return;
    }

    function fire(el)
    {
        if (!el) {
            return;
        }
        if (el.disabled) {
            return;
        }
      // Provide visual feedback if icon spinner pattern available
        el.dispatchEvent(new MouseEvent('click', {bubbles:true}));
    }

    document.addEventListener('DOMContentLoaded', () => {
        try {
            if (typeof window.ADMIN_AUTO_REFRESH !== 'undefined' && !window.ADMIN_AUTO_REFRESH) {
                // Auto-refresh globally disabled by server-side/inline flag.
                return;
            }

      // Dashboard auto-start removed by policy. Manual refresh via UI remains.

          // Reports page
            const reportsBtn = document.getElementById('refreshReportsBtn');
            if (typeof startAutoRefresh === 'function') {
                try {
                    startAutoRefresh(); } catch (e) {
                    }
                    const reportsToggle = document.getElementById('autoRefreshReports');
                    if (reportsToggle) {
                        reportsToggle.checked = true; }
            } else if (reportsBtn) {
                throttleClick(reportsBtn, SAFE_INTERVALS.reports);
            }

          // Activity Center handled internally (autoRefresh true); ensure first reload quickly
            if (document.getElementById('activity-app')) {
              // Vue component already sets interval; nothing extra.
            }

          // Performance page manual immediate refresh (interval already in template)
            const perfBtn = document.getElementById('refreshBtn');
            if (perfBtn) {
                fire(perfBtn); }

          // Generic page-level refresh buttons
            document.querySelectorAll('.js-refresh-page[data-action="refresh"]').forEach(btn => {
                throttleClick(btn, SAFE_INTERVALS.generic);
            });

          // System info refresh
            document.querySelectorAll('.js-refresh-system[data-action="refresh-system-info"]').forEach(btn => {
                  throttleClick(btn, SAFE_INTERVALS.systemInfo);
            });

          // Balance refresh buttons
            document.querySelectorAll('.btn-refresh-balance').forEach(btn => {
                  throttleClick(btn, SAFE_INTERVALS.balance);
            });

          // Translations cache refresh
            document.querySelectorAll('[data-action="refresh-translations"]').forEach(btn => {
                  throttleClick(btn, SAFE_INTERVALS.translations);
            });
        } catch (err) {
            console.warn('Auto refresh enable error', err);
        }
    });
})();
