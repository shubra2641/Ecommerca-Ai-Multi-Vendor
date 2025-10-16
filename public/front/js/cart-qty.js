// cart-qty.js
// Handles quantity increment/decrement & form auto-submit (extracted from inline script)
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.qty-decrease, .qty-increase').forEach(btn => {
            btn.addEventListener('click', function () {
                const target = document.querySelector(this.getAttribute('data-target'));
                if (!target) {
                    return;
                }
                let val = parseInt(target.value || '1');
                const available = target.dataset.available ? parseInt(target.dataset.available) : null;
                if (this.classList.contains('qty-increase')) {
                    val = val + 1; if (!isNaN(available) && val > available) {
                        val = available;
                    }
                } else {
                    val = Math.max(1, val - 1); }
                target.value = val;
                const form = target.closest('form');
                if (form) {
                    clearTimeout(form._qtyTimer); form._qtyTimer = setTimeout(() => form.submit(),250); }
            });
        });
        document.querySelectorAll('.qty-input').forEach(inp => {
            inp.addEventListener('blur', function () {
                const available = this.dataset.available ? parseInt(this.dataset.available) : null;
                let v = parseInt(this.value || '1');
                if (isNaN(v) || v < 1) {
                    v = 1;
                }
                if (!isNaN(available) && v > available) {
                    v = available;
                }
                this.value = v;
                const form = this.closest('form'); if (form) {
                    clearTimeout(form._qtyTimer); form._qtyTimer = setTimeout(() => form.submit(),250);}
            });
        });
    });
})();

