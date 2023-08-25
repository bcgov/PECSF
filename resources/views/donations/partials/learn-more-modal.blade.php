<!-- Modal -->
<div class="modal fade" id="learn-more-modal" tabindex="-1" aria-labelledby="learnMoreModalTitle" data-backdrop="static"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="donateGuideCarousel" class="carousel slide w-100" data-ride="carousel" data-interval="false">
                <div class="carousel-inner text-center" style="min-height: 550px;">
                    <div class="carousel-item active">
                        <h3 class="text-primary my-5">
                            Why donate to the Provincial Employees Community Service Fund?
                        </h3>
                        <div class="my-4">
                            <iframe id="movie_player" movie-id="https://www.youtube-nocookie.com/embed/ZMEjHqr3npo?cc_load_policy=1"
                                width="560" height="315" src="" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <h3 class="text-primary my-5">
                            When you can donate
                        </h3>
                        <div class="my-4">
                            <img src="{{asset('img/donate-intro.png')}}" alt="" style="max-width: 400px;">
                        </div>
                        <div class="px-5" >
                            <p class="mx-5 px-5">
                                Employees, including retirees, can donate anytime, however the majority of employees sign up to support their favourite charities during the annual fall PECSF awareness campaign.
                            </p>
                            <p class="mx-5 px-5">
                                Registering during the fall campaign and pledge drive ensures payroll deductions are set up for the next calendar year. Payroll deductions begin with the first pay in January.
                            </p>
                        </div>
                    </div>

                    <div class="carousel-item">
                        <h3 class="text-primary my-5">
                            How donating to PECSF works
                        </h3>
                        <div class="row">
                            <div class="col-12 col-md-4 offset-md-1">
                                <h4 class="text-primary">
                                    Step 1 - Select your preferred method for choosing charities
                                </h4>
                                <p>
                                    If you select the CRA charity list option, you can support up to 10 different charities of your choice through your donation, if they are registered and in good standing with the Canada Revenue Agency (CRA). You can also choose individual Fund Supported Pool entries.
                                </p>
                                <p>
                                    If you select the regional Fund Supported Pool option, charities and distribution amounts are pre-determined
                                    and cannot be adjusted, removed, or substituted. Visit the PECSF webpages to learn more about the
                                    <a href="https://www2.gov.bc.ca/gov/content/careers-myhr/about-the-bc-public-service/corporate-social-responsibility/pecsf/charity" target="_blank">Fund Supported Pool</a> option.
                                </p>
                            </div>
                            <div class="col-12 col-md-5 offset-md-1">
                                <img src="{{asset('img/donation-intro/step-1.png')}}" alt="" style="max-width: 450px;">
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <h3 class="text-primary my-5">
                            How donating to PECSF works
                        </h3>
                        <div class="row">
                            <div class="col-12 col-md-4 offset-md-1">
                                <h4 class="text-primary">
                                    Step 2 - Select your charities - CRA option
                                </h4>
                                <p class="">
                                    Search for CRA registered charities using keyword, category and/or province. If you don't see any results,
                                    you may need to remove filters that you have already selected, or your search may get so specific that no results will display.
                                </p>
                                {{-- <p>
                                It is recommended that you make a one-time contribution if you do not wish to use the payroll
deduction plan.
                                </p> --}}
                            </div>
                            <div class="col-12 col-md-5 offset-md-1">
                                <img src="{{asset('img/donation-intro/step-2.png')}}" alt="" style="max-width: 450px;">
                            </div>
                        </div>
                    </div>
{{-- page 6 --}}
<div class="carousel-item">
    <h3 class="text-primary my-5">
        How donating to PECSF works
    </h3>
    <div class="row">
        <div class="col-12 col-md-4 offset-md-1">
            <h4 class="text-primary">
                Step 2 - Select your charities - FSP option
            </h4>
            <p>
                Search for Individual Fund Supported Pool entries using the Search by Fund Supported Pool drop-down. Select the region and choose your charities.
            </p>
        </div>
        <div class="col-12 col-md-5 offset-md-1">
            <img src="{{asset('img/donation-intro/step-6.png')}}" alt="" style="max-width: 450px;">
        </div>
    </div>
</div>
{{-- page 7 --}}
<div class="carousel-item">
    <h3 class="text-primary my-5">
        How donating to PECSF works
    </h3>
    <div class="row">
        <div class="col-12 col-md-4 offset-md-1">
            <h4 class="text-primary">
                Step 3 - Decide on the frequency and amount
            </h4>
            <p>Current employees can choose bi-weekly or a one-time payroll deduction in any amount.
            </p>
            <p>It is recommended that you make a one-time contribution if you do not wish to use the payroll deduction plan.
            </p>
            <p>You may make both a bi-weekly and one-time payroll deduction. </p>
        </div>
        <div class="col-12 col-md-5 offset-md-1">
            <img src="{{asset('img/donation-intro/step-7.png')}}" alt="" style="max-width: 450px;">
        </div>
    </div>
</div>

                    {{-- <div class="carousel-item">
                        <h3 class="text-primary my-5">
                            How donating to PECSF works
                        </h3>
                        <div class="row">
                            <div class="col-12 col-md-4 offset-md-1">
                                <h4 class="text-primary">
                                    Step 4: Decide on the distribution
                                </h4>
                                <p>
                                Use the toggles to distribute your contributions to each charity.
                                </p>
                                <p>
                                You have the option to distribute your donation by percentage or by dollar amount.
                                </p>
                                <p>
                                By default, your donation is distributed evenly to each organization, however, you have the option to customize the distribution.
                                </p>
                            </div>
                            <div class="col-12 col-md-5 offset-md-1">
                                <img src="{{asset('img/donation-intro/step-3.png')}}" alt="" style="max-width: 450px;">
                            </div>
                        </div>
                    </div> --}}


{{-- page 8 --}}
<div class="carousel-item">
    <h3 class="text-primary my-5">
        How donating to PECSF works
    </h3>
    <div class="row">
        <div class="col-12 col-md-4 offset-md-1">
            <h4 class="text-primary">
                Step 4 - Decide on the distribution - CRA option only
            </h4>
            <p>By default, your donation is distributed evenly to each organization, however, you have the option to customize the distribution.</p>
            <p>You have the option to distribute your donation by percentage or by dollar amount. Use the toggles to distribute your contributions to each charity.</p>
        </div>
        <div class="col-12 col-md-5 offset-md-1">
            <img src="{{asset('img/donation-intro/step-8.png')}}" alt="" style="max-width: 450px;">
        </div>
    </div>
</div>
{{-- page 9 --}}
<div class="carousel-item">
    <h3 class="text-primary my-5">
        How donating to PECSF works
    </h3>
    <div class="row">
        <div class="col-12 col-md-4 offset-md-1">
            <h4 class="text-primary">
                Step 4/5 - Review and submit
            </h4>
            <p>Review your choices to ensure accuracy, and that you're ready to submit.</p>
            <p>Your <span class="font-weight-bold"><u>Payroll Deductions</u></span> begin on the first pay cheque in January following the annual awareness campaign
                and will appear on your T4 for the year that the funds were deducted.</p>
        </div>
        <div class="col-12 col-md-5 offset-md-1">
            <img src="{{asset('img/donation-intro/step-9.png')}}" alt="" style="max-width: 370px;">
        </div>
    </div>
</div>
{{-- page 10 (Last) --}}
                    <div class="carousel-item">
                        <h3 class="text-primary my-5">
                            How donating to PECSF works
                        </h3>
                        <div class="row">
                            <div class="col-12 col-md-4 offset-md-1">
                                <h4 class="text-primary">
                                    Next steps and getting assistance
                                </h4>
                                <p>
                                    After submitting your pledge, you will have the opportunity to download a PDF summary (for information only) and
                                    return to your donor history. You may return to make edits to your campaign pledge anytime during the fall awareness
                                     campaign until the end of November. To make, update or cancel your pledge outside of the campaign, email
                                     <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a>.
                                </p>
                                <p>If you have any questions or experience any difficulties during the pledge process, contact the PECSF
                                    HQ team at <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a>. They're always here and happy to assist you!
                                </p>
                                {{-- <p>
                                Your <strong><u><b>Payroll Deductions</b></u></strong> begin on the first paycheque of 2022 and will appear on your 2022 T4.
                                </p>
                                <p>
                                By default, your donation is distributed evenly to each organization, however, you have the option to customize the distribution.
                                </p> --}}
                            </div>
                            <div class="col-12 col-md-5 offset-md-1">
                                <img src="{{asset('img/donation-intro/step-10.png')}}" alt="" style="max-width: 450px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex">
                    <button href="#donateGuideCarousel" class="btn btn-outline-primary btn-md prev-btn d-none" data-slide="prev">Back</button>
                    <div class="flex-fill"></div>
                    <x-button href="#donateGuideCarousel" role="button" class="start-btn" data-slide="next">Learn more about how to donate</x-button>
                    <x-button href="#donateGuideCarousel" role="button" class="next-btn d-none" data-slide="next">Next</x-button>
                    @if ( $campaignYear->isOpen() )
                        <x-button :href="route('annual-campaign.index')" role="button" class="ready-btn d-none">I'm ready to Donate!</x-button>
                    @else
                        <x-button :href="route('donate-now.index')" role="button" class="ready-btn d-none">I'm ready to Donate!</x-button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')

<script>

    $(function () {
        $('#learn-more-modal').on('slide.bs.carousel', function (e) {

            movie_id = $('#movie_player').attr('movie-id');
            $('#movie_player').attr('src', movie_id);

            if(e.to == 0) {
                $(this).find(".prev-btn").addClass("d-none");
                $(this).find(".start-btn").removeClass("d-none");
                $(this).find(".next-btn").addClass("d-none");
            }
            else if (e.to === 8) {
                $(this).find(".next-btn").addClass("d-none");
                $(this).find(".ready-btn").removeClass("d-none");
            } else {
                $(this).find(".start-btn").addClass("d-none");
                $(this).find(".prev-btn").removeClass("d-none");
                $(this).find(".next-btn").removeClass("d-none")
                $(this).find(".ready-btn").addClass("d-none");
            }

        })

        $('#learn-more-modal').on('show.bs.modal', function (event) {
            $('#donateGuideCarousel').carousel(0);
            movie_id = $('#movie_player').attr('movie-id');
            $('#movie_player').attr('src', movie_id);
        })

        $("#learn-more-modal").on("hidden.bs.modal", function () {
            $('#movie_player').attr('src', '')
        });

    });

</script>

@endpush
