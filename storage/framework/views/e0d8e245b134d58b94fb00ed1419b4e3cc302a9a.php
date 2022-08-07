<li <?php if(isset($item['id'])): ?> id="<?php echo e($item['id']); ?>" <?php endif; ?> class="nav-item">

    <a class="nav-link py-3 <?php echo e($item['class']); ?> <?php if(isset($item['shift'])): ?> <?php echo e($item['shift']); ?> <?php endif; ?>"
       href="<?php echo e($item['href']); ?>" <?php if(isset($item['target'])): ?> target="<?php echo e($item['target']); ?>" <?php endif; ?>
       <?php echo $item['data-compiled'] ?? ''; ?>>

       <?php if( str_contains($item['icon'], ' far ') || str_contains($item['icon'], ' fa ') ): ?>
            <i class="<?php echo e($item['icon'] ?? 'far fa-fw fa-circle'); ?> <?php echo e(isset($item['icon_color']) ? 'text-'.$item['icon_color'] : ''); ?>"></i> 
        <?php else: ?>
            <i> 
                <?php if(str_contains($item['icon'], 'FAQs')): ?>
                    <svg class="icon mr-2" style="width:24px; height: 24px; top: 0;">
                        <use xlink:href="<?php echo e(asset('img/icons/faqs.svg')); ?>#sprite-faqs"></use>
                    </svg>
                <?php else: ?>
                    <svg class="icon mr-2" style="width:24px; height: 24px">
                        <use xlink:href="<?php echo e(asset('img/icons/sprite.svg')); ?>#sprite-<?php echo e($item['icon'] ?? 'home'); ?>"></use>
                    </svg>
                <?php endif; ?> 
            </i>
        <?php endif; ?>
        <?php echo e($item['text']); ?>


            <?php if(isset($item['label'])): ?>
                <span class="badge badge-<?php echo e($item['label_color'] ?? 'primary'); ?> right">
                    <?php echo e($item['label']); ?>

                </span>
            <?php endif; ?>
        </p>

    </a>

</li><?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/vendor/adminlte/partials/sidebar/menu-item-link.blade.php ENDPATH**/ ?>