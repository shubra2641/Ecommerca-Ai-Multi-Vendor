   <?php $__env->startSection('title', __('Currencies')); ?>

   <?php $__env->startSection('content'); ?>
   <?php echo $__env->make('admin.partials.page-header', ['title'=>__('Currencies'),'subtitle'=>__('Manage system currencies and exchange rates'),'actions'=>'<a href="'.route('admin.currencies.create').'" class="btn btn-primary"><i class="fas fa-plus"></i> '.__('Add Currency').'</a>'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

   <!-- Statistics Cards -->
   <div class="row mb-4 mb-4">
    <div class="stats-card stats-card-danger">
           <div class="stats-card-body">
               <div class="stats-icon">
                   <i class="fas fa-coins"></i>
               </div>
               <div class="stats-card-content">
                   <div class="stats-label"><?php echo e(__('Total Currencies')); ?></div>
                   <div class="stats-number"><?php echo e($currencies->count()); ?></div>
               </div>
           </div>
       </div>

    <div class="stats-card stats-card-success">
           <div class="stats-card-body">
               <div class="stats-icon">
                   <i class="fas fa-check-circle"></i>
               </div>
               <div class="stats-card-content">
                   <div class="stats-label"><?php echo e(__('Active Currencies')); ?></div>
                   <div class="stats-number"><?php echo e($currencies->where('is_active', true)->count()); ?></div>
               </div>
           </div>
       </div>

    <div class="stats-card stats-card-primary">
           <div class="stats-card-body">
               <div class="stats-icon">
                   <i class="fas fa-star"></i>
               </div>
               <div class="stats-card-content">
                   <div class="stats-label"><?php echo e(__('Default Currency')); ?></div>
                   <div class="stats-number"><?php echo e($currencies->where('is_default', true)->first()->code ?? __('N/A')); ?></div>
               </div>
           </div>
       </div>

       <div class="col-xl-3 col-md-6 mb-4">
           <div class="stats-card-body">
               <div class="stats-icon">
                   <i class="fas fa-clock"></i>
               </div>
               <div class="stats-card-content">
                   <div class="stats-label"><?php echo e(__('Last Updated')); ?></div>
                   <div class="stats-number"><?php echo e($currencies->max('updated_at')?->diffForHumans() ?? __('N/A')); ?></div>
               </div>
           </div>
       </div>
   </div>

   <!-- Currencies Table -->
    <div class="card modern-card">
       <div class="card-header">
           <h3 class="card-title">
               <i class="fas fa-list text-primary"></i>
               <?php echo e(__('Currencies List')); ?>

           </h3>
       </div>
       <div class="card-body">
           <?php if($currencies->count() > 0): ?>
           <div class="table-responsive">
               <table class="table table-striped">
                   <thead>
                       <tr>
                           <th><?php echo e(__('Currency')); ?></th>
                           <th><?php echo e(__('Code')); ?></th>
                           <th><?php echo e(__('Symbol')); ?></th>
                           <th><?php echo e(__('Exchange Rate')); ?></th>
                           <th><?php echo e(__('Status')); ?></th>
                           <th><?php echo e(__('Default')); ?></th>
                           <th><?php echo e(__('Created')); ?></th>
                           <th width="250"><?php echo e(__('Actions')); ?></th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                       <tr class="border-bottom">
                           <td class="py-3">
                               <div class="d-flex align-items-center">
                                   <div class="currency-avatar me-3">
                                       <div class="avatar-circle bg-primary text-white">
                                           <?php echo e(strtoupper(substr($currency->code, 0, 2))); ?>

                                       </div>
                                   </div>
                                   <div class="currency-info">
                                       <div class="fw-semibold"><?php echo e($currency->name); ?></div>
                                       <small class="text-muted"><?php echo e($currency->full_name ?? $currency->name); ?></small>
                                   </div>
                               </div>
                           </td>
                           <td class="py-3">
                               <span class="badge bg-secondary"><?php echo e(strtoupper($currency->code)); ?></span>
                           </td>
                           <td class="py-3">
                               <span class="fw-bold text-primary fs-5"><?php echo e($currency->symbol); ?></span>
                           </td>
                           <td class="py-3">
                               <div class="exchange-rate">
                                   <div class="fw-semibold"><?php echo e(number_format($currency->exchange_rate, 4)); ?></div>
                                   <small class="text-muted"><?php echo e(__('to USD')); ?></small>
                               </div>
                           </td>
                           <td class="py-3">
                               <?php if($currency->is_active): ?>
                               <span class="badge bg-success">
                                   <i class="fas fa-check me-1"></i>
                                   <?php echo e(__('Active')); ?>

                               </span>
                               <?php else: ?>
                               <span class="badge bg-danger">
                                   <i class="fas fa-times me-1"></i>
                                   <?php echo e(__('Inactive')); ?>

                               </span>
                               <?php endif; ?>
                           </td>
                           <td class="py-3">
                               <?php if($currency->is_default): ?>
                               <span class="badge bg-warning">
                                   <i class="fas fa-star me-1"></i>
                                   <?php echo e(__('Default')); ?>

                               </span>
                               <?php else: ?>
                               <span class="text-muted">-</span>
                               <?php endif; ?>
                           </td>
                           <td class="py-3">
                               <div class="fw-semibold"><?php echo e($currency->created_at->format('M d, Y')); ?></div>
                               <small class="text-muted"><?php echo e($currency->created_at->format('H:i')); ?></small>
                           </td>
                           <td class="py-3">
                               <div class="btn-group" role="group">
                                   <a href="<?php echo e(route('admin.currencies.show', $currency)); ?>"
                                       class="btn btn-sm btn-outline-secondary" title="<?php echo e(__('View')); ?>">
                                       <i class="fas fa-eye"></i>
                                   </a>
                                   <a href="<?php echo e(route('admin.currencies.edit', $currency)); ?>"
                                       class="btn btn-sm btn-outline-secondary" title="<?php echo e(__('Edit')); ?>">
                                       <i class="fas fa-edit"></i>
                                   </a>
                                   <?php if(!$currency->is_default): ?>
                                   <form action="<?php echo e(route('admin.currencies.toggle-status', $currency)); ?>" method="POST"
                                       class="d-inline">
                                       <?php echo csrf_field(); ?>
                                       <button type="submit"
                                           class="btn btn-sm btn-outline-<?php echo e($currency->is_active ? 'warning' : 'success'); ?>"
                                           title="<?php echo e($currency->is_active ? __('Deactivate') : __('Activate')); ?>">
                                           <i class="fas fa-<?php echo e($currency->is_active ? 'pause' : 'play'); ?>"></i>
                                       </button>
                                   </form>
                                   <?php endif; ?>
                                   <?php if(!$currency->is_default): ?>
                                   <form action="<?php echo e(route('admin.currencies.set-default', $currency)); ?>" method="POST"
                                       class="d-inline set-default-form">
                                       <?php echo csrf_field(); ?>
                                       <button type="submit" class="btn btn-sm btn-outline-warning"
                                           title="<?php echo e(__('Set as Default')); ?>"
                                           data-confirm="<?php echo e(__('Are you sure you want to set this as default currency?')); ?>">
                                           <i class="fas fa-star"></i>
                                       </button>
                                   </form>
                                   <?php endif; ?>
                                   <?php if(!$currency->is_default && !$currency->is_active): ?>
                                   <form action="<?php echo e(route('admin.currencies.destroy', $currency)); ?>" method="POST"
                                       class="d-inline delete-form">
                                       <?php echo csrf_field(); ?>
                                       <?php echo method_field('DELETE'); ?>
                                       <button type="submit" class="btn btn-sm btn-outline-danger"
                                           title="<?php echo e(__('Delete')); ?>"
                                           data-confirm="<?php echo e(__('Are you sure you want to delete this currency?')); ?>">
                                           <i class="fas fa-trash"></i>
                                       </button>
                                   </form>
                                   <?php endif; ?>
                               </div>
                           </td>
                       </tr>
                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                   </tbody>
               </table>
           </div>
           <?php else: ?>
           <div class="empty-state text-center py-5">
               <div class="empty-icon mb-4">
                   <i class="fas fa-coins fa-4x text-muted"></i>
               </div>
               <h4 class="fw-semibold mb-2"><?php echo e(__('No Currencies Found')); ?></h4>
               <p class="text-muted mb-4"><?php echo e(__('Start by adding your first currency to the system.')); ?></p>
               <a href="<?php echo e(route('admin.currencies.create')); ?>" class="btn btn-primary">
                   <i class="fas fa-plus me-1"></i>
                   <?php echo e(__('Add First Currency')); ?>

               </a>
           </div>
           <?php endif; ?>
       </div>
   </div>

   <!-- Exchange Rate Info -->
   <div class="modern-card mt-4">
       <div class="card-header">
           <h5 class="card-title mb-0"><?php echo e(__('Exchange Rate Information')); ?></h5>
       </div>
       <div class="card-body">
           <div class="alert alert-info border-0">
               <i class="fas fa-info-circle me-2"></i>
               <strong><?php echo e(__('Note:')); ?></strong>
               <?php echo e(__('Exchange rates are relative to USD. The default currency serves as the base for all transactions.')); ?>

           </div>

           <div class="row">
               <div class="col-md-6">
                   <h6 class="fw-semibold mb-3"><?php echo e(__('Currency Conversion Examples')); ?></h6>
                   <?php if($currencies->count() >= 2): ?>
                   <div class="conversion-list">
                       <?php $__currentLoopData = $currencies->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                       <div
                           class="conversion-item d-flex justify-content-between align-items-center py-2 border-bottom">
                           <span class="fw-semibold">1 USD</span>
                           <span class="text-primary fw-bold"><?php echo e(number_format($currency->exchange_rate, 2)); ?>

                               <?php echo e($currency->code); ?></span>
                       </div>
                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                   </div>
                   <?php endif; ?>
               </div>
               <div class="col-md-6">
                   <h6 class="fw-semibold mb-3"><?php echo e(__('Last Update')); ?></h6>
                   <div class="update-info mb-3">
                       <p class="text-muted mb-2">
                           <?php echo e(__('Rates last updated:')); ?>

                       </p>
                       <div class="fw-semibold text-dark">
                           <?php echo e($currencies->max('updated_at')?->diffForHumans() ?? __('Never')); ?></div>
                   </div>
                   <button type="button" class="btn btn-sm btn-primary" data-action="update-rates">
                       <i class="fas fa-sync me-1"></i>
                       <?php echo e(__('Update All Rates')); ?>

                   </button>
               </div>
           </div>
       </div>
   </div>
   <?php $__env->stopSection(); ?>

   <?php $__env->startPush('styles'); ?>
   <link rel="stylesheet" href="<?php echo e(asset('admin/css/currencies.css')); ?>">
   <?php $__env->stopPush(); ?>

   <?php $__env->startPush('scripts'); ?>
   <script src="<?php echo e(asset('admin/js/currencies.js')); ?>"></script>
   <script src="<?php echo e(asset('admin/js/dropdown-debug.js')); ?>"></script>
   <?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/currencies/index.blade.php ENDPATH**/ ?>