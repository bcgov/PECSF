<?php $__env->startSection("step-content"); ?>
    <div style="position:relative;top:-400px;">
<h2 class="mt-5">2. Choose your charities (up to 10)</h2>
        <form action="<?php echo e(route('donate.save.select')); ?>" method="post">
<div class=" form-row">
    <?php echo $__env->make('donate.partials.choose-charity', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo csrf_field(); ?>
</div>
            <div class="mt-2">
                <button class="btn btn-lg btn-outline-primary">Cancel</button>
                <button class="btn btn-lg btn-primary" disabled type="submit">Next</button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('css'); ?>

<style>
    #selected-charity-list {
        min-height: 200px;
    }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('js'); ?>
    <?php echo $__env->make('donate.partials.choose-charity-js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script type="x-tmpl" id="organization-tmpl">
        <?php echo $__env->make('volunteering.partials.add-organization', ['index' => 'XXX'] , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </script>
    <script>
        $(".org_hook").show();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('donate.layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/donate/select.blade.php ENDPATH**/ ?>