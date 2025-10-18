<?php $__env->startSection('title', __('Create Shipping Zone')); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.partials.page-header', ['title'=>__('Create Shipping Zone')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="card modern-card">
    <div class="card-header d-flex align-items-center gap-2">
        <h3 class="card-title mb-0"><?php echo e(__('Create Shipping Zone')); ?></h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('admin.shipping-zones.store')); ?>" class="admin-form" aria-label="create-shipping-zone">
            <?php echo csrf_field(); ?>
            <div class="mb-3"><label class="form-label fw-semibold"><?php echo e(__('Name')); ?></label><input name="name"
                    class="form-control" required placeholder="<?php echo e(__('EU Zone')); ?>"></div>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label fw-semibold"><?php echo e(__('Code (optional)')); ?></label><input
                        name="code" class="form-control" placeholder="EU-PRIMARY"></div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="active" value="1" checked id="zone-active">
                        <label class="form-check-label" for="zone-active"><?php echo e(__('Active')); ?></label>
                    </div>
                </div>
            </div>
            <h5 class="mt-4"><?php echo e(__('Rules')); ?></h5>
            <p class="text-muted small mb-2">
                <?php echo e(__('Leave governorate and city empty for a country-wide rule. City overrides governorate which overrides country.')); ?>

            </p>
            
            <div class="mb-3">
                <h6><?php echo e(__('Add Rule')); ?> <span class="text-muted small">(<?php echo e(__('Required')); ?>)</span></h6>
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <input type="hidden" name="filter_country" value="<?php echo e($filterCountry); ?>">
                            <input type="hidden" name="filter_governorate" value="<?php echo e($filterGovernorate); ?>">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold"><?php echo e(__('Country')); ?></label>
                                <select name="rules[0][country_id]" class="form-select" required onchange="this.form.submit()">
                                    <option value=""><?php echo e(__('Select Country')); ?></option>
                                    <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($country->id); ?>" <?php echo e($filterCountry == $country->id ? 'selected' : ''); ?>><?php echo e($country->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold"><?php echo e(__('Governorate')); ?></label>
                                <select name="rules[0][governorate_id]" class="form-select" onchange="this.form.submit()">
                                    <option value=""><?php echo e(__('Select Governorate')); ?></option>
                                    <?php if($filterCountry): ?>
                                        <?php $__currentLoopData = \App\Models\Governorate::where('country_id', $filterCountry)->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $governorate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($governorate->id); ?>" <?php echo e($filterGovernorate == $governorate->id ? 'selected' : ''); ?>><?php echo e($governorate->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <option value="" disabled><?php echo e(__('Please select a country first')); ?></option>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted"><?php echo e(__('Select governorate for the chosen country')); ?></small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold"><?php echo e(__('City')); ?></label>
                                <select name="rules[0][city_id]" class="form-select">
                                    <option value=""><?php echo e(__('Select City')); ?></option>
                                    <?php if($filterGovernorate): ?>
                                        <?php $__currentLoopData = \App\Models\City::where('governorate_id', $filterGovernorate)->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($city->id); ?>"><?php echo e($city->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <option value="" disabled><?php echo e(__('Please select a governorate first')); ?></option>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted"><?php echo e(__('Select city for the chosen governorate')); ?></small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold"><?php echo e(__('Price')); ?></label>
                                <input type="number" name="rules[0][price]" class="form-control" 
                                       step="0.01" min="0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold"><?php echo e(__('Estimated Days')); ?></label>
                                <input type="number" name="rules[0][estimated_days]" class="form-control" 
                                       min="1" required>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="rules[0][active]" value="1" 
                                           id="rule-active-0" checked>
                                    <label class="form-check-label" for="rule-active-0"><?php echo e(__('Active')); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/shipping_zones/create.blade.php ENDPATH**/ ?>