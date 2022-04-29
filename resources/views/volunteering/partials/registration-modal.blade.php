<!-- Modal -->
<div class="modal fade" id="volunteer-registration" tabindex="-1" aria-labelledby="volunteerRegistrationTitle" data-backdrop="static" 
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height: calc(100vh - 200px);">
                <form action="{{route('volunteering.store')}}" method="POST" id="volunteer_registration_form">
                    @csrf
                    <h4 class="modal-title text-center text-primary" id="volunteerRegistrationTitle">
                        Volunteer Registration
                    </h4>
                    <div class="formsteps d-flex mt-5" style="justify-content: space-evenly;">
                        <div class="empty flex-fill"></div>
                        <div class="step active text-center">
                            <div class="count">1</div>
                            <div class="title">Enter Organization Details</div>
                        </div>
                        <div class="divider flex-fill"></div>
                        <div class="step text-center">
                            <div>
                                <div class="count">2</div>
                                <div class="title">Set Volunteer Preferences</div>
                            </div>
                        </div>
                        <div class="divider flex-fill"></div>
                        <div class="step text-center">
                            <div>
                                <div class="count">3</div>
                                <div class="title">Volunteer Registration Summary</div>
                            </div>
                        </div>
                        <div class="divider flex-fill"></div>
                        <div class="step text-center">
                            <div>
                                <div class="count">4</div>
                                <div class="title">Begin Volunteer Training</div>
                            </div>
                        </div>
                        <div class="empty flex-fill"></div>
                    </div>
                    <div id="volunteer-registration-carousel" class="carousel slide" data-ride="carousel" data-interval="false">
                        <div class="carousel-inner">
                            <div class="carousel-item active p-3" data-step="1">
                                <div class="row mt-5">
                                    <div class="col-12 col-md-6 offset-md-3">
                                        <div class="step-1 text-center">
                                            <p class="text-muted">
                                                Please use the dropdown menu below to select your organization. *
                                            </p>
                                            <select name="organization_id" id="" class="form-control form-control-sm" required>
                                                <option value="">Please select</option>
                                                @foreach($organizations as $org) 
                                                    <option value="{{$org->id}}">{{$org->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="carousel-item p-3" data-step="1">
                                <div class="row mt-5">
                                    <div class="col-12 col-md-6 offset-md-3">
                                        <div class="step-1 text-center">
                                            <p class="text-muted">
                                                Using the field below, please identify the number of years you have been volunteering with PECSF.
                                            </p>
                                            <input name="no_of_years" type="text" class="form-control form-control-sm" placeholder="Enter number of years" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col text-center">
                                        <input type="hidden" name="no_of_years_opt_out" value="0">
                                        <label>
                                            <input type="checkbox" name="no_of_years_opt_out" value="1">
                                            I wish to opt-out from identifying the number of years I have been volunteering with PECSF.
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="carousel-item p-3" data-step="2">
                                <div class="row mt-5">
                                    <div class="col-12 col-md-6 offset-md-3">
                                        <div class="step-1 text-center">
                                            <p class="text-muted">
                                                At the end of every Campaign, PECSF distributes recognition items to all volunteers. Please select if you would like us to use your address as shown in the Global Address Listing or enter a new address in the field below.
                                            </p>
                                            <div class="row text-left">
                                                <div class="col">
                                                    <label>
                                                        <input type="radio" name="address_type" value="Global" checked>
                                                        Global Address Listing
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="row text-left">
                                                <div class="col">
                                                    <label>
                                                        <input type="radio" name="address_type" value="New">
                                                        New Address
                                                    </label>
                                                    <input name="new_address" type="text" class="form-control form-control-sm" placeholder="Physical Address, City, Prov, Postal Code">
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item p-3" data-step="2">
                                <div class="row mt-5">
                                    <div class="col-12 col-md-6 offset-md-3">
                                        <div class="step-1 text-center">
                                            <p class="text-muted">
                                                Using the dropdown below, please select your preferred volunteer role. * <br>
                                                To learn more about the available volunteer roles with PECSF, please click <a href="#" target="_blank">here</a>
                                            </p>
                                            <select name="preferred_role" id="" class="form-control form-control-sm" required>
                                                <option value="">Please select</option>
                                                <option value="Coordinator">Coordinator</option>
                                                <option value="Canvasser">Canvasser</option>
                                                <option value="Event Coordinator">Event Coordinator</option>
                                                <option value="Office contact">Office contact</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item p-3"  data-step="3">
                                <div class="row mt-5">
                                    <div class="col-12 col-md-6 offset-md-3 text-center">
                                        <p class="text-muted">
                                            Please take a moment to review yout Volunteer Preferences below before continuting to Step 4.
                                        </p>
                                        <div class="d-flex bg-light p-3 flex-column" id="summary-table">
                                            <div class="d-flex">
                                                <div>
                                                    Organization
                                                </div>
                                                <div class="flex-fill"></div>
                                                <div data-value-for="organization">
                                                    Value
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div>
                                                    Number of years volunteering with PECSF
                                                </div>
                                                <div class="flex-fill"></div>
                                                <div data-value-for="no_of_years">
                                                    Value
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex">
                                                <div>
                                                    Address Preference
                                                </div>
                                                <div class="flex-fill"></div>
                                                <div data-value-for="address_type">
                                                    Value
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div>
                                                    Preferred Volunteer Role
                                                </div>
                                                <div class="flex-fill"></div>
                                                <div data-value-for="preferred_role">
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="carousel-item p-3"  data-step="4">
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
                <x-button href="#volunteer-registration-carousel" style="outline-primary" class="prev-btn d-none" role="button" data-slide="prev">Previous</x-button>
                <div class="flex-fill"></div>
                <x-button href="#volunteer-registration-carousel" role="button" class="next-btn" data-slide="next">Next</x-button>
                <x-button href="#volunteer-registration-carousel" role="button" class="finish-btn d-none" data-slide="next">Finish Registration</x-button>
                <x-button href="#" role="button" class="signup-btn d-none">Begin Volunteer Training</x-button>
            </div>
        </div>
    </div>
</div>
