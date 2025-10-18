<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title','actions'=>null,'subtitle'=>null]));

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

foreach (array_filter((['title','actions'=>null,'subtitle'=>null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div class="modern-page-header d-flex flex-wrap gap-3 justify-content-between align-items-center mb-4">
  <div>
    <h1 class="page-title mb-1"><?php echo e($title); ?></h1>
    <?php if($subtitle): ?><p class="text-muted mb-0 small"><?php echo e($subtitle); ?></p><?php endif; ?>
  </div>
  <?php if($actions): ?>
  <div class="page-actions d-flex flex-wrap gap-2"><?php echo $actions; ?></div>
  <?php endif; ?>
</div>
<?php /**PATH D:\xampp1\htdocs\easy\resources\views/admin/partials/page-header.blade.php ENDPATH**/ ?>