(function(){
  const input = document.getElementById('sidebarQuickSearch');
  if(!input) return;
  const sidebar = document.getElementById('sidebar');
  const nav = sidebar.querySelector('.sidebar-nav');
  const normalizeBase = s => (s||'').toLowerCase().trim();
  // Arabic normalization: remove diacritics, normalize alef/ya/ta marbuta
  const stripDiacritics = s => s.replace(/[\u0610-\u061A\u064B-\u065F\u0670\u06D6-\u06ED]/g,'');
  const normalizeArabicChars = s => s
    .replace(/[إأآا]/g,'ا')
    .replace(/ى/g,'ي')
    .replace(/ة/g,'ه')
    .replace(/ؤ/g,'و')
    .replace(/ئ/g,'ي');
  const normalize = s => normalizeArabicChars(stripDiacritics(normalizeBase(s)));
  const allNavItems = () => Array.from(nav.querySelectorAll('.nav-item, .dropdown-item'));
  const dropDowns = () => Array.from(nav.querySelectorAll('.nav-dropdown'));
  const sectionTitles = () => Array.from(nav.querySelectorAll('.nav-section-title'));
  let lastQuery='';
  function highlight(el, terms){
    const target = el.querySelector('.nav-text, .dropdown-item, span.nav-text, .dropdown-item i + span') || el;
    if(!target) return;
    const raw = target.getAttribute('data-orig-text') || target.textContent;
    if(!target.getAttribute('data-orig-text')) target.setAttribute('data-orig-text', raw);
    let displayed = raw;
    const lowered = normalize(raw);
    terms.forEach(term=>{
      if(!term) return;
      const idx = lowered.indexOf(term);
      if(idx>-1){
        // naive highlight by splitting on original string indices (approx only if same length). For Arabic normalization, fallback regex search on raw ignoring case.
        const regex = new RegExp(term.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'),'i');
        displayed = displayed.replace(regex, m=>'<mark class="qs-hl">'+m+'</mark>');
      }
    });
    target.innerHTML = displayed;
  }
  function clearHighlight(){
    allNavItems().forEach(el=>{
      const t = el.querySelector('.nav-text, .dropdown-item, span.nav-text, .dropdown-item i + span');
      if(t && t.getAttribute('data-orig-text')){ t.innerHTML = t.getAttribute('data-orig-text'); }
    });
  }
  function filter(){
    const qRaw = input.value;
    const q = normalize(qRaw);
    if(q === lastQuery) return; lastQuery = q;
    clearHighlight();
    const terms = q.split(/\s+/).filter(Boolean);
    const showAll = terms.length===0;
    // Reset display
    allNavItems().forEach(el=>{ el.style.display=''; el.classList.remove('qs-hidden'); });
    dropDowns().forEach(dd=> dd.classList.remove('force-open'));
    if(showAll){ sectionTitles().forEach(t=> t.style.display=''); return; }
    // Filter
    allNavItems().forEach(el=>{
      const text = normalize(el.textContent);
      const match = terms.every(term=> text.includes(term));
      if(!match){ el.style.display='none'; el.classList.add('qs-hidden'); }
      else highlight(el, terms);
    });
    // Open dropdowns with matches
    dropDowns().forEach(dd=>{
      const hasVisible = dd.querySelectorAll('.dropdown-menu .dropdown-item:not(.qs-hidden)').length>0;
      if(hasVisible){
        dd.classList.add('show');
        const menu = dd.querySelector('.dropdown-menu');
        if(menu) menu.classList.add('show');
      } else {
        dd.classList.remove('show');
        const menu = dd.querySelector('.dropdown-menu');
        if(menu) menu.classList.remove('show');
      }
    });
    // Hide section titles with no visible following items until next section
    sectionTitles().forEach(title=>{
      let el = title.parentElement.nextElementSibling; let any=false;
      while(el && !el.querySelector?.('.nav-section-title')){
        if(el.matches && (el.matches('.nav-item')||el.matches('.nav-dropdown')) && el.style.display !== 'none'){ any=true; break; }
        el = el.nextElementSibling;
      }
      if(!any) title.style.display='none'; else title.style.display='';
    });
  }
  input.addEventListener('input', filter);
  document.addEventListener('keydown', e=>{ if((e.ctrlKey||e.metaKey) && e.key === '/'){ e.preventDefault(); input.focus(); } });
})();
