

<?php $__env->startSection("step-content"); ?>
<h3 class="mt-5">1. Select your preferred method for choosing charities</h3>
<div>
    <p class="p-1"></p>
    <div class="card mx-3 pl-3 bg-primary">
        <div class="card-body bg-light">
            If you select the CRA charity list option, you can support up to 10 different charities of your choice through your donation, if they are registered and in good standing with the Canada Revenue Agency (CRA).

            If you select the regional Fund Supported Pool option, charities and distribution amounts are pre-determined and cannot be adjusted, removed, or substituted. 

            Visit the PECSF webpages to learn more about the <a target="_blank" href="https://www2.gov.bc.ca/gov/content/careers-myhr/about-the-bc-public-service/corporate-social-responsibility/pecsf/charity" style="text-decoration: underline;">Fund Supported Pool</a> option.

        </div>
    </div>
    <p class="p-1"></p>
    <?php if($errors->any()): ?>
        <div class="alert alert-warning">
            <?php $__currentLoopData = array_unique($errors->all()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div><?php echo e($error); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('donate.save.pool-option')); ?>" method="post" class="px-4">
        <?php echo csrf_field(); ?>

        <div class="card btn btn-outline-primary text-left <?php echo e($pool_option == "C" ? 'active' : ''); ?>" id="card-pool1">
            <div class="card-body p-2 ">
                <div class="form-check ">
                    <input class="form-check-input" type="radio" name="pool_option" id="pool1" value="C"
                        <?php echo e($pool_option == "C" ? 'checked' : ''); ?>>
                    <label class="form-check-label h5" for="pool1">
                        Select up to 10 charities from the CRA List
                    </label>
                </div>
            </div>
        </div>

        <div class="card btn btn-outline-primary text-left <?php echo e($pool_option == "P" ? 'active' : ''); ?>" id="card-pool2">
            <div class="card-body p-2">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="pool_option" id="pool2" value="P"
                        <?php echo e($pool_option == "P" ? 'checked' : ''); ?>>
                    <label class="form-check-label h5" for="pool2">
                        Select a Regional Fund Supported Pool
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <button  name="cancel" value='cancel' class="btn btn-lg btn-outline-primary">Cancel</button>
            <button class="btn btn-lg btn-primary" type="submit">Next</button>
        </div>

    </form>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('js'); ?>

<script>
    $( function() {
        $('.card').click( function(event) {
            // var current_id = event.target.id;
            var option = this.id;

            if (option == 'card-pool1') {
                $('#card-pool1').addClass('active');
                $('#card-pool2').removeClass('active');
                $('#pool1').prop('checked',true);
            } else {
                $('#card-pool1').removeClass('active');
                $('#card-pool2').addClass('active');
                $('#pool2').prop('checked',true);
            }
            // ...do something...
            event.stopPropagation();
        });
    });
</script>

<?php $__env->stopPush(); ?>


<?php echo $__env->make('donate.layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/donate/start.blade.php ENDPATH**/ ?>