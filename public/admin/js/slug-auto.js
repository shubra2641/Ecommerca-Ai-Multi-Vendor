document.addEventListener('DOMContentLoaded',() => {
    const translit = (str) => {
        return (str || '')
        .normalize('NFD').replace(/\p{Diacritic}/gu,'')
        .toLowerCase()
        .replace(/[^a-z0-9\u0600-\u06FF\s-]/g,'')
        .replace(/\s+/g,'-')
        .replace(/-+/g,'-')
        .replace(/^-|-$/g,'');
    };
    const bind = (wrapper) => {
        wrapper.querySelectorAll('input[name^="title["],input[name^="name["]').forEach(input => {
            const locale = (input.name.match(/\[(.+)\]/) || [])[1];
            if (!locale) {
                return;
            }
            let slugInput = wrapper.querySelector(`input[name = "slug[${locale}]"]`);
            if (!slugInput) {
                return;}
          // Only auto if empty or previously auto
            const AUTO_FLAG = 'data-auto-slug';
            if (!slugInput.value) {
                slugInput.setAttribute(AUTO_FLAG,'1'); }
            input.addEventListener('input',() => {
                if (slugInput.getAttribute(AUTO_FLAG) === '1') {
                    slugInput.value = translit(input.value);
                }
            });
        slugInput.addEventListener('input',() => {
          // user manually changed
            if (slugInput.value.trim() !== '' ) {
                slugInput.removeAttribute(AUTO_FLAG); }
          });
        });
    };
    document.querySelectorAll('form').forEach(f => bind(f));
});
