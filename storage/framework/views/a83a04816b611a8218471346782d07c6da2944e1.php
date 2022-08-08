<style>
    #accordion{

    }
    .header{
        padding:25px;
    }

    .header img{
        left:left;
        width:350px;
        height:auto;
    }

    .header span{
        float:right;
        font-weight:bold;
        display:block;
        vertical-align: bottom;
        font-size:20px;
        position:relative;
        bottom:-55px;
    }

    table{
        width:100%;
    }

    table th{
        font-weight:bold;
        background:#f7f7f7;
        color:#000;
        font-size:14px;
        padding:10px;
    }

    table td{
        text-align:center;
        font-weight:normal;
        padding:10px;
    }
</style>

<div class="header">
    <img src="img/brand/1.png"/>
    <span>PECSF Donation Summary</span>
    <div class="clear"></div>
</div>
<hr>

<span><i>Please note that this is not a Tax Receipt</i></span>
<div id="accordion">

    <?php $__currentLoopData = $old_pledges_by_yearcd; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $pledges): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card">
            <div class="card-header" id="heading0<?php echo e($loop->index); ?>">
                <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse0<?php echo e($loop->index); ?>"
                    aria-expanded="<?php echo e($loop->index == 0 ? 'true' : 'false'); ?>" aria-controls="collapse">
                    <h1 class="">
                        <?php echo e($key); ?>

                    </h1>
                    <div class="flex-fill"></div>
                    <div class="expander">
                    </div>
                </h5>
            </div>

            <div id="collapse0<?php echo e($loop->index); ?>" class="collapse <?php echo e($loop->index == 0 ? 'show' : ''); ?>" aria-labelledby="heading0<?php echo e($loop->index); ?>" data-parent="#accordion">
                <div class="card-body">
                    <table class="table  rounded">
                        <tr class="bg-light">
                            <th>Donation Type</th>
                            <th>Benefitting Charity</th>
                            <th>Frequency</th>
                            <th>Amount</th>

                        </tr>
                        <?php $total = 0; ?>
                        <?php $__currentLoopData = $pledges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pledge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="">
                                <td><?php echo e($pledge->donation_type); ?></td>
                                <?php if($pledge->type == 'P'): ?>
                                    <td><?php echo e($pledge->fund_supported_pool->region->name ?? ''); ?>

                                    </td>
                                <?php else: ?>
                                    <td><?php echo e(''); ?> </td>
                                <?php endif; ?>
                                <td><?php echo e($pledge->frequency); ?> </td>
                                <td class="text-right">$ <?php echo e($pledge->frequency == 'Bi-Weekly' ?
                                        number_format($pledge->pay_period_amount * $pledge->campaign_year->number_of_periods,2) :
                                        number_format($pledge->one_time_amount,2)); ?> </td>

                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </table>
                </div>
            </div>
        </div>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php $__currentLoopData = $old_bi_pledges_by_yearcd; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $pledges): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <div class="card">
            <div class="card-header" id="heading1<?php echo e($loop->index); ?>">
                <h5 class="mb-0 align-items-center d-flex" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse1<?php echo e($loop->index); ?>"
                    aria-expanded="<?php echo e((count($old_pledges_by_yearcd) == 0 and $loop->index == 0) ? 'true' : 'false'); ?>" aria-controls="collapse">
                    <button class="btn btn-link font-weight-bold">
                        <?php echo e($key); ?>

                    </button>
                    <div class="flex-fill"></div>
                    <div class="expander">

                    </div>
                </h5>
            </div>

            <div id="collapse1<?php echo e($loop->index); ?>" class="collapse <?php echo e((count($old_pledges_by_yearcd) == 0 and $loop->index == 0) ? 'show' : ''); ?>" aria-labelledby="heading1<?php echo e($loop->index); ?>" data-parent="#accordion">
                <div class="card-body">
                    <table class="table  rounded">
                        <tr class="bg-light">
                            <th>Donation Type</th>
                            <th>Benefitting Charity</th>
                            <th>Frequency</th>
                            
                            <th>Amount</th>

                        </tr>
                        <?php $total = 0; ?>
                        <?php $__currentLoopData = $pledges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pledge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="">
                                <td><?php echo e($pledge->campaign_type); ?></td>
                                
                                <td >
                                    <?php if($pledge->source == 'Pool'): ?>
                                        <div><?php echo e($pledge->name1 ?? ''); ?></div>
                                        <div><?php echo e($pledge->name2 ?? ''); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($pledge->frequency); ?> </td>
                                <td class="text-right">$<?php echo e($pledge->frequency == 'Bi-Weekly' ?
                                    number_format($pledge->pledge,2) :
                                    number_format($pledge->pledge,2)); ?>

                                </td>

                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <tr>
                            
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
</div>
<?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/donations/partials/pdf.blade.php ENDPATH**/ ?>