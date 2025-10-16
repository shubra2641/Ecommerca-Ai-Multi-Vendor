/* Dashboard page JS — minimal initializer for widgets */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        try {
            // Add small hover effect for dashboard cards
            document.querySelectorAll('.dash-card').forEach(function (card) {
                card.addEventListener('mouseenter', function () {
                    card.style.transform = 'translateY(-4px)'; card.style.transition = 'transform .18s ease'; });
                card.addEventListener('mouseleave', function () {
                    card.style.transform = 'none'; });
            });

            // Truncate long list labels in recent lists (safety)
            document.querySelectorAll('.recent-list .row .label').forEach(function (el) {
                if (el.textContent.length > 60) {
                    el.textContent = el.textContent.slice(0, 57) + '...';
                } });

            console.debug('dashboard.js initialized');
        } catch (e) {
            console.error('dashboard init failed', e); }
    });
})();

