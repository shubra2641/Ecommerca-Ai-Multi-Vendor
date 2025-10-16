<!-- Global Notify Modal (used on product show & listing cards) -->
<div class="modal fade" id="notifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e(__('Notify me')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?php echo e(__('Provide an email or phone number to be notified')); ?></p>
                <div class="mb-3">
                    <label for="notifyEmail" class="form-label"><?php echo e(__('Email')); ?></label>
                    <input id="notifyEmail" class="form-control" type="email" placeholder="you@example.com">
                </div>
                <div class="mb-3">
                    <label for="notifyPhone" class="form-label"><?php echo e(__('Phone')); ?></label>
                    <input id="notifyPhone" class="form-control" type="tel" placeholder="e.g. +201234567890">
                    <div class="invalid-feedback" id="notifyPhoneError"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                <button type="button" id="notifySubmit" class="btn btn-primary"><?php echo e(__('Submit')); ?></button>
            </div>
        </div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\easy\resources\views/front/partials/notify-modal.blade.php ENDPATH**/ ?>