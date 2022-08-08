<?php $attributes = $attributes->exceptProps(['style' => 'primary', 'icon' => '', 'size' => 'md']); ?>
<?php foreach (array_filter((['style' => 'primary', 'icon' => '', 'size' => 'md']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<<?php echo e(($attributes['href'] ?? '' ? 'a' : 'button')); ?> 
    <?php echo e($attributes->merge(['class' => 'btn btn-'.$style. ' btn-'.$size])); ?>

    >
    <?php if($icon): ?>
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.fa-icon','data' => ['icon' => $icon ?? '']]); ?>
<?php $component->withName('fa-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon ?? '')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>&nbsp;
    <?php endif; ?>
    <?php echo e($slot); ?>

</<?php echo e(($attributes['href'] ?? '' ? 'a' : 'button')); ?>>
<?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/components/button.blade.php ENDPATH**/ ?>