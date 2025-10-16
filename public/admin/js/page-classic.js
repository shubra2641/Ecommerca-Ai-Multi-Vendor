'use strict';
// Classic page editor save + language tab switching (extracted from inline script)
(function () {
    function parseCfg()
    {
        var t = document.getElementById('page-classic-config'); if (!t) {
            return }; try {
                return JSON.parse(t.innerHTML.trim() || '{}'); } catch (e) {
                    return {}; } }
    function init()
    {
        var cfg = parseCfg(); if (!cfg.pageId) {
            return;
        }
        var langBtns = document.querySelectorAll('.classic-lang-btn');
        var panes = document.querySelectorAll('.classic-pane');
        langBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                 langBtns.forEach(function (b) {
                    b.classList.remove('active'); });
                 btn.classList.add('active');
                 panes.forEach(function (p) {
                    p.classList.add('d-none'); });
                 var pane = document.querySelector(".classic-pane[data-pane='" + btn.dataset.code + "']");
                if (pane) {
                    pane.classList.remove('d-none');
                }
            }); });
        var saveBtn = document.getElementById('saveClassicBtn');
        if (saveBtn) {
            saveBtn.addEventListener('click', function () {
                        var payload = { page:{ slug:document.getElementById('classicSlug').value.trim(), published:document.getElementById('classicPublished').checked ? 1 : 0, mode : document.getElementById('classicMode').value, titles : {}, seo_titles : {}, seo_descriptions : {}, seo_tags : {}, bodies : {} }, blocks : [] };
                        document.querySelectorAll('.classic-title').forEach(function (i) {
                            payload.page.titles[i.dataset.lang] = i.value; });
                        document.querySelectorAll('.classic-body').forEach(function (i) {
                            payload.page.bodies[i.dataset.lang] = i.value; });
                        document.querySelectorAll('.classic-seo-title').forEach(function (i) {
                            payload.page.seo_titles[i.dataset.lang] = i.value; });
                        document.querySelectorAll('.classic-seo-desc').forEach(function (i) {
                            payload.page.seo_descriptions[i.dataset.lang] = i.value; });
                        document.querySelectorAll('.classic-seo-tags').forEach(function (i) {
                            payload.page.seo_tags[i.dataset.lang] = i.value; });
                        fetch(cfg.saveUrl, { method:'POST', headers:{'X-CSRF-TOKEN':cfg.csrf,'Content-Type':'application/json','Accept':'application/json'}, body: JSON.stringify(payload) })
                        .then(function (r) {
                            if (r.status === 422) {
                                return r.json().then(function (e) {
                                    throw e.errors || e; }); } return r.json(); })
                        .then(function (res) {
                            if (res.status === 'ok') {
                                alert((cfg.i18n && cfg.i18n.saved) || 'Saved'); if (payload.page.mode === 'builder') {
                                    window.location = cfg.builderUrl; } } else {
                                             alert((cfg.i18n && cfg.i18n.saveError) || 'Save error'); } })
                        .catch(function (e) {
                            if (e) {
                                var flat = []; Object.values(e).forEach(function (v) {
                                    if (Array.isArray(v)) {
                                                     flat = flat.concat(v); } else {
                                        flat.push(v);
                                                     } }); alert(flat.join('\n')); } else {
                                                              alert('Network error'); } });
            });
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init); } else {
        init();
        }
})();
