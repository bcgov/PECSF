

<?php $__env->startSection('adminlte_css_pre'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css')); ?>">
    <style>
        .card.card-outline.card-primary {
            border: none;
        }
    </style>    
<?php $__env->stopSection(); ?>

<?php ( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') ); ?>
<?php ( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') ); ?>
<?php ( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') ); ?>

<?php if(config('adminlte.use_route_url', false)): ?>
    <?php ( $login_url = $login_url ? route($login_url) : '' ); ?>
    <?php ( $register_url = $register_url ? route($register_url) : '' ); ?>
    <?php ( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' ); ?>
<?php else: ?>
    <?php ( $login_url = $login_url ? url($login_url) : '' ); ?>
    <?php ( $register_url = $register_url ? url($register_url) : '' ); ?>
    <?php ( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' ); ?>
<?php endif; ?>



<?php $__env->startSection('auth_body'); ?>

    <?php ( $has_admin_error = ($errors->has('email') || $errors->has('password') )); ?>

    <?php if($message = Session::get('error-psft')): ?>
    <div class="alert alert-danger alert-dismissible">
        <a href="#" class="close" style="text-decoration: none;"  data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Login error!</strong> <?php echo e($message); ?>

    </div>
    <?php endif; ?>

    <div id="idir-login" style="<?php echo e($errors->has('email')  ? 'display:none;' : ''); ?>" >
        <div class="text-center py-3">
                <p class="h6 font-weight-bold">Log in to start your session<p>
                    <p class="my-4 ">
                        <form action="<?php echo e('/login/keycloak'); ?>" method="get">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-success">Login with Your BC Govt login ID </button>
                        </form>
                    </p>
        </div>
        <div class="py-2 border-top">
            <div class="pt-4 h6 font-weight-bold">Need Help?</div>
            <div class="">Contact your IDIR security administrator or the 7-7000 Service Desk at:</div>
            <div class="pt-2">Phone: <a href="tel:0612345678">250-387-7000</a></div>
            <div>Email: <a href="mailto:77000@gov.bc.ca" target="_blank" >77000@gov.bc.ca</a></div>

            
                <div class="py-4 small"><a class="sysadmin-login" href="">Log in as a System Administrator</a></div>
            

        </div>
    </div>    

    <div id="admin-login" style="<?php echo e(Session::get('error-psft') || $has_admin_error == false ? 'display:none;' : ''); ?>"">
        <div class="text-center py-3">
            <p class="h5 font-weight-bold">Log in to start your session<p>
        </div>

        <form action="<?php echo e($login_url); ?>" method="post">
            <?php echo csrf_field(); ?>

            
            <div class="input-group mb-3">
                <input type="email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(__('adminlte::adminlte.email')); ?>" autofocus>

                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope <?php echo e(config('adminlte.classes_auth_icon', '')); ?>"></span>
                    </div>
                </div>

                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="invalid-feedback" role="alert">
                        <strong><?php echo e($message); ?></strong>
                    </span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="input-group mb-3">
                <input type="password" name="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    placeholder="<?php echo e(__('adminlte::adminlte.password')); ?>">

                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock <?php echo e(config('adminlte.classes_auth_icon', '')); ?>"></span>
                    </div>
                </div>

                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="invalid-feedback" role="alert">
                        <strong><?php echo e($message); ?></strong>
                    </span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="row">
                <div class="col-7">
                    <div class="icheck-primary" title="<?php echo e(__('adminlte::adminlte.remember_me_hint')); ?>">
                        <input type="checkbox" name="remember" id="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>

                        <label for="remember">
                            <?php echo e(__('adminlte::adminlte.remember_me')); ?>

                        </label>
                    </div>
                </div>

                <div class="col-5">
                    <button type=submit class="btn btn-block <?php echo e(config('adminlte.classes_auth_btn', 'btn-flat btn-primary')); ?>">
                        <span class="fas fa-sign-in-alt"></span>
                        <?php echo e(__('adminlte::adminlte.sign_in')); ?>

                    </button>
                </div>
            </div>

        </form>

        <div class="py-3"><a class="idir-login" >Back</a></div>

    </div>    
<?php $__env->stopSection(); ?>

<?php $__env->startSection('auth_footer'); ?>

    
    

    
    

    
    
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
<script>
    $(function() {
        console.log( "ready!" );

        $(document).on("click",".sysadmin-login",function(event) {
            event.preventDefault();
            $('#idir-login').hide();
            $('#admin-login').show();
        });

        $(document).on("click",".idir-login",function(event) {
            event.preventDefault();
            $('#idir-login').show();
            $('#admin-login').hide();   
        });

    });
    
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminlte::auth.auth-page', ['auth_type' => 'login'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/vendor/adminlte/auth/login.blade.php ENDPATH**/ ?>