'use strict';
// Initializes TinyMCE for page form fields & auto slug generation (extracted from inline script)
(function () {
    function parseCfg()
    {
        var t = document.getElementById('page-form-fields-config'); if (!t) {
            return }; try {
                return JSON.parse(t.innerHTML.trim() || '{}'); } catch (e) {
                    return {}; } }
    function loadTiny(url, initOpts)
    {
        if (window.tinymce) {
            return window.tinymce.init(initOpts); } if (window.__tinyLoading) {
            return;
            } window.__tinyLoading = true; var s = document.createElement('script'); s.src = url; s.referrerPolicy = 'origin'; s.onload = function () {
                window.tinymce && window.tinymce.init(initOpts); }; document.head.appendChild(s); }
    function slugify(str)
    {
        return str.toLowerCase().trim().replace(/[^a-z0-9\u0621-\u064a]+/g,'-').replace(/^-+|-+$/g,''); }
    function init()
    {
        var cfg = parseCfg(); var slugInput = document.querySelector('input[name="slug"]'); if (slugInput && !slugInput.value) { // attempt auto slug from English title else first title input
            var enTitle = document.querySelector('input[name="titles[en]"]') || document.querySelector('input[name^="titles["]');
            if (enTitle) {
                enTitle.addEventListener('input', function () {
                    if (!slugInput.value) {
                        slugInput.value = slugify(this.value); } }); }
        }
        var tinyUrl = cfg.tinyUrl || 'https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js';
        var selector = cfg.selector || 'textarea.page-editor';
        var dir = document.documentElement.getAttribute('dir') || 'ltr';
        loadTiny(tinyUrl, {
            selector: selector,
            plugins: 'link lists code directionality table autoresize',
            menubar: false,
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table | ltr rtl | code',
            directionality: dir,
            min_height: 260,
            autoresize_bottom_margin: 40,
            convert_urls: false
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init); } else {
        init();
        }
})();
