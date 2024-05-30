@extends('adminlte::page')

@section('content_header')
@endsection

@section('content')

<div class="container mt-1">
    <div class="row">
        <div class="col-8 col-sm-8">
            @if ($is_renew) 
                <h1>Renew your Volunteer</h1>
            @else
                <h1>Register as a Volunteer</h1>
            @endif
            <p style="color: #687278;">"Volunteers do not necessarily have the time; they just have the heart." ~ Elizabeth Andrew</p>


            {{-- Main Content --}}
            {{-- <div class="card pb-4"> --}}

                {{-- Wizard Progress bar (stepper) --}}
                {{-- <div class="card-header border-0 py-2"> --}}
                    <div class=" card-timeline px-2 border-0" style="display:block;">
                        <ul class="bs4-step-tracking">
                            <li class="active" tabindex="0">
                                <div><i class="fas fa-id-badge fa-2xl"></i></div>Volunteer Details
                            </li>
                            <li class="" tabindex="-1">
                                <div><i class="fas fa-bars fa-2xl"></i></div>Recognition Items
                            </li>
                            <li class="" tabindex="-1">
                                <div><i class="fas fa-check fa-2xl"></i></div>Review and Submit
                            </li>
                        </ul>
                    </div>
                {{-- </div> --}}

                {{-- <div id="error-message" class="m-4 p-3 alert alert-warning" style="display:none"></div> --}}

            <div class="card-body py-0">
                <form action="{{ (isset($profile) && $profile->id) ? route("volunteering.profile.update", $profile->id) : route("volunteering.profile.store") }}"
                        id="volunteer-profile-form" method="POST">
                    @csrf
                    @if (isset($profile) && $profile->id)
                        @method('PUT')
                        <input type="hidden" id="profile_id" name="profile_id" value="{{ $profile->id }}">
                    @endisset
                    <input type="hidden" id="step" name="step" value="">
                    <input type="hidden" id="campaign_year" name="campaign_year" value="{{ today()->year }}">
                    @if ($is_renew) 
                        <input type="hidden" id="is_renew" name="is_renew" value="Y">
                    @endif
                    

                    {{-- Nav Items --}}
                    <ul class="nav nav-tabs" id="nav-tab" role="tablist" style="display:none;">
                        <li class="nav-item">
                          <a class=" nav-link active" id="nav-selection-tab" data-toggle="tab" href="#nav-selection" data-id="0" role="tab" aria-controls="nav-selection" aria-selected="false">selection</a>
                        </li>
                        <li class="nav-item ">
                            <a class=" nav-link" id="nav-in-support-of-tab" data-toggle="tab" href="#nav-in-support-of" data-id="1" role="tab" aria-controls="nav-in-support-of" aria-selected="false">In Support Of</a>
                        </li>
                        <li class="nav-item">
                          <a class=" nav-link " id="nav-summary-tab" data-toggle="tab" href="#nav-summary" data-id="3" role="tab" aria-controls="nav-summary" aria-selected="false">Summary</a>
                        </li>
                    </ul>

                    <div class="tab-content pb-3 px-1" id="nav-tabContent">
                        <div class="tab-pane fade step show active" id="nav-selection" role="tabpanel" aria-labelledby="nav-selection-tab">
                            {{-- Step 1 --}}
                            @include('volunteer-profile.partials.volunteer-details')
                        </div>

                        <div class="tab-pane fade step" id="nav-in-support-of" role="tabpanel" aria-labelledby="nav-in-support-of-tab">
                            {{-- Step 2 --}}
                            <span id="pool-selection-section">
                                @include('volunteer-profile.partials.recognition-items')
                            </span>
                        </div>

                        <div class="tab-pane fade step" id="nav-summary" role="tabpanel" aria-labelledby="nav-summary-tab">
                            <div id="summary-page">
                                Step 3
                            </div>
                        </div>

                    </div>


                    <div class="p-2 ">
                        <button type="button" class="action cancel btn btn-lg btn-outline-primary"
                            onclick="window.location='{{ route('volunteering.index') }}'"
                        >Cancel</button>
                        <button type="button" class="action back btn btn-lg btn-outline-primary"
                            style="display: none">Back</button>
                        <button type="button" class="action next btn btn-lg btn-primary ml-1"
                            >Next</button>
                        <button type="submit" class="action submit btn btn-lg btn-primary ml-1"
                            style="display: none">Register</button>
                    </div>

                </form>
            </div>
            {{-- </div> --}}


        </div>
        <div class="col-4 col-sm-4">
            <img src="{{ asset('img/donor.png') }}" alt="Group of volunteers making wreaths at a table" class="py-5 img-fluid">
        </div>

    </div>
</div>

@endsection

@push('css')
<link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
{{-- <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap4.css" rel="stylesheet"> --}}
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
    color:  #b2b2b2;  /* #878788 ; */
    padding-left: 0px;
    margin-top: 30px;
}

.bs4-step-tracking li {
    list-style-type: none;
    font-size: 13px;
    width: 25%;   /* change from 25 to 20% */
    float: left;
    position: relative;
    font-weight: 500;
    color:  #687278;  /* #878788 ; */
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
.bs4-step-tracking li.active> div {
        color: #fff;  
}

.bs4-step-tracking li> div {
    color: #687278;
    width: 38px;
    text-align: center;
    line-height: 38px;
    display: block;
    font-size: 18px;
    background: #b2b2b2;  /* #878788 ; */
    border-radius: 50%;
    margin: auto;
}

.bs4-step-tracking li:after {
    content: '';
    width: 150%;
    height: 2px;
    background: #dadada;  /* #878788 ; */
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
    color: #1a5a96; /* #dc3545 */
}

.bs4-step-tracking li.active:hover {
    cursor: pointer;
}    

.bs4-step-tracking li.active>div {
    background: #1a5a96;
}

.bs4-step-tracking li.active:after {
    background: #1a5a96;
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

    /* Temporaily override the app.scss*/
    .btn:focus, button:focus{
        border: #fff 2px solid!important;
    }

    .custom.pagination .page-item {
        margin-top: 0;
        border: #a2adb7 1px solid;
    }

    .custom.pagination a {
        text-decoration: none !important;
    }


    /* Should be override in app.scss */
    a {
        text-decoration: underline !important; 
    }

    .form-control[type='search']:focus-visible {
        outline: 2px solid #000;
    }

    hr {
        border: none;
        height: 2px;
        /* Set the hr color */
        color: #ccc;  /* old IE */
        background-color: #ccc;  Modern Browsers */
    }

</style>

@endpush

@push('js')

<script src="{{ asset('vendor/select2/js/select2.min.js') }}" ></script>

{{-- <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script> --}}
<script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

<script>

$(function () {

    // treat browser back button like the 'back' button in this wizard page
    history.pushState(null, null, location.href);
    window.addEventListener('popstate', function(event) {
        url = this.location.href;
        if (url.indexOf('donate-now/create')) {
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
    
    // prevent spacebar to trigger the page scrolling
    $('#volunteer-profile-form').on("keypress", function(e) {
        var $focusElem = $(":focus");
        if (e.which == 13 && $focusElem.is("input"))
            e.preventDefault();
    });

    // prevent spacebar to trigger the page scrolling
    $('#volunteer-profile-form').on("keypress", function(e) {
        var $focusElem = $(":focus");
        if (e.which == 32 && !($focusElem.is("input") || $focusElem.attr("contenteditable") == "true"))
            e.preventDefault();
    });

    // $('#volunteer-profile-form').on('keyup keypress', function(e) {
    //     var keyCode = e.keyCode || e.which;
    //     if (keyCode === 13) {
    //         e.preventDefault();
    //         return false;
    //     }
    // });

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

            $(window).scrollTop(0);
            hideButtons(step);
        }
    });

    // ON CLICK BACK BUTTON
    $(".back").on("click", function() {
        if (step > 1) {
            step = step - 2;
            // $(".next").trigger("click");
        }

        // to show current and hide other tabs
        if (step < $(".step").length) {
            $(".step").show();
            $(".step")
                .not(":eq(" + step++ + ")")
                .hide();
            stepProgress(step);
            $('#nav-tab li:nth-child(' + step +') a').tab('show');   // Select third tab
        }

        hideButtons(step);
        $(this).blur();
    });

    // Enter or space key on Wizard STEP icon to forward and backward 
    $('ul.bs4-step-tracking li').on('keyup', function(e) {
        var key  = e.key;
        if (key === ' ' || key === 'Enter') {
            e.preventDefault();
            $(this).trigger('click');
        }
    });

    // Click on Wizard STEP icon to jump to visited  page
    $('ul.bs4-step-tracking li').on('click', function(e) {

        if ($(this).hasClass('active') && ($(this).index() + 1 != step) ) {
            step = $(this).index();
            // $(".next").trigger("click");

            // to show current and hide other tabs
            if (step < $(".step").length) {
                $(".step").show();
                $(".step")
                    .not(":eq(" + step++ + ")")
                    .hide();
                stepProgress(step);
                $('#nav-tab li:nth-child(' + step +') a').tab('show');   // Select third tab
            }

            // Update nav buttons
            hideButtons(step);
            $(this).blur();
        }

    })

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

        // sync variable and the hidden variable
        $("input[type=hidden][name='step']").val( step );

        var limit = parseInt($(".step").length);
        $(".action").hide();
        if (step < limit) {
            $(".next").show();
            $(".cancel").hide();
        }
        if (step == 1) {
            $(".cancel").show();
        }
        if (step > 1) {
            $(".back").show();
        }
        if (step == limit) {
            $(".next").hide();
            $(".submit").show();
        }

        // reset tabindex on wizard 
        $('ul.bs4-step-tracking li').attr('tabindex', -1);
        $('ul.bs4-step-tracking li.active').attr('tabindex',0);

        // scroll to top
        $(window).scrollTop(0);

    };

    // Validation when click on 'next' button
    function checkForm() {

        // reset submission count
        submit_count = 0;

        var valid = true;
            // array for the fields in the form (for clean up previous errors)
            var fields = [];
            if (step == 1) {
                fields = ['business_unit_code', 'no_of_years', 'preferred_role' ];
            }
            if (step == 2) {
                fields = ['address_type', 'address', 'city', 'province', 'postal_code', 'opt_out_recongnition'];
            }

            $.each( fields, function( index, field_name ) {
                $('#volunteer-profile-form [name='+ field_name +']').nextAll('span.text-danger').remove();
                $('#volunteer-profile-form [name='+ field_name +']').removeClass('is-invalid');
            });

            // charities -- required error message
            // $('#city').parent().find('.ts-wrapper').removeClass('is-invalid');
            $('#city').parent().find('.select2-selection--single').removeClass('is-invalid');
            $('#city').parent().find('span.text-danger').remove();

            // $('#error-message').html('');
            // $('#error-message').hide();

            var form = $('#volunteer-profile-form');
            $('#volunteer-profile-form input[name=step]').val( step );

            $.ajax({
                method: "POST",
                url:  '{{ route("volunteering.profile.store") }}',
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
                    if (step == 2)  {
                            $('#summary-page').html(data);
                    }

                },
                error: function(response) {
                    valid = false;
                    if (response.status == 422) {

                        $.each(response.responseJSON.errors, function(field_name,error){
                            if ( field_name.includes('.') ) {
                                items = field_name.split(".");
                                pos = Number(items[ items.length -1 ]);

                                $(document).find('[name="' + items[0] + '[]"]:eq(' + pos + ')').parent().append('<span class="text-strong text-danger">' +error+ '</span>');
                                $(document).find('[name="' + items[0] + '[]"]:eq(' + pos + ')').addClass('is-invalid');

                            } else {

                                // Tom-Select dynamic field
                                if (field_name == 'city') {
                                   $('#city').parent().find('.select2-selection--single').addClass('is-invalid');
                                }
                                    
                                $(document).find('[name=' + field_name + ']').parent().append('<span class="text-strong text-danger">' +error+ '</span>');
                                $(document).find('[name=' + field_name + ']').addClass('is-invalid');

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

        return valid;
    }

    // On page 1 - Option Pool or Charity

    // On Page 2 -- Select Pool or Select Charity
    $('#city').select2({
        //   placeholder: 'Select an option'
    });
    

    // On Page 3 -- summary (handle single submission only )
    $('#volunteer-profile-form').on('submit', function () {
        $("#volunteer-profile-form button[type='submit']").attr('disabled', 'true');
        $("#volunteer-profile-form button[type='submit']").html('Registration submitted');
    });
   

});

</script>

@endpush
