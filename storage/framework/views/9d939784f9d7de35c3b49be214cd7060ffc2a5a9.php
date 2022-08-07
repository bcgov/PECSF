<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>

    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    
    <?php echo $__env->yieldContent('meta_tags'); ?>

    
    <title>
        <?php echo $__env->yieldContent('title_prefix', config('adminlte.title_prefix', '')); ?>
        <?php echo $__env->yieldContent('title', config('adminlte.title', 'AdminLTE 3')); ?>
        <?php echo $__env->yieldContent('title_postfix', config('adminlte.title_postfix', '')); ?>
    </title>

    
    <?php echo $__env->yieldContent('adminlte_css_pre'); ?>

    
    <?php if(!config('adminlte.enabled_laravel_mix')): ?>
        <link rel="stylesheet" href="<?php echo e(asset('vendor/fontawesome-free/css/all.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css')); ?>">

        
        <?php echo $__env->make('adminlte::plugins', ['type' => 'css'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <link rel="stylesheet" href="<?php echo e(asset('vendor/adminlte/dist/css/adminlte.min.css')); ?>">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <?php else: ?>
        <link rel="stylesheet" href="<?php echo e(mix(config('adminlte.laravel_mix_css_path', 'css/app.css'))); ?>">
    <?php endif; ?>

    
    <?php if(config('adminlte.livewire')): ?>
        <?php if(app()->version() >= 7): ?>
            @livewireStyles
        <?php else: ?>
            <livewire:styles />
        <?php endif; ?>
    <?php endif; ?>

    
    <?php echo $__env->yieldContent('adminlte_css'); ?>

    
    <?php if(config('adminlte.use_ico_only')): ?>
        <link rel="shortcut icon" href="<?php echo e(asset('favicons/favicon.ico')); ?>" />
    <?php elseif(config('adminlte.use_full_favicon')): ?>
        <link rel="shortcut icon" href="<?php echo e(asset('favicons/favicon.ico')); ?>" />
        <link rel="apple-touch-icon" sizes="57x57" href="<?php echo e(asset('favicons/apple-icon-57x57.png')); ?>">
        <link rel="apple-touch-icon" sizes="60x60" href="<?php echo e(asset('favicons/apple-icon-60x60.png')); ?>">
        <link rel="apple-touch-icon" sizes="72x72" href="<?php echo e(asset('favicons/apple-icon-72x72.png')); ?>">
        <link rel="apple-touch-icon" sizes="76x76" href="<?php echo e(asset('favicons/apple-icon-76x76.png')); ?>">
        <link rel="apple-touch-icon" sizes="114x114" href="<?php echo e(asset('favicons/apple-icon-114x114.png')); ?>">
        <link rel="apple-touch-icon" sizes="120x120" href="<?php echo e(asset('favicons/apple-icon-120x120.png')); ?>">
        <link rel="apple-touch-icon" sizes="144x144" href="<?php echo e(asset('favicons/apple-icon-144x144.png')); ?>">
        <link rel="apple-touch-icon" sizes="152x152" href="<?php echo e(asset('favicons/apple-icon-152x152.png')); ?>">
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('favicons/apple-icon-180x180.png')); ?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('favicons/favicon-16x16.png')); ?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('favicons/favicon-32x32.png')); ?>">
        <link rel="icon" type="image/png" sizes="96x96" href="<?php echo e(asset('favicons/favicon-96x96.png')); ?>">
        <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo e(asset('favicons/android-icon-192x192.png')); ?>">
        <link rel="manifest" href="<?php echo e(asset('favicons/manifest.json')); ?>">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="<?php echo e(asset('favicon/ms-icon-144x144.png')); ?>">
    <?php endif; ?>

</head>

<body class="<?php echo $__env->yieldContent('classes_body'); ?>" <?php echo $__env->yieldContent('body_data'); ?>>

    
    <?php echo $__env->yieldContent('body'); ?>

    
    <?php if(!config('adminlte.enabled_laravel_mix')): ?>
        <script src="<?php echo e(asset('vendor/jquery/jquery.min.js')); ?>"></script>
        <script src="<?php echo e(asset('vendor/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
        <script src="<?php echo e(asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js')); ?>"></script>

        
        <?php echo $__env->make('adminlte::plugins', ['type' => 'js'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <script src="<?php echo e(asset('vendor/adminlte/dist/js/adminlte.min.js')); ?>"></script>
    <?php else: ?>
        <script src="<?php echo e(mix(config('adminlte.laravel_mix_js_path', 'js/app.js'))); ?>"></script>
    <?php endif; ?>

    
    <?php if(config('adminlte.livewire')): ?>
        <?php if(app()->version() >= 7): ?>
            @livewireScripts
        <?php else: ?>
            <livewire:scripts />
        <?php endif; ?>
    <?php endif; ?>
    
    <?php if(!request()->is('login') && !request()->is('register') && !request()->is('password/*')): ?>
    <div class="d-flex align-items-center" style="background:#fff;padding:50px;">
        <img src="<?php echo e(asset('img/brand/1.png')); ?>" alt="" class="p-3" style="height:140px;">
        <img src="<?php echo e(asset('img/brand/2.png')); ?>" alt="" class="p-3" style="height:140px;">
        <img src="<?php echo e(asset('img/brand/3.png')); ?>" alt="" class="p-3" style="height:140px;">
        <div class="flex-fill"></div>
        <strong class="float-right p-3 h5">Charity Registration no.: 889407466 RR0001</strong>
    </div>
    <?php endif; ?>
    
    <?php echo $__env->yieldContent('adminlte_js'); ?>

</body>

</html>
<?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/vendor/adminlte/master.blade.php ENDPATH**/ ?>