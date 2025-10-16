// Small fallback to remove the admin loading overlay if other scripts crash or hang.
(function () {
    'use strict';

    function hideLoading()
    {
        try {
            var el = document.querySelector('.admin-loading, #admin-loading, .loading-overlay');
            if (el) {
                el.style.transition = 'opacity 200ms ease';
                el.style.opacity = '0';
                setTimeout(function () {
                    if (el.parentNode) {
                        el.parentNode.removeChild(el);
                    } }, 250);
            }
        } catch (e) {
            // ignore
        }
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        hideLoading();
    } else {
        document.addEventListener('DOMContentLoaded', hideLoading, { once: true });
    }

    // absolute fallback in case DOMContentLoaded never fires due to script errors elsewhere
    setTimeout(hideLoading, 4000);
})();
(function () {
    function hide()
    {
        try {
            const el = document.getElementById('loading-screen');
            if (!el) {
                return;
            }
            el.style.transition = 'opacity 0.3s ease';
            el.style.opacity = '0';
            setTimeout(() => { el.style.display = 'none'; }, 350);
        } catch (e) {
  /* ignore */}
    }
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        hide();
    } else {
        document.addEventListener('DOMContentLoaded', hide);
        window.addEventListener('load', hide);
    }
  // safety: ensure hide after 4s even if events fail
    setTimeout(hide, 4000);
})();
