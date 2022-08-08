
<div class="form-group org_hook col-md-3">
            <label for="keyword">Search by Keyword</label>
            <input class="form-control" type="text" name="keyword" value="" id="keyword" />
        </div>
        <div class="form-group org_hook col-md-3">
            <label for="category">Search by Category</label>
            <select class="form-control" type="text" name="category" id="category">
                <option value="">Choose a Category</option>

<?php $__currentLoopData = \App\Models\Charity::CATEGORY_LIST; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    </div>
    <div class="form-group org_hook col-md-3">
        <label for="category">Search by Province</label>
        <select class="form-control" type="text" name="province" id="charity_province">
            <option value="">Choose a Province</option>
            <?php $__currentLoopData = \App\Models\Charity::PROVINCE_LIST; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="charity-container form-group org_hook  col-md-9">
        <h4 class="blue">Search Results</h4>
        <?php if($organizations): ?>
            <h5 style="width:100%;text-align:left;"><?php echo e($organizations->total()); ?> results</h5>
        <?php else: ?>
            <h5 style="width:100%;text-align:center" class="align-content-center">No results</h5>
        <?php endif; ?>

        <table id="charities">
            <?php echo $__env->make("volunteering.partials.organizations", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </table>
        <div>
            <?php if($organizations): ?>
                <?php echo e($organizations->links()); ?>

            <?php else: ?>

            <?php endif; ?>

        </div>
    </div>
    <div class="col-md-3"></div>
        <br>
        <br>

<div class="charity-container form-group org_hook  col-md-9">

        <h4 class="blue">Your Charities</h4>

        <table class="charity-container" id="organizations" style="display:none;width:100%">
            <h5 style="width:100%;text-align:center" class="align-content-center">You have not chosen any charities</h5>
        </table>
</div>
<?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/donate/partials/choose-charity.blade.php ENDPATH**/ ?>