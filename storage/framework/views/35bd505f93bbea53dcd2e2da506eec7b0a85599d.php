<?php $layoutHelper = app('JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper'); ?>

<?php ( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') ); ?>

<?php if(config('adminlte.use_route_url', false)): ?>
    <?php ( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' ); ?>
<?php else: ?>
    <?php ( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' ); ?>
<?php endif; ?>

<a href="<?php echo e($dashboard_url); ?>"
    <?php if($layoutHelper->isLayoutTopnavEnabled()): ?>
        class="navbar-brand <?php echo e(config('adminlte.classes_brand')); ?>"
    <?php else: ?>
        class="brand-link <?php echo e(config('adminlte.classes_brand')); ?>"
    <?php endif; ?>>

    
    <img src="<?php echo e(asset(config('adminlte.logo_img', 'vendor/adminlte/dist/img/AdminLTELogo.png'))); ?>"
         alt="<?php echo e(config('adminlte.logo_img_alt', 'AdminLTE')); ?>"
         class="<?php echo e(config('adminlte.logo_img_class', 'brand-image img-circle elevation-3')); ?>"
         style="opacity:.8">

    
    
</a>
<?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/vendor/adminlte/partials/common/brand-logo-xs.blade.php ENDPATH**/ ?>