// Commission hint logic extracted from Blade inline script
// Adds fee + net amount text under the amount field if present.

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('withdrawalForm');
  if(!form) return;
  const rateAttr = form.getAttribute('data-commission-rate');
  if(!rateAttr) return;
  const rate = parseFloat(rateAttr || '0');
  if(!(rate > 0)) return;
  const amountInput = document.getElementById('amount');
  const hint = document.getElementById('netAmountHint');
  if(!amountInput || !hint) return;
  const netLabel = hint.getAttribute('data-net-label') || 'Net';
  const feeLabel = hint.getAttribute('data-fee-label') || 'Fee';
  function updateHint(){
    const val = parseFloat(amountInput.value || '0');
    if(val > 0){
      const fee = (val * rate/100).toFixed(2);
      const net = (val - parseFloat(fee)).toFixed(2);
      hint.textContent = netLabel + ': ' + net + ' (' + feeLabel + ' ' + fee + ')';
    } else {
      hint.textContent = '';
    }
  }
  amountInput.addEventListener('input', updateHint);
  updateHint();
});
