<aside class="main-sidebar <?php echo e(config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4')); ?>">

    
    <?php if(config('adminlte.logo_img_xl')): ?>
        <?php echo $__env->make('adminlte::partials.common.brand-logo-xl', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <?php echo $__env->make('adminlte::partials.common.brand-logo-xs', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    
    <div class="sidebar">
    
        <div class="image text-center">
          <img src="<?php echo e(asset('img/profile-pic.png')); ?>" style="max-width:90px; max-height:90px" class="img-circle elevation-2 mt-4">
        </div>
        <div class="info text-center mt-4 mb-5">
        <h5 class="text-light">Welcome back,</h5>
          <h4  style="color:#4C81AF !important;"><?php echo e(Auth::user()->name); ?></h4>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column <?php echo e(config('adminlte.classes_sidebar_nav', '')); ?>"
                data-widget="treeview" role="menu"
                <?php if(config('adminlte.sidebar_nav_animation_speed') != 300): ?>
                    data-animation-speed="<?php echo e(config('adminlte.sidebar_nav_animation_speed')); ?>"
                <?php endif; ?>
                <?php if(!config('adminlte.sidebar_nav_accordion')): ?>
                    data-accordion="false"
                <?php endif; ?>>
                
                <?php echo $__env->renderEach('adminlte::partials.sidebar.menu-item', $adminlte->menu('sidebar'), 'item'); ?>
            </ul>
        </nav>
    </div>

</aside><?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/vendor/adminlte/partials/sidebar/left-sidebar.blade.php ENDPATH**/ ?>