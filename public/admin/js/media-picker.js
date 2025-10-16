(function () {
    function buildGridItems(files, cb) { return files.map(f => `<div class='pb-media-item-sm position-relative'><img src='${f.url}' data-url='${f.url}' class='w-60 h-60 obj-cover border-ddd rounded-4'><div class='small text-truncate mt-1 mp-thumb-label'>${(f.name || '').slice(0, 10)}</div></div>`).join(''); }
    function addSearchAndPagination(container, state) {
        container.querySelector('.mp-search').addEventListener('input', e => { state.q = e.target.value; state.page = 1; state.load(); });
        container.querySelector('.mp-prev').addEventListener('click', () => { if (state.page > 1) { state.page--; state.load(); } });
        container.querySelector('.mp-next').addEventListener('click', () => { state.page++; state.load(); });
    }
    window.openUnifiedMediaPicker = function (callback) {
        const existing = document.getElementById('pb-media-modal');
        if (existing) { // reuse builder modal but augment once
            const modalEl = existing;
            const grid = modalEl.querySelector('.pb-media-grid');
            const upload = modalEl.querySelector('#pb-media-upload');
            if (!modalEl.querySelector('.mp-search')) {
                const header = modalEl.querySelector('.modal-header');
                const search = document.createElement('input');
                search.placeholder = 'Search'; search.className = 'form-control form-control-sm mp-search'; search.style.maxWidth = '160px';
                header.insertBefore(search, header.querySelector('.btn-close'));
                const pager = document.createElement('div'); pager.className = 'd-flex gap-1 align-items-center ms-2'; pager.innerHTML = `<button type='button' class='btn btn-outline-secondary btn-sm mp-prev'>&lt;</button><span class='small mp-page'>1</span><button type='button' class='btn btn-outline-secondary btn-sm mp-next'>&gt;</button>`; header.insertBefore(pager, header.querySelector('.btn-close'));
            }
            const listUrlBase = document.querySelector('meta[name=media-list-url]')?.content || '/admin/pages/media/list';
            const uploadUrl = document.querySelector('meta[name=media-upload-url]')?.content || '/admin/pages/media/upload';
            const state = { page: 1, q: '', loading: false, load() { if (state.loading) return; state.loading = true; grid.innerHTML = '<div class="text-muted small p-2">Loading...</div>'; const url = listUrlBase + `?page=${state.page}&q=${encodeURIComponent(state.q)}`; fetch(url).then(r => { if (!r.ok) throw new Error('network'); return r.json(); }).then(res => { if (Array.isArray(res.files)) { grid.innerHTML = buildGridItems(res.files, callback); grid.querySelectorAll('img').forEach(img => img.onclick = () => { callback(img.dataset.url); hide(); }); const hasMore = !!res.pagination?.has_more; modalEl.querySelector('.mp-next').disabled = !hasMore; modalEl.querySelector('.mp-prev').disabled = state.page === 1; } else grid.innerHTML = '<div class="text-danger small p-2">Error</div>'; modalEl.querySelector('.mp-page').textContent = state.page; state.loading = false; }).catch(() => { grid.innerHTML = '<div class="text-danger small p-2">Error</div>'; state.loading = false; }); } };
            addSearchAndPagination(modalEl, state); state.load();
            const bs = new bootstrap.Modal(modalEl); bs.show(); function hide() { bs.hide(); }
            upload.onchange = (e) => { const file = e.target.files[0]; if (!file) return; const fd = new FormData(); fd.append('file', file); fetch(uploadUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: fd }).then(r => r.json()).then(res => { if (res.url) { callback(res.url); hide(); } else state.load(); }).catch(() => { }); };
            return;
        }
        const wrap = document.createElement('div');
    wrap.innerHTML = `<div class="modal fade" id="unified-media-modal"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header py-2"><h6 class="modal-title mb-0 small">Media</h6><input type='text' class='form-control form-control-sm mp-search ms-2' placeholder='Search'><div class='d-flex gap-1 ms-2'><button class='btn btn-outline-secondary btn-sm mp-prev' type='button'>&lt;</button><span class='small mp-page'>1</span><button class='btn btn-outline-secondary btn-sm mp-next' type='button'>&gt;</button></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="file" id="unified-upload" class="form-control form-control-sm mb-2"><div class="uf-grid d-flex flex-wrap gap-2"></div></div></div></div></div>`;
        document.body.appendChild(wrap);
        const modal = new bootstrap.Modal(wrap.querySelector('#unified-media-modal'));
        const grid = wrap.querySelector('.uf-grid');
        const listUrlBase = document.querySelector('meta[name=media-list-url]')?.content || '/admin/pages/media/list';
        const uploadUrl = document.querySelector('meta[name=media-upload-url]')?.content || '/admin/pages/media/upload';
    const state = { page: 1, q: '', loading: false, load() { if (state.loading) return; state.loading = true; grid.innerHTML = '<div class="text-muted small">Loading...</div>'; const url = listUrlBase + `?page=${state.page}&q=${encodeURIComponent(state.q)}`; fetch(url).then(r => { if (!r.ok) throw new Error('network'); return r.json(); }).then(res => { if (Array.isArray(res.files)) { grid.innerHTML = buildGridItems(res.files, callback); grid.querySelectorAll('img').forEach(img => img.onclick = () => { callback(img.dataset.url); modal.hide(); }); const hasMore = !!res.pagination?.has_more; wrap.querySelector('.mp-next').disabled = !hasMore; wrap.querySelector('.mp-prev').disabled = state.page === 1; } else grid.innerHTML = '<div class="text-danger small">Error</div>'; wrap.querySelector('.mp-page').textContent = state.page; state.loading = false; }).catch(() => { grid.innerHTML = '<div class="text-danger small">Error</div>'; state.loading = false; }); } };
        addSearchAndPagination(wrap, state); state.load();
        wrap.querySelector('#unified-upload').onchange = (e) => { const file = e.target.files[0]; if (!file) return; const fd = new FormData(); fd.append('file', file); fetch(uploadUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: fd }).then(r => r.json()).then(res => { if (res.url) { callback(res.url); modal.hide(); } else state.load(); }).catch(() => { }); };
        modal.show();
    };
})();
