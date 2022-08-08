<?php $attributes = $attributes->exceptProps(['text' => '', 'percent' => 50, 'color' =>'primary']); ?>
<?php foreach (array_filter((['text' => '', 'percent' => 50, 'color' =>'primary']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="d-flex align-items-center my-2">    
    <div class="bg-gray" style="height: 40px; width:calc(100% - 70px); border-top-right-radius: 20px;border-bottom-right-radius: 20px">
        <div class="d-flex align-items-center bg-<?php echo e($color); ?>" style="width: <?php echo e($percent); ?>%; height: 40px; border-top-right-radius: 20px;border-bottom-right-radius: 20px">
            <span class="pl-3">
                <?php echo e($text); ?>

            </span>
        </div>
    </div>
    <div class="d-flex flex-fill justify-content-center">
        <strong><?php echo e($percent); ?>%</strong>
    </div>
</div><?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/components/progress-bar.blade.php ENDPATH**/ ?>