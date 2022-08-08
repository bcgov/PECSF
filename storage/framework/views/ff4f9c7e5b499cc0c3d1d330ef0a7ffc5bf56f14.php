
<div class="pb-1">
    <small ><?php echo e(number_format($charities->total())); ?> results</small>
</div>

<?php if( $charities->total() ): ?>

    <?php $__currentLoopData = $charities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $charity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="d-flex list border-bottom">
            <div class="p-0">
                <i class="fas fa-hand-holding-heart text-info" style="visibility:hidden;"></i>
            </div>
            <div class="pl-2">
                <div class="d-flex flex-column ml-0">
                    <span>
                        <a href="#" class="charity-modal text-dark font-weight-bold" style="coxxxlor:#353535c4"
                            value="<?php echo e($charity->id); ?>">
                            <?php
                                $text = $charity->capitalized_name();
                                foreach ($terms as $term) {
                                    $text = $term ? preg_replace('#' . preg_quote($term) . '#i', '<span class="text-danger">\\0</span>', $text) : $text;
                                }
                            ?>
                            <?php echo $text; ?>

                        </a>

                    </span>
                    <small class="text-secondary">
                        
                        <?php echo e($charity->category_name); ?> |
                        <?php echo e($charity->city); ?> |
                        <?php echo e($charity->province); ?> |
                        <?php echo e($charity->country); ?>

                    </small>
                </div>
            </div>
          <div class="ml-auto p-2">
              <a class="charity-select-add" href="#" value="<?php echo e($charity->id); ?>"
                  value-text="<?php echo e($charity->charity_name); ?>" vendor_id="<?php echo e($charity->registration_number); ?>">
                  <i class="fas fa-plus-circle fa-lg text-danger"></i>
              </a>
          </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div id="pagination" class="my-3 d-flex justify-content-center">
        <small>
            <?php echo e($charities->onEachSide(1)->links('pagination::bootstrap-4')); ?>

        </small>
    </div>

<?php endif; ?>
<?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/donate/partials/charity-pagination.blade.php ENDPATH**/ ?>