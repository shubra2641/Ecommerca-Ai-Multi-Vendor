(function () {
    const root = document;
    function qs(sel, ctx = root)
    {
        return (ctx || root).querySelector(sel); }
    function qsa(sel, ctx = root)
    {
        return Array.from((ctx || root).querySelectorAll(sel)); }
    function debounce(fn, ms)
    {
        let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }

  // Character counters
    function initCounters()
    {
        qsa('[data-counter]').forEach(inp => {
            const id = inp.getAttribute('data-counter');
            const max = parseInt(inp.getAttribute('data-max') || '0', 10);
            const display = qs('[data-counter-display="' + id + '"]');
            if (!display) {
                return;
            }
            const update = () => {
                const len = inp.value.trim().length;
                display.textContent = max ? (len + '/' + max) : len;
                if (max && len > max) {
                    display.classList.add('text-danger'); } else {
                                  display.classList.remove('text-danger'); }
            };
            inp.addEventListener('input', update);
            update();
        });
    }

  // Copy default language to empty
    function initCopyDefault()
    {
        const btn = qs('[data-copy-default]');
        if (!btn) {
            return;
        }
        btn.addEventListener('click', () => {
            const def = qs('#blog-post-default-lang') ? .value; if (!def) {
                return;
            }
            const groups = ['title', 'slug', 'excerpt', 'body', 'seo_title', 'seo_description', 'seo_tags'];
            groups.forEach(g => {
                qsa('[name^="' + g + '["]').forEach(el => {
                    const name = el.getAttribute('name');
                    const m = name.match(/^.+\[(.+)]$/); if (!m) {
                        return;
                    } const lang = m[1];
                    if (lang === def) {
                        return;
                    }
                    if (!el.value.trim()) {
                        const defEl = qs('[name="' + g + '[' + def + ']"]');
                        if (defEl && defEl.value.trim()) {
                            el.value = defEl.value;
                            el.dispatchEvent(new Event('input'));
                            markFilled(el);
                        }
                    }
                });
            });
        });
    }

  // Highlight AI-filled fields
    function markFilled(el)
    {
        el.classList.add('ai-filled');
        setTimeout(() => { el.classList.remove('ai-filled'); }, 2500);
    }

  // Hook into existing AI population events from product-form.js (which dispatches input already)
    function observeAIFills()
    {
        const targets = new Set(['excerpt', 'body', 'seo_description']);
        const observer = new MutationObserver(muts => {
            muts.forEach(m => {
                if (m.type === 'childList' && m.target instanceof HTMLElement) {
                  // skip
                }
            });
        });
      // Instead simpler: delegate on click after AI button returns (product-form.js sets value then removes loading attr).
        qsa('.js-ai-generate-post, .js-ai-generate-post-seo').forEach(btn => {
            btn.addEventListener('click', () => {
                const lang = btn.getAttribute('data-lang');
                const target = btn.classList.contains('js-ai-generate-post-seo') ? 'seo_description' : btn.getAttribute('data-target');
                if (!lang || !target) {
                    return;
                }
                const fieldSel = target === 'seo_description' ? 'input[name="seo_description[' + lang + ']"]' : (target === 'excerpt' ? 'textarea[name="excerpt[' + lang + ']"]' : 'textarea[name="body[' + lang + ']"]');
                const field = qs(fieldSel);
                if (!field) {
                    return;
                }
            // Poll until field not empty or 6s
                let tries = 0; const iv = setInterval(() => {
                    tries++;
                    if (field.value.trim().length > 0 || tries > 60) {
                        if (field.value.trim()) {
                            markFilled(field);
                        }
                        clearInterval(iv);
                    }
                }, 100);
            });
        });
    }

  // Removed inline <style> injection to satisfy SRIConsistencyTest; styles must exist in static CSS.
    function injectStyles()
    { }

    function init()
    {
        injectStyles();
        initCounters();
        initCopyDefault();
        observeAIFills();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init); } else {
        init();
        }
})();
