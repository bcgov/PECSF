<?php $__env->startSection('content'); ?>
<div class="container mt-1">
    <div class="row">
        <div class="col-9 col-sm-9">
            <h1>Make a Donation</h1>
            <p class="text-muted">When you give through PECSF 100% of your donated dollars goes to the organizations you choose to support.</p>
        </div>
        <div class="col-3 col-sm-3">
            <img src="<?php echo e(asset('img/donor.png')); ?>" alt="Group of volunteers making wreaths at a table" class="py-5 img-fluid">
        </div>
        <?php echo $__env->yieldContent("step-content"); ?>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/donate/layout/main.blade.php ENDPATH**/ ?>