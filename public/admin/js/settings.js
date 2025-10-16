// Settings Page JavaScript
// Handles front-end interactions for System Settings

(function () {
    // Refresh server info display
    window.refreshSystemInfo = function () {
        const serverTimeElement = document.getElementById('server-time');
        if (serverTimeElement) {
            const now = new Date();
            serverTimeElement.textContent = now.toLocaleString('en-CA', {
                year: 'numeric', month: '2-digit', day: '2-digit',
                hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
            }).replace(',', '');
        }
        showNotification('System information refreshed successfully!', 'success');
    };

    // Reset settings form
    window.resetForm = function () {
        if (confirm('Are you sure you want to reset all changes?')) {
            const form = document.querySelector('.settings-form');
            if (form) {
                form.reset();
            }
            showNotification('Form has been reset', 'info');
        }
    };

    // Notification helper
    function showNotification(message, type = 'info')
    {
        const notification = document.createElement('div');
        notification.className = `alert alert - ${type} alert - dismissible fade show position - fixed`;
        notification.style.cssText = 'top:20px; right:20px; z-index:9999; min-width:300px;';
        notification.innerHTML = `
            ${message}
            < button type = "button" class = "btn-close" data - bs - dismiss = "alert" > < / button >
        `;
        document.body.appendChild(notification);
        setTimeout(() => { if (notification.parentNode) {
                notification.remove();
        } }, 3000);
    }

    // Confirm and handle quick actions
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.action-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                const button = form.querySelector('.action-btn');
                const action = button.getAttribute('data-action');
                let confirmMessage = '';
                switch (action) {
                    case 'clear-cache': confirmMessage = 'Are you sure you want to clear the cache?'; break;
                    case 'clear-logs': confirmMessage = 'Are you sure you want to clear all logs?'; break;
                    case 'optimize': confirmMessage = 'Are you sure you want to optimize the system?'; break;
                }
                if (confirmMessage && !confirm(confirmMessage)) {
                    e.preventDefault(); return; }
                // loading state
                if (button) {
                    button.disabled = true;
                    const original = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span> Processing...</span>';
                    setTimeout(() => { button.disabled = false; button.innerHTML = original; }, 3000);
                }
            });
        });

        // Validate settings form
        const settingsForm = document.querySelector('.settings-form');
        if (settingsForm) {
            settingsForm.addEventListener('submit', function (e) {
                const email = document.getElementById('contact_email');
                const phone = document.getElementById('contact_phone');
                if (email && !email.value.trim()) {
                    e.preventDefault(); showNotification('Contact email is required', 'danger'); email.focus(); return;
                }
                if (phone && !phone.value.trim()) {
                    // optional phone
                }
            });
        }

    // Periodic server time refresh removed by policy. Use refreshSystemInfo() to update on demand.
    });
})();
