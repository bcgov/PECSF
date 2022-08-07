<?php $__currentLoopData = $organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $organization): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr >
        <td style="width:80%;"><b><?php echo e($organization->charity_name); ?></b><br><?php echo e($organization::CATEGORY_LIST[$organization->category_code]); ?> | <?php echo e($organization->city); ?> | <?php echo e($organization->province); ?> | <?php echo e($organization->country); ?></td>
        <td class="blue" style="width:9%"><b><u>View Details</u></b></td>
        <td style="width:5%"><div style="width:100px;" class="select btn btn-outline-primary" name="<?php echo e($organization->charity_name); ?>" org_id="<?php echo e($organization->id); ?>">Select</div></td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/volunteering/partials/organizations.blade.php ENDPATH**/ ?>