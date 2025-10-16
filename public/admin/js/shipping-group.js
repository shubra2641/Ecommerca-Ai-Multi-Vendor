'use strict';
// Handles dynamic locations for shipping group creation (extracted from inline script)
(function(){
  function parseConfig(){ var t=document.getElementById('shipping-group-config'); if(!t) return null; try{return JSON.parse(t.innerHTML.trim());}catch(e){return null;} }
  function init(){
    var cfg=parseConfig(); if(!cfg) return;
    var countries=cfg.countries||[];
    var govRoute=(cfg.routes||{}).governorates;
    var cityRoute=(cfg.routes||{}).cities; // may be unused here but keep for parity
    var existing=cfg.existing||[];
    var list=document.getElementById('locations-list');
    var addBtn=document.getElementById('add-location');
    if(!list||!addBtn) return;
    addBtn.addEventListener('click', function(){ addRow(); });
    function addRow(data){ data=data||{}; var idx=Date.now(); var div=document.createElement('div');
      div.className='location-row mb-2 p-2 border rounded';
      div.innerHTML='\n      <div class="row">\n        <div class="col-md-3"><select name="locations[][country_id]" class="form-control country-select" data-idx="'+idx+'"><option value="">-- Country --</option>'+countries.map(function(c){return '<option value="'+c.id+'" '+(data.country_id==c.id?'selected':'')+'>'+c.name+'</option>';}).join('')+'</select></div>\n        <div class="col-md-3"><select name="locations[][governorate_id]" class="form-control governorate-select" data-idx="'+idx+'"><option value="">-- Governorate --</option></select></div>\n        <div class="col-md-3"><select name="locations[][city_id]" class="form-control city-select" data-idx="'+idx+'"><option value="">-- City --</option></select></div>\n        <div class="col-md-2"><input name="locations[][price]" class="form-control" placeholder="Price" value="'+(data.price||'')+'"></div>\n        <div class="col-md-1"><input name="locations[][estimated_days]" class="form-control" placeholder="Days" value="'+(data.estimated_days||'')+'"></div>\n      </div>\n      <div class="mt-2"><button type="button" class="btn btn-sm btn-danger remove-location">Remove</button></div>';
      list.appendChild(div);
      var countrySel=div.querySelector('.country-select');
      countrySel.addEventListener('change', onCountryChange);
      div.querySelector('.remove-location').addEventListener('click', function(){ div.remove(); });
      if(data.country_id){ // populate governorates & cities for existing
        populateGovernorates(countrySel, data.governorate_id, div, data.city_id);
      }
    }
    function onCountryChange(e){ var sel=e.target; populateGovernorates(sel); }
    function populateGovernorates(countrySel, selectedGov, row, selectedCity){ var rowEl=row||countrySel.closest('.location-row'); var gov=rowEl.querySelector('.governorate-select'); var city=rowEl.querySelector('.city-select'); gov.innerHTML='<option value="">-- Governorate --</option>'; city.innerHTML='<option value="">-- City --</option>'; if(!countrySel.value) return; fetch(govRoute+'?country='+encodeURIComponent(countrySel.value)).then(function(r){return r.json();}).then(function(res){ (res.data||[]).forEach(function(g){ gov.insertAdjacentHTML('beforeend','<option value="'+g.id+'">'+g.name+'</option>'); }); if(selectedGov){ gov.value=selectedGov; populateCities(gov, rowEl, selectedCity); } gov.onchange=function(){ populateCities(gov, rowEl); }; }); }
    function populateCities(govSel, rowEl, selectedCity){ var city=rowEl.querySelector('.city-select'); city.innerHTML='<option value="">-- City --</option>'; if(!govSel.value) return; fetch((cfg.routes||{}).cities+'?governorate='+encodeURIComponent(govSel.value)).then(function(r){return r.json();}).then(function(res){ (res.data||[]).forEach(function(c){ city.insertAdjacentHTML('beforeend','<option value="'+c.id+'">'+c.name+'</option>'); }); if(selectedCity){ city.value=selectedCity; } }); }
    // seed existing
    existing.forEach(function(e){ addRow(e); });
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
