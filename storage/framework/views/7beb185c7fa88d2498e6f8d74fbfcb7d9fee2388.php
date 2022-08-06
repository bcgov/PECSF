<ul class="nav nav-pills mb-3" id="pills-tab" >
    <li class="nav-item">
        <a class="nav-link <?php echo e(str_contains( Route::current()->getName(), 'bank_deposit_form') ? 'active' : ''); ?>"
           
           href="<?php echo e(route('bank_deposit_form')); ?>" role="tab" aria-controls="pills-home" aria-selected="true">PECSF Event Bank Deposit Form</a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo e(str_contains( Route::current()->getName(), 'volunteering.supply_order_form') ? 'active' : ''); ?>"
           
           href="<?php echo e(route('settings.fund-supported-pools.index')); ?>"  aria-controls="pills-profile" aria-selected="false">Supply Order Form</a>
    </li>


</ul>
<?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/volunteering/partials/form_tabs.blade.php ENDPATH**/ ?>