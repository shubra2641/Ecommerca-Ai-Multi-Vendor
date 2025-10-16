/* Unified Admin Charts Initializer
   - Reads per-page JSON payloads (script#*-data or div#*-data)
   - Initializes Chart.js charts found in the DOM (canvas elements with known IDs)
   - Provides a small registry to allow page-specific data adapters
*/
(function(){
    'use strict';

    // Parse JSON from a script tag or an element with data-payload (base64)
    function parseJsonElement(selector){
        var el = document.querySelector(selector);
        if(!el) return null;
        try{
            if(el.tagName && el.tagName.toLowerCase() === 'script'){
                return JSON.parse(el.textContent || el.innerText || '{}');
            }
            var payload = el.getAttribute('data-payload') || el.textContent || el.innerText || '';
            try{ return JSON.parse(payload); }catch(e){
                try{ return JSON.parse(atob(payload)); }catch(e2){ return null; }
            }
        }catch(e){ console.error('admin-charts: parse error', selector, e); return null; }
    }

    // Wait until Chart.js is ready, with retries
    function waitForChart(cb){
        if(window.Chart) return cb();
        var tries = 0;
        (function poll(){
            if(window.Chart) return cb();
            if(tries++ > 40) return console.warn('admin-charts: Chart.js not available');
            setTimeout(poll, 150);
        })();
    }

    // Create charts with options matching previous page-specific scripts
    function buildUserAnalyticsChart(ctx, chartData){
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: (window.__tFn ? __tFn('New Users') : 'New Users'),
                    data: chartData.userData || chartData.values || [],
                    borderColor: chartData.borderColor || '#007bff',
                    backgroundColor: chartData.backgroundColor || 'rgba(0,123,255,0.1)',
                    tension: chartData.tension || 0.4,
                    fill: chartData.fill !== undefined ? chartData.fill : true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f3f4' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    function buildDoughnutChart(ctx, labels, data, colors){
        return new Chart(ctx, {
            type: 'doughnut',
            data: { labels: labels, datasets: [{ data: data, backgroundColor: colors || ['#007bff','#ffc107','#dc3545'], borderWidth:0 }] },
            options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } } }
        });
    }

    function buildFinancialDoughnut(ctx, labels, data){
        return new Chart(ctx, { type:'doughnut', data:{ labels:labels, datasets:[{ data:data, backgroundColor:['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b'] }] }, options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } } } });
    }

    function buildFinancialLine(ctx, labels, data, label){
        return new Chart(ctx, { type:'line', data:{ labels:labels, datasets:[{ label: label||'Trends', data:data, borderColor:'#4e73df', backgroundColor:'rgba(78,115,223,0.1)', fill:true }] }, options:{ responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true, ticks:{ callback:function(v){ return v; } } } } } });
    }

    // Common UI behaviors: refresh, export, tooltips
    function initCommonUi(){
        // Refresh Reports button
        var refreshBtn = document.getElementById('refreshReportsBtn');
        if(refreshBtn){
            refreshBtn.addEventListener('click', function(){
                var icon = this.querySelector('i'); if(icon) icon.classList.add('fa-spin');
                setTimeout(function(){ if(icon) icon.classList.remove('fa-spin'); location.reload(); }, 1000);
            });
        }

        // Export buttons (data-export attribute or old data-export-* patterns)
        document.querySelectorAll('[data-export], [data-export-type]').forEach(function(btn){
            btn.addEventListener('click', function(e){
                e.preventDefault();
                var format = this.dataset.export || this.dataset.exportType || this.getAttribute('data-export');
                var originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التصدير...';
                setTimeout(function(){ try{ btn.innerHTML = originalHtml; alert('تَم التصدير بنجاح: '+(format||'file').toUpperCase()); }catch(e){} }, 1200);
            });
        });

        // Bootstrap tooltips
        try{
            var triggers = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            triggers.map(function(el){ return new bootstrap.Tooltip(el); });
        }catch(e){ /* bootstrap may not be loaded */ }
    }

    // Hide common loading placeholders used by pages
    function hideLoaders(){
        ['reports-loading','stats-loading','chart-loading','activity-loading'].forEach(function(id){
            var el = document.getElementById(id);
            if(!el) return;
            // remove classes that show loader
            el.classList.add('envato-hidden');
            el.classList.remove('d-none');
        });
        // hide global error boxes if any
        ['stats-error','chart-error','reports-error'].forEach(function(id){ var e = document.getElementById(id); if(e) e.classList.add('envato-hidden'); });
    }

    // Adapters
    var adapters = {
        reports: function(){
            var data = parseJsonElement('#reports-data');
            if(!data){ hideLoaders(); return; }
            var chartData = data.chartData || {};

            var uaEl = document.getElementById('userAnalyticsChart');
            if(uaEl && chartData){
                try{ buildUserAnalyticsChart(uaEl.getContext('2d'), chartData); }catch(e){ console.error('admin-charts: userAnalytics error', e); }
            }

            var udEl = document.getElementById('userDistributionChart');
            if(udEl){
                try{
                    // If stats are provided in the payload, use them
                    var stats = data.stats || {};
                    var labels = [ (window.__tFn?__tFn('Active Users'):'Active'), (window.__tFn?__tFn('Pending Users'):'Pending'), (window.__tFn?__tFn('Inactive Users'):'Inactive') ];
                    var values = [ stats.activeUsers||0, stats.pendingUsers||0, stats.inactiveUsers||0 ];
                    buildDoughnutChart(udEl.getContext('2d'), labels, values, ['#007bff','#ffc107','#dc3545']);
                }catch(e){ console.error('admin-charts: userDistribution error', e); }
            }

            initCommonUi();
            try{ hideLoaders(); }catch(e){}
        },

        financial: function(){
            var data = parseJsonElement('#report-financial-data');
            if(!data){ hideLoaders(); return; }
            var charts = data.charts || {};
            if(charts.balanceDistribution){
                var bd = document.getElementById('balanceDistributionChart');
                if(bd){ try{ buildFinancialDoughnut(bd.getContext('2d'), charts.balanceDistribution.labels || [], charts.balanceDistribution.values || []); }catch(e){ console.error('admin-charts: balanceDistribution error', e); } }
            }
            if(charts.monthlyTrends){
                var mt = document.getElementById('monthlyTrendsChart');
                if(mt){ try{ var mfLabel = (charts.monthlyTrends.label) ? charts.monthlyTrends.label : (window.__tFn ? __tFn('Monthly Financial Trends') : 'Monthly Financial Trends'); buildFinancialLine(mt.getContext('2d'), charts.monthlyTrends.labels || [], charts.monthlyTrends.values || [], mfLabel); }catch(e){ console.error('admin-charts: monthlyTrends error', e); } }
            }

            initCommonUi();
            try{ hideLoaders(); }catch(e){}
        },

        dashboard: function(){
            var data = parseJsonElement('#dashboard-data');
            if(!data){ hideLoaders(); return; }
            var charts = data.charts || {};

            // Users Line Chart (userChart)
            var uEl = document.getElementById('userChart');
            if(uEl && charts.users){
                try {
                    new Chart(uEl.getContext('2d'), {
                        type: 'line',
                        data: { labels: charts.users.labels || [], datasets: [{
                            label: (window.__tFn?__tFn('Users'):'Users'),
                            data: charts.users.data || [],
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0,123,255,0.1)',
                            tension: 0.4,
                            fill: true
                        }]},
                        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true }, x:{ grid:{ display:false } } } }
                    });
                }catch(e){ console.error('admin-charts: dashboard users chart error', e); }
            }

            // Sales & Revenue Mixed / Dual Dataset Line Chart (salesChart)
            var sEl = document.getElementById('salesChart');
            if(sEl && charts.sales){
                try {
                    new Chart(sEl.getContext('2d'), {
                        type: 'line',
                        data: { labels: charts.sales.labels || [], datasets: [
                            {
                                label: (window.__tFn?__tFn('Orders'):'Orders'),
                                data: charts.sales.orders || [],
                                borderColor: '#17a2b8',
                                backgroundColor: 'rgba(23,162,184,0.15)',
                                tension: 0.3,
                                fill: true,
                                yAxisID: 'y'
                            },
                            {
                                label: (window.__tFn?__tFn('Revenue'):'Revenue'),
                                data: charts.sales.revenue || [],
                                borderColor: '#28a745',
                                backgroundColor: 'rgba(40,167,69,0.15)',
                                tension: 0.3,
                                fill: true,
                                yAxisID: 'y1'
                            }
                        ]},
                        options: { responsive:true, maintainAspectRatio:false, interaction:{ mode:'index', intersect:false }, stacked:false, plugins:{ legend:{ position:'bottom' } }, scales:{ y:{ type:'linear', position:'left', beginAtZero:true }, y1:{ type:'linear', position:'right', grid:{ drawOnChartArea:false }, beginAtZero:true } } }
                    });
                }catch(e){ console.error('admin-charts: dashboard sales chart error', e); }
            }

            // Order Status Doughnut (orderStatusChart)
            var oEl = document.getElementById('orderStatusChart');
            if(oEl && charts.ordersStatus){
                try {
                    new Chart(oEl.getContext('2d'), {
                        type: 'doughnut',
                        data: { labels: charts.ordersStatus.labels || [], datasets: [{ data: charts.ordersStatus.data || [], backgroundColor: ['#007bff','#28a745','#ffc107','#dc3545','#17a2b8'] }] },
                        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } } }
                    });
                }catch(e){ console.error('admin-charts: dashboard order status chart error', e); }
            }

            initCommonUi();
            try{ hideLoaders(); }catch(e){}
        }
    };

    // Auto-run adapters when DOM ready and Chart.js available
    function ready(fn){ if(document.readyState==='complete' || document.readyState==='interactive') setTimeout(fn,0); else document.addEventListener('DOMContentLoaded', fn); }

    ready(function(){ waitForChart(function(){
        try{
            if(document.getElementById('reports-data')) adapters.reports();
            if(document.getElementById('report-financial-data')) adapters.financial();
            if(document.getElementById('dashboard-data')) adapters.dashboard();
            // If none of the adapters matched but generic loaders exist, hide them.
            if(!document.getElementById('reports-data') && !document.getElementById('report-financial-data') && !document.getElementById('dashboard-data')){
                try{ hideLoaders(); }catch(e){}
            }
        }catch(e){ console.error('admin-charts: adapter run error', e); }
    }); });

})();
