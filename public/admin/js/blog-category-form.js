(function () {
    const copyBtn = document.querySelector('[data-copy-default]');
    if (!copyBtn) {
        return;
    }
    copyBtn.addEventListener('click', function () {
        const def = document.getElementById('blog-cat-default-lang');
        if (!def) {
            return;
        } const defCode = def.value;
        const nameDefault = document.querySelector(`input[name = "name[${defCode}]"]`);
        const descDefault = document.querySelector(`textarea[name = "description[${defCode}]"]`);
        const seoTitleDefault = document.querySelector(`input[name = "seo_title[${defCode}]"]`);
        const seoDescDefault = document.querySelector(`input[name = "seo_description[${defCode}]"]`);
        const seoTagsDefault = document.querySelector(`input[name = "seo_tags[${defCode}]"]`);
        const fields = ['name','description','seo_title','seo_description','seo_tags'];
        fields.forEach(f => {
            const defEl = {name:nameDefault, description:descDefault, seo_title:seoTitleDefault, seo_description:seoDescDefault, seo_tags:seoTagsDefault}[f];
            if (!defEl || !defEl.value.trim()) {
                return;
            }
            document.querySelectorAll(`[name ^ = "${f}["]`).forEach(el => {
                const m = el.name.match(/\[(.+?)\]/); if (!m) {
                    return;
                } const code = m[1]; if (code === defCode) {
                    return;
                } if (!el.value.trim()) {
                    el.value = defEl.value;
                }
            });
        });
    });
})();
