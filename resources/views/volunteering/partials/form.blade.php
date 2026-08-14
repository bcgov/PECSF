<form id="bank_deposit_form" action="{{ route('bank_deposit_form') }}" method="POST"  enctype="multipart/form-data">
    @csrf
    <br>

    <input type="hidden" name="form_id" id="form_id" value="0" />

    <div class="form-row"
        style="left: 5px;
    position: relative;width:100%;border-top-left-radius:5px;border-top-right-radius:5px;background:#1a5a96;color:#fff;padding-left:15px;padding-top:10px;">
        <h2>Event bank deposit form</h2>
    </div>
    <div class="card" style="border-radius:0px;">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="organization_code">Organization</label>
                    <select type="text" class="form-control " name="organization_code" id="organization_code"
                        placeholder="" role="listbox" aria-label="Organization">
                        <option value="" selected="selected">Choose an Organization</option>

                    </select>
                    <span class="organization_code_errors errors">
                        @error('organization_code')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>
                </div>
                <div class="form-group col-md-4">
                    <label for="form_submitter">Form submitter</label>
                    <!--<div id="form_submitter">{{ $current_user->name }}</div>-->
                    <input type="text" disabled class="form-control" value="{{ $current_user->name }}" />

                    <input type="hidden" disabled value="{{ $current_user->id }}" name="form_submitter" />

                    <span class="form_submitter_errors errors">
                        @error('form_submitter')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>

                </div>
                <div class="form-group col-md-4">
                    <label for="campaign_year">Campaign year</label>
                    <!--<div id="campaign_year">{{ $campaign_year->calendar_year - 1 }}</div>-->
                    <input type="text" disabled class="form-control"
                        value="{{ $campaign_year->calendar_year - 1 }}" />
                    <input type="hidden" value="{{ $campaign_year->id }}" name="campaign_year" />
                    <span class="campaign_year_errors errors">
                        @error('form_submitter')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>

                </div>
            </div>
            <br>
            <div class="form-row form-header">
                <h3 class="blue">Donation or event details</h3>
            </div>

            <div class="form-row form-body">
                <div class="form-group col-md-6">
                    <label for="description">Donation or event name</label>
                    <input class="form-control" type="text" name="description" id="description" aria-label="Donation or event name"/>
                    <span>Include Event Name-Date (DD/MM/YYYY) - Name of Coordinator<br>For Cash or Cheque, write CASH
                        or Cheque – Cheque #</span>
                    <span class="description_errors errors">
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>
                </div>

                <div class="form-group col-md-3 event_type">
                    <label for="event_type">Donation or event type</label>
                    <select class="form-control" type="text" id="event_type" name="event_type" role="listbox">
                        <option value="">Select an event type</option>
                        <option value="Cash One-Time Donation">Cash one-time donation</option>
                        <option value="Cheque One-Time Donation">Cheque one-time donation</option>
                        <option value="Fundraiser">Fundraiser</option>
                        <option value="Gaming">Gaming</option>
                    </select>
                    <span class="event_type_errors errors">
                        @error('form_submitter')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>

                </div>
                <div class="form-group col-md-3 sub_type">
                    <label for="sub_type">Sub type</label>
                    <select class="form-control" type="text" id="sub_type" name="sub_type" role="listbox">
                        <option value="none">None</option>
                    </select>
                    <span class="sub_type_errors errors">
                        @error('form_submitter')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>

                </div>

                <div class="form-group col-md-3">
                    <label for="deposit_date">Deposit date</label>
                    <input class="form-control" type="date" id="deposit_date" name="deposit_date" aria-label="Deposit date">
                    <span class="deposit_date_errors errors">
                        @error('form_submitter')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>

                </div>

                <div class="form-group col-md-3">
                    <label for="sub_type">Deposit amount ($)</label>
                    <input class="form-control" type="text" id="deposit_amount" name="deposit_amount" aria-label="Deposit amount" />

                    <span class="deposit_amount_errors errors">
                        @error('form_submitter')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>

                </div>

                <div id="bcgovid" class="form-group col-md-3" style="display:none;">
                    <label for="bc_gov_id">Donor Employee ID</label>
                    <input class="form-control" type="text" name="bc_gov_id" id="bc_gov_id" aria-label="Donor Employee ID" />
                    <span class="bc_gov_id_errors errors">
                        @error('bc_gov_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>
                </div>

                <div id="employeename" class="form-group col-md-3" style="">
                    <label for="employee_name">Employee Name</label>
                    <input class="form-control" type="text" name="employee_name" id="employee_name" aria-label="Employee Name" />
                    <span class="employee_name_errors errors">
                        @error('employee_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>
                </div>

                <div id="pecsfid" class="form-group col-md-3" style="">
                    <label for="pecsf_id">PECSF ID</label>
                    <input class="form-control" type="text" name="pecsf_id" id="pecsf_id" aria-label="PECSF ID"/>
                    <span class="pecsf_id_errors errors">
                        @error('pecsf_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>
                </div>

            </div>
            <br>



            <div class="form-row form-header">
                <h3 class="blue">Work location</h3>
            </div>
            <div class="form-row form-body">

                <div class="form-group col-md-4">
                    <label for="employment_city">Employment city</label>
                    <select
                        onchange="$('#region').val($('[code='+this.options[this.selectedIndex].attributes[0].value+']').attr('value')).trigger('change');"
                        class="form-control search_icon" type="text" id="employment_city" name="employment_city" role="listbox" aria-label="Employment city">
                        <option value="">Select a city</option>
                        @foreach ($cities as $city)
                            <option region="{{ $city->TGB_REG_DISTRICT }}" value="{{ $city->city }}">
                                {{ $city->city }}</option>
                        @endforeach
                    </select>

                    <span class="employment_city_errors errors">
                        @error('employment_city')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>

                </div>
                <div class="form-group col-md-4">
                    <label for="region">Region</label>
                    <select class="form-control search_icon" id="region" name="region" role="listbox" aria-label="Region">
                        <option value="">Select a region</option>
                        @foreach ($regions as $region)
                            <option code="{{ $region->code }}" value="{{ $region->id }}">{{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="region_errors errors">
                        @error('region')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>

                </div>

                <div class="form-group col-md-4">
                    <label for="business_unit">Business unit</label>
                    <select class="form-control search_icon" id="business_unit" name="business_unit" role="listbox" aria-label="Business unit">
                        <option value="">Select a business unit</option>
                        @foreach ($business_units as $bu)
                            @if (!empty($bu->name))
                                <option value="{{ $bu->id }}" data-org="{{ $bu->org_code }}">{{ $bu->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <span class="business_unit_errors errors">
                        @error('business_unit')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>

                </div>


            </div>
            <br>
            <div class="form-row form-header address_hook" style="display:none;">
                <h3 class="blue">Mailing address for charitable receipt</h3>
            </div>

            <div class="form-row form-body address_hook" style="display:none;">
                <span style="padding:10px;">A charitable donation receipt will be issued for cash and cheque donations
                    in February following the calendar year in which the donation is received.</span>

                <div class="form-group col-md-12" id="address_line_1" style="">
                    <label for="address_1">Address line 1</label>
                    <input class="form-control" type="text" id="address_1" name="address_1" aria-label="Address 1"/>

                    <span class="address_1_errors errors">
                        @error('address_1')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>

                </div>


                <div class="form-group col-md-4">
                    <label for="city">City</label>

                    <select class="form-control search_icon" type="text" id="city" name="city" role="listbox" aria-label="City">
                        <option value="">Select a city</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->city }}" province="{{ $city->province }}">
                                {{ $city->city }}</option>
                        @endforeach

                    </select>
                    <span class="city_errors errors">
                        @error('city')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>

                </div>

                <div class="form-group col-md-4">
                    <label for="province">Province</label>
                    <select class="form-control" type="text" id="province" name="province" role="listbox" aria-label="Province">
                        <option value="">Select a province</option>
                        <option value="British Columbia">British columbia</option>
                        <option value="Ontario">Ontario</option>
                    </select>
                    <span class="province_errors errors">
                        @error('province')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>

                </div>
                <div class="form-group col-md-4">
                    <label for="postal_code">Postal Code</label>
                    <input class="form-control" type="text" id="postal_code" name="postal_code" aria-label="Postal Code"/>
                    <span class="postal_code_errors errors">
                        @error('postal_code')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </span>

                </div>

            </div>

            <div class="form-row form-header">
                <h3 class="blue">Charity selections and distribution</h3>
            </div>
            <div class="form-row form-body">
                <div class="form-row p-3">
                    <div class="form-group col-md-12 method_selection" tabindex="0">
                        <input type="radio" checked id="charity_selection_1" name="charity_selection" tabindex="-1"
                            value="fsp" role="radiogroup" aria-label="Fund supported pool"/>
                        <label class="blue pl-2" for="charity_selection_1">Fund supported pool</label>
                        <span class="charity_selection_errors errors">
                            @error('charity_selection')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </span>
                    </div>
                    <div>
                        <p class="pl-4">
                            By choosing this option your donation will support the current Fund Supported Pool of regional
                            programs. Click on the tiles to learn about the programs in each regional pool.
                        </p>
                    </div>


                    {{-- @foreach ($pools as $pool)
                        <div class="form-group col-md-2 form-pool" tabindex="0">

                            <div style="width:100%;".
                                class="BC-Gov-SecondaryButton card h-100 {{ $pool->id == $regional_pool_id ? 'active' : '' }}"
                                data-id="pool{{ $pool->id }}">
                               
                                <div class="card-body m-1 p-2">

                                    <div class="form-check float-left">
                                        <input class="form-check-input" type="radio" name="regional_pool_id" role="radiogroup" 
                                            id="pool{{ $pool->id }}" value="{{ $pool->id }}"
                                            {{ $pool->id == $regional_pool_id ? 'checked' : '' }} tabindex="-1">

                                    </div>
                                    <br>

                                    <label style="font-weight:bold;font-size:16px;text-align: left; width: 100%;"
                                        class="form-check-label pl-4 pt-2" for="pool{{ $pool->id }}">
                                        {{ $pool->region->name }}
                                    </label>
                                    <a 
                                        style="width:100%;text-align:center;display:block"
                                        class="pt-2 more-info bottom-center" data-id="{{ $pool->id }}"
                                        data-name="{{ $pool->region->name }}" data-source="" data-type=""
                                        data-yearcd="{{ date('Y', strtotime($pool->start_date)) }}">View Details</a>
                                </div>


                            </div>

                        </div>
                    @endforeach

                    @for ($i = 0; $i < count($pools) % 6; $i++)
                        <div class="form-group col-md-2 form-pool">



                        </div>
                    @endfor --}}      

                    <div class="pt-2 px-4 row row-cols-1 row-cols-md-2 row-cols-lg-4 row-cols-xl-6"  id="step-regional-pools-area">
                        @foreach( $pools as $pool )
                        <div class="col mb-2">
                
                            <div class="card h-100 {{ $pool->id == $regional_pool_id ? 'active' : '' }}" data-id="pool{{ $pool->region_id }}"  tabindex="0">
                                {{-- <img src="https://picsum.photos/200" class="card-img-top" alt="..."
                                        width="50" height="50"> --}}
                                <div class="card-body m-1 p-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="regional_pool_id" id="pool{{ $pool->region_id }}"
                                            value="{{ $pool->id }}" {{ $pool->id == $regional_pool_id ? 'checked' : '' }}  tabindex="-1">
                                        <label class="form-check-label h5 pl-2 pt-1" for="xxxpool{{ $pool->region_id }}">
                                            {{ $pool->region->name }}
                                        </label>
                                    </div>
                
                                    <div class=" text-right m-2 pt-2" data-id="{{ $pool->region_id }}">
                                        <i class="more-info fas fa-info-circle fa-2x bottom-right" data-id="{{ $pool->id }}" tabindex="0"
                                            data-name="{{ $pool->region->name }}" aria-label="More information on the Pool"></i>
                                    </div>
                                </div>
                            </div>
                
                        </div>
                        @endforeach
                    </div>

                </div>

            </div>
                
            <div class="form-row form-body">
                <div class="form-row p-3">
                    <div class="form-group col-md-6 method_selection" tabindex="0">
                        <input type="radio" id="charity_selection_2" name="charity_selection" value="dc" role="radiogroup" tabindex="-1" aria-label="Donor choice" />
                        <label class="blue pl-2" for="charity_selection_2">Donor choice</label>
                    </div>
                    <div class="form-group  org_hook col-md-6">
                        <a href="https://apps.cra-arc.gc.ca/ebci/hacc/srch/pub/dsplyBscSrch?request_locale=en"
                            target="_blank"><img class="float-right"
                                style="width:28px;height:auto;position:relative;top:-2px;"
                                src="{{ asset('img/icons/external_link.png') }}"></img>
                            <span class="blue float-right" style="text-decoration: underline;padding-right:4px">View CRA Charity List </span>
                        </a>
                    </div>
                    <div class="form-group col-md-12">
                        <p class="pl-4">By choosing this option you can support up to 10 Canada Revenue Agency (CRA) registered
                            charitable organizations.
                            Our system uses the official name of the charity registered with the CRA. You can use the View
                            CRA Charity List link to confirm if the organization you would like to support is registered.
                            You can also support a specific branch or program name.</p>

                    </div>
                    @include('donate.partials.choose-charity')

                </div>
            </div>

            <br>
            <div class="form-row form-header" >
                <h3>Attach/Upload Document(s)</h3>


                <span class="pl-3 attachments_errors errors">

                    @error('attachments')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </span>
                                   
            </div>

            <div class="form-row form-body">


                <div class="col-md-12">
                    <p ><span class="font-weight-bold">Browse to attach your completed PECSF Event Bank Deposit Form attachment with bank receipt.</span><br>
                        <em>(Please note that you can only upload files with a maximum size of 2MB each, in pdf, xls, xlsx, csv, png, jpg or jpeg format, and the total number of files should not exceed 3.)</em>
                    </p>
                </div>

                <div class="col-md-12">
                    <div class="needsclick dropzone" id="attachment-dropzone" ></div>  
                </div>

                {{-- <div style="padding:8px; " class="upload-area form-group col-md-12">

                    <a onclick="$('#attachment_input_1').click();"
                        style="background:#fff;border:none;font-weight:bold;color:#000;text-align:center;"
                        id="upload-area-text" for="attachment_input_1">
                        <i style="color:#1a5a96;" class="fas fa-file-upload fa-5x"></i>
                        <br>
                        <br>
                        <u>Browse</u> Files</a>
                    <input style="display:none" id="attachment_input_1" name="attachments[]" type="file" />
                </div>
                <table id="attachments" class=" form-group col-md-12">

                </table> --}}
            </div>

            <div style="display:none;" id="my-template">
                <div id="mytmp" class="dz-preview dz-file-preview">
                    <div class="dz-image"><img data-dz-thumbnail /></div>	
                    <div class="dz-details">
                        <div class="dz-size"><span data-dz-size></span></div>
                        <div class="dz-filename"><span data-dz-name></span></div>
                    </div>
                    <div class="dz-progress">
                        <span class="dz-upload" data-dz-uploadprogress></span>
                    </div>
                    <div class="dz-error-message"><span data-dz-errormessage></span></div>
                    <div class="dz-success-mark">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" height="54px" viewBox="0 0 54 54" width="54px" fill="#000000">
                            <path d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                        </svg> --}}
                    </div>
                    <div class="dz-error-mark">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000">
                            <path d="M0 0h24v24H0z" fill="none" />
                            <circle cx="12" cy="19" r="2" />
                            <path d="M10 3h4v12h-4z" />
                        </svg>
                    </div>
                    <div class="dz-remove" data-dz-remove>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000">
                            <path d="M0 0h24v24H0z" fill="none" />
                            <path d="M14.59 8L12 10.59 9.41 8 8 9.41 10.59 12 8 14.59 9.41 16 12 13.41 14.59 16 16 14.59 13.41 12 16 9.41 14.59 8zM12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                        </svg>
                    </div>
                </div>

            </div>

        </div>

    




        <br>
        <br>
        <input type="submit" style="margin-left:20px;" class="col-md-2 btn btn-primary" value="Submit" />
        <br>
        <br>
        <p style="padding:20px;">Once information has been submitted to PECSF Administration, no further changes are
            possible through eForm. Please contact <a
            href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a>.</p>

        <h5 style="padding-left:20px;">Freedom of Information and Protection of Privacy Act</h5>
        <p style="padding:20px;">

            Personal information on this form is collected by the BC Public Service Agency for the purposes of
            processing and reporting your charitable contributions to the Community Fund and for program evaluation and
            improvement under sections 26 (c) and (e) of the Freedom of Information and Protection of Privacy Act.

            Questions about the collection of your personal information can be directed to the Campaign Manager,
            Provincial Employees Community Services Fund at 250 356-1736 or <a
                href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a>. </p>
    </div>

</form>
<!-- Modal -->
<div class="modal fade" id="regionalPoolModal" tabindex="-1" role="dialog"
    aria-labelledby="pledgeDetailModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="pledgeDetailModalTitle">Regional Charity Pool
                    <span class=""></span>
                </h5>
                <button type="button" class="close closeModalBtn" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pledgeDetail">
            </div>
            <div class="modal-footer">
                <button type="button" style="color:#000;"
                    class="btn btn-outline-primary closeModalBtn">Close</button>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>

    /* Region Pool Area */
    #step-regional-pools-area .card  {
        color: #1a5a96;
        background-color: #f8fafc;
        border: 1px solid #d3e1ee;
        min-height: 100px;
        border-radius: 10px;
    }

    #step-regional-pools-area input[name='regional_pool_id'] {
        width: 18px;
        height: 20px;
    }

    #step-regional-pools-area .card label {
        font-weight: 700;
    }

    #step-regional-pools-area .card:hover {
        /* background-color: darkgray; */
        background-color: #1a5a96;
        color: white;
    }
    #step-regional-pools-area .card.active {
        background-color: #1a5a96;
        color: white;
    }

    #step-regional-pools-area .bottom-right {
        position: absolute;
        bottom: 8px;
        right: 8px;
    }


</style>
@endpush

@push('js')
<script>

$(function () {

    // Enter or space key on Wizard STEP icon to forward and backward 
    $('#step-regional-pools-area .card').on('keyup', function(e) {
        // Enter or space key on Wizard STEP icon to forward and backward    
        var key  = e.key;
        if (key === ' ' || key === 'Enter') {
            e.preventDefault();
            $(this).trigger('click');
        }
    });

    $('#step-regional-pools-area .card').click( function(event) {
        event.stopPropagation();

        id = $(this).attr('data-id');

        // console.log('radio button clicked -- ' + event.target.id);
        // console.log('radio button clicked -- ' + $(this).attr('data-id') );

        if (id) {

            // Need to set the selection on card
            $('#step-regional-pools-area .card').each(function( index, element ) {
                // console.log( index + ": " + $( this ).val() + " - " + event.target.id );
                $(element).removeClass('active');
                $(element).prop('checked',false);
            });

            $('#step-regional-pools-area .card[data-id=' + id + ']').addClass('active');
            $('#'+id).prop('checked',true);
        }
    });

    var selected_more_info = "";
    $('#step-regional-pools-area .more-info').on('keyup', function(e) {
        // Enter or space key on Wizard STEP icon to forward and backward    
        var key  = e.key;
        if (key === ' ' || key === 'Enter') {
            e.preventDefault();
            $(this).trigger('click');
        }
    });

    $('.more-info').click( function(event) {
        event.stopPropagation();

        // var current_id = event.target.id;
        id = $(this).attr('data-id');
        name = $(this).attr('data-name');

        // console.log( 'more info - ' + id );
        selected_more_info = this;
        if ( id  ) {
            // Lanuch Modal page for listing the Pool detail
            $.ajax({
                url: '/donate-now/regional-pool-detail/' + id,
                type: 'GET',
                // data: $("#notify-form").serialize(),
                dataType: 'html',
                success: function (result) {
                    $('#regionalPoolModal  .modal-title span').html(name);
                    target = '#regionalPoolModal .pledgeDetail';
                    $(target).html('');
                    $(target).html(result);
                },
                complete: function() {
                },
                error: function (result) {
                    target = '.pledgeDetail';
                    $(target).html('');
                    $(target).html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
                }
            })

            $('#regionalPoolModal').modal('show')
        }
    });

    $('#regionalPoolModal').on('hidden.bs.modal', function (e) {
        // do something...
        $( selected_more_info ).focus();
    })
    
});

</script>
@endpush
