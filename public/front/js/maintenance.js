(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.getElementById('maintenance');
        if (!root) {
            return;
        }

        var reopen = root.getAttribute('data-reopen');
        if (!reopen) {
            return;
        }

        var reopenDate = new Date(reopen);
        if (isNaN(reopenDate.getTime())) {
            return;
        }

        var el = document.getElementById('countdown');
        if (!el) {
            return;
        }

        function update()
        {
            var now = new Date();
            var diff = Math.max(0, reopenDate - now);
            var s = Math.floor(diff / 1000) % 60;
            var m = Math.floor(diff / (1000 * 60)) % 60;
            var h = Math.floor(diff / (1000 * 60 * 60));
            el.textContent = String(h).padStart(2,'0') + ":" + String(m).padStart(2,'0') + ":" + String(s).padStart(2,'0');
            if (diff <= 0) {
                // Attempt to reload to let server reflect changed maintenance flag
                try {
                    window.location.reload(true); } catch (e) {
                                  window.location.reload(); }
            }
        }

      // Throttle intervals for performance on low-end devices
        update();
        setInterval(update, 1000);
    });
})();

