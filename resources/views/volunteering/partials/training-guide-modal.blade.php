<!-- Modal -->
<div class="modal fade" id="training-guide-modal" tabindex="-1" aria-labelledby="trainingModalTitle" data-backdrop="static"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" aria-label="Welcome to Volunteering!">Training</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="min-height: 650px;">
                <div id="trainingGuideCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
                    <div class="carousel-inner">
                        {{-- page 1 --}}
                        <div class="carousel-item active text-center">
                            <h3 class="text-primary my-3 pb-5">
                                Welcome to PECSF Training!
                            </h3>
                            <div class="my-1 border-dark">
                                <iframe id="training_guide_movie_player" movie-id="https://www.youtube-nocookie.com/embed/vsyojouAq5U?cc_load_policy=1&rel=0&autoplay=0"
                                    width="640" height="400" src="" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </div>

                        {{-- page 2 --}}
                        <div class="carousel-item ">
                            <h3 class="text-primary text-center">
                                What are the different types of volunteer training available?
                            </h3>
                            <h4 class="text-primary text-center ">
                                PECSF 101 – Did You Know? Canvasser Training 
                            </h4>
                            <p class="px-5 pt-2">An introduction to PECSF program for first-time volunteers, canvassers, and workplace contacts! 
                                Learn about the benefits of PECSF, how to share them with employees in your area, and feel confident 
                                in the online pledge system!  
                            </p>
                            <p class="px-5 font-weight-bold">Upon the completion of training, each employee with be able to:  </p>

                            <div class="list-group list-group-flush px-5 text-left">
                                <div class="list-group-item list-group-item-action">
                                    <i class="far fa-check-square fa-1x"></i>
                                    <span class="pl-2">Articulate the benefits of the PECSF program with their respective departments and organization.
                                    </span> 
                                </div>
                                <div class="list-group-item list-group-item-action">
                                    <i class="far fa-check-square fa-1x"></i>
                                    <span class="pl-2">Demonstrate proficiency in delivering the PECSF Thank You Calendar to employees in a timely and professional manner, 
                                        ensuring effective communication of its significance and purpose. 
                                    </span>
                                </div>
                                <div class="list-group-item list-group-item-action">
                                    <i class="far fa-check-square fa-1x"></i>
                                    <span class="pl-2">Effectively display posters and promotional materials related to
                                    the PECSF campaign, as well as collect entry prize draw forms in a manner that maximizes engagement and participation.
                                    </span>
                                </div>

                                <div class="list-group-item list-group-item-action">
                                    <i class="far fa-check-square fa-1x"></i>
                                    <span class="pl-2">Readily assist their colleagues and provide accurate answers to inquiries throughout the duration 
                                            of the PECSF campaign, fostering a supportive and informed environment. 
                                    </span>
                                </div>
                                <div class="list-group-item list-group-item-action">
                                    <i class="far fa-check-square fa-1x"></i>
                                    <span class="pl-2">Proficiently navigate the online pledge system, efficiently search for charity choices, 
                                        and provide assistance to fellow employees in selecting charitable organizations during the PECSF campaign. 
                                    </span>
                                </div>
                                
                               
                            </div>

                            <p class="px-5 pt-4">
                                Become a PECSF Canvasser and Champion in your office!  
                                @if ( \App\Models\CampaignYear::isVolunteerRegistrationOpenNow() )
                                    <x-button :href="route('volunteering.profile.create')" role="button" class="">I'm ready to volunteer!</x-button>
                                @endif
                                <a href="#link">Find Learning (gov.bc.ca)</a>
                            </p>

                        </div>

                        {{-- page 3 --}}
                        <div class="carousel-item ">
                            <h3 class="text-primary my-1 pb-2 text-center">
                                PECSF Gaming & Events - Know Your Limit! 
                            </h3>
                            <p>
                                In depth training for event planners and 50/50 gaming volunteers. 
                                You will learn the basics of hosting awareness events in the workplace, 
                                PECSF fundraiser and 50/50 gaming regulations, and step-by-step instructions 
                                to complete the online PECSF eForm for reporting all events. 
                            </p>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <img src="{{asset('img/volunteering-intro/step-2.jpg')}}" alt="" style="max-width: 200px;">
                                </div>
                                <div class="pt-2 col-10 col-md-10 offset-md-1">
                                    <b class="h5 my-3">
                                        Upon the completion of training, each employee with be able to:  
                                    </b>
                                    <ul class="text-left check-bullet ">
                                        <li><x-bullet />Demonstrate a foundational understanding of hosting awareness events in the workplace, 
                                            including key principles, planning considerations, and effective implementation strategies, to foster a culture of engagement.</li>
                                        <li><x-bullet />Competently understand the regulatory framework governing 50/50 gaming activities, including essential do's and don'ts,
                                             as well as comprehensive knowledge of all mandatory reporting requirements, ensuring compliance and integrity in gaming events. </li>
                                        <li><x-bullet />Confidently utilize the online PECSF e-Form through step-by-step instructions, aimed at simplifying banking processes and 
                                            online reporting while ensuring accuracy in financial transactions (including one-time cash/cheque donations) and reporting procedures.</li>
                                    </ul>
                                </div>
                            </div>

                            <p class="px-5 pt-4">
                                Become a PECSF Event Coordinator in your office!
                                @if ( \App\Models\CampaignYear::isVolunteerRegistrationOpenNow() )
                                    <x-button :href="route('volunteering.profile.create')" role="button" class="">I'm ready to volunteer!</x-button>
                                @endif
                                <a href="#link">Find Learning (gov.bc.ca)</a>
                            </p>

                        </div>

                        {{-- page 4 --}}
                        <div class="carousel-item ">
                            <h3 class="text-primary my-1 pb-2 text-center">
                                PECSF Lead Coordinator  
                            </h3>
                            <p>
                                In depth training for PECSF lead coordinators that are responsible for leading their entire organization’s campaign. 
                                This course provides information on PECSF, how to coordinate your volunteers, how to create a successful action plan 
                                and how to conduct donor incentive draws.  
                            </p>
                            <div class="row">
                                {{-- <div class="col-12 text-center">
                                    <img src="{{asset('img/volunteering-intro/step-2.jpg')}}" alt="" style="max-width: 200px;">
                                </div> --}}
                                <div class="px-2 pt-2 col-12 col-md-12 ">
                                    <b class="h5 my-3">
                                        Upon the completion of training, each employee with be able to:  
                                    </b>
                                    <ul class="text-left check-bullet ">
                                        <li><x-bullet />Effectively lead and oversee their ministry/organization's workplace campaign. </li>
                                        <li><x-bullet />Strategically apply insights, management techniques, and communication strategies to lead the 
                                            campaign to success, fostering engagement, and achieving fundraising goals.  </li>
                                        <li><x-bullet />Proficiently collaborate with their executive sponsor, communications team, and PECSF committee to 
                                            develop a focused and goal-oriented Campaign Action Plan. You will acquire the skills needed to identify objectives, 
                                            allocate resources, and create actionable strategies aimed at maximizing campaign success and impact.</li>
                                        <li><x-bullet />Demonstrate the ability to set the tone for their campaign, establish a clear campaign cycle timeframe, 
                                            and effectively recruit, develop, and support a team of volunteers. You will acquire the skills necessary to 
                                            inspire and lead their team, ensuring alignment with campaign objectives and fostering a collaborative and supportive environment.</li>
                                        <li><x-bullet />    
                                            Understand PECSF’s Donor Privacy Policy and its direct application to Donor Incentive Draws. You will have a clear understanding of 
                                            the policy's principles, ensuring compliance and ethical handling of donor information within the context of participation incentive draws.</li>
                                    </ul>
                                </div>
                            </div>

                            <p class="px-2 pt-4">
                                You work closely with PECSF HQ to ensure successful outcomes including receiving ministry/organization specific campaign statistics 
                                for the purposes of participation incentive draws upon completion of privacy enhancement training and sign off procedure. 
                            </p>

                            
                            <p class="px-5 pt-4">
                                Become a PECSF Lead Coordinator in your office!
                                @if ( \App\Models\CampaignYear::isVolunteerRegistrationOpenNow() )
                                    <x-button :href="route('volunteering.profile.create')" role="button" class="">I'm ready to volunteer!</x-button>
                                @endif
                                <a href="#link">Find Learning (gov.bc.ca)</a>
                            </p>

                        </div>

                        {{-- page 5 --}}
                        <div class="carousel-item">
                            <h3 class="text-primary text-center my-3">
                                Still have question about volunteering or volunteer training?
                            </h3>
                            <div class="row mb-3">
                                <div class="col-12 col-md-4 offset-md-2">

                                    <ul class="text-left check-bullet ">
                                        <li><x-bullet />Read the questions in our FAQ section </li>
                                        <li><x-bullet />Visit the PECSF website – 
                                                <a href="https://www2.gov.bc.ca/gov/content/careers-myhr/about-the-bc-public-service/corporate-social-responsibility/pecsf/volunteer" target="_blank"> 
                                                    Become a Volunteer section </a></li>
                                        <li><x-bullet />Contact <a href="mailto:kristina.allsopp@gov.bc.ca">Kristina Allsopp</a>, PECSF Volunteer Experience and Training Analyst</li>
                                    </ul>
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
                <x-button href="#trainingGuideCarousel" style="outline-primary" class="prev-btn d-none" role="button" data-slide="prev">Back</x-button>
                <div class="flex-fill"></div>
                <x-button href="#trainingGuideCarousel" role="button" class="next-btn" data-slide="next">Next</x-button>
                <x-button data-toggle="modal" data-target="#volunteer-registration" role="button" class="ready-btn d-none">I'm ready to Volunteer!</x-button>
            </div> --}}
            <div class="modal-footer d-flex">
                <button type="button" class="btn btn-outline-secondary close-btn" data-dismiss="modal" aria-label="Close">Close</button>
                <button href="#trainingGuideCarousel" class="btn btn-outline-primary btn-md prev-btn d-none" data-slide="prev">Back</button>
                <div class="flex-fill">
                    <div class="text-center ">
                        <h6 class="font-weight-bold" style="padding-left: 190px;">Slide <span class="current_page"> 1 of 5 </span></h6>
                    </div>
                </div>
                <x-button href="#trainingGuideCarousel" role="button" class="start-btn" data-slide="next">Learn more about how to volunteer</x-button>
                <x-button href="#trainingGuideCarousel" role="button" class="next-btn d-none" data-slide="next">Next</x-button>
                @if ( \App\Models\CampaignYear::isVolunteerRegistrationOpenNow() )
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
	#training-guide-modal a {
        text-decoration: underline;
	}

    #training-guide-modal a:hover {
        font-weight: bold;
    }

</style>

@endpush

@push('js')

<script>

    $(function () {

        var total = 5;
        $('#training-guide-modal').on('slide.bs.carousel', function (e) {

            movie_id = $('#training_guide_movie_player').attr('movie-id');
            $('#training_guide_movie_player').attr('src', movie_id);
            
            if(e.to == 0) {

                $(this).find(".close-btn").removeClass("d-none");
                $(this).find(".prev-btn").addClass("d-none");
                $(this).find(".start-btn").removeClass("d-none");
                $(this).find(".next-btn").addClass("d-none");
                $(this).find(".ready-btn").addClass("d-none");

                $('.modal-footer h6').css('padding-left', '190px');
                $('.current_page').html( "1 of " + total);
            }
            else if (e.to === total -1 ) {
                $(this).find(".close-btn").addClass("d-none");
                $(this).find(".next-btn").addClass("d-none");
                $(this).find(".ready-btn").removeClass("d-none");

                $('.modal-footer h6').css({'padding-left':''});
                $('.current_page').html( (e.to + 1) + " of " + total);
            } else {
                $(this).find(".close-btn").addClass("d-none");
                $(this).find(".start-btn").addClass("d-none");
                $(this).find(".prev-btn").removeClass("d-none");
                $(this).find(".next-btn").removeClass("d-none")
                $(this).find(".ready-btn").addClass("d-none");

                $('.modal-footer h6').css({'padding-left':''});
                $('.current_page').html( (e.to + 1) + " of " + total);
            }

        })

        $('#training-guide-modal').on('show.bs.modal', function (event) {
            $('#trainingGuideCarousel').carousel(0);
            movie_id = $('#training_guide_movie_player').attr('movie-id');
            $('#training_guide_movie_player').attr('src', movie_id);
        })

        $("#training-guide-modal").on("hidden.bs.modal", function () {
            $('#training_guide_movie_player').attr('src', '')
        });

    });

</script>

@endpush