'use strict';
// Attaches click handlers for elements with data-open-media (replaces inline category scripts)
(function () {
    function init()
    {
        document.querySelectorAll('[data-open-media]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = btn.getAttribute('data-open-media');
                if (typeof openUnifiedMediaPicker === 'function') {
                    openUnifiedMediaPicker(function (url) {
                        var input = document.querySelector('[name="' + target + '"]');
                        if (input) {
                            input.value = url; }
                    });
                }
            });
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init); } else {
        init();
        }
})();
