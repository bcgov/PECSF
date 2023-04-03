@extends('adminlte::page')

@section('content_header')

@endsection

@section('content')

<div class="container mt-1">
  <div class="row">
    <div class="col-9 col-sm-9">
        <h1>Make a Donation</h1>
        <p class="text-muted">When you give through PECSF 100% of your donated dollars goes to the organizations you choose to support.</p>

        {{-- Wizard Progress bar (stepper) --}}
        <div class="card-header border-0 p-0">
            <div class=" card-timeline px-2 border-0">
                <ul class="bs4-step-tracking">
                    <li class="active">
                        <div><i class="fas fa-bars fa-2xl"></i></div>Method
                    </li>
                    <li class="">
                        <div><i class="fas fa-dollar-sign fa-2xl"></i></div>Pool or Non-Pool
                    </li>
                    <li class="">
                        <div><i class="fas fa-random fa-2xl"></i></div>Amount
                    </li>
                    <li class="amount-distribution">
                        <div><i class="fas fa-random fa-2xl"></i></div>Amount Distribution
                    </li>
                    <li class="">
                        <div><i class="fas fa-check fa-2xl"></i></div>Review and Submit
                    </li>
                </ul>
            </div>
        </div>

        <div class="card-body py-0">
            <form action="{{ isset($pledge) ? route("annual-campaign.update", $pledge->id) : route("annual-campaign.store") }}"
                    id="annual-campaign-form" method="POST">
                @csrf
                @isset($pledge)
                    @method('PUT')
                    <input type="hidden" id="pledge_id" name="pledge_id" value="{{ $pledge->id }}">
                @endisset
                <input type="hidden" id="step" name="step" value="{{ $step }}">
                <input type="hidden" id="campaign_year_id" name="campaign_year_id" value="{{ $campaign_year->id }}">
                <input type="hidden" name="number_of_periods" value="{{ $campaign_year->number_of_periods }}">

                {{-- Nav Items --}}
                <ul class="nav nav-tabs" id="nav-tab" role="tablist" style="display:none;">
                    <li class="nav-item">
                    <a class=" nav-link active" id="nav-method-tab" data-toggle="tab" href="#nav-method" data-id="0" role="tab" aria-controls="nav-method" aria-selected="false">Method</a>
                    </li>
                    <li class="nav-item ">
                        <a class=" nav-link" id="nav-selection-tab" data-toggle="tab" href="#nav-selection" data-id="2" role="tab" aria-controls="nav-selection" aria-selected="false">Pool or Non-Pool</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="nav-amount-tab" data-toggle="tab" href="#nav-amount" data-id="1" role="tab" aria-controls="nav-amount" aria-selected="false">Amount</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="nav-distribution-tab" data-toggle="tab" href="#nav-distribution" data-id="1" role="tab" aria-controls="nav-distribution" aria-selected="false">Amount Distribution</a>
                    </li>
                    <li class="nav-item">
                    <a class=" nav-link " id="nav-summary-tab" data-toggle="tab" href="#nav-summary" data-id="3" role="tab" aria-controls="nav-summary" aria-selected="false">Summary</a>
                    </li>
                </ul>

                <div class="tab-content pb-3 px-1" id="nav-tabContent">
                    <div class="tab-pane fade step show active" id="nav-method" role="tabpanel" aria-labelledby="nav-method-tab">

                        <h3>1. Select your preferred method for choosing charities</h3>
                        <p class="p-1"></p>
                        <div class="card mx-3 p-0 pl-2 bg-primary">
                            <div class="card-body bg-light">
                                If you select the CRA charity list option, you can support up to 10 different charities of your choice through your donation, if they are registered and in good standing with the Canada Revenue Agency (CRA).
                                If you select the regional Fund Supported Pool option, charities and distribution amounts are pre-determined and cannot be adjusted, removed, or substituted. 
                                Visit the PECSF webpages to learn more about the <a target="_blank" href="https://www2.gov.bc.ca/gov/content/careers-myhr/about-the-bc-public-service/corporate-social-responsibility/pecsf/charity" style="text-decoration: underline;">Fund Supported Pool</a> option.

                            </div>
                        </div>

                        @include('annual-campaign.partials.method-selection')

                    </div>
                    <div class="tab-pane fade step" id="nav-selection" role="tabpanel" aria-labelledby="nav-selection-tab">
                        <h3>2. Select your regional charity pool</h3>

                        @include('annual-campaign.partials.pools')
                        @include('annual-campaign.partials.choose-charity')

                    </div>
                    <div class="tab-pane fade step" id="nav-amount" role="tabpanel" aria-labelledby="nav-amount-tab">

                        <h3>3. Decide on the frequency and amount</h3>

                        @include('annual-campaign.partials.amount')

                    </div>
                    <div class="tab-pane fade step" id="nav-distribution" role="tabpanel" aria-labelledby="nav-distribution-tab">
                        <h3>4. Decide on the distributions</h3>
                        <p>You can distribute your contributions to each charity here. Start from the top and specify the amount of percentage so that together they are total 100%.
                        </p>

                        <span id="step-distribution-page">
                            @include('annual-campaign.partials.distribution')
                        </span>

                    </div>
                    <div class="tab-pane fade step" id="nav-summary" role="tabpanel" aria-labelledby="nav-summary-tab">
                        <span id="summary-page">
                            Final Step -- review and submission
                        </span>
                    </div>
                    {{-- <div class="tab-pane fade step" id="nav-contact-1" role="tabpanel" aria-labelledby="nav-contact-tab">
                        Step 4 - Etsy mixtape wayfarers, ethical wes anderson tofu before they sold out mcsweeney's organic lomo retro fanny pack lo-fi farm-to-table readymade. Messenger bag gentrify pitchfork tattooed craft beer, iphone skateboard locavore carles etsy salvia banksy hoodie helvetica. DIY synth PBR banksy irony. Leggings gentrify squid 8-bit cred pitchfork. Williamsburg banh mi whatever gluten-free, carles pitchfork biodiesel fixie etsy retro mlkshk vice blog. Scenester cred you probably haven't heard of them, vinyl craft beer blog stumptown. Pitchfork sustainable tofu synth chambray yr.
                    </div> --}}
                </div>


                <div class="p-2 ">
                    <button type="button" class="action cancel btn btn-lg btn-outline-primary"
                        onclick="window.location='{{ route('donations.list') }}'"
                        >Cancel</button>
                    <button type="button" class="action back btn btn-lg  btn-outline-primary"
                        style="display: none">Back</button>
                    <button type="button" class="action next btn btn-lg  btn-primary "
                        >Next</button>
                    <button type="submit" class="action submit btn btn-lg  btn-primary "
                        style="display: none">Pledge</button>
                </div>

            </form>
        </div>

    </div>
    <div class="col-3 col-sm-3">
        <img src="{{ asset('img/donor.png') }}" alt="Group of volunteers making wreaths at a table" class="py-5 img-fluid">
    </div>

    {{-- <div class="col-0 col-sm-9">

    </div> --}}

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
        color:  #b2b2b2;  /* #878788 ; */
        padding-left: 0px;
        margin-top: 30px
    }

    .bs4-step-tracking li {
        list-style-type: none;
        font-size: 13px;
        width: 20%;   /* change from 25 to 20% */
        float: left;
        position: relative;
        font-weight: 400;
        color:  #b2b2b2;  /* #878788 ; */
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


    #nav.nav a.nav-link.active {
        text-decoration: underline !important;
        font-weight: bold;
    }

</style>

@endpush

@push('js')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}" ></script>

<script>

$(function () {

    // For keep tracking the current page in wizard, and also count for the signle submission only
    var step = {{ $step }};
    var submit_count = 0;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on("keyup keypress", "#annual-campaign-form", function(e) { 
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    $(".next").on("click", function() {

        pool_option = $("input[name='pool_option']:checked").val();

        var nextstep = false;
        if (step == 1) {
            nextstep = checkForm();

            // Hide or Unhide Pool or charity page
            if (pool_option == 'P') {
                $('#step-regional-pools-area').show();
                $('#step-charities-area').hide();
            } else {
                $('#step-regional-pools-area').hide();
                $('#step-charities-area').show();
            }

        } else if (step == 2) {
            nextstep = checkForm();
        } else if (step == 3) {
            nextstep = checkForm();
            if (nextstep == true) {
                if (pool_option == 'P') {
                    step++;         // skip amount distribution
                }
            }
        } else if (step == 4) {

            // Online Validation on distributed amount and percentages
            pass = validate_distribution(); 
            if (pass) {
            nextstep = checkForm();
                if (!nextstep)
                    $(window).scrollTop(200);
            }

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

        var limit = parseInt($(".step").length);

        if (step == limit) {
            pool_option = $("input[name='pool_option']:checked").val();
            if ( pool_option == 'P') {
                step = step - 3;
            } else {
                step = step - 2;
            }
            // $(".next").trigger("click");
        } else if (step >= 1) {
            step = step - 2;    // Normal
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

        // Update nav buttons
        hideButtons(step);
        $(this).blur();

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
    };

    // Validation when click on 'next' button
    function checkForm() {

        // reset submission count
        submit_count = 0;

        var valid = true;
            // array for the fields in the form (for clean up previous errors)
            var fields = [];
            if (step >= 0) {
                fields = ['pool_option'];
            }
            if (step >= 1) {
                fields = ['pool_id'];
            }
            if (step >= 2) {
                fields = ['one_time_amount_custom', 'bi_weekly_amount_custom'];
            }

            $.each( fields, function( index, field_name ) {
                $('#annual-campaign-form [name='+ field_name +']').nextAll('span.text-danger').remove();
                $('#annual-campaign-form [name='+ field_name +']').removeClass('is-invalid');
            });

            // charities -- required error message
            $('.min-charities-error').html('');
            $('.min-charities-error').removeClass('error');
            $(".charity-error-hook").css("border","none");

            // Distribution page
            $('#annual-campaign-form [name="charities[]"]').nextAll('span.text-danger').remove();

            $('#annual-campaign-form [name^="oneTimePercent"]').nextAll('span.text-danger').remove();
            $('#annual-campaign-form [name^="oneTimePercent"]').removeClass('is-invalid');
            $('#annual-campaign-form [name^="oneTimeAmount"]').nextAll('span.text-danger').remove();
            $('#annual-campaign-form [name^="oneTimeAmount"]').removeClass('is-invalid');
            $('#annual-campaign-form [name^="biWeeklyPercent"]').nextAll('span.text-danger').remove();
            $('#annual-campaign-form [name^="biWeeklyPercent"]').removeClass('is-invalid');
            $('#annual-campaign-form [name^="biWeeklyAmount"]').nextAll('span.text-danger').remove();
            $('#annual-campaign-form [name^="biWeeklyAmount"]').removeClass('is-invalid');

            var form = $('#annual-campaign-form');
            $('#annual-campaign-form input[name=step]').val( step );

            $.ajax({
                method: "POST",
                url:  '/annual-campaign' ,
                //data: form.serialize(),
                data: form.find(':not(input[name=_method])').serialize(),  // serializes the form's elements exclude _method.
                async: false,
                cache: false,
                timeout: 30000,
                success: function(data)
                {
                    // console.log(data );
                    if (step == 3 && pool_option == 'P' || step == 4)  {
                        $('#summary-page').html(data);
                    }
                    if (step == 3 && pool_option == 'C')  {
                        $('#step-distribution-page').html(data);
                    }
                },
                error: function(response) {
                    valid = false;
                    if (response.status == 422) {
                        $.each(response.responseJSON.errors, function(field_name,error){
                            if ( field_name.includes('.') ) {
                                items = field_name.split(".");
                                pos = Number(items[ items.length -1 ]);

                                if (field_name.includes('oneTimePercent') || field_name.includes('oneTimeAmount') ||
                                    field_name.includes('biWeeklyPercent') || field_name.includes('biWeeklyAmount')
                                   ) {
                                    // $("input[name='oneTimePercent[47162]']")
                                    $(document).find("input[name='" + items[0] + "[" + pos + "]']").parent().append('<span class="text-strong text-danger">' +error+ '</span>');
                                    $(document).find("input[name='" + items[0] + "[" + pos + "]']").addClass('is-invalid');
                                }

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

                            // additional checking for pledge existence
                            // code = $("select[name='organization_id'] option:selected").attr('code');

                            // if (step == 1 && field_name == 'campaign_year_id' && code != 'GOV') {

                            //     pledge_id = get_campaign_pledge_id();
                            //     if (pledge_id > 0) {
                            //         $(document).find('[name=' + field_name + ']').parent().append('<span class="d-block text-strong text-danger">' +
                            //             'There is an existing pledge for this donor. Would you like to change it? Click <a ' +
                            //             'href="/admin-pledge/campaign/'+pledge_id+'/edit">here</a> to proceed.' + '</span>');
                            //     }
                            // }

                        })
                    }
                    console.log('Error');
                }
            });

        return valid;
    }

    // Page 5 -- summary (handle single submission only )
     $('#annual-campaign-form').on('submit', function () {

        $("input[type=hidden][name='step']").val( step );
        $("#annual-campaign-form button[type='submit']").attr('disabled', 'true');
        $("#annual-campaign-form button[type='submit']").html('Pledge submitted');
    });

});

</script>

// Page 2 -- charities 
@include('annual-campaign.partials.choose-charity-js')
<script type="x-tmpl" id="organization-tmpl">
    @include('annual-campaign.partials.add-charity', ['index' => 'XXX', 'charity' => 'YYY'] )
</script>
<script>
    $(".org_hook").show();
</script>

// Page 4 -- distribution 
@include('annual-campaign.partials.distribution-js')

<script>
    
    function validate_distribution() {

        // Determine the selected One-Time and Biweekly amounts
        frequency = $('input[name="frequency"]:checked').val();

        one_time_expected = $('#oneTimeSection').find(".total-amount").data('expected-total');
        one_time_calculated = $('#oneTimeSection').find(".total-amount").val();
        bi_weekly_expected = $('#biWeeklySection').find(".total-amount").data('expected-total');
        bi_weekly_calculated = $('#biWeeklySection').find(".total-amount").val();

        one_time_percent = $('#oneTimeSection').find(".total-percent").val();
        bi_weekly_percent = $('#biWeeklySection').find(".total-percent").val();

        msg = ''; 
        tab = $('input[name="distributionByPercentBiWeekly"]:checked').val();
        if ( (frequency == 'bi-weekly' || frequency == 'both')  && tab == '0' && bi_weekly_percent != 100) {
            msg += 'The sum of Bi-weekly percentage <b>' + bi_weekly_percent + '%</b> did not match with 100%.';
            // $('#distributeByPercentageBiWeekly').trigger('click');
            }
        if ( (frequency == 'bi-weekly' || frequency == 'both')  && tab == '1' && bi_weekly_calculated != bi_weekly_expected) {
            msg += 'The total distributed Bi-weekly amount <b>$ ' + bi_weekly_calculated + '</b> did not match with your selection $' + bi_weekly_expected + '.';
            // $('#distributeByDollarBiWeekly').trigger('click');
        }

        tab = $('input[name="distributionByPercentOneTime"]:checked').val();
        // if (one_time_percent && one_time_percent != 100) {
        if ((frequency === 'one-time' || frequency === 'both') && tab == '0' && one_time_percent != 100) {
            if (msg) {
                msg += '<br/> And <br/>'; 
            }
                msg += 'The sum of One Time percentage <b>' + one_time_percent + '%</b> did not match with 100%.';
                // $('#distributeByPercentageOneTime').trigger('click');
        }
        if ((frequency === 'one-time' || frequency === 'both') && tab == '1' && one_time_expected != one_time_calculated) {
                if (msg) {
                    msg += '<br/> And <br/>'; 
                }
                msg += 'The total distributed One Time amount <b>$ ' + one_time_calculated + '</b> did not match with your selection $ ' + one_time_expected + '.';
                // $('#distributeByDollarOneTime').trigger('click');
        }

        if (msg) {
                Swal.fire({
                icon: 'error',
                title: 'Distributed percentage and amount',
                html: msg + ' <br/><br/>Please correct before click the Next button.',
                animation: true,
            });

            return false;
        }

        return true;

    }
   
</script>

@if ($is_duplicate)
    <script>
    $(function () {
        $(".next").trigger("click");
    });
    </script>
@endif

@endpush
