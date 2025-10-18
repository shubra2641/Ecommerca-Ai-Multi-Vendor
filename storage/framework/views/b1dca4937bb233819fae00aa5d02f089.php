<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
'icon' => 'circle-off', // not used as we inline svg, kept for future
'title' => null,
'message' => null,
'actionLabel' => null,
'actionUrl' => null,
'secondaryLabel' => null,
'secondaryUrl' => null,
]));

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

foreach (array_filter(([
'icon' => 'circle-off', // not used as we inline svg, kept for future
'title' => null,
'message' => null,
'actionLabel' => null,
'actionUrl' => null,
'secondaryLabel' => null,
'secondaryUrl' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div class="empty-state unified-empty">
    <div class="empty-icon empty-icon-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
            stroke-linejoin="round" aria-hidden="true" width="40" height="40"
            class="empty-img">
            <circle cx="12" cy="12" r="9" stroke-opacity="0.45" />
            <path d="M9 10h6" />
            <path d="M10 14h4" stroke-opacity="0.6" />
        </svg>
    </div>
    <?php if($title): ?><h3 class="empty-title"><?php echo e($title); ?></h3><?php endif; ?>
    <?php if($message): ?><p class="empty-message"><?php echo e(e($message)); ?></p><?php endif; ?>
    <?php if($actionLabel && $actionUrl): ?>
    <div class="empty-actions">
        <a href="<?php echo e($actionUrl); ?>" class="btn btn-primary"><?php echo e($actionLabel); ?></a>
        <?php if($secondaryLabel && $secondaryUrl): ?>
        <a href="<?php echo e($secondaryUrl); ?>" class="btn btn-outline"><?php echo e($secondaryLabel); ?></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div><?php /**PATH D:\xampp1\htdocs\easy\resources\views/front/components/empty-state.blade.php ENDPATH**/ ?>