'use strict';
// Working hours dynamic rows extracted from inline script
(function(){
  function init(){ const table=document.querySelector('#hoursTable tbody'); const addBtn=document.getElementById('addHour'); if(!table||!addBtn) return; function reindex(){ Array.from(table.querySelectorAll('tr')).forEach((tr,i)=>{ tr.querySelectorAll('input').forEach(inp=>{ inp.name=inp.name.replace(/hours\[(.*?)\]/,'hours['+i+']'); }); }); }
    addBtn.addEventListener('click',()=>{ const tr=document.createElement('tr'); tr.innerHTML='<td><input type="text" name="hours[][day]" class="form-control form-control-sm"></td><td><input type="text" name="hours[][from]" class="form-control form-control-sm"></td><td><input type="text" name="hours[][to]" class="form-control form-control-sm"></td><td><button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button></td>'; table.appendChild(tr); table.querySelector('.empty-row')?.remove(); reindex(); });
    table.addEventListener('click',e=>{ if(e.target.closest('.remove-row')){ const tr=e.target.closest('tr'); tr.remove(); if(!table.querySelector('tr')){ const empty=document.createElement('tr'); empty.className='empty-row'; empty.innerHTML='<td colspan="4" class="text-muted small text-center py-3">'+(table.dataset.nohours||'No hours')+'</td>'; table.appendChild(empty);} reindex(); }});
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
