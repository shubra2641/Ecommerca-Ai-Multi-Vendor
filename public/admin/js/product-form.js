(function () {
    // Variation handling
    const variationMeta = document.getElementById('product-variation-meta');
    const typeSelect = document.getElementById('type-select');
    const simpleBlocks = document.querySelectorAll('.simple-only');
    const variableBlock = document.querySelector('.variable-only');
    function syncType()
    {
        if (!typeSelect) {
            return;
        }
        if (typeSelect.value === 'variable') {
            simpleBlocks.forEach(b => b.classList.add('envato-hidden'));
            if (variableBlock) {
                variableBlock.classList.remove('envato-hidden');
            }
        } else {
            simpleBlocks.forEach(b => b.classList.remove('envato-hidden'));
            if (variableBlock) {
                variableBlock.classList.add('envato-hidden');
            }
        }
    }
    typeSelect ? .addEventListener('change', syncType); syncType();
    const variationsTableBody = document.querySelector('#variations-table tbody');
    const addVariationBtn = document.getElementById('add-variation');
    const existing = variationMeta ? JSON.parse(variationMeta.dataset.existing || '[]') : [];
    const attributes = variationMeta ? JSON.parse(variationMeta.dataset.attributes || '[]') : [];
    function buildAttributeSelectors(attrData, idx)
    {
        let html = '';
        // Determine used attributes from admin checkboxes (if present)
        const usedCheckboxes = Array.from(document.querySelectorAll('.used-attr-checkbox:checked'));
        let usedSlugs = [];
        if (usedCheckboxes.length) {
            usedSlugs = usedCheckboxes.map(c => c.value);
        }

        // build attribute selectors into template, only for used attributes when checkboxes present
        attributes.filter(a => {
            const anyCheckbox = document.querySelector('.used-attr-checkbox') !== null;
            if (anyCheckbox) {
                return usedSlugs.length ? usedSlugs.includes(a.slug) : false;
            }
            return true;
        }).forEach(a => {
            const selected = (attrData && attrData[a.slug]) ? attrData[a.slug] : '';
            html += ` < div class = "mb-1" > < select class = "form-select form-select-sm" name = "variations[${idx}][attributes][${a.slug}]" > ` + ` < option value = "" > ${a.name} < / option > ` + a.values.map(v => ` < option value = "${v.slug}" ${selected === v.slug ? 'selected' : ''} > ${v.value} < / option > `).join('') + ` < / select > < / div > `;
        });
        return html;
}
    function rowTemplate(v, idx)
    {
        return ` < tr >
        < td > < input type = "checkbox" name = "variations[${idx}][active]" ${v.active ? 'checked' : ''} > < / td >
        < td class = "min-w-140" > ${buildAttributeSelectors(v.attributes || {}, idx)} < / td >
        < td > < input name = "variations[${idx}][sku]" value = "${v.sku || ''}" class = "form-control form-control-sm" > < / td >
        < td > < input type = "number" step = "0.01" name = "variations[${idx}][price]" value = "${v.price || ''}" class = "form-control form-control-sm w-90" > < / td >
        < td class = "w-220" >
            < input type = "number" step = "0.01" name = "variations[${idx}][sale_price]" value = "${v.sale_price || ''}" class = "form-control form-control-sm mb-1" placeholder = "Sale Price" >
            < input type = "datetime-local" name = "variations[${idx}][sale_start]" value = "${v.sale_start || ''}" class = "form-control form-control-sm mb-1" >
            < input type = "datetime-local" name = "variations[${idx}][sale_end]" value = "${v.sale_end || ''}" class = "form-control form-control-sm" >
        <  / td >
        < td class = "w-160" >
            < div class = "d-flex flex-column gap-1" >
                < input type = "number" name = "variations[${idx}][stock_qty]" value = "${v.stock_qty || 0}" class = "form-control form-control-sm" placeholder = "Stock" >
                < input type = "number" name = "variations[${idx}][reserved_qty]" value = "${v.reserved_qty || 0}" class = "form-control form-control-sm" placeholder = "Reserved" >
                    < input type = "hidden" name = "variations[${idx}][manage_stock]" value = "0" >
                    < label class = "small" > < input type = "checkbox" name = "variations[${idx}][manage_stock]" value = "1" ${v.manage_stock ? 'checked' : ''} > Manage < / label >
                        < input type = "hidden" name = "variations[${idx}][backorder]" value = "0" >
                        < label class = "small" > < input type = "checkbox" name = "variations[${idx}][backorder]" value = "1" ${v.backorder ? 'checked' : ''} > Backorder < / label >
                        < input type = "hidden" name = "variations[${idx}][image]" value = "${v.image || ''}" >
                        < div class = "d-flex align-items-center gap-2 mt-1" >
                            < img src = "${v.image || '/images/placeholder.svg'}" class = "var-thumb w-60 h-60 obj-cover border-ddd rounded-4" >
                            < div class = "d-flex flex-column" >
                                < button type = "button" class = "btn btn-sm btn-outline-secondary pf-open-media" data - target - name = "variations[${idx}][image]" > Media < / button >
                                < button type = "button" class = "btn btn-sm btn-outline-danger pf-clear-media mt-1" > Clear < / button >
                            <  / div >
                        <  / div >
            <  / div >
        <  / td >
        < td > < button type = "button" class = "btn btn-sm btn-danger pf-remove-var" > X < / button > < input type = "hidden" name = "variations[${idx}][id]" value = "${v.id || ''}" > < / td >
    <  / tr > `;
    }
    function addRow(v = { active: 1 })
    {
        if (!variationsTableBody) {
            return;
        } const idx = variationsTableBody.querySelectorAll('tr').length; variationsTableBody.insertAdjacentHTML('beforeend', rowTemplate(v, idx)); }
    existing.forEach(v => addRow(v));
    addVariationBtn ? .addEventListener('click', () => addRow({ active : 1 }));
    variationsTableBody ? .addEventListener('click', e => { if (e.target.classList.contains('pf-remove-var')) {
            e.target.closest('tr') ? .remove(); } });

    // Delegate media picker open/clear for dynamic variation rows
    document.addEventListener('click', function (e) {
        const openBtn = e.target.closest('.pf-open-media');
        if (openBtn) {
            e.preventDefault();
            if (!window.openUnifiedMediaPicker) {
                return;
            }
            const targetName = openBtn.getAttribute('data-target-name');
            openUnifiedMediaPicker(function (url) {
                if (!url) {
                    return;
                }
                const row = openBtn.closest('tr');
                if (!row) {
                    return;
                }
                // find hidden input by exact name
                const input = row.querySelector(`input[name = "${targetName}"]`);
                if (input) {
                    input.value = url;
                }
                const img = row.querySelector('.var-thumb');
                if (img) {
                    img.src = url;
                }
            });
            return;
        }
        const clearBtn = e.target.closest('.pf-clear-media');
        if (clearBtn) {
            e.preventDefault();
            const row = clearBtn.closest('tr');
            if (!row) {
                return;
            }
            const input = row.querySelector('input[name$="[image]"]');
            if (input) {
                input.value = '';
            }
            const img = row.querySelector('.var-thumb');
            if (img) {
                img.src = '/images/placeholder.svg';
            }
            return;
        }
    });

    // Gallery handling (multi image)
    const galleryWrap = document.getElementById('gallery-manager');
    const galleryInput = document.getElementById('gallery-input');
    const galleryBtn = document.getElementById('add-gallery-image');
    function parseGalleryValue()
    {
        let arr = [];
    if (!galleryInput) {
        return arr;
    }
    try {
        const raw = galleryInput.value;
        if (raw === '[') {
            return []; // malformed partial
        }
        if (!raw || raw === 'null' || raw === 'undefined') {
            return [];
        }
        const parsed = JSON.parse(raw);
        if (Array.isArray(parsed)) {
            return parsed;
        }
        if (parsed && typeof parsed === 'object') {
            return Object.values(parsed).filter(v => typeof v === 'string');
        }
        if (typeof parsed === 'string') {
            return [parsed];
        }
    } catch (e) {
        const raw = (galleryInput.value || '').trim();
        if (raw && !raw.startsWith('[') && !raw.startsWith('{')) {
            return [raw];
        }
    }
        return arr;
    }
    function normalizeGallery(arr)
    {
        if (!Array.isArray(arr)) {
            return [];
        }
        return arr.filter(u => typeof u === 'string' && u.trim() !== '');
    }
    function renderGallery()
    {
        if (!galleryWrap || !galleryInput) {
            return;
        }
        galleryWrap.innerHTML = '';
        let arr = normalizeGallery(parseGalleryValue());
        arr.forEach((u, i) => {
            const url = (u || '').trim();
                const item = document.createElement('div');
                item.className = 'position-relative pb-media-item-sm';
                const img = document.createElement('img');
                img.className = 'img-thumbnail';
                img.src = url || '';
                img.classList.add('w-60','h-60','obj-cover');
                img.onerror = function () {
                    this.onerror = null;
                    this.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Crect width='100%25' height='100%25' fill='%23ddd'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-size='10' fill='%23666'%3ENA%3C/text%3E%3C/svg%3E";
                };
                const btn = document.createElement('button');
                btn.type = 'button'; btn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 pf-del'; btn.setAttribute('data-i', i);
                btn.style.lineHeight = '1'; btn.style.padding = '2px 6px'; btn.innerHTML = '&times;';
                const label = document.createElement('div');
                label.className = 'small text-truncate mt-1 mp-thumb-label';
                label.textContent = (url || '').slice(0,10);
                item.appendChild(img);
                item.appendChild(label);
                item.appendChild(btn);
                galleryWrap.appendChild(item);
        });
    }
    renderGallery();
    galleryBtn ? .addEventListener('click', () => {
        if (!window.openUnifiedMediaPicker) {
            return;
        }
        openUnifiedMediaPicker(url => {
            let arr = normalizeGallery(parseGalleryValue());
            url = (url || '').trim();
            if (url && !arr.includes(url)) {
                arr.push(url);
            }
            galleryInput.value = JSON.stringify(arr);
            renderGallery();
        });
    });
    galleryWrap ? .addEventListener('click', e => {
        if (e.target.classList.contains('pf-del')) {
            let arr = normalizeGallery(parseGalleryValue());
            const i = parseInt(e.target.getAttribute('data-i'));
            if (!isNaN(i) && i >= 0 && i < arr.length) {
                arr.splice(i, 1);
                galleryInput.value = JSON.stringify(arr);
                renderGallery();
            }
        }
    });

    // Ensure gallery value is serialized right before submit
    const form = document.getElementById('product-form');
    form ? .addEventListener('submit', () => {
        if (!galleryInput) {
            return;
        }
        const arr = normalizeGallery(parseGalleryValue());
        galleryInput.value = JSON.stringify(arr);
    });

    // Digital fields toggle based on physical_type select
    (function () {
        const physicalSelect = document.querySelector('[name="physical_type"]');
        const digitalBlocks = document.querySelectorAll('.digital-only');
        function syncDigital()
        {
            const isDigital = physicalSelect && physicalSelect.value === 'digital';
            digitalBlocks.forEach(b => { if (isDigital) {
                    b.classList.remove('envato-hidden'); } else {
                b.classList.add('envato-hidden');
                    } });
        }
        physicalSelect ? .addEventListener('change', syncDigital);
        // initialize on load
        syncDigital();
    })();
    // Serials toggle when has_serials checkbox changes
    (function () {
        const chk = document.getElementById('has_serials_checkbox');
        const serialsBlock = document.querySelectorAll('.serials-only');
        function syncSerials()
        {
            const show = chk && chk.checked;
            serialsBlock.forEach(b => { if (show) {
                    b.classList.remove('envato-hidden'); } else {
                b.classList.add('envato-hidden');
                    } });
        }
        chk ? .addEventListener('change', syncSerials);
        syncSerials();
    })();
})();

// AI Generation (moved from inline script to comply with CSP)
document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.js-ai-generate, .js-ai-generate-seo');
    if (!btn) {
        return;
    }
    e.preventDefault();
    if (btn.dataset.loading === '1') {
        return;
    }
    const lang = btn.getAttribute('data-lang');
    const nameInput = document.querySelector(`input[name = "name[${lang}]"]`);
    if (!nameInput || !nameInput.value.trim()) {
        alert('Enter product name first');
        return;
    }
    // Resolve endpoint safely
    const scriptTag = document.querySelector('script[data-ai-suggest-url]');
    const endpoint = (window.AI_PRODUCT_SUGGEST_ENDPOINT)
        || (scriptTag && scriptTag.dataset.aiSuggestUrl)
        || (document.body.dataset && document.body.dataset.aiSuggestUrl)
        || '/admin/products/ai/suggest';
    btn.dataset.loading = '1';
    const originalHtml = btn.innerHTML;
    btn.classList.add('disabled');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    try {
        const resp = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ name: nameInput.value, locale: lang })
        });
        let data = {};
        try {
            data = await resp.json(); } catch (_) {
            /* ignore parse error */ }
            if (!resp.ok || data.error) {
                console.error('AI error', { status: resp.status, data, endpoint });
                if (data.error === 'rate_limited_local' || data.error === 'rate_limited_provider' || resp.status === 429) {
                    const retry = data.retry_after || 30;
                    const src = data.source === 'provider' ? 'provider' : 'local system';
                    const limitInfo = data.limit ? (' (limit: ' + data.limit + '/min)') : '';
                    alert('Rate limit exceeded from ' + src + '. Try again after ' + retry + ' seconds' + limitInfo + '.');
                } else if (data.error === 'provider_error') {
                    const pm = data.provider_body ? .error ? .message || data.provider_message || '';
                    alert('AI provider error (' + resp.status + ') ' + pm);
                } else if (data.error === 'connection_failed') {
                    alert('Failed to connect to AI provider. Check network.');
                } else {
                    alert('AI error: ' + (data.error || resp.status));
                }
                return;
            }
            const desc = document.querySelector(`textarea[name = "description[${lang}]"]`);
            const shortDesc = document.querySelector(`textarea[name = "short_description[${lang}]"]`);
            const seoDesc = document.querySelector(`textarea[name = "seo_description[${lang}]"]`);
            const seoKeywords = document.querySelector(`input[name = "seo_keywords[${lang}]"]`);
            if (desc && data.description) {
                desc.value = data.description.trim();
            }
            if (shortDesc && data.short_description) {
                shortDesc.value = data.short_description.trim();
            }
            if (seoDesc && data.seo_description) {
                seoDesc.value = data.seo_description.trim();
            }
            if (seoKeywords && data.seo_keywords) {
                seoKeywords.value = data.seo_keywords.trim();
            }
    } catch (err) {
        console.error('AI request failed', { err, endpoint });
        alert('Request failed');
    } finally {
        btn.dataset.loading = '0';
        btn.classList.remove('disabled');
        btn.innerHTML = originalHtml;
    }
});

// Category AI Generation (base description + SEO)
document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.js-ai-generate-category');
    if (!btn) {
        return;
    }
    e.preventDefault();
    if (btn.dataset.loading === '1') {
        return;
    }
    const nameInput = document.querySelector('input[name="name"]');
    if (!nameInput || !nameInput.value.trim()) {
        alert('Enter category name first'); return; }
    const endpoint = '/admin/products/product-categories/ai/suggest';
    btn.dataset.loading = '1';
    const original = btn.innerHTML; btn.classList.add('disabled'); btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    try {
        const resp = await fetch(endpoint, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}, body: JSON.stringify({ name: nameInput.value }) });
        let data = {}; try {
            data = await resp.json(); } catch (_) {
            }
            if (!resp.ok || data.error) {
                if (data.error === 'rate_limited_local' || data.error === 'rate_limited_provider' || resp.status === 429) {
                    const retry = data.retry_after || 30; const src = data.source === 'provider' ? 'provider' : 'local system'; const limitInfo = data.limit ? (' (limit: ' + data.limit + '/min)') : ''; alert('Rate limit exceeded from ' + src + '. Try again after ' + retry + ' seconds' + limitInfo + '.');
                } else if (data.error === 'provider_error') {
                    const pm = data.provider_body ? .error ? .message || data.provider_message || ''; alert('AI provider error (' + resp.status + ') ' + pm);
                } else if (data.error === 'connection_failed') {
                    alert('Failed to connect to AI provider. Check network.'); } else {
                                alert('AI error: ' + (data.error || resp.status)); }
                    return;
            }
            if (btn.dataset.targetPrefix === 'base') {
                const desc = document.querySelector('textarea[name="description"]');
                if (desc && data.description) {
                    desc.value = data.description.trim();
                }
            }
            if (btn.dataset.targetPrefix === 'seo') {
                const seoDesc = document.querySelector('textarea[name="seo_description"]');
                const seoKeywords = document.querySelector('input[name="seo_keywords"]');
                if (seoDesc && data.seo_description) {
                    seoDesc.value = data.seo_description.trim();
                }
                if (seoKeywords && data.seo_keywords) {
                    seoKeywords.value = data.seo_keywords.trim();
                }
            }
    } catch (err) {
        console.error('AI category request failed', err); alert('Request failed'); }
    finally { btn.dataset.loading = '0'; btn.classList.remove('disabled'); btn.innerHTML = original; }
});

// Blog Post AI Generation (excerpt/body/seo)
document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.js-ai-generate-post, .js-ai-generate-post-seo');
    if (!btn) {
        return;
    }
    e.preventDefault();
    if (btn.dataset.loading === '1') {
        return;
    }
    const lang = btn.getAttribute('data-lang') || document.querySelector('[data-bs-target^="#panel-"]') ? .id ? .replace('panel-','');
    const titleInput = document.querySelector(`input[name = "title[${lang}]"]`);
    if (!titleInput || !titleInput.value.trim()) {
        alert('Enter post title first'); return; }
    const endpoint = '/admin/blog/posts/ai/suggest';
    btn.dataset.loading = '1'; const original = btn.innerHTML; btn.classList.add('disabled'); btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    try {
        const resp = await fetch(endpoint,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({title:titleInput.value,locale:lang})});
        let data = {}; try {
            data = await resp.json();} catch (_) {
            }
            if (!resp.ok || data.error) {
                if (data.error === 'rate_limited_local' || data.error === 'rate_limited_provider' || resp.status === 429) {
                    const retry = data.retry_after || 30; const src = data.source === 'provider' ? 'provider' : 'local system'; const limitInfo = data.limit ? (' (limit: ' + data.limit + '/min)') : ''; alert('Rate limit exceeded from ' + src + '. Try again after ' + retry + ' seconds' + limitInfo + '.');
                } else if (data.error === 'provider_error') {
                    const pm = data.provider_body ? .error ? .message || data.provider_message || ''; alert('AI provider error (' + resp.status + ') ' + pm);
                } else if (data.error === 'connection_failed') {
                    alert('Failed to connect to AI provider. Check network.'); } else {
                                alert('AI error: ' + (data.error || resp.status)); }
                    return;
            }
            if (btn.classList.contains('js-ai-generate-post')) {
                if (btn.dataset.target === 'excerpt') {
                    const ex = document.querySelector(`textarea[name = "excerpt[${lang}]"]`); if (ex && data.excerpt) {
                        ex.value = data.excerpt.trim();
                    }
                } else if (btn.dataset.target === 'body') {
                    const body = document.querySelector(`textarea[name = "body[${lang}]"]`); if (body && data.body_intro) {
                        if (!body.value.trim()) {
                            body.value = data.body_intro.trim(); } else {
                            body.value += "\n\n" + data.body_intro.trim();
                            }
                    }
                }
                // fill seo description & tags opportunistically
                const seoDesc = document.querySelector(`input[name = "seo_description[${lang}]"]`);
                const seoTags = document.querySelector(`input[name = "seo_tags[${lang}]"]`);
                if (seoDesc && data.seo_description && !seoDesc.value.trim()) {
                    seoDesc.value = data.seo_description.trim();
                }
                if (seoTags && data.seo_tags && !seoTags.value.trim()) {
                    seoTags.value = data.seo_tags.trim();
                }
            } else if (btn.classList.contains('js-ai-generate-post-seo')) {
                const seoDesc = document.querySelector('input[name="seo_description"], input[name="seo_description[' + lang + ']"]');
                const seoTags = document.querySelector('input[name="seo_tags"], input[name="seo_tags[' + lang + ']"]');
                if (seoDesc && data.seo_description) {
                    seoDesc.value = data.seo_description.trim();
                }
                if (seoTags && data.seo_tags) {
                    seoTags.value = data.seo_tags.trim();
                }
            }
    } catch (err) {
        console.error('AI blog post request failed', err); alert('Request failed'); }
    finally { btn.dataset.loading = '0'; btn.classList.remove('disabled'); btn.innerHTML = original; }
});

// Blog Category AI Generation (description & SEO)
document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.js-ai-generate-blog-category');
    if (!btn) {
        return;
    }
    e.preventDefault();
    if (btn.dataset.loading === '1') {
        return;
    }
    const lang = btn.getAttribute('data-lang');
    const nameInput = document.querySelector(`input[name = "name[${lang}]"]`);
    if (!nameInput || !nameInput.value.trim()) {
        alert('Enter category name first'); return; }
    const endpoint = '/admin/blog/categories/ai/suggest';
    btn.dataset.loading = '1'; const original = btn.innerHTML; btn.classList.add('disabled'); btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    try {
        const resp = await fetch(endpoint,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({name:nameInput.value,locale:lang})});
        let data = {}; try {
            data = await resp.json();} catch (_) {
            }
            if (!resp.ok || data.error) {
                if (data.error === 'rate_limited_local' || data.error === 'rate_limited_provider' || resp.status === 429) {
                    const retry = data.retry_after || 30; const src = data.source === 'provider' ? 'provider' : 'local system'; const limitInfo = data.limit ? (' (limit: ' + data.limit + '/min)') : ''; alert('Rate limit exceeded from ' + src + '. Try again after ' + retry + ' seconds' + limitInfo + '.');
                } else if (data.error === 'provider_error') {
                    const pm = data.provider_body ? .error ? .message || data.provider_message || ''; alert('AI provider error (' + resp.status + ') ' + pm);
                } else if (data.error === 'connection_failed') {
                    alert('Failed to connect to AI provider. Check network.'); } else {
                                alert('AI error: ' + (data.error || resp.status)); }
                    return;
            }
            if (btn.dataset.target === 'description') {
                const desc = document.querySelector(`textarea[name = "description[${lang}]"]`); if (desc && data.description) {
                    desc.value = data.description.trim();
                }
            } else if (btn.dataset.target === 'seo') {
                const seoDesc = document.querySelector(`input[name = "seo_description[${lang}]"]`); const seoTags = document.querySelector(`input[name = "seo_tags[${lang}]"]`);
                if (seoDesc && data.seo_description) {
                    seoDesc.value = data.seo_description.trim();
                }
                if (seoTags && data.seo_tags && !seoTags.value.trim()) {
                    seoTags.value = data.seo_tags.trim();
                }
            }
    } catch (err) {
        console.error('AI blog category request failed', err); alert('Request failed'); }
    finally { btn.dataset.loading = '0'; btn.classList.remove('disabled'); btn.innerHTML = original; }
});
