<form id="bank_deposit_form" action="<?php echo e(route("bank_deposit_form")); ?>" method="POST"
      enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <br>


    <div class="form-row" style="width:100%;border-top-left-radius:5px;border-top-right-radius:5px;background:#1a5a96;color:#fff;padding:8px;">
        <h1>Event bank deposit form</h1>
    </div>
    <div class="card">
        <div class="card-body">

    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="organization_code">Organization code</label>
            <select type="text" class="form-control errors" name="organization_code" id="organization_code" placeholder="">
            </select>
            <span class="organization_code_errors errors">
                          <?php $__errorArgs = ['organization_code'];
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
            <label for="form_submitter">Form submitter</label>
            <div id="form_submitter"><?php echo e($current_user->name); ?></div>
            <input type="hidden" value="<?php echo e($current_user->id); ?>" name="form_submitter" />

            <span class="form_submitter_errors errors">
                       <?php $__errorArgs = ['form_submitter'];
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
            <label for="campaign_year">Campaign year</label>
            <div id="campaign_year"><?php echo e($campaign_year->calendar_year); ?></div>
            <input type="hidden" value="<?php echo e($campaign_year->id); ?>" name="campaign_year" />
            <span class="campaign_year_errors errors">
                       <?php $__errorArgs = ['form_submitter'];
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
    </div>
<br>
    <div class="form-row form-header">
            <h3 class="blue">Event details</h3>
    </div>

    <div class="form-row form-body">
        <div class="form-group col-md-6">
            <label for="description">Event name</label>
            <input class="form-control" type="text" name="description" id="description" />
            <span>Include Event Name-Date (DD/MM/YYYY) - Name of Coordinator</span>
            <span class="description_errors errors">
                       <?php $__errorArgs = ['description'];
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
        <div id="pecsfid" class="form-group col-md-6">
            <label for="pecsf_id">PECSF ID</label>
            <input class="form-control" type="text" name="pecsf_id" id="pecsf_id" />
            <span class="pecsf_id_errors errors">
                       <?php $__errorArgs = ['pecsf_id'];
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
        <div id="bcgovid" class="form-group col-md-6" style="display:none;">
            <label for="bc_gov_id">BC gov ID</label>
            <input class="form-control" type="text" name="bc_gov_id" id="bc_gov_id" />
            <span class="bc_gov_id_errors errors">
                       <?php $__errorArgs = ['bc_gov_id'];
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

        <div class="form-group col-md-3">
            <label for="event_type">Event type</label>
            <select class="form-control" type="text" id="event_type" name="event_type">
                <option value="">Select an event type</option>
                <option value="Cash One-Time Donation">Cash one-time donation</option>
                <option value="Cheque One-Time Donation">Cheque one-time donation</option>
                <option value="Fundraiser">fundraiser</option>
                <option value="Gaming">gaming</option>
            </select>
            <span class="event_type_errors errors">
                       <?php $__errorArgs = ['form_submitter'];
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
        <div class="form-group col-md-3 sub_type">
            <label for="sub_type">Sub type</label>
            <select class="form-control" type="text" id="sub_type" name="sub_type" disabled="true">
                <option value="false">Disabled</option>
            </select>
            <span class="sub_type_errors errors">
                       <?php $__errorArgs = ['form_submitter'];
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

        <div class="form-group col-md-3">
            <label for="sub_type">Deposit date</label>
            <input class="form-control" type="date" id="deposit_date" name="deposit_date">
            <span class="deposit_date_errors errors">
                       <?php $__errorArgs = ['form_submitter'];
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

        <div class="form-group col-md-3">
            <label for="sub_type">Deposit amount ($)</label>
            <input class="form-control" type="text" id="deposit_amount" name="deposit_amount" />

            <span class="deposit_amount_errors errors">
                       <?php $__errorArgs = ['form_submitter'];
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

    </div>
<br>



    <div class="form-row form-header">
            <h3 class="blue">Work location</h3>
    </div>
    <div class="form-row form-body">

        <div class="form-group col-md-4">
            <label for="event_type">Employment city</label>
            <select class="form-control search_icon" type="text" id="employment_city" name="employment_city" >
                <option value="">Select a city</option>
                <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($city->city); ?>"><?php echo e($city->city); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <span class="employment_city_errors errors">
                       <?php $__errorArgs = ['employment_city'];
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
            <label for="region">Region</label>
            <select class="form-control search_icon" id="region" name="region">
                <option value="">Select a region</option>
            <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($region->id); ?>"><?php echo e($region->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <span class="region_errors errors">
                       <?php $__errorArgs = ['region'];
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
            <label for="sub_type">Business unit</label>
            <select class="form-control search_icon" id="business_unit" name="business_unit">
                <option value="">Select a business unit</option>
            <?php $__currentLoopData = $business_units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(!empty($bu->name)): ?>
                    <option value="<?php echo e($bu->id); ?>"><?php echo e($bu->name); ?></option>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <span class="business_unit_errors errors">
                       <?php $__errorArgs = ['business_unit'];
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


    </div>
    <br>
    <div class="form-row form-header address_hook" style="display:none;">
            <h3 class="blue">Mailing address for charitable receipt</h3>
    </div>
    <div class="form-row form-body address_hook" style="display:none;">

        <div class="form-group col-md-12" id="address_line_1" style="">
            <label for="event_type">Address line 1</label>
            <input class="form-control" type="text" id="address_1" name="address_1"/>

            <span class="address_1_errors errors">
                       <?php $__errorArgs = ['address_1'];
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
            <label for="sub_type">City</label>

            <select class="form-control search_icon" type="text" id="city" name="city" >
                <option value="">Select a city</option>
            <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($city->city); ?>"><?php echo e($city->city); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <span class="city_errors errors">
                       <?php $__errorArgs = ['city'];
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
            <label for="sub_type">Province</label>
            <select class="form-control" type="text" id="province" name="province">
                <option value="">Select a  province</option>

                <option value="Alberta">Alberta</option>
                <option value="British Columbia">British columbia</option>
                <option value="Manitoba">Manitoba</option>
                <option value="New Brunswick">New brunswick</option>
                <option value="Newfoundland and Labrador">Newfoundland and labrador</option>
                <option value="Nova Scotia">Nova scotia</option>
                <option value="Nunavut">Nunavut</option>
                <option value="Prince Edward Island">Prince edward island</option>
                <option value="Quebec">Quebec</option>
                <option value="Saskatchewan">Saskatchewan</option>
                <option value="Yukon">Yukon</option>

                <option value="Ontario">Ontario</option>
            </select>
            <span class="province_errors errors">
                       <?php $__errorArgs = ['province'];
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
            <label for="sub_type">Postal Code</label>
            <input class="form-control" type="text" id="postal_code" name="postal_code" />
            <span class="postal_code_errors errors">
                       <?php $__errorArgs = ['postal_code'];
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

    </div>
<br>
    <br>
    <div class="form-row form-header">
            <h3 class="">Charity selections and distribution</h3>
    </div>

    <div class="form-row  form-body">
        <div class="form-group col-md-12">
            <input type="radio" checked id="charity_selection_1" name="charity_selection" value="fsp" />
            <label class="blue" for="charity_selection_1">Fund supported pool</label>
            <span class="charity_selection_errors errors">
                       <?php $__errorArgs = ['charity_selection'];
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

            <br>
            <span style="padding:20px;">
    By choosing this option your donation will support the current Fund Supported Pool of regional programs. Click on the tiles to learn about the programs in each regional pool.
</span>
        </div>


        <?php $__currentLoopData = $pools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="form-group col-md-2 form-pool">

                <div class="card h-100 <?php echo e($pool->id == $regional_pool_id ? 'active' : ''); ?>" data-id="pool<?php echo e($pool->id); ?>">
                    
                    <div class="card-body m-1 p-2">

                        <div class="form-check float-left">
                            <input class="form-check-input" type="radio" name="regional_pool_id" id="pool<?php echo e($pool->id); ?>"
                                   value="<?php echo e($pool->id); ?>" <?php echo e($pool->id == $regional_pool_id ? 'checked' : ''); ?>>

                        </div>
                        <br>

                        <label style="font-weight:bold;font-size:12px;text-align: center;
    width: 100%;" class="form-check-label pl-3" for="xxxpool<?php echo e($pool->id); ?>">
                            <?php echo e($pool->region->name); ?>

                        </label>
                        <span style="color:blue;text-decoration:underline;width:100%;text-align:center;display:block" class="more-info bottom-center" data-id="<?php echo e($pool->id); ?>"
                              data-name="<?php echo e($pool->region->name); ?>" data-source="" data-type="" data-yearcd="<?php echo e(date("Y",strtotime($pool->start_date))); ?>">View Details</span>
                    </div>


                </div>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php for($i=0;$i<(4 - count($pools));$i++): ?>
            <div class="form-group col-md-2 form-pool">



            </div>
            <?php endfor; ?>


        <div class="form-group col-md-6">
            <input type="radio" id="charity_selection_2" name="charity_selection" value="dc" />
            <label class="blue" for="charity_selection_2">Donor choice</label>
        </div>
        <div class="form-group  org_hook col-md-6">
            <a href="https://apps.cra-arc.gc.ca/ebci/hacc/srch/pub/dsplyBscSrch?request_locale=en" target="_blank"><img class="float-right" style="width:26px;height:26px;position:relative;top:-4px;" src="<?php echo e(asset("img/icons/external_link.png")); ?>"></img><h5 class="blue float-right">View CRA Charity List</h5></a>
        </div>


        <div class="form-group org_hook col-md-4">
            <label for="keyword">Search by Keyword</label>
            <input class="form-control" type="text" name="keyword" value="" id="keyword" />
        </div>
        <div class="form-group org_hook col-md-4">
            <label for="category">Search by Category</label>
            <select class="form-control" type="text" name="category" id="category">
                <option value="">Choose a Category</option>
                <?php $__currentLoopData = $organizations[0]::CATEGORY_LIST; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                   <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
             </select>
        </div>
        <div class="form-group org_hook col-md-4">
            <label for="category">Search by Province</label>
            <select class="form-control" type="text" name="province" id="charity_province">
                <option value="">Choose a Province</option>
                <?php $__currentLoopData = $organizations[0]::PROVINCE_LIST; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group org_hook  col-md-12">
            <h4 class="blue">Search Results</h4>
            <h5><?php echo e($organizations->total()); ?> results</h5>
        <table id="charities">
          <?php echo $__env->make("volunteering.partials.organizations", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </table>
            <div>
                <?php echo e($organizations->links()); ?>

            </div>
            <h4>Your Charities</h4>
            <table id="organizations" style="display:none;width:100%">



            </table>
        </div>
    </div>






<br>



    <div class="form-row form-header">
            <h3 class="blue">Attachment</h3>

    </div>

    <div class="form-row form-body">
        <div style="padding:8px;" class="upload-area form-group col-md-3">
            <i style="color:#1a5a96;margin-left:155px;" class="fas fa-file-upload fa-5x"></i>
            <br>
            <br>
            <label style="text-align:center;margin-left: 75px;" id="upload-area-text" for="attachment_input_1">Drag and Drop Or <u>Browse</u> Files</label>
            <input style="display:none" id="attachment_input_1" name="attachments[]" type="file" />
        </div>
        <div id="attachments" class="form-group col-md-6">

        </div>
    </div>
            <span class="attachment_errors errors">
                       <?php $__errorArgs = ['attachments'];
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
<br>
    <br>
    <input type="submit" class="btn btn-primary" value="Submit" />
    <br>
    <br>
    <p>Once information has been submitted to PECSF Administration, no further changes are<br> possible through eForm. Please contact pecsf@gov.bc.ca</p>
    <h5>Freedom of Information and Protection of Privacy Act</h5>
    <p>
        Personal information on this form is collected by the BC Public Service Agency for the purposes of processing and reporting your charitable contributions to the Community Fund under section 26(c) of the Freedom of Information and Protection of Privacy Act.
        Questions about the collection of your personal information can be directed to the Campaign Manager, Provincial Employees Community Services Fund at 250 356-1736 or PECSF@gov.bc.ca.
    </p>
        </div>
    </div>
</form>
<!-- Modal -->
<div class="modal fade" id="regionalPoolModal" tabindex="-1" role="dialog" aria-labelledby="pledgeDetailModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="pledgeDetailModalTitle">Regional Charity Pool
                    <span class="text-dark font-weight-bold"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pledgeDetail">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>

</script>
<?php /**PATH C:\Users\x257354\Sites\PECSF\resources\views/volunteering/partials/form.blade.php ENDPATH**/ ?>