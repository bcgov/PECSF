<h4>My Statistics</h4>
<div class="card">
    <div class="card-body">
        <div class="text-center text-primary">
            <p class="mt-1">
                <strong>No statistics to Display</strong>
            </p>
            <p>
                You are not registered as PECSF volunteer. <br>
                Click below to get started!
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
        </div>
    </div>
</div><?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/volunteering/partials/no-statistics.blade.php ENDPATH**/ ?>