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
                            <h3 class="text-primary my-3 pb-3">
                                Welcome to PECSF Training!
                            </h3>
                            <div class="my-1">
                                <iframe id="training_guide_movie_player" movie-id="https://www.youtube-nocookie.com/embed/vsyojouAq5U?cc_load_policy=1&rel=0&autoplay=0"
                                    width="800" height="500" src="" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </div>

                        {{-- page 2 --}}
                        <div class="carousel-item page-2">
                            <h3 class="text-primary text-center">
                                What are the different types of volunteer training available?
                            </h3>
                            <h5 class="pt-3 pb-2 text-primary text-center ">
                                PECSF 101 - Did You Know? Canvasser Training 
                            </h5>

                            <div class="row">
                                <div class="col-11 col-md-11">
                                    <p class="px-5 pt-2">An introduction to PECSF program for first-time volunteers, canvassers, and workplace contacts! 
                                        Learn about the benefits of PECSF, how to share them with employees in your area, and feel confident 
                                        in the online pledge system!  
                                    </p>
                                   
                                </div>
                            </div>
                            
                            {{-- <div class="text-center">
                                <img src="{{asset('img/volunteering-intro/step-2.jpg')}}" alt="" style="max-width: 200px;">
                            </div> --}}

                            <div class="row justify-content-center">
                                <div class="col-11 col-md-11">
                                    <p class="font-weight-bold">Upon the completion of training, each employee with be able to: </p>
                                    {{-- <b class="h5 my-3">
                                        Upon the completion of training, each employee with be able to:  
                                    </b> --}}
                                    <ul class="text-left ">
                                        <li><i class="far fa-check-circle text-primary"></i>
                                            Articulate the benefits of the PECSF program with their respective departments and organization.
                                        </li>
                                        <li><i class="far fa-check-circle text-primary"></i>
                                            Demonstrate proficiency in delivering the PECSF Thank You Calendar 
                                            to employees in a timely and professional manner, ensuring effective communication of its significance and purpose.
                                        </li>
                                        <li><i class="far fa-check-circle text-primary"></i>
                                            Effectively display posters and promotional materials related to
                                            the PECSF campaign, as well as collect entry prize draw forms in a manner that maximizes engagement and participation.
                                        </li>
                                        <li><i class="far fa-check-circle text-primary"></i>
                                            Readily assist their colleagues and provide accurate answers to inquiries throughout the duration of the PECSF campaign, 
                                            fostering a supportive and informed environment.
                                        </li>
                                        <li><i class="far fa-check-circle text-primary"></i>
                                            Proficiently navigate the online pledge system, efficiently search for charity choices, and provide assistance to fellow 
                                            employees in selecting charitable organizations during the PECSF campaign.
                                        </li>
                                    </ul>

                                </div>
                            </div>

                            
                        </div>

                        {{-- page 3 --}}
                        <div class="carousel-item page-3">
                            <h5 class="text-primary my-1 pb-2 text-center">
                                PECSF Gaming & Events - Know Your Limit! 
                            </h5>
                            <div class="row justify-content-center">
                                <div class="col-11 col-md-11">
                                    <p>
                                        In depth training for event planners and 50/50 gaming volunteers. 
                                        You will learn the basics of hosting awareness events in the workplace, 
                                        PECSF fundraiser and 50/50 gaming regulations, and step-by-step instructions 
                                        to complete the online PECSF eForm for reporting all events. 
                                    </p>
                                </div>
                            </div>

                            {{-- <div class="text-center">
                                <img src="{{asset('img/volunteering-intro/step-2.jpg')}}" alt="" style="max-width: 200px;">
                            </div> --}}

                            <div class="row justify-content-center">
                                <div class="pt-2 col-11">
                                    <p class="my-3 font-weight-bold">
                                        Upon the completion of training, each employee with be able to:  
                                    </p>
                                    <ul class="text-left  ">
                                        <li><i class="far fa-check-circle text-primary"></i>Demonstrate a foundational understanding of hosting awareness events in the workplace, 
                                            including key principles, planning considerations, and effective implementation strategies, to foster a culture of engagement.</li>
                                        <li><i class="far fa-check-circle text-primary"></i>Competently understand the regulatory framework governing 50/50 gaming activities, including essential do's and don'ts,
                                             as well as comprehensive knowledge of all mandatory reporting requirements, ensuring compliance and integrity in gaming events. </li>
                                        <li><i class="far fa-check-circle text-primary"></i>Confidently utilize the online PECSF e-Form through step-by-step instructions, aimed at simplifying banking processes and 
                                            online reporting while ensuring accuracy in financial transactions (including one-time cash/cheque donations) and reporting procedures.</li>
                                    </ul>
                                    
                                </div>
                            </div>

                        </div>

                        {{-- page 4 --}}
                        <div class="carousel-item page-4">
                            <h5 class="text-primary my-1 pb-2 text-center">
                                PECSF Lead Coordinator  
                            </h5>
                            
                            <div class="row justify-content-center">
                                <div class="col-11">
                                    <p class="text-left">
                                        In depth training for PECSF lead coordinators that are responsible for leading their entire organization’s campaign. 
                                        This course provides information on PECSF, how to coordinate your volunteers, how to create a successful action plan 
                                        and how to conduct donor incentive draws.  
                                    </p>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                {{-- <div class="col-12 text-center">
                                    <img src="{{asset('img/volunteering-intro/step-2.jpg')}}" alt="" style="max-width: 200px;">
                                </div> --}}
                                
                                <div class="col-11 col-md-11">

                                    <p class="my-1 font-weight-bold">
                                        Upon the completion of training, each employee with be able to:  
                                    </p>

                                    <ul class="text-left ">
                                        <li><i class="far fa-check-circle text-primary"></i>Effectively lead and oversee their ministry/organization's workplace campaign. </li>
                                        <li><i class="far fa-check-circle text-primary"></i>Strategically apply insights, management techniques, and communication strategies to lead the 
                                            campaign to success, fostering engagement, and achieving fundraising goals.  </li>
                                        <li><i class="far fa-check-circle text-primary"></i>Proficiently collaborate with their executive sponsor, communications team, and PECSF committee to 
                                            develop a focused and goal-oriented Campaign Action Plan. </li>
                                        <li><i class="far fa-check-circle text-primary"></i>Demonstrate the ability to set the tone for their campaign, establish a clear campaign cycle timeframe, 
                                            and effectively recruit, develop, and support a team of volunteers. </li>
                                        <li><i class="far fa-check-circle text-primary"></i>Understand PECSF’s Donor Privacy Policy and its direct application to Donor Incentive Draws. You will have a clear understanding of 
                                            the policy's principles, ensuring compliance and ethical handling of donor information within the context of participation incentive draws.</li>
                                    </ul>

                                    <p class="px-2 pt-2">
                                        You work closely with PECSF HQ to ensure successful outcomes including receiving ministry/organization specific campaign statistics 
                                        for the purposes of participation incentive draws upon completion of privacy enhancement training and sign off procedure. 
                                    </p>

                                    {{-- <p class="pt-2">
                                        Become a PECSF Lead Coordinator in your office!
                                        @if ( \App\Models\CampaignYear::isVolunteerRegistrationOpenNow() )
                                            <x-button :href="route('volunteering.profile.create')" role="button" class="">I'm ready to volunteer!</x-button>
                                        @endif
                                        <a target="_blank" href="https://learning.gov.bc.ca/psc/CHIPSPLM/EMPLOYEE/ELM/c/LM_OD_EMPLOYEE_FL.LM_FND_LRN_FL.GBL?Action=U&KWRD=PECSF">
                                            Find Learning (gov.bc.ca)
                                        </a>
                                    </p> --}}

                                </div>
                            </div>

                        </div>

                        {{-- page 5 --}}
                        {{-- <div class="carousel-item page-5">
                            <h3 class="text-primary text-center my-3">
                                Still have question about volunteering or volunteer training?
                            </h3>
                            <div class="row pt-5 mb-3">
                                <div class="col-12 col-md-4 offset-md-2">

                                    <ul class="text-left  ">
                                        <li><i class="far fa-check-circle text-primary"></i>Read the questions in our <a target="_blank" href="/contact">FAQ section</a></li>
                                        <li><i class="far fa-check-circle text-primary"></i>Visit the PECSF website - <a target="_blank" href="https://www2.gov.bc.ca/gov/content/careers-myhr/about-the-bc-public-service/corporate-social-responsibility/pecsf/volunteer" target="_blank"> 
                                                    Become a Volunteer section</a></li>
                                        <li><i class="far fa-check-circle text-primary"></i>Contact <a href="mailto:kristina.allsopp@gov.bc.ca">Kristina Allsopp</a>, PECSF Volunteer Experience and Training Analyst</li>
                                    </ul>
                                </div>

                                <div class="col-12 col-md-4 offset-md-1">
                                    <img src="{{asset('img/volunteering-intro/step-5.jpg')}}" alt="" style="max-width: 300px;">
                                </div>
                            </div>
                        </div> --}}
                        <div class="carousel-item text-center page-5">
                            <h3 class="text-primary my-3 pb-4">
                                How volunteering with PECSF works
                            </h3>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <img src="{{asset('img/volunteering-intro/step-5.jpg')}}" alt="" style="max-width: 200px;">
                                </div>
                            </div>
                            <div class="row pt-3">
                                <div class="col-8 offset-md-3">
                                    <ul class="text-left">
                                        <li><i class="far fa-check-circle text-primary"></i>Read the questions and answers in our <a target="_blank" href="/contact">FAQ section</a></li>
                                        <li><i class="far fa-check-circle text-primary"></i>Visit the PECSF website - <a target="_blank" href="https://www2.gov.bc.ca/gov/content/careers-myhr/about-the-bc-public-service/corporate-social-responsibility/pecsf/volunteer" target="_blank"> 
                                                    Become a Volunteer section</a></li>
                                        <li><i class="far fa-check-circle text-primary"></i>Contact <a href="mailto:kristina.allsopp@gov.bc.ca">Kristina Allsopp</a>, PECSF Volunteer Experience and Training Analyst</li>
                                    </ul>
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
            <div class="modal-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-4 text-left">
                            <button type="button" class="btn btn-outline-secondary close-btn" data-dismiss="modal" aria-label="Close">Close</button>
                            <button href="#trainingGuideCarousel" class="btn btn-outline-primary btn-md prev-btn d-none" data-slide="prev">Back</button>
                        </div>
                        <div class="col-4 text-center">
                            <h6 class="font-weight-bold">Slide <span class="current_page"> 1 of 5 </span></h6>
                        </div>
                        <div class="col-4 text-right">
                            <x-button href="#trainingGuideCarousel" role="button" class="start-btn" data-slide="next">Learn more about training</x-button>
                            <x-button href="#trainingGuideCarousel" role="button" class="next-btn d-none" data-slide="next">Next</x-button>
                            <x-button role="button" class="ready-btn d-none">I'm ready for training!</x-button>
                        </div>
                    </div>
                </div>
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


    /* #training-guide-modal ul li {
        display: list-item;
        list-style: none;
        margin-left: 0;
        padding-left: 0;
        margin: 1.2em;
    } */

    #training-guide-modal  ul {
        list-style: none;
        margin-left: 0;
        padding-left: 0;
    }

    #training-guide-modal  li {
        list-style: none;
        padding-left: 1em;
        text-indent: -1em;
        margin: 1.2em;
    }

    #training-guide-modal .page-2 ul li i {
        padding-right: 11px !important;
    }
    #training-guide-modal .page-3 ul li i {
        padding-right: 14px !important;
    }

    #training-guide-modal .page-4 ul li i {
        padding-right: 14px !important;
    }

    #training-guide-modal .page-5 ul li i {
        padding-right: 14px !important;
    }

</style>

@endpush

@push('js')

<script>

    $(function () {

        var training_total = 5;
        $('#training-guide-modal').on('slide.bs.carousel', function (e) {

            movie_id = $('#training_guide_movie_player').attr('movie-id');
            $('#training_guide_movie_player').attr('src', movie_id);
            
            if(e.to == 0) {

                $(this).find(".close-btn").removeClass("d-none");
                $(this).find(".prev-btn").addClass("d-none");
                $(this).find(".start-btn").removeClass("d-none");
                $(this).find(".next-btn").addClass("d-none");
                $(this).find(".ready-btn").addClass("d-none");

                $('.current_page').html( "1 of " + training_total);
            }
            else if (e.to === training_total -1 ) {
                $(this).find(".close-btn").addClass("d-none");
                $(this).find(".next-btn").addClass("d-none");
                $(this).find(".ready-btn").removeClass("d-none");

                $('.current_page').html( (e.to + 1) + " of " + training_total);
            } else {
                $(this).find(".close-btn").addClass("d-none");
                $(this).find(".start-btn").addClass("d-none");
                $(this).find(".prev-btn").removeClass("d-none");
                $(this).find(".next-btn").removeClass("d-none")
                $(this).find(".ready-btn").addClass("d-none");

                $('.current_page').html( (e.to + 1) + " of " + training_total);
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

        $('#training-guide-modal button.btn.ready-btn').on('click',  function () {
            $('#training-guide-modal').modal('hide');

            window.open(
                "https://learning.gov.bc.ca/psc/CHIPSPLM/EMPLOYEE/ELM/c/LM_OD_EMPLOYEE_FL.LM_FND_LRN_FL.GBL?Action=U&KWRD=PECSF",
                '_blank' // <- This is what makes it open in a new window.
            );
            
        });

    });

</script>

@endpush