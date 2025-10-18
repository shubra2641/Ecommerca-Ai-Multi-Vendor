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
                                <?php echo e($zone->active ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="zone-active"><?php echo e(__('Active')); ?></label>
                        </div>
                    </div>
                </div>

                <?php if($rules->count() > 0): ?>
                    <div class="mb-3">
                        <h6><?php echo e(__('Existing Rules')); ?></h6>
                        <?php $__currentLoopData = $rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold"><?php echo e(__('Country')); ?></label>
                                            <select name="rules[<?php echo e($index); ?>][country_id]" class="form-select">
                                                <option value=""><?php echo e(__('Select Country')); ?></option>
                                                <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($country->id); ?>" <?php echo e($rule->country_id == $country->id ? 'selected' : ''); ?>>
                                                        <?php echo e($country->name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold"><?php echo e(__('Governorate')); ?></label>
                                            <select name="rules[<?php echo e($index); ?>][governorate_id]" class="form-select">
                                                <option value=""><?php echo e(__('Select Governorate')); ?></option>
                                                <?php $__currentLoopData = \App\Models\Governorate::where('country_id', $rule->country_id)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $governorate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($governorate->id); ?>" <?php echo e($rule->governorate_id == $governorate->id ? 'selected' : ''); ?>>
                                                        <?php echo e($governorate->name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold"><?php echo e(__('City')); ?></label>
                                            <select name="rules[<?php echo e($index); ?>][city_id]" class="form-select">
                                                <option value=""><?php echo e(__('Select City')); ?></option>
                                                <?php if($rule->governorate_id): ?>
                                                    <?php $__currentLoopData = \App\Models\City::where('governorate_id', $rule->governorate_id)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($city->id); ?>" <?php echo e($rule->city_id == $city->id ? 'selected' : ''); ?>>
                                                            <?php echo e($city->name); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold"><?php echo e(__('Price')); ?></label>
                                            <input type="number" name="rules[<?php echo e($index); ?>][price]" class="form-control" 
                                                   value="<?php echo e($rule->price); ?>" step="0.01" min="0" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold"><?php echo e(__('Estimated Days')); ?></label>
                                            <input type="number" name="rules[<?php echo e($index); ?>][estimated_days]" class="form-control" 
                                                   value="<?php echo e($rule->estimated_days); ?>" min="1" required>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="rules[<?php echo e($index); ?>][active]" value="1" 
                                                       id="rule-active-<?php echo e($index); ?>" <?php echo e($rule->active ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="rule-active-<?php echo e($index); ?>"><?php echo e(__('Active')); ?></label>
                                            </div>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <a href="<?php echo e(route('admin.shipping-zones.edit', ['shipping_zone' => $zone, 'remove_rule' => $index])); ?>" 
                                               class="btn btn-outline-danger btn-sm" 
                                               onclick="return confirm('<?php echo e(__('Are you sure you want to remove this rule?')); ?>')">
                                                <?php echo e(__('Remove')); ?>

                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <?php echo e(__('No rules found. Add a rule below to get started.')); ?>

                    </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <h6><?php echo e(__('Add New Rule')); ?></h6>
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold"><?php echo e(__('Country')); ?></label>
                                    <select name="rules[new][country_id]" class="form-select" required>
                                        <option value=""><?php echo e(__('Select Country')); ?></option>
                                        <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($country->id); ?>"><?php echo e($country->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold"><?php echo e(__('Governorate')); ?></label>
                                    <select name="rules[new][governorate_id]" class="form-select">
                                        <option value=""><?php echo e(__('Select Governorate')); ?></option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold"><?php echo e(__('City')); ?></label>
                                    <select name="rules[new][city_id]" class="form-select">
                                        <option value=""><?php echo e(__('Select City')); ?></option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold"><?php echo e(__('Price')); ?></label>
                                    <input type="number" name="rules[new][price]" class="form-control" 
                                           step="0.01" min="0" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold"><?php echo e(__('Estimated Days')); ?></label>
                                    <input type="number" name="rules[new][estimated_days]" class="form-control" 
                                           min="1" required>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="rules[new][active]" value="1" 
                                               id="new-rule-active" checked>
                                        <label class="form-check-label" for="new-rule-active"><?php echo e(__('Active')); ?></label>
                                    </div>
                                </div>
        </div>
            </div>
            </div>
        </div>

                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('admin.shipping-zones.index')); ?>" class="btn btn-outline-secondary"><?php echo e(__('Cancel')); ?></a>
                    <button type="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
                </div>
            </form>
    </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/shipping_zones/edit.blade.php ENDPATH**/ ?>