'use strict';
// Initializes users report charts (registration & role distribution)
(function () {
    function parse()
    {
        var t = document.getElementById('report-users-data'); if (!t) {
            return }; try {
                return JSON.parse(t.innerHTML.trim() || '{}'); } catch (e) {
                    return {}; } }
    function init()
    {
        var d = parse(); if (!d.charts) {
            return;
        } if (window.Chart) {
            build(d.charts); } else {
            wait(function () {
                build(d.charts); }); } }
    function wait(cb)
    {
        var n = 0; (function loop()
        {
            if (window.Chart) {
                cb(); return; } if (n++ > 40) {
                return;
                } setTimeout(loop,150); })(); }
    function build(ch)
    {
        if (ch.registration) {
            var r = document.getElementById('registrationChart');
            if (r) {
                new Chart(r.getContext('2d'), {
                    type:'line',
                    data:{
                        labels:ch.registration.labels,
                        datasets:[{
                            label:ch.registration.label || 'New Users',
                            data:ch.registration.values,
                            borderColor:'#4e73df',
                            backgroundColor:'rgba(78,115,223,0.1)',
                            borderWidth:2,
                            fill:true,
                            tension:.3
                        }]
                    },
                    options:{
                        responsive:true,
                        maintainAspectRatio:false,
                        scales:{ y:{ beginAtZero:true, ticks:{ stepSize:1 } } },
                        plugins:{ legend:{ display:false } }
                    }
                });
            }
        }
        if (ch.roles) {
            var rc = document.getElementById('roleChart');
            if (rc) {
                new Chart(rc.getContext('2d'), {
                    type:'doughnut',
                    data:{
                        labels:ch.roles.labels,
                        datasets:[{
                            data:ch.roles.values,
                            backgroundColor:['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b'],
                            borderWidth:2,
                            borderColor:'#ffffff'
                        }]
                    },
                    options:{
                        responsive:true,
                        maintainAspectRatio:false,
                        plugins:{ legend:{ position:'bottom', labels:{ padding:20, usePointStyle:true } } }
                    }
                });
            }
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init); } else {
        init();
        }
})();
