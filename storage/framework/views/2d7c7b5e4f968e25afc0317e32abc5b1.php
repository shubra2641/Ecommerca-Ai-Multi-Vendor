<!-- Toast Container -->
<div class="toast-stack" id="toast-container"></div>

<!-- Flash Messages Data -->
<?php if(session('success')): ?>
    <div id="flash-success" data-message="<?php echo e(session('success')); ?>" style="display: none;"></div>
<?php endif; ?>

<?php if(session('info')): ?>
    <div id="flash-info" data-message="<?php echo e(session('info')); ?>" style="display: none;"></div>
<?php endif; ?>

<?php if(session('warning')): ?>
    <div id="flash-warning" data-message="<?php echo e(session('warning')); ?>" style="display: none;"></div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div id="flash-error" data-message="<?php echo e(session('error')); ?>" style="display: none;"></div>
<?php endif; ?>

<?php if($errors->any()): ?>
    <div id="flash-errors" data-errors="<?php echo e(json_encode($errors->all())); ?>" style="display: none;"></div>
<?php endif; ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/partials/flash.blade.php ENDPATH**/ ?>