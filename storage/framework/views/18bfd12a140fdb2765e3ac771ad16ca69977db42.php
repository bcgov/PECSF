<!-- Modal -->
<div class="modal fade" id="learn-more-modal" tabindex="-1" aria-labelledby="learnMoreModalTitle" data-backdrop="static" 
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="learnMoreModalTitle">
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="donateGuideCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
                <div class="carousel-inner text-center">
                    <div class="carousel-item active">
                        <h3 class="text-primary my-5">
                            Why donate to the Provincial Employees Community Service Fund?
                        </h3>
                        <div class="my-4">
                            <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/Zysh5X1sEk8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <h3 class="text-primary my-5">
                            When you can donate
                        </h3>
                        <div class="my-4">
                            <img src="<?php echo e(asset('img/donate-intro.png')); ?>" alt="" style="max-width: 400px;">
                        </div>
                        <p class="m-4">
                            Employees, including retirees, can donate anytime, however the majority of employees sign <br> up to support their favourite charities during the annual fall PECSF awareness campaign.
                        </p>
                        <p class="m-4">
                            Registering during the Fall campaign and pledge drive ensures payroll deductions are set up for the next calendar year. Payroll deductions begin with the first pay in January.
                        </p>
                    </div>

                    <div class="carousel-item">
                        <h3 class="text-primary my-5">
                            How donating to PECSF works
                        </h3>
                        <div class="row">
                            <div class="col-12 col-md-4 offset-md-1">
                                <h4 class="text-primary">
                                    Step 1: Choose your charities
                                </h4>
                                <p>
                                    Simply use the search bar to type in the name of a charity.
                                </p>
                                <p>
                                    Up to 10 charitable organizations can be funded through this new online portal.
                                </p>
                            </div>
                            <div class="col-12 col-md-5 offset-md-1">
                                <img src="<?php echo e(asset('img/donation-intro/step-1.png')); ?>" alt="" style="max-width: 450px;">
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <h3 class="text-primary my-5">
                            How donating to PECSF works
                        </h3>
                        <div class="row">
                            <div class="col-12 col-md-4 offset-md-1">
                                <h4 class="text-primary">
                                    Step 2: Decide on the frequency and amount
                                </h4>
                                <p>
                                    Current employees can choose biweekly deductions in any amount or a one-time payroll deduction.
                                </p>
                                <p>
                                It is recommended that you make a one-time contribution if you do not wish to use the payroll
deduction plan.
                                </p>
                            </div>
                            <div class="col-12 col-md-5 offset-md-1">
                                <img src="<?php echo e(asset('img/donation-intro/step-2.png')); ?>" alt="" style="max-width: 450px;">
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <h3 class="text-primary my-5">
                            How donating to PECSF works
                        </h3>
                        <div class="row">
                            <div class="col-12 col-md-4 offset-md-1">
                                <h4 class="text-primary">
                                    Step 3: Decide on the distribution
                                </h4>
                                <p>
                                Use the toggles to distribute your contributions to each charity. 
                                </p>
                                <p>
                                You have the option to distribute your donation by percentage or by dollar amount.
                                </p>
                                <p>
                                By default, your donation is distributed evenly to each organization, however, you have the option to customize the distribution.
                                </p>
                            </div>
                            <div class="col-12 col-md-5 offset-md-1">
                                <img src="<?php echo e(asset('img/donation-intro/step-3.png')); ?>" alt="" style="max-width: 450px;">
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <h3 class="text-primary my-5">
                            How donating to PECSF works
                        </h3>
                        <div class="row">
                            <div class="col-12 col-md-4 offset-md-1">
                                <h4 class="text-primary">
                                    Step 4: review and submit
                                </h4>
                                <p>
                                Check over your choices to ensure accuracy, and that you’re ready to submit. Once you press “submit”, the action cannot be undone.
                                </p>
                                <p>
                                Your <strong><u><b>Payroll Deductions</b></u></strong> begin on the first paycheque of 2022 and will appear on your 2022 T4.
                                </p>
                                <p>
                                By default, your donation is distributed evenly to each organization, however, you have the option to customize the distribution.
                                </p>
                            </div>
                            <div class="col-12 col-md-5 offset-md-1">
                                <img src="<?php echo e(asset('img/donation-intro/step-4.png')); ?>" alt="" style="max-width: 450px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex">
                    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.button','data' => ['href' => '#donateGuideCarousel','style' => 'outline-primary','class' => 'prev-btn d-none','role' => 'button','dataSlide' => 'prev']]); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['href' => '#donateGuideCarousel','style' => 'outline-primary','class' => 'prev-btn d-none','role' => 'button','data-slide' => 'prev']); ?>Previous <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                    <div class="flex-fill"></div>
                    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.button','data' => ['href' => '#donateGuideCarousel','role' => 'button','class' => 'next-btn','dataSlide' => 'next']]); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['href' => '#donateGuideCarousel','role' => 'button','class' => 'next-btn','data-slide' => 'next']); ?>Next <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.button','data' => ['href' => route('donate'),'role' => 'button','class' => 'ready-btn d-none']]); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('donate')),'role' => 'button','class' => 'ready-btn d-none']); ?>I'm ready to Donate! <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/donations/partials/learn-more-modal.blade.php ENDPATH**/ ?>