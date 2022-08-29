<!-- Modal -->
<div class="modal fade" id="volunteer-registration" tabindex="-1" aria-labelledby="volunteerRegistrationTitle" data-backdrop="static"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div style="background:#1a5a96;color:#fff;padding-left:15px;padding-top:10px;" class="modal-header">
                <h1 style="color:#fff;" class="modal-title" id="volunteerRegistrationTitle">
                    Register As a Volunteer
                </h1>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height: calc(100vh - 200px);">
                <form action="{{route('volunteering.store')}}" method="POST" id="volunteer_registration_form">
                    @csrf


                    <div id="volunteer-registration-carousel" class="carousel slide" data-ride="carousel" data-interval="false">
                        <div class="carousel-inner">

                            <div class="carousel-item p-3 active" data-step="1">
                                <h1 class="text-primary">Volunteer Details</h1>
                                <div class="row mt-5">
                                    <div class="col-12 col-md-6 ">

                                    <div class="step-1 ">
                                        <p class="text-muted">
                                            Your Organization
                                        </p>
                                        <select name="organization_id" id="" class="form-control" required>
                                            <option value="">Please select</option>
                                            @foreach($organizations as $org)
                                                <option value="{{$org->id}}">{{$org->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    </div>
                                </div>
                                <div class="row mt-5">
                                    <div class="col-12 col-md-6">
                                        <div class="step-1">
                                            <div class="step-1">
                                            <p class="text-muted">
                                                How many years have you been working with PECSF
                                            </p>
                                                <select name="no_of_years" id="" class="form-control" required>
                                                    <option value="">Please select</option>
                                                        <option value="-1">Prefer not to say</option>
                                                    <option value="0">0</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select>

                                        </div>
                                    </div>
                                    </div>

                                </div>
                                    <div class="row mt-5">
                                    <div class="col-12 col-md-6">
                                            <div class="step-1">
                                                <p class="text-muted">
                                                    Your Preferred Volunteer Role
                                                </p>
                                                <select name="volunteer_role" id="" class="form-control" required>
                                                    <option value="">Please Select</option>
                                                    <option value="canvasser">Canvasser</option>
                                                    <option value="lead_coordinator">Lead Coordinator</option>
                                                    <option value="office_contract">Office Contract</option>
                                                    <option value="event_planner">Event Planner</option>
                                                </select>
                                            </div>
                                        </div>

                            </div>


                                <div class="row mt-5">
                                    <x-button href="#volunteer-registration-carousel" style="outline-primary" class="prev-btn d-none" role="button" data-slide="prev">Previous</x-button>
                                    <x-button href="#volunteer-registration-carousel" role="button" class="next-btn" data-slide="next">Next</x-button>
                                    <x-button href="#volunteer-registration-carousel" role="button" class="finish-btn d-none" data-slide="next">Finish Registration</x-button>
                                    <x-button href="#" role="button" class="signup-btn d-none">Begin Volunteer Training</x-button>
                                </div>
                            </div>
                            <div class="carousel-item p-3" data-step="2">
                                <h1 class="text-primary">Recognition Items</h1>
                                <p class="text-muted">At the end of every campaign, PECSF distributes recognition items to all volunteers. Please select if you would like us to use your address as shown in the Global Address Listing or enter a new address in the field below.</p>

                                <div class="row text-left mt-4">
                                    <div class="col">
                                        <label>
                                            <input type="radio" selected name="global" value="global">
                                            Use my Global Address Listing
                                        </label>
                                    </div>
                                </div>
                                <div class="row text-left mt-4">
                                    <div class="col">
                                        <label>
                                            <input type="radio" name="global" value="new_address">
                                            Use the following address:
                                        </label>
                                    </div>
                                </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Street address</label>
                                                            <input name="street_address" type="text" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>City</label>
                                                            <select name="city" class="form-control">
                                                                <option value="">Select a City</option>
                                                            </select>
                                                        </div>


                                                        <div class="col-md-4">
                                                            <label>Province</label>
                                                            <select class="form-control" name="province">
                                                                <option value="">Select a Province</option>
                                                            </select>
                                                        </div>


                                                        <div class="col-md-4">
                                                            <label>Postal Code</label>
                                                            <input name="postal_code" type="text" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                            <div class="row text-left mt-4">
                                                <div class="col">
                                                    <label>
                                                        <input type="radio" name="address_type" value="Opt-out">
                                                        I wish to opt-out from receiving recognition items.
                                                    </label>
                                                </div>
                                            </div>

                                <div class="row mt-5">
                                    <x-button href="#volunteer-registration-carousel" style="outline-primary" class="prev-btn d-none" role="button" data-slide="prev">Previous</x-button>
                                    <x-button href="#volunteer-registration-carousel" role="button" class="next-btn" data-slide="next">Next</x-button>
                                    <x-button href="#volunteer-registration-carousel" role="button" class="finish-btn d-none" data-slide="next">Finish Registration</x-button>
                                    <x-button href="#" role="button" class="signup-btn d-none">Begin Volunteer Training</x-button>
                                </div>
                            </div>


                            <div class="carousel-item p-3"  data-step="3">
                                <h1>Confirmation</h1>
<h2>Your Details</h2>
                                <div class="row mt-5">
                                    <div class="col-12 col-md-12 ">
                                        <div class="d-flex p-3 flex-column" id="summary-table">
                                            <div class="d-flex">
                                            <strong>
                                                Your Organization
                                            </strong>
                                            </div>
                                            <div class="d-flex">
                                                <div data-value-for="organization">
                                                    Value
                                                </div>
                                            </div>

                                            <div class="d-flex">
                                                <div>
                                                    Number of years you have been volunteering with PECSF
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div data-value-for="no_of_years">
                                                    Value
                                                </div>
                                            </div>

                                            <div class="d-flex">
                                                <div>
                                                    Your preferred Volunteer Role
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div data-value-for="preferred_role">

                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div>
                                                    <h2 class="text-primary">Mailing Address</h2>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div data-value-for="address_type">
                                                    Value
                                                </div>
                                            </div>
<hr>
                                            <div class="col-md-12">
                                                <p>Personal information collected through this registration process is collected by the BC Public Service Agency for the purpose of facilitating PECSF volunteering recognition activities and program improvements under section 26 (c) and (e) of the Freedom of Information and Protection of Privacy Act</p>
                                            <p>Questions about the collection of your personal information can be directed to the Campaign Manager Provincial Employees Community Services Fund at 250 356-1736 or <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a></p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row mt-5">
                                    <x-button href="#volunteer-registration-carousel" style="outline-primary" class="prev-btn d-none" role="button" data-slide="prev">Previous</x-button>
                                    <x-button href="#volunteer-registration-carousel" role="button" class="next-btn" data-slide="next">Next</x-button>
                                    <x-button href="#volunteer-registration-carousel" role="button" class="finish-btn d-none" data-slide="next">Finish Registration</x-button>
                                    <x-button href="#" role="button" class="signup-btn d-none">Begin Volunteer Training</x-button>
                                </div>
                            </div>
                            <div class="carousel-item p-3"  data-step="4">
                                <h1>Registration Complete</h1>
                                <div class="row mt-5">
                                    <div class="col-12 col-md-6 offset-md-3">
                                        <div class="step-1 text-center">
                                            <p class="text-muted">
                                                Below you will find your first 30-minutes online volunteer training video. When you are finished watching the video, click the button below to sign up for more training events.
                                            </p>
                                            <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/ui-7PMerNnU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-5">
                                    <x-button href="#volunteer-registration-carousel" style="outline-primary" class="prev-btn d-none" role="button" data-slide="prev">Previous</x-button>
                                    <x-button href="#volunteer-registration-carousel" role="button" class="next-btn" data-slide="next">Next</x-button>
                                    <x-button href="#volunteer-registration-carousel" role="button" class="finish-btn d-none" data-slide="next">Finish Registration</x-button>
                                    <x-button href="#" role="button" class="signup-btn d-none">Begin Volunteer Training</x-button>
                                </div>
                            </div>
                            <div class="carousel-item p-3"  data-step="5">
                                <div class="row mt-5">
                                    <div class="col-12 col-md-6 offset-md-3">
                                        <div class="step-1 text-center">
                                            <p class="text-muted">
                                                You have successfully registered as a volunteer with PECSF. <br>
                                                Click the button below to begin volunteer training process.
                                            </p>
                                            <div class="m-5">
                                                <img src="{{asset('img/volunteering-intro/finished-registraion.jpeg')}}" class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
