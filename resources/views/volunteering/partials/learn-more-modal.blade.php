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
                <div id="donateGuideCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
                    <div class="carousel-inner">
                        {{-- page 1 --}}
                        <div class="carousel-item active text-center">
                            <h3 class="text-primary my-3 pb-5">
                                Welcome to PECSF Volunteering!
                            </h3>
                            <div class="my-1">
                                <iframe id="movie_player" movie-id="https://www.youtube-nocookie.com/embed/ui-7PMerNnU?cc_load_policy=1&rel=0&autoplay=0"
                                    width="560" height="315" src="" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
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
                                    <ul class="check-bullet">
                                        <li><x-bullet />Putting your public service values into action</li>
                                        <li><x-bullet />Making a difference in your community</li>
                                        {{-- <li><x-bullet />Challenge yourself</li> --}}
                                        <li><x-bullet />Building your team spirit, strengthens and connections</li>
                                        <li><x-bullet />Showcasing or developing your skills as a leader </li>
                                        <li><x-bullet />Network with others across the provincial government</li>
                                        <li><x-bullet />Offering your skills - there are so many ways to contribute!</li>
                                    </ul>
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
                            <div class="row">
                                <div class="col-12">
                                    <img src="{{asset('img/volunteering-intro/step-2.jpg')}}" alt="" style="max-width: 300px;">
                                </div>
                                <div class="col-12 col-md-6 offset-md-3">
                                    <b class="h5 my-3">
                                        As a volunteer you commit to:
                                    </b>
                                    <ul class="text-left check-bullet ">
                                        <li><x-bullet />Volunteering during the September to November awareness campaign</li>
                                        <li><x-bullet />Attend the campaign kick-off event in your region (where applicable)</li>
                                        <li><x-bullet />Two hours of PECSF online volunteering training</li>
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
                                <div class="row text-left">
                                    <div class="col-6 col-md-6">
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
                                            <div class="col-3">
                                                <img src="{{asset('img/volunteering-intro/step-3-2.jpg')}}" style="width:100px" class="m-3">
                                            </div>
                                            <div class="col-9 justify-content-center d-flex align-items-center">  
                                                <span><b>Canvassers</b> - In this role, you help donors with making their pledges, distribute the annual PECSF calendar, share information about the PECSF program with colleagues and collect the campaign prize draw entries. </span>
                                            </div>
                                        </div>

                                        <div class="row pt-4">
                                            <div class="col-3">
                                                <img src="{{asset('img/volunteering-intro/step-3-3.jpg')}}" style="width:100px" class="m-3">
                                            </div>
                                            <div class="col-9 justify-content-center d-flex align-items-center">  
                                                <span><b> Event Coordinators</b> -  In this role, you build awareness and 
                                                    enthusiasm for the campaign by creating and running gaming and fundraising events 
                                                    as outlined in the lead coordinator’s organizations plan.</span>
                                            </div>
                                        </div>

                                        <div class="row pt-4">
                                            <div class="col-3">
                                                <img src="{{asset('img/volunteering-intro/step-3-1.jpg')}}" style="width:100px" class="m-3">
                                            </div>
                                            <div class="col-9 justify-content-center d-flex align-items-center">  
                                                <span><b>Executive Sponsor</b> - In this role, you assist the Lead Coordinator 
                                                    in spreading the message about the benefits of PECSF by sending out executive messages to 
                                                    staff, sharing at the executive table and attending PECSF events.</span>
                                            </div>
                                        </div>

                                        

                                        
                                    </div>
                                
                                {{-- right  --}}
                                
                                    <div class="col-6 col-md-6">
                                        {{-- <div class="px-3">
                                            <img src="{{asset('img/volunteering-intro/step-3-1.jpg')}}" style="width:100px" class="m-3">
                                            <div class="d-flex"><b>Coordinators</b> - lead and set the tone of workspace campaign</div>
                                        </div>
                                        <div class="px-3">
                                            <img src="{{asset('img/volunteering-intro/step-3-2.jpg')}}" style="width:100px" class="m-3">
                                            <b> Canvassers</b> - provide one-on-one contact with Employees
                                        </div>
                                        <div class="px-3">
                                            <img src="{{asset('img/volunteering-intro/step-3-3.jpg')}}" style="width:100px" class="m-3">
                                            <b> Event Coordinators</b> - plan and run successful fundraising events
                                        </div> --}}

                                        <div class="row">
                                            <div class="col-3">
                                                <img src="{{asset('img/volunteering-intro/step-3-1.jpg')}}" style="width:100px" class="m-3">
                                            </div>
                                            <div class="col-9 justify-content-center d-flex align-items-center">  
                                                <span><b>Lead Coordinators</b> - In this role, you recruit canvassers, event planners, 
                                                        other volunteers to help make an action plan, procure resources, delegate tasks to 
                                                        volunteers, guide the process and share your team’s success with your organization.</span>
                                            </div>
                                        </div>

                                        <div class="row pt-4">
                                            <div class="col-3">
                                                <img src="{{asset('img/volunteering-intro/step-3-2.jpg')}}" style="width:100px" class="m-3">
                                            </div>
                                            <div class="col-9 justify-content-center d-flex align-items-center">  
                                                <span><b>Volunteer</b> - This role is a general volunteer, you could be helping to sell 50/50 tickets, assisting with events, 
                                                    distributing calendars, or helping to create PECSF communications in your organization.</span>
                                            </div>
                                        </div>

                                        <div class="row pt-4">
                                            <div class="col-3">
                                                <img src="{{asset('img/volunteering-intro/step-3-3.jpg')}}" style="width:100px" class="m-3">
                                            </div>
                                            <div class="col-9 justify-content-center d-flex align-items-center">  
                                                <span><b>Committee Member</b> - Fund Supported Pool Regional Committee members raise the awareness of local charities 
                                                    and PECSF annual campaign. Members select and allocate funds to charitable organizations on a three-year cycle. 
                                                    Contact PECSF at <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a> to express your interest in starting a committee in your region or (if available) volunteer on an existing committee.</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- page 5 --}}
                        <div class="carousel-item">
                            <h3 class="text-primary text-center my-3">
                                How volunteering with PECSF works
                            </h3>
                            <div class="row">
                                <div class="col-12 col-md-4 offset-md-2">
                                    <h4 class="text-primary mt-5">
                                        Step 1: Register as a volunteer
                                    </h4>
                                    <p>
                                        We want to personalize your new volunteer experience as much as possible.
                                    </p>
                                    <p>
                                        In efforts to do that, we need to gather some info about your current role, your organization, and location so that we can also help you connect with other volunteer in your ministry or branch.
                                    </p>
                                </div>
                                <div class="col-12 col-md-4 offset-md-1">
                                    <img src="{{asset('img/volunteering-intro/step-4.jpg')}}" alt="" style="max-width: 300px;">
                                </div>
                            </div>
                        </div>

                        {{-- page 6 --}}
                        <div class="carousel-item">
                            <h3 class="text-primary text-center my-3">
                                How volunteering with PECSF works
                            </h3>
                            <div class="row">
                                <div class="col-12 col-md-4 offset-md-2">
                                    <h4 class="text-primary mt-5">
                                        Step 2: Attend Volunteer Training
                                    </h4>
                                    <p>
                                        All volunteer are required to attend annual virtual volunteer training.
                                    </p>
                                    <p>
                                        With the new <b><u>PECSF Calendar</u></b>, we've made it much easier for you to register and access all the tools and resources you will need to succeed and enjoy your experience as a volunteer.
                                    </p>
                                </div>
                                <div class="col-12 col-md-4 offset-md-1">
                                    <img src="{{asset('img/volunteering-intro/step-5.jpg')}}" alt="" style="max-width: 300px;">
                                </div>
                            </div>
                        </div>

                        {{-- page 7 --}}
                        <div class="carousel-item">
                            <h3 class="text-primary text-center my-3">
                                How volunteering with PECSF works
                            </h3>
                            <div class="row mb-3">
                                <div class="col-12 col-md-4 offset-md-2">
                                    <h4 class="text-primary mt-5">
                                        Step 3: Share your experience!
                                    </h4>
                                    <p>
                                        With the new <b><u>Volunteer Discussion Board</u></b> you can share photos, videos and encouraging messages with your fellow colleagues to keep the upto date with your overall experience as a PECSF volunteer, and stay connected as a team while we are all working remotely.
                                    </p>
                                    <p>
                                        <b>Are you ready to become a volunteer ?</b>
                                    </p>
                                </div>
                                <div class="col-12 col-md-4 offset-md-1">
                                    <img src="{{asset('img/volunteering-intro/step-5.jpg')}}" alt="" style="max-width: 300px;">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            {{-- <div class="modal-footer d-flex">
                <x-button href="#donateGuideCarousel" style="outline-primary" class="prev-btn d-none" role="button" data-slide="prev">Back</x-button>
                <div class="flex-fill"></div>
                <x-button href="#donateGuideCarousel" role="button" class="next-btn" data-slide="next">Next</x-button>
                <x-button data-toggle="modal" data-target="#volunteer-registration" role="button" class="ready-btn d-none">I'm ready to Volunteer!</x-button>
            </div> --}}
            <div class="modal-footer d-flex">
                <button type="button" class="btn btn-outline-secondary close-btn" data-dismiss="modal" aria-label="Close">Close</button>
                <button href="#donateGuideCarousel" class="btn btn-outline-primary btn-md prev-btn d-none" data-slide="prev">Back</button>
                <div class="flex-fill">
                    <div class="text-center ">
                        <h6 class="font-weight-bold" style="padding-left: 190px;">Slide <span class="current_page"> 1 of 7 </span></h6>
                    </div>
                </div>
                <x-button href="#donateGuideCarousel" role="button" class="start-btn" data-slide="next">Learn more about how to volunteer</x-button>
                <x-button href="#donateGuideCarousel" role="button" class="next-btn d-none" data-slide="next">Next</x-button>
                @if ( $campaignYear->isVolunteerRegistrationOpen() )
                    <x-button :href="route('volunteering.profile.create')" role="button" class="ready-btn d-none">I'm ready to volunteer!</x-button>
                @else
                    <x-button :href="route('volunteering.index')" role="button" class="ready-btn d-none">I'm ready to volunteer!</x-button>
                @endif
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
        $('#learn-more-modal').on('slide.bs.carousel', function (e) {

            movie_id = $('#movie_player').attr('movie-id');
            $('#movie_player').attr('src', movie_id);
            
            if(e.to == 0) {

                $(this).find(".close-btn").removeClass("d-none");
                $(this).find(".prev-btn").addClass("d-none");
                $(this).find(".start-btn").removeClass("d-none");
                $(this).find(".next-btn").addClass("d-none");
                $(this).find(".ready-btn").addClass("d-none");

                $('.modal-footer h6').css('padding-left', '190px');
                $('.current_page').html( "1 of 7");
            }
            else if (e.to === 6) {
                $(this).find(".close-btn").addClass("d-none");
                $(this).find(".next-btn").addClass("d-none");
                $(this).find(".ready-btn").removeClass("d-none");

                $('.modal-footer h6').css({'padding-left':''});
                $('.current_page').html( (e.to + 1) + " of 7");
            } else {
                $(this).find(".close-btn").addClass("d-none");
                $(this).find(".start-btn").addClass("d-none");
                $(this).find(".prev-btn").removeClass("d-none");
                $(this).find(".next-btn").removeClass("d-none")
                $(this).find(".ready-btn").addClass("d-none");

                $('.modal-footer h6').css({'padding-left':''});
                $('.current_page').html( (e.to + 1) + " of 7");
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