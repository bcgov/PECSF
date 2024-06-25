@extends('adminlte::page')

@section('content_header')

@include('admin-pledge.partials.tabs')

    <h4 class="mx-1 mt-3">{{ isset($pledge) ? 'Edit ' : 'Create '}} a Campaign Pledge</h4>

    <div class="mx-1 pt-2">
        <button class="btn btn-outline-primary go-back-btn" onclick="window.location.href='{{ route('admin-pledge.campaign.index') }}'">
            Back    
        </button> 
    </div>

@endsection

@section('content')


<div class="card pb-4">

    {{-- Wizard Progress bar (stepper) --}}
    <div class="card-header border-0 p-0" style="display: none;">
        <div class=" card-timeline px-2 border-0">
            <ul class="bs4-step-tracking">
                <li class="active">
                    <div><i class="fas fa-bars fa-2xl"></i></div>Donor
                </li>
                <li class="">
                    <div><i class="fas fa-dollar-sign fa-2xl"></i></div>Frequency and Amount
                </li>
                <li class="">
                    <div><i class="fas fa-random fa-2xl"></i></div>Pool or Non-Pool
                </li>
                <li class="">
                    <div><i class="fas fa-check fa-2xl"></i></div>Review and Submit
                </li>
            </ul>
        </div>
    </div>

  <div class="card-body py-0 pt-2">
    <form action="{{ isset($pledge) ? route("admin-pledge.campaign.update", $pledge->id) : route("admin-pledge.campaign.store") }}"
            id="admin-pldege-campaign-form" method="POST">
        @csrf
        @isset($pledge)
            @method('PUT')
            <input type="hidden" id="pledge_id" name="pledge_id" value="{{ $pledge->id }}">
        @endisset
        <input type="hidden" id="step" name="step" value="">

        @isset($pledge)
            <div class="d-flex  align-items-center my-2 ">
                <h4><strong>Transaction ID :</strong> {{ $pledge->id }}</h4>
                <div class="flex-fill"></div>

                @if($canCancel)
                    <div class="d-flex  align-items-center ">
                        <div class="mr-2">
                            <button  class="cancel-pledge btn btn-outline-danger mr-2" >Cancel this transaction</button>
                        </div>
                    </div>
                @endif
            </div>
            <hr>
        @endisset            



        {{-- Nav Items --}}
        <ul class="nav nav-tabs" id="nav-tab" role="tablist" style="display:none;">
            <li class="nav-item">
              <a class=" nav-link active" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" data-id="0" role="tab" aria-controls="nav-profile" aria-selected="false">Donor</a>
            </li>
            <li class="nav-item ">
                <a class=" nav-link" id="nav-amount-tab" data-toggle="tab" href="#nav-amount" data-id="2" role="tab" aria-controls="nav-amount" aria-selected="false">Frequency and Amount</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="nav-selection-tab" data-toggle="tab" href="#nav-selection" data-id="1" role="tab" aria-controls="nav-selection" aria-selected="false">Selection</a>
            </li>
            <li class="nav-item">
              <a class=" nav-link " id="nav-summary-tab" data-toggle="tab" href="#nav-summary" data-id="3" role="tab" aria-controls="nav-summary" aria-selected="false">Summary</a>
            </li>
        </ul>

        <div class="tab-content pb-3 px-1" id="nav-tabContent">
            <div class="tab-pane fade step show active" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                <div class="pl-2 font-weight-bold">Step 1 -  Select donor</div>
                    <ul class="ml-2 pt-1">
                        <li>Enter the employee’s organization</li>
                        <li>Enter the employee’s ID</li>
                    </ul>

                @include('admin-pledge.campaign.partials.profile')

            </div>
            <div class="tab-pane fade step" id="nav-amount" role="tabpanel" aria-labelledby="nav-amount-tab">
                <div class="pl-2 font-weight-bold">Step 2 -  Choose the frequency and amount for deductions</div>
                    <ul class="ml-2 pt-1">
                        <li>Choose a bi-weekly or one-time deduction amount(s) </li>
                        <li>If deduction is a one-time only, ensure to select none under bi-weekly </li>
                        <li>If deduction is bi-weekly only, ensure to select none under one-time</li>
                    </ul>
                @include('admin-pledge.campaign.partials.amount')
            </div>
            <div class="tab-pane fade step" id="nav-selection" role="tabpanel" aria-labelledby="nav-selection-tab">

                <div class="pl-2 font-weight-bold">Step 3 - Select preferred method for choosing charities</div>

                <ul class="ml-2 pt-1">
                    <li>Choose a regional Fund Supported Pool or the CRA charity list option </li>
                </ul>


                @include('admin-pledge.campaign.partials.method-selection')

            </div>
            <div class="tab-pane fade step" id="nav-summary" role="tabpanel" aria-labelledby="nav-summary-tab">
                <div id="summary-page">
                    Step 4
                </div>
            </div>
            {{-- <div class="tab-pane fade step" id="nav-contact-1" role="tabpanel" aria-labelledby="nav-contact-tab">
                Step 4 - Etsy mixtape wayfarers, ethical wes anderson tofu before they sold out mcsweeney's organic lomo retro fanny pack lo-fi farm-to-table readymade. Messenger bag gentrify pitchfork tattooed craft beer, iphone skateboard locavore carles etsy salvia banksy hoodie helvetica. DIY synth PBR banksy irony. Leggings gentrify squid 8-bit cred pitchfork. Williamsburg banh mi whatever gluten-free, carles pitchfork biodiesel fixie etsy retro mlkshk vice blog. Scenester cred you probably haven't heard of them, vinyl craft beer blog stumptown. Pitchfork sustainable tofu synth chambray yr.
            </div> --}}
        </div>


        <div class="p-2 ">
            <button type="button" class="action cancel btn  btn-outline-secondary"
                onclick="window.location='{{ route('admin-pledge.campaign.index') }}'"
                >Cancel</button>
            <button type="button" class="action back btn  btn-outline-secondary"
                style="display: none">Back</button>
            <button type="button" class="action next btn  btn-primary ml-2"
                >Next</button>
            <button type="submit" class="action submit btn  btn-primary ml-2"
                style="display: none">Submit</button>
        </div>

        {{-- <div class="container">
          <div class="col">
            <div class="card mt-3">
              <div class="card-header font-weight-bold">My Bootstrap 5 multi-step-form</div>

                <div class="card-body p-5 step" style="">Step 1</div>

                    <div class="card-body p-5 step" style="display: none;">Step 2</div>
                    <div class="card-body p-5 step" style="display: none;">Step 3</div>
                    <div class="card-body p-5 step" style="display: none;">Step 4</div>
                    <div class="card-body p-5 step" style="display: none;">Step 5</div>

                    <div class="card-footer">
                        <button type="button" class="action back btn btn-sm btn-outline-secondary"
                            style="display: none">Back</button>
                        <button type="button" class="action next btn btn-sm btn-outline-primary float-right"
                            >Next</button>
                        <button type="button" class="action submit btn btn-sm btn-outline-success float-right"
                            style="display: none">Submit</button>
                    </div>
                </div>

            </div>
        </div> --}}


    </form>
  </div>
</div>
@endsection

@push('css')

<link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" rel="stylesheet">

<style>
    .select2-selection--multiple{
        overflow: hidden !important;
        height: auto !important;
        min-height: 38px !important;
    }

    .select2-container .select2-selection--single {
        height: 38px !important;
        }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #687278 !important;
    }

    .select2-selection--single.is-invalid {
        border-color: #e3342f ;
        padding-right: 2.19rem !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23e3342f' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23e3342f' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.4em + 0.6875rem) center;
        background-size: calc(0.8em + 0.375rem) calc(0.8em + 0.375rem);
    }

    /* tracking */
.bs4-step-tracking {
    margin-bottom: 30px;
    overflow: hidden;
    color: #878788;
    padding-left: 0px;
    margin-top: 30px
}

.bs4-step-tracking li {
    list-style-type: none;
    font-size: 13px;
    width: 25%;   /* change from 25 to 20% */
    float: left;
    position: relative;
    font-weight: 400;
    color: #878788;
    text-align: center;
    z-index: 100;
}

.bs4-step-tracking li:first-child:before {
    margin-left: 15px !important;
    padding-left: 11px !important;
    text-align: left !important
}

.bs4-step-tracking li:last-child:before {
    margin-right: 5px !important;
    padding-right: 11px !important;
    text-align: right !important
}

.bs4-step-tracking li> div {
    color: #fff;
    width: 38px;
    text-align: center;
    line-height: 38px;
    display: block;
    font-size: 18px;
    background: #878788;
    border-radius: 50%;
    margin: auto;
}

.bs4-step-tracking li:after {
    content: '';
    width: 150%;
    height: 2px;
    background: #878788 ;
    position: absolute;
    left: 0%;
    right: 0%;
    top: 20px;
    /* z-index: -1; */
     z-index: -2;
}

.bs4-step-tracking li:first-child:after {
    left: 50%;
}

.bs4-step-tracking li:last-child:after {
    left: 0% !important;
    width: 50% !important
}

.bs4-step-tracking li.active {
    font-weight: bold;
    color: #007bff; /* #dc3545 */
}

.bs4-step-tracking li.active>div {
    background: #007bff;
}

.bs4-step-tracking li.active:after {
    background: #007bff;
}

    #nav-tab li:not(.active)  a{
        pointer-events: none;
        color: #555;
    }

    #nav-tab li a.active {
        pointer-events: none;
        color: #000;
    }


    nav.nav a.nav-link.active {
        text-decoration: underline !important;
        font-weight: bold;
    }


    .summary-card .form-control[disabled] {
        border: 0;
        background-color: rgb(252, 252, 252) ;
    }

    .amount-selection input[type=radio] {
        width: 18px;
        height: 18px;
    }

    .amount-selection .form-check {
        padding-top: 4px ;
    }

    .amount-selection .form-check-label {
        padding-left: 8px;
    }

    .form-control:disabled, .form-control[readonly] {
        border: none;
        /* text-align: right; */
        background-color: #f7f7f7;
    }

    .form-control.amount:disabled, .form-control.amount[readonly] {
        text-align: right;
    }

    .form-control:focus-visible {
        outline: 2px solid #3b99fc;
        /* outline: 2px solid #000; */
    }

        
	.select2-container--default.select2-container--focus .select2-selection--single, .select2-container--default.select2-container--focus .select2-selection--multiple
	{
        outline: 2px solid #3b99fc;
        /* outline: 2px solid #000 !important; */
        border-color: #000 !important;
	} 

</style>

@endpush

@push('js')

<script src="{{ asset('vendor/select2/js/select2.min.js') }}" ></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>


<script type="x-tmpl" id="charity-tmpl">
    @include('admin-pledge.campaign.partials.charities', ['index' => 'XXX'] )
</script>

<script>

$(function () {

    // First load
    var first_load = true;
    var ods_export_done = {{ isset($pledge) && $pledge->ods_export_status ? 'true' : 'false' }};

    // For keep tracking the current page in wizard, and also count for the signle submission only
    var step = 1;
    var submit_count = 0;

    // treat browser back button like the 'back' button in this wizard page
    history.pushState(null, null, location.href);
    window.addEventListener('popstate', function(event) {
        url = this.location.href;
        if (url.indexOf('admin-pledge/campaign')) {
            current_step = $("input[type=hidden][name='step']").val();
            if (current_step == 1) {
                // back
                $(".cancel").trigger("click");
            } else {
                history.pushState(null, null, location.href);
                $('.modal').modal('hide');
                $(".back").trigger("click");
            }
        }
    });

    $(".next").on("click", function() {
        var nextstep = false;
        if (step == 1) {

            @empty($pledge) 

                calendar_year = $('#campaign_year_id option:selected').attr('calendar_year')
                Swal.fire( {
                    title: 'Are you certain that you want the pledge to apply to Calendar Year ' + calendar_year + ' ?',
                    // text: 'This action cannot be undone.',
                    // icon: 'question',
                    showDenyButton: true,
                    // showCancelButton: true,
                    confirmButtonText: 'Yes',
                    denyButtonText: 'No',
                    buttonsStyling: false,
                    //confirmButtonClass: 'btn btn-danger',
                    customClass: {
                        confirmButton: 'btn btn-primary', //insert class here
                        cancelButton: 'btn btn-danger ml-2', //insert class here
                        denyButton: 'btn btn-outline-secondary ml-2',
                    }
                    //denyButtonText: `Don't save`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        nextstep = checkForm();
                    } 
                })
            @endempty
            @isset($pledge) 
                nextstep = checkForm();
            @endisset
        } else if (step == 2) {
            nextstep = checkForm();
            recalculate_allocation();
        } else if (step == 3) {
            nextstep = checkForm();
        } else {
            nextstep = checkForm();
            // nextstep = true;
        }

        if (nextstep == true) {
            if (step < $(".step").length) {
                $(".step").show();
                $(".step")
                    .not(":eq(" + step++ + ")")
                    .hide();
                stepProgress(step);
                $('#nav-tab li:nth-child(' + step +') a').tab('show');   // Select third tab
            }
            hideButtons(step);
        }
    });

    // ON CLICK BACK BUTTON
    $(".back").on("click", function() {
        if (step > 1) {
            step = step - 2;
            $(".next").trigger("click");
        }
        hideButtons(step);
    });

    // CALCULATE PROGRESS BAR
    stepProgress = function(currstep) {

        console.log(currstep);

        var percent = parseFloat(100 / $(".step").length) * currstep;
        percent = percent.toFixed();
        // $(".progress-bar")
        //     .css("width", percent + "%")
        //     .html(percent + "%");
        //
        $('.bs4-step-tracking li').map( function (index, item) {
            if (index < currstep) {
                $(item).addClass('active');
            } else {
                $(item).removeClass('active');
            }
        });
    };

    // DISPLAY AND HIDE "NEXT", "BACK" AND "SUMBIT" BUTTONS
    hideButtons = function(step) {
        // sync variable and the hidden variable
        $("input[type=hidden][name='step']").val( step );

        var limit = parseInt($(".step").length);
        $(".action").hide();
        $(".cancel").hide();
        if (step < limit) {
            $(".next").show();
        }
        if(step==1) {
            $(".cancel").show();
        }
        if (step > 1) {
            $(".back").show();
        }
        if (step == limit) {
            $(".next").hide();
            $(".submit").show();
        }

        // scroll to top
        $(window).scrollTop(0);
        $('button.go-back-btn').focus();
    };

    // Validation when click on 'next' button
    function checkForm() {

        if (ods_export_done) {
            $('#nav-amount input:disabled').prop("disabled", false);
        }

        // reset submission count
        submit_count = 0;

        var valid = true;
            // array for the fields in the form (for clean up previous errors)
            var fields = [];
            if (step == 1) {
                fields = ['campaign_year_id','organization_id', 'user_id', 'pecsf_id', 'pecsf_first_name', 'pecsf_last_name', 'pecsf_city','user_office_city'];
            }
            if (step == 2) {
                fields = ['pay_period_amount_other', 'one_time_amount_other'];
            }
            if (step == 3) {
                fields = ['pool_option', 'pool_id'];
            }

            $.each( fields, function( index, field_name ) {
                $('#admin-pldege-campaign-form [name='+ field_name +']').nextAll('span.text-danger').remove();
                $('#admin-pldege-campaign-form [name='+ field_name +']').removeClass('is-invalid');
            });
            $('#admin-pldege-campaign-form [name="charities[]"]').nextAll('span.text-danger').remove();
            $('#admin-pldege-campaign-form [name="charities[]"]').removeClass('is-invalid');
            $('#admin-pldege-campaign-form [name="percentages[]"]').nextAll('span.text-danger').remove();
            $('#admin-pldege-campaign-form [name="percentages[]"]').removeClass('is-invalid');
            $('#admin-pldege-campaign-form [name="additional[]"]').nextAll('span.text-danger').remove();
            $('#admin-pldege-campaign-form [name="additional[]"]').removeClass('is-invalid');
            // Amount field 
            $('.pay_period_amount_error').html('');
            $('.one_time_amount_error').html('');

            var form = $('#admin-pldege-campaign-form');
            $('#admin-pldege-campaign-form input[name=step]').val( step );

            $.ajax({
                method: "POST",
                url : "{{ route('admin-pledge.campaign.store') }}",
                //data: form.serialize(),
                data: form.find(':not(input[name=_method])').serialize(),  // serializes the form's elements exclude _method.
                async: false,
                cache: false,
                timeout: 30000,
                success: function(data)
                {
                    if(data && data.indexOf('body class="login-page"') != -1){
                        window.location.href = '/login';
                    }

                    // console.log(data );
                    if (step == 3)  {
                            $('#summary-page').html(data);
                    }
                },
                error: function(response) {
                    valid = false;
                    if (response.status == 422) {
                        i = 0;
                        $.each(response.responseJSON.errors, function(field_name,error){
                            error_field_name = null;

                            if ( field_name.includes('.') ) {
                                items = field_name.split(".");
                                pos = Number(items[ items.length -1 ]);
                                error_field_name = $(document).find('[name="' + items[0] + '[]"]:eq(' + pos + ')')
                                // $(document).find('[name="' + items[0] + '[]"]:eq(' + pos + ')').parent().append('<span class="text-strong text-danger">' +error+ '</span>');
                                // $(document).find('[name="' + items[0] + '[]"]:eq(' + pos + ')').addClass('is-invalid');
                            } else {
                                if (field_name.includes('pay_period_amount_error')) {
                                    $('.pay_period_amount_error').append('<span id="'+field_name+'-error" role="alert" class="text-strong text-danger">' +error+ '</span>');
                                    $('input[name=pay_period_amount]:checked').focus();
                                    i++;
                                } else if (field_name.includes('one_time_amount_error')) {
                                    $('.one_time_amount_error').append('<span id="'+field_name+'-error" role="alert" class="text-strong text-danger">' +error+ '</span>');
                                    $('input[name=pay_period_amount]:checked').focus();
                                    i++;
                                } else {
                                    error_field_name = $(document).find('[name=' + field_name + ']');
                                }
                            }

                            // Append error message and set in-valid
                            if (error_field_name) {
                                $(error_field_name).parent().append('<span id="'+error_field_name.attr('name')+'-error" role="alert" class="text-strong text-danger">' +error+ '</span>');
                                if ($(error_field_name).hasClass('select2')) {
                                    $(error_field_name).parent().find("[role='combobox']").addClass('is-invalid');
                                    $(error_field_name).parent().find("[role='combobox']").attr('aria-describedby', error_field_name.attr('name') +'-error');  
                                    $(error_field_name).parent().find("[role='combobox']").attr('aria-labelledby', error_field_name.attr('name') +'-error');  
                                    $(error_field_name).parent().find("[role='combobox']").attr("aria-invalid","true");
                                } else {
                                    $(error_field_name).addClass('is-invalid');
                                    $(error_field_name).attr('aria-describedby', error_field_name.attr('name') + '-error');  
                                }
                            }

                            // set focus on the first error
                            if (i == 0) {
                                $(error_field_name).focus();
                            }
                            i++;

                            // additional checking for pledge existence
                            code = $("select[name='organization_id'] option:selected").attr('code');

                            if (step == 1 && field_name == 'campaign_year_id' && code != 'GOV') {

                                pledge_id = get_campaign_pledge_id();
                                if (pledge_id > 0) {
                                    $(document).find('[name=' + field_name + ']').parent().append('<span class="d-block text-strong text-danger">' +
                                        'There is an existing pledge for this donor. Would you like to change it? Click <a ' +
                                        'href="/admin-pledge/campaign/'+pledge_id+'/edit">here</a> to proceed.' + '</span>');
                                }
                            }

                        })
                    }
                    if (response.status == 401 || response.status == 419) {
                        // session expired 
                        window.location.href = '/login'; 
                    }
                    console.log('Error');
                }
            });

        if (step <= 2 && ods_export_done) {
            $('#nav-amount input').prop("disabled", true);
        }

        return valid;
    }

    // On page 1 - reset Donor/User Profile
    function reset_user_profile_info() {
        $('#user_first_name').val('');
        $('#user_last_name').val('');
        $('#user_email').val('');
        $('#user_emplid').val('');
        $('#user_dept').val('');
        $('#user_bu').val('');
        $('#user_org').val('');
        $('#user_office_city').val('');
        $('#user_region').val('');
    }

    function reset_pecsf_profile_info() {
        $('#pecsf_id').val('');
        $('#pecsf_first_name').val('');
        $('#pecsf_last_name').val('');
        $('#pecsf_city').val('');
        $('#pecsf_bu').val('');
        $('#pecsf_region').val('');
    }

    $('#organization_id').change( function() {

        pledge_id = $('#pledge_id').val();
        if (!pledge_id) {
            reset_user_profile_info();
        }

        code = $("select[name='organization_id']").find(":selected").attr('code');
        if (code == 'GOV') {
            $('.emplid_section').show();
            $('.pecsf_id_section').hide();

            $('#user_id').val(null).trigger('change');            
        } else {
            $('.emplid_section').hide();
            $('.pecsf_id_section').show();

            if (pledge_id && first_load) {
                // No action
            } else {
                reset_pecsf_profile_info();
                get_nongov_user_detail();
            }
            first_load = false;
        }

    });

    $('#user_id').select2({
        allowClear: true,
        placeholder: "Select an employee",
        ajax: {
            url: '{{ route('admin-pledge.administrators.users') }}'
            , dataType: 'json'
            , delay: 250
            , data: function(params) {
                var query = {
                     'org_id' : $('#organization_id').val(),
                    'q': params.term
                , }
                return query;
            }
            , processResults: function(data) {
                return {
                    results: data
                    };
            }
            , cache: false
            
        }
    });

    // $('#user_id').on('select2:open', function (e) {
    //     $('#select2-user_id-results').attr('aria-labelledby', "user_id_label"); // Clear this an don't want to use it

    //     // $('.emplid_section .select2-selection').attr('aria-label', 'Select employee');
    // });
        
    $('#user_id').on('select2:select', function (e) {
        var data = e.params.data;

        reset_user_profile_info();
        if (data.emplid) {
            $('#user_first_name').val( data.first_name );
            $('#user_last_name').val( data.last_name );
            $('#user_email').val( data.email);
            $('#user_emplid').val( data.emplid );
            $('#user_dept').val( data.department );
            $('#user_bu').val( data.business_unit );
            $('#user_org').val( data.organization);
            $('#user_office_city').val( data.office_city);
            $('#user_region').val(data.region);
        }

    });

    $('#user_id').on('select2:unselect', function (e) {
        var data = e.params.data;
            reset_user_profile_info();
    });

    $('#pecsf_city').change( function() {
        region = $('#pecsf_city option:selected').attr('data-region');
        $("#pecsf_region").val( region );
    });

    function get_campaign_pledge_id()
    {
        pledge_id = 0;
        $.get({
            url: '{{ route('admin-pledge.administrators.pledgeid') }}' +
                        '?org_id=' + $('#organization_id').val() +
                        '&campaign_year_id=' + $('#campaign_year_id').val() +
                        '&user_id=' + $('#user_id').val() +
                        '&pecsf_id=' + $('#pecsf_id').val(),
            dataType: 'json',
            async: false,
            cache: false,
            timeout: 30000,
            success: function(data)
            {
                // console.log( data.id );
                pledge_id =  data.id;
            },
            error: function(response) {
                 console.log('Error');
            }
        });

        return pledge_id;

    }

    function get_nongov_user_detail() {

        // clean up the old values
        $('#pecsf_first_name').val('');
        $('#pecsf_last_name').val('');
        $('#pecsf_city').val('');
        $('#pecsf_bu').val('');
        $('#pecsf_region').val('');
        

        $.get({
            url: '{{ route('admin-pledge.administrators.nongovuser') }}' +
                        '?org_id=' + $('#organization_id').val() +
                        '&pecsf_id=' + $('#pecsf_id').val(),
            dataType: 'json',
            async: false,
            cache: false,
            timeout: 30000,
            success: function(data)
            {
                console.log( data );
                if(data) {
                    $('#pecsf_first_name').val( data.first_name );
                    $('#pecsf_last_name').val( data.last_name );
                    $('#pecsf_city').val( data.city );
                    $('#pecsf_bu').val(data.pecsf_bu);
                    $('#pecsf_region').val(data.pecsf_region);
                }
            },
            error: function(response) {
                 console.log('Error');
            }
        });
    }

    $('#pecsf_id').on('blur', function (e) {
        e.stopPropagation();
        get_nongov_user_detail();
    })

    $('#pecsf_id').on('keypress', function (e) {
        e.stopPropagation();

        var keycode = (e.keyCode ? e.keyCode : e.which);
        if(keycode == '13') {
            // console.log('enter pressed - ' +  this.value);
            get_nongov_user_detail();
        }
    })



    function reallocate_charity_amount( elem ) {

        // Calculate bi-weekly amount
        pay_period_amount = $("input[name='pay_period_amount']:checked").val();
        pay_period_amount_other = $("input[name='pay_period_amount_other']").val();
        pay_period_amt = Math.max(pay_period_amount, pay_period_amount_other);
        $('#pay_period_figure').html( '$ ' + parseFloat(pay_period_amt).toFixed(2) );

        if($.isNumeric(pay_period_amt)){
                pay_period_amt_allocated = parseFloat( pay_period_amt * elem.value / 100).toFixed(2) ;
                $(elem).closest('div.form-row').find("input[name='pay_period_allocated_amount[]']").val( pay_period_amt_allocated );
         } else {
             //Not a number
         }

        // Calculate one-time amount
        one_time_amount = $("input[name='one_time_amount']:checked").val();
        one_time_amount_other = $("input[name='one_time_amount_other']").val();
        one_time_amt = Math.max(one_time_amount, one_time_amount_other);
        $('#one_time_figure').html( '$ ' + parseFloat(one_time_amt).toFixed(2) );

        if($.isNumeric(one_time_amt)){
                one_time_amt_allocated = parseFloat( one_time_amt * elem.value / 100).toFixed(2) ;
                $(elem).closest('div.form-row').find("input[name='one_time_allocated_amount[]']").val( one_time_amt_allocated );
         } else {
             //Not a number
         }

    }

    function recalculate_allocation() {
        $("#method-selection-2 input[name='percentages[]'").each(function() {
            //$( this ).toggleClass( "example" );
            console.log( this );
            reallocate_charity_amount( this );
        });
    }


    $("input[name=pay_period_amount_other]").focus(function() {
        $("input[name=pay_period_amount][value='']").prop('checked', true);
        recalculate_allocation();
    });

    $("input[name=pay_period_amount]").change(function() {
        $("input[name=pay_period_amount_other]").val('');
        if (this.id == 'pay_period_amt_5') {
            $("input[name=pay_period_amount_other]").attr('tabindex', '0');
            $("input[name=pay_period_amount_other]").attr('aria-required','true');
        } else {
            $("input[name=pay_period_amount_other]").attr('tabindex', '-1');
            $("input[name=pay_period_amount_other]").attr('aria-required','false');
        }
        recalculate_allocation();
    });

    $("input[name=one_time_amount_other]").focus(function() {
        $("input[name=one_time_amount][value='']").prop('checked', true);
        recalculate_allocation();
    });

    $("input[name=one_time_amount]").change(function() {
        $("input[name=one_time_amount_other]").val('');
        if (this.id == 'one_time_amount_5') {
            $("input[name=one_time_amount_other]").attr('tabindex', '0');
            $("input[name=one_time_amount_other]").attr('aria-required','true');
        } else {
            $("input[name=one_time_amount_other]").attr('tabindex', '-1');
            $("input[name=one_time_amount_other]").attr('aria-required','false');
        }
        recalculate_allocation();
    });


    // Page 3
    // function to initialize select2 dynamic
    function initializeSelect2(selectElementObj) {
        selectElementObj.select2({
            placeholder: 'select charity',
            allowClear: true,
            ajax: {
                url: '/settings/fund-supported-pools/charities'
                , dataType: 'json'
                , delay: 250
                , data: function(params) {
                    var query = {
                        'q': params.term
                    , }
                    return query;
                }
                , processResults: function(data) {
                    return {
                        results: data
                        };
                }
                , cache: false
            }
        });
    }


    $(document).on("click", "button[type='submit']", function(e) {

        // this.disabled = true;
        $("#admin-pldege-campaign-form").submit(function(e){
            if(submit_count > 0){
                e.preventDefault();
            }
            submit_count++;
        });
    });

    //onload: call the above function
    $("select[name='charities[]']").each(function() {
        initializeSelect2($(this));
    });

    $(document).on("select2:select", "select[name='charities[]']", function(e) {
        $(e.target).closest('td').find("input[name='additional[]']").val('');
    });

    $(document).on("select2:clear", "select[name='charities[]']", function(e) {
        $(e.target).closest('td').find("input[name='additional[]']").val('');
    });

    // variable for keep track detail lines
    let row_number = {{ (isset($pledge)) ? $pledge->distinct_charities->count() + 1 : 1 }};

    $("#add_row").click(function(e){
        e.preventDefault();
        let new_row_number = row_number - 1;

        text = $("#charity-tmpl").html();
        text = text.replace(/XXX/g, row_number);
        text = text.replace(/YYY/g, row_number + 1);
        $('#charity' + row_number).html( text );

        // Initialize select2 on new add row
        $('#charity' + row_number).find("select[name='charities[]']").each(function() {
            initializeSelect2($(this));
        });
        $(document).find('#charity' + row_number + ' span.select2-selection').eq(0).focus();

        $('#charity-table').append('<tr id="charity' + (row_number + 1) + '"></tr>');
        row_number++;

    });


    $(document).on("click", "#nav-selection button.delete_this_row" , function(e) {
        e.preventDefault();

        if (step == 3) {
            row_count = $('#charity-table tr').length;

            if (row_count > 2) {
                Swal.fire({
                    text: 'Are you sure to delete this line ?'  ,
                    // icon: 'question'
                    //showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    //denyButtonText: `Don't save`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        // Swal.fire('Saved!', '', '')
                        //$(this).parent().parent().parent().parent().remove();


                        el = '#' + $(this).attr('data-id');

                        // refocus to next item
                        next_el = $(el).next();
                        if (next_el && $(next_el).find('button.delete_this_row').length > 0 ) {
                            $(next_el).find('span.select2-selection').eq(0).focus();
                        } else {
                            $('#add_row').focus();
                        }

                        $(el).remove();

                    }
                })
            } else {
                Swal.fire({
                    icon: "error",
                    // title: "Oops...",
                    text: "At minimum, one charity needs to be defined",
                });
            }
        }
    });

    $(document).on("keyup", "input[name='percentages[]']", function(e) {
        reallocate_charity_amount( e.target );
    });

    $(document).ready(function() {
        $("#organization_id").trigger("change"); // Initial the page when 1st laoded
    });

});


// Cancel
@if($canCancel)

    $(function () {

        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        });

        $(document).on("click", ".cancel-pledge" , function(e) {
            e.preventDefault();

            id = {{ $pledge->id }};
            // title = $pledge->id;

            Swal.fire( {
                title: 'Are you sure you want to cancel the pledge "' + id + '" ?',
                text: 'This action cannot be undone.',
                // icon: 'question',
                showDenyButton: true,
                // showCancelButton: true,
                confirmButtonText: 'Yes',
                denyButtonText: 'No',
                buttonsStyling: false,
                //confirmButtonClass: 'btn btn-danger',
                customClass: {
                	confirmButton: 'btn btn-primary', //insert class here
                    cancelButton: 'btn btn-danger ml-2', //insert class here
                    denyButton: 'btn btn-outline-secondary ml-2',
                }
                //denyButtonText: `Don't save`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    // Swal.fire('Saved!', '', '')
                    $.ajax({
                        method: "POST",
                        url:  '/admin-pledge/campaign/' + id + '/cancel',
                        success: function(data)
                        {
                            // oTable.ajax.reload(null, false);	// reload datatables
                            // Toast('Success', 'Pledge ' + id +  ' was successfully cancel.', 'bg-success' );
                            console.log(data );
                            window.location = '{{ route("admin-pledge.campaign.index") }}';

                        },
                        error: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.responseJSON.error,
                            })
                            console.log(response.responseJSON.error);
                        }
                    });
                } else if (result.isCancelledDenied) {
                    // Swal.fire('Changes are not saved', '', '')
                }
            })

        });

    });
@endif

</script>

@endpush
