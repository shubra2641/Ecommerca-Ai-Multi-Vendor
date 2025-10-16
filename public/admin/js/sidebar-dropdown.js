'use strict';
// Handles sidebar nav dropdown toggling (replaces inline script in admin layout)
(function () {
    function init()
    {
        document.querySelectorAll('.sidebar-nav .nav-dropdown > a.dropdown-toggle').forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                var parent = trigger.closest('.nav-dropdown');
                if (!parent) {
                    return;
                }
                var menu = parent.querySelector('.dropdown-menu');
                var isOpen = parent.classList.toggle('show');
                if (menu) {
                    if (isOpen) {
                            menu.classList.add('show'); } else {
                        menu.classList.remove('show');
                            } }
                trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init); } else {
        init();
        }
})();
