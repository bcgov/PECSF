<div id="step-summary-area">

    <div class="row">
        <div class="col-12">
            <h3 class="mt-0">{{ $pool_option == 'C' ?  '5. Summary' : '4. Summary' }}</h3>
            <p class="mt-3">Please review your donation plan and press <b>“Pledge”</b> when ready! Use the "Back” button, to make changes to your pledge.</p>
                <div class="card bg-light p-3">
                    <p class="card-title"><b>Deductions</b></p>
                    <div class="card">
                        <div class="card-body">
                            <span><b>Your Bi-weekly payroll deductions:</b></span>
                            {{-- <span class="float-right mb-2">${{ $calculatedTotalAmountBiWeekly*26 }}</span><br> --}}
                            <span class="float-right mb-2">${{ number_format($annualBiWeeklyAmount,2) }}</span><br>
                            <h6>AND / OR</h6>
                            <span><b>Your One-time payroll deductions:</b></span>
                            {{-- <span class="float-right">${{ $calculatedTotalAmountOneTime }}</span> --}}
                            <span class="float-right">${{ number_format($annualOneTimeAmount,2) }}</span>
                            <hr>
                            <p class="text-right"><b>Total :</b> ${{ number_format($annualBiWeeklyAmount + $annualOneTimeAmount,2) }}</p>
                        </div>
                    </div>
                    @csrf
                    @foreach(['bi-weekly','one-time'] as $key)
                        @if($key === 'one-time' && ( $frequency === 'one-time' || $frequency === 'both'))
                            @php $key_ = $key; @endphp
                            @php $keyCase = 'oneTime'; @endphp
                            @php $multiplier = 1; @endphp
                            @include('annual-campaign.partials.summary-distribution')
                        @endif
                        @if($key === 'bi-weekly' && ( $frequency === 'bi-weekly' || $frequency === 'both'))
                            @php $key_ = $key;@endphp
                            @php $keyCase = 'biWeekly'; @endphp
                            @php $multiplier = $number_of_periods; @endphp
                            @include('annual-campaign.partials.summary-distribution')
                        @endif
                    @endforeach
                </div>
                <div class="row">
                    <p class="py-4">
                    Please note that <b>this is not a tax receipt</b>.
                    Payroll deductions begin with the first paycheque in January and will appear on your payroll issued T4 for year when the funds are collected. PECSF issues cheques twice a year.
                    In August for payroll deductions from January - June, and in March for payroll deductions from July - December.
                    </p>
                </div>
                <div class="">
                    @if ($pool_option == 'C')
                        @foreach ($charities as $charity)
                            <input type="hidden" name="charityOneTimeAmount[{{$charity['id']}}]" value="{{$charity['one-time-amount-distribution']}}">
                            <input type="hidden" name="charityBiWeeklyAmount[{{$charity['id']}}]" value="{{$charity['bi-weekly-amount-distribution']}}">
                            <input type="hidden" name="charityOneTimePercentage[{{$charity['id']}}]" value="{{$charity['one-time-percentage-distribution']}}">
                            <input type="hidden" name="charityBiWeeklyPercentage[{{$charity['id']}}]" value="{{$charity['bi-weekly-percentage-distribution']}}">
                            <input type="hidden" name="charityAdditional[{{$charity['id']}}]" value="{{$charity['additional']}}">
                            {{-- <input type="hidden" name="frequency" value="{{$frequency}}">
                            <input type="hidden" name="pool_option" value="{{$pool_option}}">
                            <input type="hidden" name="regional_pool_id" value="{{$regional_pool_id}}"> --}}
                        @endforeach
                        <input type="hidden" name="annualOneTimeAmount" value="{{$annualOneTimeAmount}}">
                        <input type="hidden" name="annualBiWeeklyAmount" value="{{$annualBiWeeklyAmount}}">
                    @else
                        <input type="hidden" name="annualOneTimeAmount" value="{{$annualOneTimeAmount}}">
                        <input type="hidden" name="annualBiWeeklyAmount" value="{{$annualBiWeeklyAmount}}">
                        {{-- <input type="hidden" name="frequency" value="{{$frequency}}">
                        <input type="hidden" name="pool_option" value="{{$pool_option}}">
                        <input type="hidden" name="regional_pool_id" value="{{$regional_pool_id}}"> --}}
                    @endif
                </div>

        </div>
        {{-- <div class="col-12 col-sm-5 text-center">
            <img src="{{ asset('img/donor.png') }}" alt="Group of volunteers making wreaths at a table" class="py-5 img-fluid">
             <p>
                Making changes to your pledge outside of Campaign time? Please contact <a href="mailto:PECSF@gov.bc.ca" class="text-primary">PECSF@gov.bc.ca</a> directly or submit an <a href="https://www2.gov.bc.ca/gov/content/careers-myhr" class="text-primary" target="_blank">AskMyHR</a> service request to make any changes on your existing pledge outside the annual campaign/open enrollment period (September-December).
            </p>
            <p><b>Questions?</b> <a href="https://www.gov.bc.ca/pecsf" class="text-primary" target="_blank">www.gov.bc.ca/pecsf</a>         <b>Email:</b> <a href="mailto:PECSF@gov.bc.ca" class="text-primary">PECSF@gov.bc.ca</a></p>
        </div> --}}
    </div>
    <div class="row">

            <div>
                <strong>Freedom of Information and Protection of Privacy Act</strong>
                <p>Personal information on this form is collected under sections 26(c) and (e) of FOIPPA by the BC Public Service Agency for the purposes of processing and reporting on your pledge for charitable contributions to the Community Fund, administering your participation in your organization’s incentive draws for the current campaign, producing general campaign statistics, and identifying donor and giving trends to guide data-driven program improvements.</p>
                <p>Questions about the collection of your personal information can be directed to the Campaign Manager, Provincial Employees Community Services Fund, at 250 356-1736, <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a> or PO Box 9564 Stn Prov Govt, Victoria, BC V8W 9C5.
                </p>

    </div>

</div>


<script>
$(function () {

    // $("#step-summary-area .frequencybiWeekly").show();
    // if($("#step-summary-area .frequencyoneTime").length > 0){
    //     $("#step-summary-area .frequencybiWeekly").hide();
    // }
    $("#step-summary-area .frequencyoneTime").show();
        if($("#step-summary-area .frequencybiWeekly").length > 0){
        $("#step-summary-area .frequencyoneTime").hide();
    }

    $(document).on('click', '#distributeByDollar, #distributeByPercentage', function () {
        const frequency = $(this).attr('id') === 'distributeByDollarAmountOneTime' ? '#oneTimeSection' : '#biWeeklySection';
        if ($(this).attr('id') == "distributeByDollar") {
            $("body").find("#step-summary-area .by-amount").removeClass("d-none");
            $("body").find("#step-summary-area .by-percent").addClass("d-none");
            $("body").find("#step-summary-area .percent-amount-text").html("Distribute by Percentage");
        } else {
            $("body").find("#step-summary-area .by-percent").removeClass("d-none");
            $("body").find("#step-summary-area .by-amount").addClass("d-none");
            $("body").find("#step-summary-area .percent-amount-text").html("Distribute by Dollar Amount");
        }
        $("#step-summary-area .percent-input").change();
        $("#step-summary-area .amount-input").change();
    });
    $(document).on('change', '#step-summary-area .percent-input', function () {
        let total = 0;
        const section = $(this).parents('#step-summary-area .amountDistributionSection');
        section.find("#step-summary-area .percent-input").each( function () {
            total += Number($(this).val());
        });

        section.find("#step-summary-area .total-percent").val( total );
        section.find("#step-summary-area .total-percent-text").text('Total Amount: '+ total.toFixed(2) + '%');
    });


    $(document).on('change', '#step-summary-area .amount-input', function () {
        let total = 0;
        const section = $(this).parents('#step-summary-area .amountDistributionSection');

        const expectedTotal = section.find("#step-summary-area .total-amount").data('expected-total');
        section.find("#step-summary-area .amount-input").each( function () {
            total += Number($(this).val());
        });
        section.find("#step-summary-area .total-amount").val(total);
    });

    $("#step-summary-area .percent-input").change();
    $("#step-summary-area .amount-input").change();
});

</script>

