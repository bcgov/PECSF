<h3 class="mt-1 text-primary">3. Confirmation</h3>
<p class="mt-3">Please review your detail information and press <b>Register</b> when ready!</p>

<div class="card">
    <div class="card-body">

        <h4 class="text-primary">Your Volunteer Details</h4>
        <div class="row pt-2">
            <div class="col-12">
                <div class="font-weight-bold">Campaign Year</div>
                <div>
                    {{ $request->campaign_year }}
                </div>
            </div>
        </div>   

        <div class="row pt-2">
            <div class="col-12">
                <div class="font-weight-bold">Organization</div>
                <div>
                    {{ $business_unit->name }} ({{ $business_unit->code }})
                </div>
            </div>
        </div>                    

        <div class="row pt-2">
            <div class="col-12">
                <div class="font-weight-bold">Number of years you have been volunteering with PECSF</div>
                <div>
                    {{ $request->no_of_years }}
                </div>
            </div>
        </div>                    

        <div class="row pt-2">
            <div class="col-12">
                <div class="font-weight-bold">Your preferred Volunteer Role</div>
                <div>
                    {{ $role_name }}
                </div>
            </div>
        </div>     

        <hr>
        <h4 class="text-primary">Recognition Items</h4>
        <div class="row pt-2">
            <div class="col-12">
                <div class="font-weight-bold">
                    {{ ($request->address_type == 'G') ? 'Use my Global Address Listing' : 'Use the following address' }}
                    </div> 
                <div>
                    {{  $address }}
                </div>
            </div>    
        </div>                 

        <div class="row pt-2">
            <div class="col-12">
                <div class="font-weight-bold">Opt-out from receiving recognition items</div>
                <div>
                    {{ $request->opt_out_recongnition == 'Y' ? 'Yes' : 'No' }}
                </div>
            </div>    
        </div>                 

        <hr>
        <div class="row pt-2">
            <div class="col-12">
                
                <p>Personal information collected through this registration process is collected 
                    by the BC Public Service Agency for the purpose of facilitating PECSF volunteering 
                    recognition activities and program improvements under section 26 (c) and (e) of the 
                    Freedom of Information and Protection of Privacy Act</p>
                <p>Questions about the collection of your personal information can be directed to the Campaign Manager
                    Provincial Employees Community Services Fund at 250 356-1736 
                    or <a href="mailto:PECSF@gov.bc.ca">PECSF@gov.bc.ca</a></p>
            </div>
        </div>

    </div>
</div>
