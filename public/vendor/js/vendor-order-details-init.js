// Initialization logic extracted from Blade for vendor order details page.

document.addEventListener('DOMContentLoaded', () => {
  const i18nEl = document.getElementById('vendorOrderI18n');
  if(i18nEl){
    window.VENDOR_I18N_COPIED = i18nEl.getAttribute('data-copied') || 'Copied';
  }
  const imageModal = document.getElementById('imageModal');
  const modalImage = imageModal?.querySelector('.modal-image');
  const imageButtons = document.querySelectorAll('[data-modal-target="imageModal"]');
  imageButtons.forEach(btn => {
    btn.addEventListener('click', function(){
      if(!modalImage) return;
      const productImage = this.closest('.product-image-container')?.querySelector('.product-thumbnail');
      if(productImage){
        modalImage.src = productImage.src;
        modalImage.alt = productImage.alt;
      }
    });
  });
  window.copyToClipboard = function(text){
    if(navigator.clipboard){
      navigator.clipboard.writeText(text).then(()=>{
        if(window.VendorOrderDetails){
          window.VendorOrderDetails.showNotification(window.VENDOR_I18N_COPIED||'Copied', 'success');
        }
      });
    }
  };
  document.addEventListener('click', e => {
    if(e.target.closest('[data-action="print"], [data-action="print-details"]')){
      e.preventDefault();
      window.print();
    }
  });
});
