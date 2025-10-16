'use strict';
// Feature image picker for blog post create/edit (extracted from inline IIFE)
(function () {
    function init()
    {
        var box = document.getElementById('featPreview'); if (!box || typeof openUnifiedMediaPicker !== 'function') {
            return;
        } box.addEventListener('click', function () {
            openUnifiedMediaPicker(function (url) {
                box.innerHTML = '<img src="' + url + '" class="obj-cover w-100 h-100">'; var hidden = document.getElementById('featured_image_path'); if (hidden) {
                    hidden.value = url;
                } }); }); }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init); } else {
        init();
        }
})();
