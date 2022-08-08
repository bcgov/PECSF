<?php $__env->startSection('content_header'); ?>

    
    <div class="d-flex justify-content-center pt-3">
        <div class="card border-warning text-center" style="background:#D9EAF7;max-width: 50em; border-radius: 1em;">
            <div class="card-body" style="color:#1a5a96;">
                <h5 class="card-title"></h5>
                <?php if( $campaignYear->isOpen() ): ?>
                    <p class="card-text text-left">
                        From <?php echo e($campaignYear->start_date->format('F jS')); ?> - <?php echo e($campaignYear->end_date->format('F jS')); ?> we are in a period of open enrolment for the PECSF Campaign.
                        The choices you make and save by end of day <?php echo e($campaignYear->end_date->format('F jS')); ?> will begin with your first pay period in January.
                    </p>
                    <?php if($pledge): ?>
                        <p class="card-text text-left">
                            To make changes to your proposed pledge, click into the box below where your 2023 choices are shown.
                        </p>
                        <a href="<?php echo e(route('donate')); ?>" class="btn btn-primary">Make change to your PECSF pledge</a>
                    <?php else: ?>
                        <a href="<?php echo e(route('donate')); ?>" class="btn btn-primary">Donate to Annual Campaign Now</a>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Thank you for choosing to support PECSF!</p>
                    <p class="card-text text-left">
                        
                        If you need to change or stop your PECSF campaign payroll pledge deduction, please email <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a>.
                        
                    </p>
                    <p>
                        To make a new one-time donation outside of campaign, click “Donate to PECSF Now” below.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="d-flex mt-3">
        <h1>My Donations</h1>
        <?php if($pledges->count() > 0): ?>
            <div class="flex-fill"></div>
            <?php if(!$campaignYear->isOpen() ): ?>
                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.button','data' => ['href' => route('donate')]]); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('donate'))]); ?>Donate to PECSF Now <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
            <?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.button','data' => ['style' => 'outline-primary','class' => 'ml-2','dataToggle' => 'modal','dataTarget' => '#learn-more-modal']]); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['style' => 'outline-primary','class' => 'ml-2','data-toggle' => 'modal','data-target' => '#learn-more-modal']); ?>Why donate to PECSF? <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="d-flex flex-column">
        <p class="m-0">
            Since you started giving* through PECSF, you've donated $<?php echo e(number_format($totalPledgedDataTillNow,0)); ?>, as BC Public Servant.
        </p>
        <small>reflects pledge totals from 2005 onwards</small>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-center justify-content-lg-start mb-2" role="tablist">
            <div class="px-4 py-1 mr-2 border-bottom border-primary">
                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.button','data' => ['role' => 'tab','href' => '#','style' => '']]); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['role' => 'tab','href' => '#','style' => '']); ?>
                    Donation History
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
            </div>
        </div>
        
        <?php if($old_pledges_by_yearcd->count() > 0 or $old_bi_pledges_by_yearcd->count() > 0 ): ?>
            <?php echo $__env->make('donations.partials.history', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php else: ?>
        <div class="text-center text-primary">
            <p>
                <strong>No Campaign has been started yet.</strong>
            </p>
            <p>
                You do not have any active campaigns right now. <br>
                Click on one of the options below to get started!
            </p>
            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.button','data' => ['href' => route('donate')]]); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('donate'))]); ?>Donate to PECSF Now <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
            <p class="pt-3">
                OR
            </p>
            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.button','data' => ['style' => 'link','dataToggle' => 'modal','dataTarget' => '#learn-more-modal']]); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['style' => 'link','data-toggle' => 'modal','data-target' => '#learn-more-modal']); ?>Learn more about donating to PECSF. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="justify-content-center">
            <a href="<?php echo e(route('donations.list')); ?>?download_pdf=true"><button style="background:#fff;margin-left:auto;margin-right:auto;display:block;width:40%;border:#12406b 1px solid;padding:8px;text-align:center;">Export Summary</button></a>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="pledgeDetailModal" tabindex="-1" role="dialog" aria-labelledby="pledgeDetailModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header bg-light">
            <h5 class="modal-title text-dark" id="pledgeDetailModalTitle">Pledge Detail
                    <span class="text-dark font-weight-bold"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>

<?php echo $__env->make('donations.partials.learn-more-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php $__env->startPush('js'); ?>
<script>
    $('#learn-more-modal').on('slide.bs.carousel', function (e) {
        if(e.to == 0) {
            $(this).find(".prev-btn").addClass("d-none");
        }
        else if (e.to === 5) {
            $(this).find(".next-btn").addClass("d-none");
            $(this).find(".ready-btn").removeClass("d-none");
        } else {
            $(this).find(".prev-btn").removeClass("d-none");
            $(this).find(".next-btn").removeClass("d-none")
            $(this).find(".ready-btn").addClass("d-none");
        }
    })

    $('.more-info').click( function(event) {
        event.stopPropagation();
        // var current_id = event.target.id;
        yearcd = $(this).data('yearcd');
        frequency = $(this).data('frequency');
        source = $(this).data('source');
        type = $(this).data('type');
        id  = $(this).data('id');

        target = '.modal-body';
        $(target).html('');

        console.log( 'more info - ' );
        if ( yearcd  ) {
            // Lanuch Modal page for listing the Pool detail
            $.ajax({
                url: '/donations/old-pledge-detail',
                type: 'GET',
                data: 'yearcd='+ yearcd + '&frequency='+ frequency +'&source='+ source + '&id='+id+ '&type='+type   ,
                dataType: 'html',
                success: function (result) {
                    // $('.modal-title span').html(name);
                    $(target).html(result);
                },
                complete: function() {
                },
                error: function () {
                    alert("error");
                    $(target).html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
                }
            })

            $('#pledgeDetailModal').modal('show')
        }
    });


</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/donations/index.blade.php ENDPATH**/ ?>