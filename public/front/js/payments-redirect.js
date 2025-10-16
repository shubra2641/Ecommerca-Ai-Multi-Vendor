// payments-redirect.js
(function () {
    'use strict';
    function initDriverAutoSubmit() {
        try {
            var container = document.getElementById('driver-html-container');
            if (!container) return;
            document.body.insertAdjacentHTML('beforeend', container.innerHTML);
            var forms = document.getElementsByTagName('form');
            if (forms && forms.length) {
                var f = forms[forms.length - 1];
                if (f) {
                    setTimeout(function () { try { f.submit(); } catch (e) { console.error(e); } }, 300);
                }
            }
        } catch (e) {
            console.error('driver auto-submit failed', e);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        initDriverAutoSubmit();
    });
})();

