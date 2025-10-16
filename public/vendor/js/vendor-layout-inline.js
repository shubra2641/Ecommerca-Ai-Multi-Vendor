// Externalized small inline behaviors from vendor layout to satisfy CSP
(function(){
    'use strict';

    // Sidebar Toggle Functionality
    const sidebar = document.getElementById('vendorSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const mobileToggle = document.getElementById('mobileToggle');
    const sidebarToggle = document.getElementById('sidebarToggle');

    function toggleSidebar() {
        sidebar?.classList.toggle('open');
        overlay?.classList.toggle('show');
        document.body.classList.toggle('sidebar-open');
    }

    function closeSidebar() {
        sidebar?.classList.remove('open');
        overlay?.classList.remove('show');
        document.body.classList.remove('sidebar-open');
    }

    // Event Listeners
    mobileToggle?.addEventListener('click', toggleSidebar);
    sidebarToggle?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    // Close sidebar on window resize if screen becomes large
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            closeSidebar();
        }
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            try {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } catch (e) {
                // ignore if bootstrap not available yet
            }
        });
    }, 5000);
})();
