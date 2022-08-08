
<?php $__env->startSection('content_header'); ?>
    <div class="d-flex mt-3">
        <h1>PECSF Volunteering</h1>
        <div class="flex-fill"></div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php if($user->isVolunteer()): ?>
    <?php echo $__env->make('volunteering.partials.statistics', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php else: ?>
    <?php echo $__env->make('volunteering.partials.no-statistics', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php echo $__env->make('volunteering.partials.overall-graph', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="card">
    <div class="card-body">
        <?php echo $__env->make('volunteering.partials.tabs', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php if($user->isVolunteer()): ?>
            <div class="card">
                <div class="card-body">
                    <strong>
                        Event Coordinator Training Session 1
                    </strong>
                    <div>
                        📅 Monday, May 2nd 2022 | 
                        ⏰ 10.00 am 12.30 pm PST | 
                        📍 <a href="#">https://teams.microsoft.com/dl/lancher/launcherread</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <strong>
                        Event Coordinator Training Session 2
                    </strong>
                    <div>
                        📅 Monday, June 2nd 2022 | 
                        ⏰ 10.00 am 12.30 pm PST | 
                        📍 <a href="#">https://teams.microsoft.com/dl/lancher/launcherread</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
        <div class="text-center text-primary">
            <p class="mt-5">
                <strong>No Events to Display</strong>
            </p>
            <p>
                You do not have any active campaigns right now. <br>
                Click on one of the options below to get started!
            </p>
            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.button','data' => ['dataToggle' => 'modal','dataTarget' => '#volunteer-registration']]); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['data-toggle' => 'modal','data-target' => '#volunteer-registration']); ?>Register as a Volunteer <?php echo $__env->renderComponent(); ?>
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
<?php $component->withAttributes(['style' => 'link','data-toggle' => 'modal','data-target' => '#learn-more-modal']); ?>Learn more about volunteering with PECSF. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php echo $__env->make('volunteering.partials.learn-more-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('volunteering.partials.registration-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->startPush('js'); ?>
<script>
    $('#learn-more-modal').on('slide.bs.carousel', function (e) {
        if(e.to == 0) {
            $(this).find(".prev-btn").addClass("d-none");
        }
        else if (e.to === 6) {
            $(this).find(".next-btn").addClass("d-none");
            $(this).find(".ready-btn").removeClass("d-none");
        } else {
            $(this).find(".prev-btn").removeClass("d-none");
            $(this).find(".next-btn").removeClass("d-none")
            $(this).find(".ready-btn").addClass("d-none");
        }
    });
    $("#volunteer-registration").on('click', '[name=no_of_years_opt_out]', function (e) {
        $("#volunteer-registration").find("[name=no_of_years]").prop('required', !this.checked);
    });
    $("#volunteer-registration").on('click', '[name=address_type]', function (e) {
        const isRequired = $("#volunteer-registration").find("[type=radio][name=address_type]:checked").val() === 'new';
            $("#volunteer-registration").find("[name=new_address]").prop('required', isRequired);
        
    });
    $('#volunteer-registration').on('slide.bs.carousel', function (e) {
        const activeStep = Number.parseInt($(e.relatedTarget).data('step')) - 1;
        const requiredQuestion = $("#volunteer-registration-carousel").find('.carousel-item.active').find("[required]");
        if (requiredQuestion && requiredQuestion.length && requiredQuestion.val() === '') {
            alert("Please fill the mandatory fields to proceed");
            return false;
        }
        $(".formsteps .step").each((index, e) => {
            $(e).removeClass("active").removeClass("done");
            if (activeStep === index) {
                $(e).addClass("active");
            } else if (index < activeStep) {
                $(e).addClass("done");
            }
        });
        $(".formsteps .divider").each((index, e) => {
            $(e).removeClass("done");
            if (index < activeStep) {
                $(e).addClass("done");
            }
        });
        if(e.to == 0) {
            $(this).find(".prev-btn").addClass("d-none");
        } else if (e.to == 5) {
            $(this).find(".prev-btn").removeClass("d-none");
            $(this).find(".finish-btn").removeClass("d-none");
            $(this).find(".signup-btn").addClass("d-none");
            $(this).find(".next-btn").addClass("d-none");
        }  else if (e.to == 6) {
            $(this).find(".finish-btn").addClass("d-none");
            $(this).find(".signup-btn").removeClass("d-none");
            $(this).find(".prev-btn").addClass("d-none");
        } else {
            $(this).find(".prev-btn").removeClass("d-none");
            $(this).find(".next-btn").removeClass("d-none");
            $(this).find(".signup-btn").addClass("d-none");
            $(this).find(".finish-btn").addClass("d-none");
        }
        
        const no_of_years = $("#volunteer-registration").find("[type=checkbox][name=no_of_years_opt_out]").prop('checked') ? 'Opt-out' : $("#volunteer-registration").find("[type=text][name=no_of_years]").val();
        const address_type = $("#volunteer-registration").find("[type=radio][name=address_type]:checked").val();
        


        $("#summary-table").find('[data-value-for="organization"]').html($("#volunteer-registration").find("[name=organization_id] option:selected").text());
        $("#summary-table").find('[data-value-for="no_of_years"]').html(no_of_years);
        $("#summary-table").find('[data-value-for="address_type"]').html(address_type);
        $("#summary-table").find('[data-value-for="preferred_role"]').html($("#volunteer-registration").find("[name=preferred_role] option:selected").text());
    });

    $('.signup-btn').on('click', function () {
        window.location.reload();
    });
    let registrationUnderProcess = false;
    $('.finish-btn').on('click', function () {
        if (registrationUnderProcess) {
            return;
        }
        const form = $('#volunteer_registration_form').get(0);
        registrationUnderProcess = true;
        $.ajax({
            type: "POST",
            url: form.action,
            data: $(form).serialize(),
            success: function (response) {
                // Silent
            },
            error: function (res) {
                alert('something wrong!');
                console.error(res);
            },
            complete: function () {
                registrationUnderProcess = false;
            }
        });
    });

</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/volunteering/index.blade.php ENDPATH**/ ?>