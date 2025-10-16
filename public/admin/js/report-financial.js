'use strict';
// Initializes financial report charts (extracted)
(function(){
  function parse(){ var t=document.getElementById('report-financial-data'); if(!t) return {}; try { return JSON.parse(t.innerHTML.trim()||'{}'); } catch(e){ return {}; } }
  function init(){ var d=parse(); if(!d.charts) return; if(window.Chart){ buildCharts(d.charts); } else { waitForChart(function(){ buildCharts(d.charts); }); } }
  function waitForChart(cb){ var tries=0; (function poll(){ if(window.Chart){ cb(); return; } if(tries++>40) return; setTimeout(poll,150); })(); }
  function buildCharts(c){ if(c.balanceDistribution){ var ctx=document.getElementById('balanceDistributionChart'); if(ctx){ new Chart(ctx.getContext('2d'), { type:'doughnut', data:{ labels:c.balanceDistribution.labels, datasets:[{ data:c.balanceDistribution.values, backgroundColor:['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b'] }] }, options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } } } }); } }
    if(c.monthlyTrends){ var tctx=document.getElementById('monthlyTrendsChart'); if(tctx){ new Chart(tctx.getContext('2d'), { type:'line', data:{ labels:c.monthlyTrends.labels, datasets:[{ label:c.monthlyTrends.label||'Trends', data:c.monthlyTrends.values, borderColor:'#4e73df', backgroundColor:'rgba(78,115,223,0.1)', fill:true }] }, options:{ responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true, ticks:{ callback:v=>'$'+v } } } } }); } }
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
