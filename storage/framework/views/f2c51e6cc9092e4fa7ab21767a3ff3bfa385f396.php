<tr class="organization" id="organization<?php echo e($index); ?>">
    <td>
        <div class="form-row raised">
            <div class="form-group col-md-8 charity">
                <label for="event_type">Organization Name:</label>
               <!--<input class="form-control" type="text" id="organization_name" name="organization_name[]"/>-->
                <div>
                    <input type="text" disabled class="form-control errors organization_name" name="id[]"  placeholder="" />

                    
                </div>
                <span class="organization_name_errors  errors">
                       <?php $__errorArgs = ['organization_name.'.$index];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                  </span>

            </div>

            <div class="form-group col-md-4">
                <label for="sub_type">Donation Percent (%)</label>
                <input class="form-control" type="text" id="donation_percent" name="donation_percent[]">
                <span class="donation_percent_errors  errors">
                       <?php $__errorArgs = ['donation_percent.'.$index];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                  </span>

            </div>
            <div class="form-group col-md-12">
                <label for="sub_type">Specific Community Or Initiative (Optional):</label>
                <input class="form-control" type="text" id="specific_community_or_initiative" name="specific_community_or_initiative[]" />
                <span class="specific_community_or_initiative_errors  errors">
                       <?php $__errorArgs = ['specific_community_or_initiative.'.$index];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                  </span>
            </div>
            <div class="form-group col-md-12">
                <button class="btn btn-danger remove">Remove</button>
            </div>
        </div>
    </td>
</tr>

<?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/volunteering/partials/add-organization.blade.php ENDPATH**/ ?>