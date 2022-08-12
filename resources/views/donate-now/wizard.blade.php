@extends('adminlte::page')

@section('content_header')
@endsection


@section('content')
<div class="container mt-1">
    <div class="row">
        <div class="col-9 col-sm-9">
            <h1>Make a Donation</h1>
            <p class="text-muted">When you give through PECSF 100% of your donated dollars goes to the organizations you choose to support.</p>

            
            {{-- Main Content --}}
            <div class="card pb-4">

                {{-- Wizard Progress bar (stepper) --}}
                <div class="card-header border-0 p-0">
                    <div class=" card-timeline px-2 border-0">
                        <ul class="bs4-step-tracking">
                            <li class="active">
                                <div><i class="fas fa-random fa-2xl"></i></div>Pool or Non-Pool
                            </li>
                            <li class="">
                                <div><i class="fas fa-bars fa-2xl"></i></div>In Support Of
                            </li>
                            <li class="">
                                <div><i class="fas fa-dollar-sign  fa-2xl"></i></div>Amount
                            </li>
                            <li class="">
                                <div><i class="fas fa-check fa-2xl"></i></div>Review and Submit
                            </li>
                        </ul>
                    </div>
                </div>
            
              <div class="card-body py-0">
                <form action="{{ isset($pledge) ? route("donate-now.update", $pledge->id) : route("donate-now.store") }}" 
                        id="admin-pldege-campaign-form" method="POST">
                    @csrf
                    @isset($pledge)
                        @method('PUT')
                        <input type="hidden" id="pledge_id" name="pledge_id" value="{{ $pledge->id }}">
                    @endisset
                    <input type="hidden" id="step" name="step" value="">
                    <input type="hidden" id="yearcd" name="yearcd" value="{{ $yearcd }}">
                    
                    {{-- Nav Items --}}
                    <ul class="nav nav-tabs" id="nav-tab" role="tablist" style="display:none;">
                        <li class="nav-item">
                          <a class=" nav-link active" id="nav-selection-tab" data-toggle="tab" href="#nav-selection" data-id="0" role="tab" aria-controls="nav-selection" aria-selected="false">selection</a>
                        </li>
                        <li class="nav-item ">
                            <a class=" nav-link" id="nav-in-support-of-tab" data-toggle="tab" href="#nav-in-support-of" data-id="1" role="tab" aria-controls="nav-in-support-of" aria-selected="false">In Support Of</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-amount-tab" data-toggle="tab" href="#nav-amount" data-id="2" role="tab" aria-controls="nav-amount" aria-selected="false">Amount</a>
                        </li>
                        <li class="nav-item">  
                          <a class=" nav-link " id="nav-summary-tab" data-toggle="tab" href="#nav-summary" data-id="3" role="tab" aria-controls="nav-summary" aria-selected="false">Summary</a>
                        </li>
                    </ul>
                
                    <div class="tab-content pb-3 px-1" id="nav-tabContent">
                        <div class="tab-pane fade step show active" id="nav-selection" role="tabpanel" aria-labelledby="nav-selection-tab">
                            {{-- Step 1 --}}
                            @include('donate-now.partials.method-selection')
                        </div>

                        <div class="tab-pane fade step" id="nav-in-support-of" role="tabpanel" aria-labelledby="nav-in-support-of-tab">
                            {{-- Step 2 --}}
                            <span id="pool-selection-section">
                                @include('donate-now.partials.regional-pool')
                            </span>

                            <span id="charity-selection-section">
                                {{-- @include('donate-now.partials.choose-charity') --}}
                            </span>
                        </div>

                        <div class="tab-pane fade step" id="nav-amount" role="tabpanel" aria-labelledby="nav-amount-tab">
                           
                            {{-- Step 3 --}}

                            @include('donate-now.partials.amount')
            
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
                        <button type="button" class="action back btn  btn-outline-secondary"
                            style="display: none">Back</button>
                        <button type="button" class="action next btn  btn-outline-primary float-right"
                            >Next</button>
                        <button type="submit" class="action submit btn  btn-outline-success float-right"
                            style="display: none">Submit</button>
                    </div>

                </form>
              </div>
            </div>


        </div>
        <div class="col-3 col-sm-3">
            <img src="{{ asset('img/donor.png') }}" alt="Group of volunteers making wreaths at a table" class="py-5 img-fluid">
        </div>

        <div class="col-9 col-sm-9">

        </div>

    </div>
</div>

@endsection

@push('css')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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


</style>

@endpush

@push('js')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

{{-- <script type="x-tmpl" id="charity-tmpl">
    @include('admin-pledge.campaign.partials.charities', ['index' => 'XXX'] )
</script> --}}

<script>

$(function () {

    // For keep tracking the current page in wizard, and also count for the signle submission only
    var step = 1;
    var submit_count = 0;
   
    $(".next").on("click", function() {
        var nextstep = false;
        if (step == 1) {
            nextstep = checkForm();
        } else if (step == 2) {
            nextstep = checkForm();
            // recalculate_allocation();
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

        // console.log(currstep);

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
        var limit = parseInt($(".step").length);
        $(".action").hide();
        if (step < limit) {
            $(".next").show();
        }
        if (step > 1) {
            $(".back").show();
        }
        if (step == limit) {
            $(".next").hide();
            $(".submit").show();
        }

        // display/hide either Pool or Chariry section
        pool_option =  $("input[name='pool_option']:checked").val();
        if (pool_option == 'C') {
            $("#pool-selection-section").hide();
            $("#charity-selection-section").show();
        } else {
            $("#pool-selection-section").show();
            $("#charity-selection-section").hide();
        }

    };

    // Validation when click on 'next' button
    function checkForm() {

        // reset submission count 
        submit_count = 0;
        
        var valid = true;
            // array for the fields in the form (for clean up previous errors)
            var fields = [];
            if (step == 1) {
                fields = ['campaign_year_id','organization_id', 'user_id', 'pecsf_id', 'pecsf_first_name', 'pecsf_last_name', 'pecsf_city'];
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
            $('#admin-pldege-campaign-form [name="percentages[]"]').nextAll('span.text-danger').remove();

            var form = $('#admin-pldege-campaign-form');
            $('#admin-pldege-campaign-form input[name=step]').val( step );

            $.ajax({
                method: "POST",
                url:  '{{ route("donate-now.store") }}', 
                //data: form.serialize(), 
                data: form.find(':not(input[name=_method])').serialize(),  // serializes the form's elements exclude _method.
                async: false,
                cache: false,
                timeout: 30000,
                success: function(data)
                {
                    // console.log(data ); 
                    if (step == 3)  {
                            $('#summary-page').html(data); 
                    }
                },
                error: function(response) {
                    valid = false;
                    if (response.status == 422) {   
                        // $.each(response.responseJSON.errors, function(field_name,error){
                        //     if ( field_name.includes('.') ) {   
                        //         items = field_name.split(".");
                        //         pos = Number(items[ items.length -1 ]);
                        //         $(document).find('[name="' + items[0] + '[]"]:eq(' + pos + ')').parent().append('<span class="text-strong text-danger">' +error+ '</span>');
                        //         $(document).find('[name="' + items[0] + '[]"]:eq(' + pos + ')').addClass('is-invalid');
                        //     } else {
                        //         $(document).find('[name=' + field_name + ']').parent().append('<span class="text-strong text-danger">' +error+ '</span>');
                        //         $(document).find('[name=' + field_name + ']').addClass('is-invalid');
                        //     }
                        // })
                    }
                    console.log('Error');
                }
            });

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
        $('#user_region').val('');  
    }

    $('#organization_id').change( function() {

        pledge_id = $('#pledge_id').val();
        if (!pledge_id) {
            reset_user_profile_info();
        }
        $('#user_id').val(null).trigger('change');
        
        code = $("select[name='organization_id']").find(":selected").attr('code');
        if (code == 'GOV') {
            $('.emplid_section').show();   
            $('.pecsf_id_section').hide();
        } else {
            $('.emplid_section').hide();   
            $('.pecsf_id_section').show();
        }

    });

    $('#user_id').select2({
        allowClear: true,
        placeholder: "Type employee ID",
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
            $('#user_region').val(data.region);
        }
    });

    $('#user_id').on('select2:unselect', function (e) {
        var data = e.params.data;
            reset_user_profile_info();            
    });


    function get_nongov_user_detail() {

        // clean up the old values 
        $('#pecsf_first_name').val('');
        $('#pecsf_last_name').val('');
        $('#pecsf_city').val('');
        
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


    // Page 2 -- Amount
    function reallocate_charity_amount( elem ) {

        // Calculate bi-weekly amount
        pay_period_amount = $("input[name='pay_period_amount']:checked").val();
        pay_period_amount_other = $("input[name='pay_period_amount_other']").val();
        pay_period_amt = Math.max(pay_period_amount, pay_period_amount_other);
        $('#pay_period_figure').html( parseFloat(pay_period_amt).toFixed(2) );

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
        $('#one_time_figure').html( parseFloat(one_time_amt).toFixed(2) );
        
        if($.isNumeric(one_time_amt)){
                one_time_amt_allocated = parseFloat( one_time_amt * elem.value / 100).toFixed(2) ;
                $(elem).closest('div.form-row').find("input[name='one_time_allocated_amount[]']").val( one_time_amt_allocated );
         } else {
             //Not a number
         }

    }

    // function recalculate_allocation() {
    //     $("#method-selection-2 input[name='percentages[]'").each(function() {
    //         //$( this ).toggleClass( "example" );
    //         console.log( this );
    //         reallocate_charity_amount( this );
    //     });
    // }


    // $("input[name=pay_period_amount_other]").focus(function() {
    //     $("input[name=pay_period_amount][value='']").prop('checked', true);
    //     recalculate_allocation();
    // });

    // $("input[name=pay_period_amount]").change(function() {
    //     $("input[name=pay_period_amount_other]").val('');
    //     recalculate_allocation();
    // });

    // $("input[name=one_time_amount_other]").focus(function() {
    //     $("input[name=one_time_amount][value='']").prop('checked', true);
    //     recalculate_allocation();
    // });

    // $("input[name=one_time_amount]").change(function() {
    //     $("input[name=one_time_amount_other]").val('');
    //     recalculate_allocation();
    // });


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

    // Page 4 -- summary (handle single submission only )
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

    

});

</script>

{{-- @include('donate-now.partials.choose-charity-js') --}}

@endpush