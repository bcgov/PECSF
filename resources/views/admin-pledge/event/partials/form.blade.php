<form id="bank_deposit_form" action="{{ route('admin-pledge.maintain-event.update',$pledge->id) }}" method="POST"  enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" id="pledge_id" name="pledge_id" value="{{ $pledge->id }}">

    {{-- <input type="hidden" name="form_id" id="form_id" value="{{ $pledge->id }}" /> --}}
    <div class="d-flex  align-items-center my-2">
        <h4>Transaction ID: </b>{{ $pledge->id }}</h4>
    </div>

    <div class="card">
        <div class="card-body p-1">
            <div class="form-row form-header bg-primary">
                <h4>Organization and Campaign Year</h4>
            </div>

            <div class="form-row form-body">
                <div class="form-group col-md-4">
                    <label for="organization_code">Organization</label>

                    <input type="text" disabled class="form-control " name="organization_code" id="organization_code" value="{{ $pledge->organization->name }}">
                </div>
                <div class="form-group col-md-4">
                    <label for="form_submitter">Form submitter</label>
                    <input type="text" disabled class="form-control" value="{{ $pledge->form_submitted_by->name }}" name="form_submitter" />
                </div>

                <div class="form-group col-md-2">
                    <label for="campaign_year">Campaign year</label>
                    <input type="text" disabled class="form-control"
                        value="{{ $pledge->campaign_year->calendar_year - 1 }}" />
                </div>

                <div class="form-group col-md-2">
                    <label for="Status">Approval Status</label>
                    <input type="text" disabled class="form-control"
                        value="{{ $pledge->status }}" />
                </div>
            </div>

            <div class="form-row form-header bg-primary mt-3">
                <h4>Donation or event details</h4>
            </div>

            <div class="form-row form-body">
                <div class="form-group col-md-6">
                    <label for="description">Donation or event name</label>
                    <input class="form-control" readonly type="text" name="description" id="description" value={{ $pledge->description }} />
                </div>

                <div class="form-group col-md-3 event_type">
                    <label for="event_type">Donation or event type</label>
                    <input class="form-control" readonly type="text" id="event_type" name="event_type" value="{{ $pledge->event_type }}">
                </div>

                <div class="form-group col-md-3 sub_type">
                    <label for="sub_type">Sub type</label>
                    <input class="form-control" readonly type="text" id="sub_type" name="sub_type" value="{{ $pledge->subtype }}">
                </div>

                <div class="form-group col-md-2">
                    <label for="sub_type">Deposit date</label>
                    <input class="form-control" readonly type="date" id="deposit_date" name="deposit_date" value="{{ $pledge->deposit_date->format('Y-m-d') }}">
                </div>

                <div class="form-group col-md-2">
                    <label for="sub_type">Deposit amount ($)</label>
                    <input class="form-control" readonly type="text" id="deposit_amount" name="deposit_amount" value="{{ number_format($pledge->deposit_amount,2) }}" />
                </div>

                <div id="bcgovid" class="form-group col-md-2">
                    <label for="bc_gov_id">Employee ID</label>
                    <input class="form-control" readonly type="text" name="bc_gov_id" id="bc_gov_id" value="{{ $pledge->bc_gov_id }}" />
                </div>

                <div id="employeename" class="form-group col-md-3" style="">
                    <label for="employee_name">Employee Name</label>
                    <input class="form-control" readonly type="text" name="employee_name" id="employee_name" value="{{ $pledge->employee_name }}" />
                </div>

                <div id="pecsfid" class="form-group col-md-3" style="">
                    <label for="pecsf_id">PECSF ID</label>
                    <input class="form-control" readonly type="text" name="pecsf_id" id="pecsf_id" value="{{ $pledge->pecsf_id }}"/>
                </div>
            </div>

            <div class="form-row form-header bg-primary mt-3">
                <h4>Work location</h4>
            </div>
            <div class="form-row form-body">
                <div class="form-group col-md-4">
                    <label for="event_type">Employment city</label>
                    <input class="form-control search_icon"readonly  type="text" id="employment_city" name="employment_city" value="{{  $pledge->employment_city }}">
                </div>
                <div class="form-group col-md-4">
                    <label for="region">Region</label>
                    <input class="form-control search_icon" readonly  id="region" name="region" value="{{ $pledge->region ? $pledge->region->name : '' }}">
                </div>

                <div class="form-group col-md-4">
                    <label for="sub_type">Business unit</label>
                    <input class="form-control search_icon" readonly id="business_unit" name="business_unit" value="{{ $pledge->bu->name }}">

                </div>
            </div>


            <div class="pt-3"></div>  
            <div class="form-row form-header bg-primary">
                <h4>Mailing address for charitable receipt</h4>
            </div>

            <div class="form-row form-body">
                <div class="form-group col-md-12" id="address_line_1" style="">
                    <label for="event_type">Address line 1</label>
                    <input class="form-control" readonly type="text" id="address_1" name="address_1" value="{{ $pledge->address_line_1 }}" />
                </div>


                <div class="form-group col-md-4">
                    <label for="sub_type">City</label>
                    <input class="form-control" readonly type="text" id="city" name="city" value="{{ $pledge->address_city }}">
                </div>

                <div class="form-group col-md-4">
                    <label for="sub_type">Province</label>
                    <input class="form-control" readonly type="text" id="province" name="province" value="{{  $pledge->address_province }}">
                </div>
                <div class="form-group col-md-4">
                    <label for="sub_type">Postal Code</label>
                    <input class="form-control" readonly type="text" id="postal_code" name="postal_code" value="{{  $pledge->address_postal_code }}" />
                </div>
            </div>

            <div class="pt-3"></div>              
            <div class="form-row form-header bg-primary">
                <h3 class="blue">Charity selections and distribution</h3>
            </div>
            <div class="form-row form-body">
                <div class="form-row p-3">
                    <div class="form-group col-md-12 method_selection" tabindex="0">
                        <input type="radio" {{ $pledge->charity_selection == 'fsp' ? 'checked' : '' }} id="charity_selection_1" name="charity_selection" tabindex="-1"
                            value="fsp" role="radiogroup" aria-label="Fund supported pool"/>
                        <label class="blue pl-2" for="charity_selection_1">Fund supported pool</label>
                        <span class="charity_selection_errors errors">
                            @error('charity_selection')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </span>
                    </div>
                    <div class="p-2">
                        <span class="p-2">
                            By choosing this option your donation will support the current Fund Supported Pool of regional
                            programs. Click on the tiles to learn about the programs in each regional pool.
                        </span>
                    </div>

                    <div class="pt-2 px-3 row row-cols-1 row-cols-md-2 row-cols-lg-4 row-cols-xl-6"  id="step-regional-pools-area">
                        @foreach( $pools as $pool )
                        <div class="col mb-2">
                
                            <div class="card h-100 {{ $pool->id == $pledge->regional_pool_id ? 'active' : '' }}" data-id="pool{{ $pool->region_id }}"  tabindex="0">
                                {{-- <img src="https://picsum.photos/200" class="card-img-top" alt="..."
                                        width="50" height="50"> --}}
                                <div class="card-body m-1 p-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="regional_pool_id" id="pool{{ $pool->region_id }}"
                                            value="{{ $pool->id }}" {{ $pool->id == $pledge->regional_pool_id ? 'checked' : '' }}  tabindex="-1">
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
                        <input type="radio" id="charity_selection_2" name="charity_selection" value="dc" role="radiogroup" tabindex="-1" aria-label="Donor choice"
                                {{ $pledge->charity_selection == 'dc' ? 'checked' : '' }} />
                        <label class="blue pl-2" for="charity_selection_2">Donor choice</label>
                    </div>
                    <div class="form-group  org_hook col-md-6">
                        <a href="https://apps.cra-arc.gc.ca/ebci/hacc/srch/pub/dsplyBscSrch?request_locale=en"
                            target="_blank"><img class="float-right"
                                style="width:26px;height:26px;position:relative;top:-4px;"
                                src="{{ asset('img/icons/external_link.png') }}"></img>
                            <h5 class="blue float-right">View CRA Charity List</h5>
                        </a>
                    </div>
                    <div class="form-group col-md-12">
                        <p class="pl-2">By choosing this option you can support up to 10 Canada Revenue Agency (CRA) registered
                            charitable organizations.
                            Our system uses the official name of the charity registered with the CRA. You can use the View
                            CRA Charity List link to confirm if the organization you would like to support is registered.
                            You can also support a specific branch or program name.</p>

                    </div>
                    @include('donate.partials.choose-charity')

                </div>
            </div>

            <div class="pt-3"></div>  
            <div class="form-row form-header bg-primary">
                <h4>File(s)</h4>
            </div>

            <div class="form-row form-body">
                <div class = "row col-md-12">
                    @foreach($pledge->attachments as $attachment)
                        @if ($attachment && $attachment->original_filename)
                            <div class = "col-12 col-lg-6 col-md-8">{{ $loop->index +1 }} -  {{ $attachment->original_filename }}</div>
                            <div class = "col-12 col-lg-6 col-md-4"><a href="{{  "/bank_deposit_form/download/" . $attachment->id }}">Download</a></div>
                        @endif
                    @endforeach
                 </div>
            </div>


            <div class="pt-3"></div>  
            <div class="form-row form-header bg-primary">
                <h4>Audit Information</h4>
            </div>

            <div class="form-row form-body">

                <div class="row col-md-12">
                    <label for="created_by_name" class="col-sm-3 col-form-label">Created By :</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control"  name="created_by_name" value="{{ $pledge->created_by->name }}" readonly>
                    </div>

                    <label for="formatted_created_at" class="col-sm-3 col-form-label">Created at :</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control"  name="formatted_created_at"  value="{{ $pledge->created_at }}" readonly>
                    </div>
                </div>

                <div class="row col-md-12">
                    <label for="updated_by_name" class="col-sm-3 col-form-label">Updated By :</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control"  name="updated_by_name" value="{{ $pledge->updated_by->name }}" readonly>
                    </div>

                    <label for="formatted_updated_at" class="col-sm-3 col-form-label">Updated at :</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control"  name="formatted_updated_at" value="{{ $pledge->updated_at }}" readonly>
                    </div>
                </div>

                <div class="row col-md-12">
                    <label for="updated_by_name" class="col-sm-3 col-form-label">Approved By :</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control"  name="approved_by_name" value="{{ $pledge->approved_by->name }}" readonly>
                    </div>

                    <label for="formatted_updated_at" class="col-sm-3 col-form-label">Approved at :</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control"  name="approved_at" value="{{ $pledge->approved_at }}" readonly>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="pt-3"></div>  
    <input type="submit" class="col-md-2 btn btn-primary" value="Save" />

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

    /* method selection */
    .method_selection input[name='charity_selection'] {
        margin-top: -1px;
        vertical-align: middle;
        width: 20px;
        height: 20px;
    }

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

    // Hide or Unhide Pool or charities
    $("input[name='charity_selection']").click(function() {

        if($(this).val() == "dc"){
            $("#organizations").show();
            $(".org_hook").show();
            $("#add_row").show();
            // $(".form-pool").hide();
            $("#step-regional-pools-area").hide();
            $("#pool_filter").parents(".form-group").show();
        } else {
            // $(".form-pool").show();
            $("#step-regional-pools-area").show();
            $("#organizations").hide();
            $("#add_row").hide();
            $(".org_hook").hide();
            $("#pool_filter").parents(".form-group").hide();

            pool_id = $("input[name='regional_pool_id']:checked").val();
            if (!(pool_id)) {
                $("input[name='regional_pool_id']:first").prop("checked", true);
            }
        }
    });

    @if ($pledge->charity_selection == 'fsp') 
        $("#charity_selection_1").trigger("click"); 
    @else 
        $("#charity_selection_2").trigger("click"); 
    @endif;

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



    $("#bank_deposit_form").submit(function(e)
    {
        e.preventDefault();
        var form = document.getElementById("create_pool");


        $(".max-charities-error").hide();
        $(".charity-error-hook").css("border","none")

        formData = new FormData();

        $("#bank_deposit_form select").each(function(){
            // if($(this).val()){
            //     if($(this).val().length > 0){
                if ($(this).val() == 'false') {
                    formData.append($(this).attr("name"), '');
                } else {
                    formData.append($(this).attr("name"), $(this).val());
                }
            //     }
            // }
        });

        $("#bank_deposit_form input").each(function(){
            if($(this).attr('type') != "submit"){
                if($(this).attr('type') == "radio"){
                    if($(this).is(':checked')){
                        formData.append($(this).attr("name"), $(this).val());
                    }
                } else{
                    // if($(this).val().length > 0){
                        formData.append($(this).attr("name"), $(this).val());
                    // }
                }
            }
        });

        formData.append("org_count", $(".organization").length);
        // formData.append("ignoreFiles", ignoreFiles);

        $(this).fadeTo("slow",0.2);
        $.ajax({
            url: $("#bank_deposit_form").attr("action"),
            type:"POST",
            data: formData,
            headers: {'X-CSRF-TOKEN': $("input[name='_token']").val()},
            processData: false,
            cache: false,
            contentType: false,
            dataType: 'json',
            success:function(data){

                console.log(data);
                window.location = '{{ route("admin-pledge.maintain-event.index") }}';

                // Swal.fire({
                //     title: '<strong>Success!</strong>',
                //     icon: 'success',
                //     html:
                //         'Form Submitted!',
                //     showCloseButton: false,
                //     showCancelButton: true,
                //     focusConfirm: false,
                // }).then((result) => {
                    // $("#bank_deposit_form").fadeTo("slow",1);
                    // $('.errors').html("");

                    // window.location = response[0];
                    // console.log(response);
                // });
                // $('[submission_id='+$('#form_id').val()+']').val(1).trigger('change');
            },
            error: function(response) {
                $('.errors').html("");
                $(".donation_percent_errors").html("");
                if(response.responseJSON.errors){
                    errors = response.responseJSON.errors;
                    for(const prop in response.responseJSON.errors){
                        count = prop.substring(prop.indexOf(".")+1);
                        tag = prop.substring(0,prop.indexOf("."));
                        error = errors[prop][0].split(".");
                        error = error[0] + error[1].substring(1,error[1].length);
                        error = error.replace("_"," ");
                        $("."+prop+"_errors").html('<span class="invalid-feedback">'+error+'</span>');
                        $(".donation_percent_errors").eq((parseInt(prop.replace("donation_percent.",""))) - 1).html('<span class="invalid-feedback">'+error+'</span>');
                        $("."+prop.substring(0,(prop.indexOf(".") - 1 ))+"_errors").html('<span class="invalid-feedback">'+error+'</span>');
                    }
                }
                $(".invalid-feedback").css("display","block");
                $("#bank_deposit_form").fadeTo("slow",1);
            },
        });

    });

    
});

</script>
@endpush
