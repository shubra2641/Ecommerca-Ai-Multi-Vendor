'use strict';
// Translations page interactions extracted from inline jQuery script
(function () {
    function parse()
    {
        const t = document.getElementById('languages-translations-data'); if (!t) {
            return }; try {
                return JSON.parse(t.innerHTML.trim() || '{}');} catch (e) {
                    return {};}};
    function init()
    {
        const cfg = parse(); if (!window.jQuery) {
            return; } const $ = window.jQuery;
      // Delete translation
        $('.delete-btn').on('click', function () {
            const key = $(this).data('translation-key'); if (confirm(cfg.i18n.confirmDelete)) {
                $('#deleteKey').val(key); $('#deleteTranslationForm').submit(); } });
      // Expand / collapse
        $('#expandAll').on('click', () => $('.translation-textarea').attr('rows',5));
        $('#collapseAll').on('click', () => $('.translation-textarea').attr('rows',2));
      // Reset changes
        $('#resetChanges').on('click', () => { if (confirm(cfg.i18n.confirmReset)) {
                location.reload(); } });
      // Auto-resize textareas
        $('.translation-textarea').on('input', function () {
            this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px'; });
      // Search
        $('#translationSearch').on('keyup', function () {
            const term = $(this).val().toLowerCase(); $('.translation-item').each(function () {
                   const key = $(this).find('.translation-key').text().toLowerCase(); const val = $(this).find('.translation-textarea').val().toLowerCase(); $(this).toggle(key.includes(term) || val.includes(term)); }); });
      // Basic validation for add form
        $('form').on('submit', function (e) {
            const action = $(this).attr('action'); if (action && action.includes('add')) {
                  const key = $('#key').val().trim(); const value = $('#value').val().trim(); if (!key || !value) {
                    e.preventDefault(); alert(cfg.i18n.fillBoth); return false; } } });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init); } else {
        init();
        }
})();
