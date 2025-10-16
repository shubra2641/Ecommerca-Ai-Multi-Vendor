(function () {
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form.js-confirm-delete').forEach(function (f) {
            f.addEventListener('submit', function (e) {
                const msg = f.getAttribute('data-confirm') || 'Are you sure?';
                if (!confirm(msg)) {
                    e.preventDefault();
                }
            });
        });
    });
})();