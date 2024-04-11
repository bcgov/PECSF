@extends('adminlte::page')

@section('content_header')
@endsection


@section('content')
<div class="container mt-1">
    <div class="row">
        <div class="col-9 col-sm-9">
            <h1>Make a one-time donation</h1>
            <p style="color: #687278;">When you give through PECSF 100% of your donated dollars goes to the organizations you<br> choose to support.</p>


            {{-- Main Content --}}
            {{-- <div class="card pb-4"> --}}

                {{-- Wizard Progress bar (stepper) --}}
                {{-- <div class="card-header border-0 py-2"> --}}
                    <div class=" card-timeline px-2 border-0" style="display:block;">
                        <ul class="bs4-step-tracking">
                            <li class="active" tabindex="0">
                                <div><i class="fas fa-random fa-2xl"></i></div>Pool or Non-Pool
                            </li>
                            <li class="" tabindex="-1">
                                <div><i class="fas fa-bars fa-2xl"></i></div>In Support Of
                            </li>
                            <li class="" tabindex="-1">
                                <div><i class="fas fa-dollar-sign  fa-2xl"></i></div>Amount
                            </li>
                            <li class="" tabindex="-1">
                                <div><i class="fas fa-check fa-2xl"></i></div>Review and Submit
                            </li>
                        </ul>
                    </div>
                {{-- </div> --}}

                {{-- <div id="error-message" class="m-4 p-3 alert alert-warning" style="display:none"></div> --}}


              <div class="card-body py-0">
                <form action="{{ isset($pledge) ? route("donate-now.update", $pledge->id) : route("donate-now.store") }}"
                        id="donate-now-pledge-form" method="POST">
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
                                @include('donate-now.partials.choose-charity')
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
                        <button type="button" class="action cancel btn btn-lg btn-outline-primary"
                            onclick="window.location='{{ route('donations.list') }}'"
                        >Cancel</button>
                        <button type="button" class="action back btn btn-lg btn-outline-primary"
                            style="display: none">Back</button>
                        <button type="button" class="action next btn btn-lg btn-primary ml-1"
                            >Next</button>
                        <button type="submit" class="action submit btn btn-lg btn-primary ml-1"
                            style="display: none">Pledge</button>
                    </div>

                </form>
              </div>
            {{-- </div> --}}


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

</style>

@endpush

@push('js')

<script src="{{ asset('vendor/select2/js/select2.min.js') }}" ></script>
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
    $('#donate-now-pledge-form').on("keypress", function(e) {
        var $focusElem = $(":focus");
        if (e.which == 13 && $focusElem.is("input"))
            e.preventDefault();
    });

    // prevent spacebar to trigger the page scrolling
    $('#donate-now-pledge-form').on("keypress", function(e) {
        var $focusElem = $(":focus");
        if (e.which == 32 && !($focusElem.is("input") || $focusElem.attr("contenteditable") == "true"))
            e.preventDefault();
    });

    // $('#donate-now-pledge-form').on('keyup keypress', function(e) {
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

        // display/hide either Pool or Chariry section
        pool_option =  $("input[name='pool_option']:checked").val();
        if (pool_option == 'C') {
            $("#pool-selection-section").hide();
            $("#charity-selection-section").show();
            $('#charity-selection-section #charity_keyword').focus();
        } else {
            $("#pool-selection-section").show();
            $("#charity-selection-section").hide();
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
                fields = ['pool_option', 'pool_id'];
            }
            if (step == 2) {
                fields = ['pool_id', 'charity_id', 'special_program'];
            }
            if (step == 3) {
                fields = ['one_time_amount', 'one_time_amount_custom'];
            }

            $.each( fields, function( index, field_name ) {
                $('#donate-now-pledge-form [name='+ field_name +']').nextAll('span.text-danger').remove();
                $('#donate-now-pledge-form [name='+ field_name +']').removeClass('is-invalid');
            });

            // charities -- required error message
            $('.min-charities-error').html('');
            $('.min-charities-error').removeClass('error');
            $(".charity-error-hook").css("border","none");
            $('#donate-now-pledge-form [name="charities[]"]').nextAll('span.text-danger').remove();

            // $('#error-message').html('');
            // $('#error-message').hide();

            var form = $('#donate-now-pledge-form');
            $('#donate-now-pledge-form input[name=step]').val( step );

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

                        // $('#error-message').html( response.responseJSON.errors );
                        // $('#error-message').html('');
                        // $.each(response.responseJSON.errors, function( field_name, error){
                        //     $('#error-message').append('<div class="text-strong text-danger">' + error + '</div>');
                        // })
                        // $('#error-message').show();
                        // $("html, body").animate({ scrollTop: 0 }, 500);
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

                        $.each(response.responseJSON.errors, function(field_name,error){
                            if ( field_name.includes('.') ) {
                                items = field_name.split(".");
                                pos = Number(items[ items.length -1 ]);

                                $(document).find('[name="' + items[0] + '[]"]:eq(' + pos + ')').parent().append('<span class="text-strong text-danger">' +error+ '</span>');
                                $(document).find('[name="' + items[0] + '[]"]:eq(' + pos + ')').addClass('is-invalid');
                            } else {

                                if (field_name == 'charities') {
                                    $('.min-charities-error').html("<i class='fas fa-exclamation-circle'></i> " + error );
                                    $('.min-charities-error').addClass('error');
                                    $(".charity-error-hook").css("border","red 2px solid")
                                } else {
                                    $(document).find('[name=' + field_name + ']').parent().append('<span class="text-strong text-danger">' +error+ '</span>');
                                    $(document).find('[name=' + field_name + ']').addClass('is-invalid');
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

        return valid;
    }

    // On page 1 - Option Pool or Charity

    // On Page 2 -- Select Pool or Select Charity

    // On Page 3

    // On Page 4 -- summary (handle single submission only )
    $('#donate-now-pledge-form').on('submit', function () {
        $("#donate-now-pledge-form button[type='submit']").attr('disabled', 'true');
        $("#donate-now-pledge-form button[type='submit']").html('Pledge submitted');
    });

    // $(document).on("click", "button[type='submit']", function(e) {

    //     // this.disabled = true;
    //     $("#donate-now-pledge-form").submit(function(e){
    //         if(submit_count > 0){
    //             e.preventDefault();
    //         }
    //         submit_count++;
    //     });
    // });

});

</script>


@include('donate-now.partials.choose-charity-js')
<script type="x-tmpl" id="organization-tmpl">
    @include('donate-now.partials.add-charity', ['index' => 'XXX', 'charity' => 'YYY'] )
</script>
<script>
    $(".org_hook").show();
</script>

@endpush
