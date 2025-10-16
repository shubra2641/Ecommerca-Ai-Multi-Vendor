<aside class="catalog-sidebar">
    <div class="sidebar-block">
        <h4><?php echo e(__('Price')); ?></h4>
        <div class="price-range">
            <div class="value-row">
                <span><?php echo e(__('Min')); ?>: <strong id="prMinVal"><?php echo e(request('min_price') ?: 0); ?></strong></span>
                <span><?php echo e(__('Max')); ?>: <strong id="prMaxVal"><?php echo e(request('max_price') ?: 1000); ?></strong></span>
            </div>
            <div class="range-wrapper">
                <input type="range" min="0" max="1000" step="10" value="<?php echo e(request('min_price') ?: 0); ?>" id="prMin">
                <input type="range" min="0" max="1000" step="10" value="<?php echo e(request('max_price') ?: 1000); ?>" id="prMax">
            </div>
            <input type="hidden" form="catalogFilters" name="min_price" id="prMinHidden" value="<?php echo e(request('min_price')); ?>">
            <input type="hidden" form="catalogFilters" name="max_price" id="prMaxHidden" value="<?php echo e(request('max_price')); ?>">
        </div>
    </div>
    <div class="sidebar-block">
        <h4><?php echo e(__('Category')); ?></h4>
        <nav class="category-list">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="cat-item">
                <a href="<?php echo e(route('products.category',$cat->slug)); ?>"><?php echo e($cat->name); ?></a>
                <?php if($cat->children->count()): ?>
                <div class="cat-children">
                    <?php $__currentLoopData = $cat->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('products.category',$child->slug)); ?>"><?php echo e($child->name); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>
    </div>
    <div class="sidebar-block">
        <h4><?php echo e(__('Brand')); ?></h4>
        <div class="brand-search"><input type="search" placeholder="<?php echo e(__('Search')); ?>" disabled></div>
        <div class="brand-list">
            <?php if(isset($brandList) && $brandList->count()): ?>
            <?php $__currentLoopData = $brandList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="brand-item">
                <input type="checkbox" form="catalogFilters" name="brand[]" value="<?php echo e($b->slug); ?>" <?php echo e(in_array($b->slug,$csSelectedBrands ?? [])?'checked':''); ?>>
                <span><?php echo e($b->name); ?></span>
                <span class="count"><?php echo e($b->products_count); ?></span>
            </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>
</aside>
<?php /**PATH D:\xampp\htdocs\easy\resources\views/front/products/partials/sidebar.blade.php ENDPATH**/ ?>