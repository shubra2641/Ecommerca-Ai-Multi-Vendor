<?php $__env->startSection('title', __('Edit Shipping Zone')); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.partials.page-header', ['title'=>__('Edit Shipping Zone')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="card modern-card">
    <div class="card-header d-flex align-items-center gap-2">
        <h3 class="card-title mb-0"><?php echo e(__('Edit Shipping Zone')); ?></h3>
    </div>
    <div class="card-body">
            <form method="POST" action="<?php echo e(route('admin.shipping-zones.update',$zone)); ?>" class="admin-form" aria-label="edit-shipping-zone">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="mb-3"><label class="form-label fw-semibold"><?php echo e(__('Name')); ?></label><input name="name"
                        class="form-control" value="<?php echo e($zone->name); ?>" required></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-semibold"><?php echo e(__('Code (optional)')); ?></label><input
                            name="code" class="form-control" value="<?php echo e($zone->code); ?>"></div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="active" value="1" id="zone-active"
                                <?php echo e($zone->active? 'checked':''); ?>>
                            <label class="form-check-label" for="zone-active"><?php echo e(__('Active')); ?></label>
                        </div>
                    </div>
                </div>
                <h5 class="mt-4"><?php echo e(__('Rules')); ?></h5>
                <p class="text-muted small mb-2">
                    <?php echo e(__('Leave governorate and city empty for a country-wide rule. City overrides governorate which overrides country.')); ?>

                </p>
                <div id="rules-list"></div>
                <button type="button" id="add-rule" class="btn btn-sm btn-outline-secondary mt-2"><?php echo e(__('Add Rule')); ?></button>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <a href="<?php echo e(route('admin.shipping-zones.index')); ?>" class="btn btn-outline-secondary"><?php echo e(__('Cancel')); ?></a>
            </div>
            <div>
                <button class="btn btn-primary mt-0"><?php echo e(__('Save')); ?></button>
            </div>
        </div>
            </form>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script id="shipping-zone-config" type="application/json">{!! json_encode([
    'countries'=>$countries->map(fn($c)=>['id'=>$c->id,'name'=>$c->name])->values(),
    'existing'=>$rules->map(fn($r)=>['country_id'=>$r->country_id,'governorate_id'=>$r->governorate_id,'city_id'=>$r->city_id,'price'=>$r->price,'estimated_days'=>$r->estimated_days])->values(),
    'routes'=>[
        'governorates'=>url('/admin/products/locations/governorates'),
        'cities'=>url('/admin/products/locations/cities')
    ],
    'i18n'=>['remove'=>__('Remove')]
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)</script>
<script src="<?php echo e(asset('admin/js/shipping-zone.js')); ?>" defer></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\easy\resources\views/admin/shipping_zones/edit.blade.php ENDPATH**/ ?>