<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['items' => []]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['items' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<nav class="breadcrumb-nav" aria-label="<?php echo e(__('Breadcrumb navigation')); ?>">
    <ol class="breadcrumb">
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="breadcrumb-item <?php echo e($loop->last ? 'active' : ''); ?>" <?php echo e($loop->last ? 'aria-current="page"' : ''); ?>>
                <?php if($loop->last): ?>
                    <span><?php echo e($item['title']); ?></span>
                <?php else: ?>
                    <a href="<?php echo e($item['url']); ?>" class="breadcrumb-link">
                        <?php if(isset($item['icon'])): ?>
                            <i class="<?php echo e($item['icon']); ?>" aria-hidden="true"></i>
                        <?php endif; ?>
                        <span><?php echo e($item['title']); ?></span>
                    </a>
                <?php endif; ?>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ol>
</nav>
<?php /**PATH D:\xampp1\htdocs\easy\resources\views/components/breadcrumb.blade.php ENDPATH**/ ?>