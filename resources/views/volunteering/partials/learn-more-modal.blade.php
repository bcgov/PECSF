<!-- Modal -->
<div class="modal fade" id="learn-more-modal" tabindex="-1" aria-labelledby="learnMoreModalTitle" data-backdrop="static"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" aria-label="Welcome to Volunteering!">Learn more about how to volunteer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="min-height: 650px;">
                <div id="learnGuideCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
                    <div class="carousel-inner">
                        {{-- page 1 --}}
                        <div class="carousel-item active text-center">
                            <h3 class="text-primary my-3 pb-3">
                                Welcome to PECSF Volunteering!
                            </h3>
                            <div class="">
                                <iframe id="movie_player" movie-id="https://www.youtube-nocookie.com/embed/ui-7PMerNnU?cc_load_policy=1&rel=0&autoplay=0"
                                    width="780" height="510" src="" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </div>

                        {{-- page 2 --}}
                        <div class="carousel-item">
                            <h3 class="text-primary text-center my-3 pb-5">
                                What are the benefits of volunteering with PECSF?
                            </h3>
                            <div class="row">
                                <div class="col offset-sm-1">
                                    <span class="font-weight-bold">Your benefits are: </span>
                                    <div class="container pt-3">
                                        <div class="row justify-content-center text-left">
                                            <div class="col-12 col-md-12">
                                                <div class="row py-2">
                                                    <div class="col-1 text-right">
                                                        <i class="far fa-check-circle pr-2 text-primary"></i>
                                                    </div>
                                                    <div class="col-11">  
                                                        Put your public service values into action
                                                    </div>
                                                </div>

                                                <div class="row py-2">
                                                    <div class="col-1 text-right">
                                                        <i class="far fa-check-circle pr-2 text-primary"></i>
                                                    </div>
                                                    <div class="col-11">  
                                                        Make a difference in your community
                                                    </div>
                                                </div>

                                                <div class="row py-2">
                                                    <div class="col-1 text-right">
                                                        <i class="far fa-check-circle pr-2 text-primary"></i>
                                                    </div>
                                                    <div class="col-11">  
                                                        Build your team spirit and connections
                                                    </div>
                                                </div>

                                                <div class="row py-2">
                                                    <div class="col-1 text-right">
                                                        <i class="far fa-check-circle pr-2 text-primary"></i>
                                                    </div>
                                                    <div class="col-11">  
                                                        Showcase or develop your skills as a leader
                                                    </div>
                                                </div>

                                                <div class="row py-2">
                                                    <div class="col-1 text-right"><i class="far fa-check-circle pr-2 text-primary"></i></div>
                                                    <div class="col-11">  
                                                        Make valuable networking connections with others in the BC Public Service
                                                    </div>
                                                </div>
                                                
                                                <div class="row py-2">
                                                    <div class="col-1 text-right"><i class="far fa-check-circle pr-2 text-primary"></i></div>
                                                    <div class="col-11">  
                                                        Offer your skills - there are so many ways to contribute!
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <img src="{{asset('img/volunteering-intro/step-1-1.jpg')}}" class="img-fluid pr-5 mb-2">
                                    <img src="{{asset('img/volunteering-intro/step-1-2.jpg')}}" class="img-fluid pl-5">
                                </div>
                                <div class="col-1"></div>
                            </div>
                        </div>

                        {{-- page 3 --}}
                        <div class="carousel-item text-center">
                            <h3 class="text-primary my-3 pb-4">
                                Your commitment as a volunteer
                            </h3>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <img src="{{asset('img/volunteering-intro/step-2.jpg')}}" alt="" style="max-width: 300px;">
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <b class="h5 py-3">
                                    As a volunteer you commit to:
                                </b>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-7 ">
                                    <ul class="pl-5 text-left check-bullet ">
                                        <li><i class="far fa-check-circle pr-2 text-primary"></i>Volunteering during the September to November awareness campaign</li>
                                        <li><i class="far fa-check-circle pr-2 text-primary"></i>Attend the campaign kick-off event in your region (where applicable)</li>
                                        <li><i class="far fa-check-circle pr-2 text-primary"></i>Two hours of PECSF online volunteering training</li>
                                    </ul>
                                </div>

                            </div>
                        </div>

                        {{-- page 4 --}}
                        <div class="carousel-item text-center">
                            <h3 class="text-primary my-4">
                                What are some of the available volunteer roles?
                            </h3>
                            <div class="container">
                                <div class="row justify-content-center text-left">
                                    <div class="col-10 col-md-10">
                                        {{-- <div class="px-3">
                                            <img src="{{asset('img/volunteering-intro/step-3-1.jpg')}}" style="width:100px" class="m-3">
                                            <b>Coordinators</b> - lead and set the tone of workspace campaign
                                        </div>
                                        <div class="px-3">
                                            <img src="{{asset('img/volunteering-intro/step-3-2.jpg')}}" style="width:100px" class="m-3">
                                            <b> Canvassers</b> - provide one-on-one contact with Employees
                                        </div>
                                        <div class="px-3">
                                            <img src="{{asset('img/volunteering-intro/step-3-3.jpg')}}" style="width:100px" class="m-3">
                                            <b> Event Coordinators</b> - plan and run successful fundraising events
                                        </div> --}}
                                        <div class="row pt-2">
                                            <div class="col-3 text-center">
                                                <img src="{{asset('img/volunteering-intro/step-3-2.jpg')}}" style="width:100px" class="m-3">
                                            </div>
                                            <div class="col-9 justify-content-center d-flex align-items-center">  
                                                <span><b>Canvasser</b> - In this role, you help donors with making their pledges, distribute the annual PECSF calendar, 
                                                            share information about the PECSF program with colleagues and collect the campaign prize draw entries. </span>
                                            </div>
                                        </div>

                                        <div class="row pt-4">
                                            <div class="col-3 text-center">
                                                <img src="{{asset('img/volunteering-intro/step-3-3.jpg')}}" style="width:100px" class="m-3">
                                            </div>
                                            <div class="col-9 justify-content-center d-flex align-items-center">  
                                                <span><b> Event Coordinator</b> - In this role, you build awareness and enthusiasm for the campaign by creating 
                                                            and running gaming and fundraising events as outlined in your lead coordinator's plan. </span>
                                            </div>
                                        </div>

                                        <div class="row pt-4 align-items-center">
                                            <div class="col-3 text-center text-primary">
                                                <i class="fas fa-user-tie fa-5x"></i>
                                                {{-- <img src="{{asset('img/volunteering-intro/step-3-1.jpg')}}" style="width:100px" class="m-3"> --}}
                                            </div>
                                            <div class="col-9 justify-content-center d-flex align-items-center">  
                                                <span><b>Executive Sponsor</b> - In this role, you assist your Lead Coordinator 
                                                    in spreading the message about the benefits of PECSF by sending out executive messages to 
                                                    staff, sharing at the executive table and attending PECSF events.</span>
                                            </div>
                                        </div>

                                        

                                        
                                    </div>
                                
                                </div>
                            </div>
                        </div>

                        {{-- page 5 --}}
                        <div class="carousel-item text-center">
                            <h3 class="text-primary my-4">
                                What are some of the available volunteer roles?
                            </h3>
                            <div class="container">
                                <div class="row justify-content-center text-left">
                              
                                    <div class="col-10 col-md-10">

                                        <div class="row">
                                            <div class="col-3 text-center">
                                                <img src="{{asset('img/volunteering-intro/step-3-1.jpg')}}" style="width:100px" class="m-3">
                                            </div>
                                            <div class="col-9 justify-content-center d-flex align-items-center">  
                                                <span><b>Lead Coordinator</b> -  In this role, you recruit canvassers, event planners, and other volunteers 
                                                    to help make an action plan, procure resources, delegate tasks to volunteers, guide the process and share your team's 
                                                    success with your organization. </span>
                                            </div>
                                        </div>

                                        <div class="row pt-4 align-items-center">
                                            <div class="col-3 text-center text-primary">
                                                <i class="fas fa-hands-helping fa-5x"></i>
                                            </div>
                                            <div class="col-9 justify-content-center d-flex align-items-center">  
                                                <span><b>Volunteer</b> - Not sure where you fit? This role is a general volunteer; you could be helping sell 50/50 tickets,
                                                    assisting with events, distributing calendars, or creating PECSF communications in your organization.</span>
                                            </div>
                                        </div>

                                        <div class="row pt-4 align-items-center">
                                            <div class="col-3 text-center text-primary">
                                                <i class="fas fa-handshake fa-5x"></i>
                                                {{-- <img src="{{asset('img/volunteering-intro/step-3-3.jpg')}}" style="width:100px" class="m-3"> --}}
                                            </div>
                                            <div class="col-9 justify-content-center d-flex align-items-center">  
                                                <span><b>Committee Member</b> - Fund Supported Pool Regional Committee members raise the awareness of local charities and PECSF annual campaign. 
                                                    Members select and allocate funds to charitable organizations on a three-year cycle. Contact PECSF at <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a> 
                                                    to express your interest in starting a committee in your region or volunteer on an existing committee (if available).</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- page 6 --}}
                        <div class="carousel-item text-center">
                            <h3 class="text-primary my-3 pb-4">
                                How volunteering with PECSF works
                            </h3>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <img src="{{asset('img/volunteering-intro/step-4.jpg')}}" alt="" style="max-width: 160px;">
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-7 text-left">
                                    <h4 class="text-primary mt-5">
                                        Step 1: Register as a volunteer
                                    </h4>
                                    <p>
                                        We want you to personalize your volunteer experience as much as possible and be able to connect you with other volunteers in your organization, region, or office.  
                                    </p>
                                    <p>
                                        In order to do so, we need to gather some information about your current role, organization, and location. 
                                    </p>
                                </div>
                            </div>
                        </div>


                        {{-- page 7 --}}
                        <div class="carousel-item text-center">
                            <h3 class="text-primary my-3 pb-4">
                                How volunteering with PECSF works
                            </h3>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <img src="{{asset('img/volunteering-intro/step-5.jpg')}}" alt="" style="max-width: 160px;">
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-7 text-left">
                                    <h4 class="text-primary mt-5">
                                        Step 2: Attend Volunteer Training
                                    </h4>
                                    <p>
                                        All volunteers are required to attend annual virtual volunteer training.
                                    </p>
                                    <p>
                                        Visit the training section to learn more about the various courses offered to help prepare you 
                                        with all of the tools and resources to help you succeed and enjoy your experience as a volunteer. 
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- page 8 --}}
                        <div class="carousel-item text-center">
                            <h3 class="text-primary my-3 pb-4">
                                How volunteering with PECSF works
                            </h3>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <i class="fab fa-microblog fa-10x"></i>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-7 text-left">
                                    <h4 class="text-primary mt-5">
                                        Step 3: Share your experience!
                                    </h4>
                                    <p>
                                        Volunteers often find great fulfillment in sharing their experiences and hearing about the experience of others. 
                                        You can share your experience through quotes, photos, videos or blogs about your experience, your fellow volunteers, 
                                        or about charities you are passionate about. By sharing these experiences, you can reflect on your growth while inspiring 
                                        others to get involved with PECSF and their community. Share your volunteer experience with PECSF HQ at 
                                        <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a>
                                        to illustrate the impact of volunteerism and to help motivate others to join and help create a culture of generosity.
                                    </p>
                                    <p>
                                        <b>Are you ready to become a volunteer?</b>
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            {{-- <div class="modal-footer d-flex">
                <x-button href="#learnGuideCarousel" style="outline-primary" class="prev-btn d-none" role="button" data-slide="prev">Back</x-button>
                <div class="flex-fill"></div>
                <x-button href="#learnGuideCarousel" role="button" class="next-btn" data-slide="next">Next</x-button>
                <x-button data-toggle="modal" data-target="#volunteer-registration" role="button" class="ready-btn d-none">I'm ready to Volunteer!</x-button>
            </div> --}}
            <div class="modal-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-4 text-left">
                            <button type="button" class="btn btn-outline-secondary close-btn" data-dismiss="modal" aria-label="Close">Close</button>
                            <button href="#learnGuideCarousel" class="btn btn-outline-primary btn-md prev-btn d-none" data-slide="prev">Back</button>
                        </div>
                        <div class="col-4 text-center">
                            <h6 class="font-weight-bold">Slide <span class="current_page"> 1 of 8 </span></h6>
                        </div>
                        <div class="col-4 text-right">
                            <x-button href="#learnGuideCarousel" role="button" class="start-btn" data-slide="next">Learn more about how to volunteer</x-button>
                            <x-button href="#learnGuideCarousel" role="button" class="next-btn d-none" data-slide="next">Next</x-button>
                            @if ( \App\Models\CampaignYear::isVolunteerRegistrationOpenNow() )
                                <x-button :href="route('volunteering.profile.create')" role="button" class="ready-btn d-none">I'm ready to volunteer!</x-button>
                            @else
                                <x-button :href="route('volunteering.index')" role="button" class="ready-btn d-none">Close</x-button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('css')

<style>
	#learn-more-modal a {
        text-decoration: underline;
	}

    #learn-more-modal a:hover {
        font-weight: bold;
    }

</style>

@endpush

@push('js')

<script>

    $(function () {

        var learn_total = 8;
        $('#learn-more-modal').on('slide.bs.carousel', function (e) {

            movie_id = $('#movie_player').attr('movie-id');
            $('#movie_player').attr('src', movie_id);
            
            if(e.to == 0) {

                $(this).find(".close-btn").removeClass("d-none");
                $(this).find(".prev-btn").addClass("d-none");
                $(this).find(".start-btn").removeClass("d-none");
                $(this).find(".next-btn").addClass("d-none");
                $(this).find(".ready-btn").addClass("d-none");

                $('.current_page').html( "1 of " + learn_total);
            }
            else if (e.to === (learn_total - 1)) {
                $(this).find(".close-btn").addClass("d-none");
                $(this).find(".next-btn").addClass("d-none");
                $(this).find(".ready-btn").removeClass("d-none");

                $('.current_page').html( (e.to + 1) + " of " + learn_total);
            } else {
                $(this).find(".close-btn").addClass("d-none");
                $(this).find(".start-btn").addClass("d-none");
                $(this).find(".prev-btn").removeClass("d-none");
                $(this).find(".next-btn").removeClass("d-none")
                $(this).find(".ready-btn").addClass("d-none");

                $('.current_page').html( (e.to + 1) + " of " + learn_total);
            }

        })

        $('#learn-more-modal').on('show.bs.modal', function (event) {
            $('#learnGuideCarousel').carousel(0);
            $('#learn-more-modal .current_page').html( "1 of " + learn_total);

            movie_id = $('#movie_player').attr('movie-id');
            $('#movie_player').attr('src', movie_id);
        })

        $("#learn-more-modal").on("hidden.bs.modal", function () {
            $('#movie_player').attr('src', '')
        });

    });

</script>

@endpush
