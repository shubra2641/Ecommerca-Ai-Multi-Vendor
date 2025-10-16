'use strict';
// Loads dashboard data from <template id="dashboard-data"> instead of inline window.* globals
(function () {
    function parseTemplate()
    {
        var tpl = document.getElementById('dashboard-data');
        if (!tpl) {
            return null;
        }
        try {
            return JSON.parse(tpl.innerHTML.trim() || '{}'); } catch (e) {
                    return null; }
    }
    function init()
    {
        var data = parseTemplate();
        if (!data) {
            return;
        }
      // Backwards compatibility: populate expected globals if downstream scripts still reference them
        window.dashboardChartData = data.chartData || {};
        window.dashboardSalesChartData = data.salesChartData || {};
        window.dashboardStats = data.stats || {};
        window.dashboardSystemHealth = data.systemHealth || {};
        window.dashboardTopUsers = data.topUsers || [];
      // If dashboard-init script exists (legacy) ensure it runs after data injection
        if (window.dashboardFunctions && typeof window.dashboardFunctions.initializeChart === 'function') {
          // rely on existing dashboard-init.js DOMContentLoaded listeners; nothing else needed
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init); } else {
        init();
        }
})();
