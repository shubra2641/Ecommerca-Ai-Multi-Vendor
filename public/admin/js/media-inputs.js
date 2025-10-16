/* Simple bridge to the existing unified media picker used elsewhere in the admin */
(function () {
    document.addEventListener('click', function (e) {
        var t = e.target.closest('[data-open-media]');
        if (!t) {
            return;
        }
        var targetName = t.getAttribute('data-open-media');
        if (typeof openUnifiedMediaPicker !== 'function') {
            alert('Media picker not available'); return;
        }
        e.preventDefault();
        openUnifiedMediaPicker(function (url) {
            var input = document.querySelector('[name="' + targetName + '"]');
            if (input) {
                input.value = url;
            }
        });
    });
})();
