// Progressive enhancement for landing categories
// Works without JS: server renders first category subcats.
(function () {
    const wrap = document.querySelector('[data-cat-root]');
    if (!wrap) {
        return; // degrade gracefully
    }
    const pills = Array.from(wrap.querySelectorAll('[data-cat-pill]'));
    const subWrap = wrap.querySelector('[data-subcats]');
    const placeholder = wrap.getAttribute('data-placeholder');
    const max = parseInt(wrap.getAttribute('data-max') || '12',10);
    function renderSubs(children)
    {
        if (!subWrap) {
            return;
        }
        subWrap.innerHTML = '';
        if (!children || !children.length) {
            const p = document.createElement('div');p.className = 'subcat-empty';p.textContent = wrap.getAttribute('data-empty') || 'No subcategories';subWrap.appendChild(p);return;}
        children.slice(0,max).forEach(c => {
            const a = document.createElement('a');
            a.href = c.slug ? (wrap.getAttribute('data-category-route-template') || '/products/category/{slug}').replace('{slug}', encodeURIComponent(c.slug)) : '#';
            a.className = 'subcat-card';
            const img = document.createElement('img');img.loading = 'lazy';img.src = c.image || placeholder; if (!c.image) {
                img.classList.add('is-cat-placeholder');
            } img.alt = c.name || '';
            const lab = document.createElement('div');lab.className = 'sc-label';lab.textContent = c.name || '';
            a.appendChild(img);a.appendChild(lab);subWrap.appendChild(a);
        });
    }
    if (pills.length) {
      // mark first active if none pre-marked
        if (!pills.some(p => p.classList.contains('active'))) {
            pills[0].classList.add('active'); }
    }
    pills.forEach(btn => btn.addEventListener('click', function () {
        pills.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        let data = [];try {
            data = JSON.parse(this.getAttribute('data-children') || '[]');} catch (e) {
            }
            renderSubs(data);
    }));
})();

