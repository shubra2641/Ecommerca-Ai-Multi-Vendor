'use strict';
// Contact blocks index: sortable ordering & toggle enabled (extracted from inline script)
(function(){
  function parse(){ const t=document.getElementById('contact-blocks-index-data'); if(!t) return {}; try{return JSON.parse(t.innerHTML.trim()||'{}');}catch(e){return {};}};
  function init(){ const cfg=parse(); const tbody=document.getElementById('sortable-blocks'); if(!tbody) return; if(window.Sortable){ makeSortable(); } else loadSortable(makeSortable);
    function makeSortable(){ new Sortable(tbody,{animation:150,handle:'.cursor-grab',onEnd:updateNumbers}); }
    function updateNumbers(){ Array.from(tbody.querySelectorAll('tr')).forEach((tr,i)=>{ const input=tr.querySelector('.small-order'); if(input) input.value=i+1; }); }
    const saveBtn=document.getElementById('saveOrdering'); if(saveBtn){ saveBtn.addEventListener('click',()=>{ const order={}; tbody.querySelectorAll('tr').forEach((tr,i)=> order[tr.dataset.id]=i+1); fetch(cfg.sortRoute,{method:'POST',headers:{'X-CSRF-TOKEN':cfg.csrf,'Accept':'application/json','Content-Type':'application/json'},body:JSON.stringify({order})}).then(r=>r.json()).then(()=>{ window.notify && window.notify.success(cfg.i18n.saved); }); }); }
    tbody.querySelectorAll('.toggle-enabled').forEach(cb=>{ cb.addEventListener('change',()=>{ const id=cb.dataset.id; const fd=new URLSearchParams({_method:'PUT',enabled:cb.checked?1:0}); fetch(cfg.updateBase+'/'+id,{method:'POST',headers:{'X-CSRF-TOKEN':cfg.csrf,'Accept':'application/json'},body:fd}).then(r=> r.ok ? r.json().catch(()=>({})) : Promise.reject()).then(()=>{ window.notify && window.notify.success(cb.checked? cfg.i18n.enabled: cfg.i18n.disabled); }).catch(()=>{ window.notify && window.notify.error(cfg.i18n.fail); cb.checked=!cb.checked; }); }); });
  }
  function loadSortable(cb){ const s=document.createElement('script'); s.src='/vendor/sortable/Sortable.min.js'; s.onload=cb; document.head.appendChild(s); }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
