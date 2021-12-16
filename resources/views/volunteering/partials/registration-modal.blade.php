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
            <div class="modal-body">
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
                                        <select name="" id="" class="form-control form-control-sm">
                                            <option value="">Please select</option>
                                            @foreach(config('global.organizations') as $org) 
                                                <option value="">{{$org}}</option>
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
                                            Using the field below, please identify the number of years you have been volunteering with PECSF. *
                                        </p>
                                        <input type="text" class="form-control form-control-sm" placeholder="Enter number of years">
                                    </div>
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
                                                    <input type="radio" name="address">
                                                    Global Address Listing
                                                </label>
                                            </div>
                                            <div class="col">
                                                <label>
                                                    <input type="radio" name="address">
                                                    New Address
                                                </label>
                                                <input type="text" class="form-control form-control-sm" placeholder="Physical Address, City, Prov, Postal Code">
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
                                        <select name="" id="" class="form-control form-control-sm">
                                            <option value="">Please select</option>
                                            <option value="Coordinator">Coordinator</option>
                                            <option value="Canvasser">Canvasser</option>
                                            <option value="Event Coordinator">Event Coordinator</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item p-3"  data-step="3">
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
                    </div>
                </div>
                
            
            </div>
            <div class="modal-footer">
                <x-button href="#volunteer-registration-carousel" style="outline-primary" class="prev-btn d-none" role="button" data-slide="prev">Previous</x-button>
                <div class="flex-fill"></div>
                <x-button href="#volunteer-registration-carousel" role="button" class="next-btn" data-slide="next">Next</x-button>
                <x-button href="#volunteer-registration-carousel" role="button" class="finish-btn d-none" data-slide="next">Finish Registration</x-button>
                <x-button href="#" role="button" class="signup-btn d-none">Sign up for more training</x-button>
            </div>
        </div>
    </div>
</div>
