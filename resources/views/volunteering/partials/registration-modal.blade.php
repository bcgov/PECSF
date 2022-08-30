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
                                                        <option value="Prefer not to say">Prefer not to say</option>
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
                                                <select name="preferred_role" id="" class="form-control" required>
                                                    <option value="">Please Select</option>
                                                    <option value="Canvasser">Canvasser</option>
                                                    <option value="Lead Coordinator">Lead Coordinator</option>
                                                    <option value="Office Contact">Office Contact</option>
                                                    <option value="Event Planner">Event Planner</option>
                                                </select>
                                            </div>
                                        </div>

                            </div>


                                <div class="row mt-5">
                                    <x-button href="#volunteer-registration-carousel" style="outline-primary" class="prev-btn d-none" role="button" data-slide="prev">Previous</x-button>
                                    &nbsp;
                                    <x-button href="#volunteer-registration-carousel" role="button" class="next-btn" data-slide="next">Next</x-button>
                                    &nbsp;
                                    <x-button href="#volunteer-registration-carousel" role="button" class="finish-btn d-none" data-slide="next">Finish Registration</x-button>
                                    &nbsp;
                                    <x-button href="#" role="button" class="signup-btn d-none">Begin Volunteer Training</x-button>
                                </div>
                            </div>
                            <div class="carousel-item p-3" data-step="2">
                                <h1 class="text-primary">Recognition Items</h1>
                                <p class="text-muted">At the end of every campaign, PECSF distributes recognition items to all volunteers. Please select if you would like us to use your address as shown in the Global Address Listing or enter a new address in the field below.</p>

                                <div class="row text-left mt-4">
                                    <div class="col">
                                        <label>
                                            <input checked type="radio" name="address_type" value="global">
                                            Use my Global Address Listing
                                        </label>
                                    </div>
                                </div>
                                <div class="row text-left mt-4">
                                    <div class="col">
                                        <label>
                                            <input type="radio" name="address_type" value="new_address">
                                            Use the following address:
                                        </label>
                                    </div>
                                </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Street address</label>
                                                            <input name="street_address" type="text" class="form-control" placeholder="">
                                                            <span id="street_address_error" class="text-danger"></span>

                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-4">
                                                            <label>City</label>
                                                            <select name="city" class="form-control">
                                                                <option value="">Select a City</option>
                                                                @foreach($cities as $city)
                                                                    <option value="{{$city->city}}">{{$city->city}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span id="city_error" class="text-danger"></span>
                                                        </div>


                                                        <div class="col-md-4">
                                                            <label>Province</label>
                                                            <select class="form-control" name="province">
                                                                <option value="">Select a Province</option>
                                                                <option value="Alberta">Alberta</option>
                                                                <option value="British Columbia">British columbia</option>
                                                                <option value="Manitoba">Manitoba</option>
                                                                <option value="New Brunswick">New brunswick</option>
                                                                <option value="Newfoundland and Labrador">Newfoundland and labrador</option>
                                                                <option value="Nova Scotia">Nova scotia</option>
                                                                <option value="Nunavut">Nunavut</option>
                                                                <option value="Prince Edward Island">Prince edward island</option>
                                                                <option value="Quebec">Quebec</option>
                                                                <option value="Saskatchewan">Saskatchewan</option>
                                                                <option value="Yukon">Yukon</option>

                                                                <option value="Ontario">Ontario</option>
                                                            </select>
                                                            <span id="province_error" class="text-danger"></span>
                                                        </div>


                                                        <div class="col-md-4">
                                                            <label>Postal Code</label>
                                                            <input name="postal_code" type="text" class="form-control" placeholder="">
                                                            <span id="postal_code_error" class="text-danger"></span>
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
                                    <x-button href="#volunteer-registration-carousel" style="outline-primary" class="prev-btn d-none" role="button" data-slide="prev">Back</x-button>
                                    &nbsp;
                                    <x-button href="#volunteer-registration-carousel" role="button" class="next-btn" data-slide="next">Next</x-button>
                                    &nbsp;
                                    <x-button href="#volunteer-registration-carousel" role="button" class="finish-btn d-none" data-slide="next">Finish Registration</x-button>
                                    &nbsp;
                                    <x-button href="#" role="button" class="signup-btn d-none">Begin Volunteer Training</x-button>
                                </div>
                            </div>


                            <div class="carousel-item p-3"  data-step="3">
                                <h1 class="text-primary">Confirmation</h1>
                                <h2 class="text-primary">Your Details</h2>
                                <div class="row">
                                    <div class="col-12 col-md-12 ">
                                        <div class="d-flex p-3 flex-column" id="summary-table">
                                            <div class="d-flex ">
                                            <strong>
                                                Your Organization
                                            </strong>
                                            </div>
                                            <div class="d-flex">
                                                <div data-value-for="organization">
                                                    Value
                                                </div>
                                            </div>

                                            <div class="d-flex mt-2">
                                               <strong>
                                                    Number of years you have been volunteering with PECSF
                                               </strong>
                                            </div>
                                            <div class="d-flex">
                                                <div data-value-for="no_of_years">
                                                    Value
                                                </div>
                                            </div>

                                            <div class="d-flex mt-2">
                                                <strong>
                                                    Your preferred Volunteer Role
                                                </strong>
                                            </div>
                                            <div class="d-flex">
                                                <div data-value-for="preferred_role">
                                                    Value
                                                </div>
                                            </div>
                                            <div class="d-flex mt-1">
                                                <div>
                                                    <h2 class="text-primary">Mailing Address</h2>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div data-value-for="address_type">
                                                    <h3>Value</h3>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <hr>
                                                <p>Personal information collected through this registration process is collected by the BC Public Service Agency for the purpose of facilitating PECSF volunteering recognition activities and program improvements under section 26 (c) and (e) of the Freedom of Information and Protection of Privacy Act</p>
                                            <p>Questions about the collection of your personal information can be directed to the Campaign Manager Provincial Employees Community Services Fund at 250 356-1736 or <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a></p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row mt-1">
                                    <x-button href="#volunteer-registration-carousel" style="outline-primary" class="prev-btn d-none" role="button" data-slide="prev">Back</x-button>
                                    &nbsp;
                                    <x-button href="#volunteer-registration-carousel" role="button" class="next-btn" data-slide="next">Next</x-button>
                                    &nbsp;
                                    <x-button href="#volunteer-registration-carousel" role="button" class="finish-btn d-none" data-slide="next">Register</x-button>
                                    &nbsp;
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
