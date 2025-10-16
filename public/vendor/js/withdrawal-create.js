// Modern Withdrawal Form JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const amountBtns = document.querySelectorAll('.amount-btn');
    const form = document.getElementById('withdrawalForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Get values from PHP variables (will be set in the blade file) or fallback to form data attributes
    let minimumAmount = parseFloat(window.withdrawalConfig?.minimumAmount || 0);
    let availableBalance = parseFloat(window.withdrawalConfig?.availableBalance || 0);
    try {
        if ((!minimumAmount || !isFinite(minimumAmount)) && form && form.dataset && form.dataset.minimum) {
            minimumAmount = parseFloat(form.dataset.minimum || 0);
        }
        if ((!availableBalance || !isFinite(availableBalance)) && form && form.dataset && form.dataset.available) {
            availableBalance = parseFloat(form.dataset.available || 0);
        }
    } catch (err) {
        console.warn('withdrawal-create: failed to read dataset fallback', err);
    }
    
    // Amount suggestion buttons
    amountBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const amount = this.dataset.amount;
            amountInput.value = amount;
            amountInput.focus();
            
            // Add visual feedback
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });
    
    // Form validation
    form.addEventListener('submit', function(e) {
        const amount = parseFloat(amountInput.value);
        const termsChecked = document.getElementById('terms').checked;
        
        if (!amount || amount < minimumAmount || amount > availableBalance) {
            e.preventDefault();
            alert('Please enter a valid amount between ' + minimumAmount + ' and ' + availableBalance);
            return;
        }
        
        if (!termsChecked) {
            e.preventDefault();
            alert('Please agree to the terms and conditions');
            return;
        }
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        submitBtn.disabled = true;
    });
    
    // Real-time amount validation
    amountInput.addEventListener('input', function() {
        const amount = parseFloat(this.value);
        
        if (amount && (amount < minimumAmount || amount > availableBalance)) {
            this.style.borderColor = '#e53e3e';
        } else {
            this.style.borderColor = '#4facfe';
        }
    });
    
    // Payment method selection animation
    const paymentRadios = document.querySelectorAll('.payment-radio');
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove active class from all cards (guarded)
            const cards = document.querySelectorAll('.payment-card') || [];
            cards.forEach(card => {
                try { card.style.transform = 'scale(1)'; } catch (e) {}
            });
            
            // Add animation to selected card (guarded)
            const selectedCard = this.nextElementSibling;
            if (selectedCard) {
                try { selectedCard.style.transform = 'scale(1.02)'; } catch (e) {}
                setTimeout(() => {
                    try { selectedCard.style.transform = 'scale(1)'; } catch (e) {}
                }, 200);
            }
        });
    });
});