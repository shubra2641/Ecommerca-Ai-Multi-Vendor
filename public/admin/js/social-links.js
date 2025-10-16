'use strict';
// Drag-reorder social links table (extracted from inline script)
(function(){
  function init(){ var tbody=document.getElementById('sortable-body'); if(!tbody) return; var saveBtn=document.getElementById('save-order'); var dirty=false;
    function update(){ var rows=tbody.querySelectorAll('tr'); rows.forEach(function(row, idx){ var hidden=row.querySelector('input[type="hidden"][name^="orders["]'); if(!hidden){ hidden=document.createElement('input'); hidden.type='hidden'; hidden.name='orders['+row.dataset.id+']'; row.appendChild(hidden); } hidden.value=idx+1; }); if(saveBtn) saveBtn.disabled=!dirty; }
    var dragSrc=null; tbody.addEventListener('dragstart', function(e){ var tr=e.target.closest('tr'); if(!tr) return; dragSrc=tr; e.dataTransfer.effectAllowed='move'; tr.classList.add('opacity-50'); }); tbody.addEventListener('dragend', function(e){ var tr=e.target.closest('tr'); if(tr) tr.classList.remove('opacity-50'); }); tbody.addEventListener('dragover', function(e){ e.preventDefault(); var tr=e.target.closest('tr'); if(!tr || tr===dragSrc) return; var rect=tr.getBoundingClientRect(); var before=(e.clientY-rect.top)<rect.height/2; tbody.insertBefore(dragSrc, before? tr : tr.nextSibling); dirty=true; update(); });
    tbody.querySelectorAll('tr').forEach(tr=>tr.setAttribute('draggable','true')); update(); }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
