@extends('adminlte::page')
@section('content_header')
    <div class="d-flex mt-3">
        <h1>Volunteer Profile</h1>
        <div class="flex-fill"></div>
    </div>

    <div class="mx-1 pt-3">
        <button class="btn btn-outline-primary" onclick="window.location.href='{{ route('volunteering.index') }}'">
            Back
        </button>
    </div>

@endsection
@section('content')

<div class="row">
    <div class="col-12">

        <div class="card-deck pt-2 pb-5 px-5">
            <div class="card">
            <div class="card-body text-center">
                <h1 class="card-text text-center text-primary">{{ $user->primary_job->years_of_service ? $user->primary_job->years_of_service : 'N/A' }}</h1>
                <p class="card-text  text-secondary">Years of BC Government service</p>
            </div>
            </div>
            <div class="card">
            <div class="card-body text-center">
                <h1 class="card-text text-center text-primary">{{ $user->primary_job->years_of_volunteer }}</h1>
                <p class="card-text text-secondary">Years as a volunteer</p>
            </div>
            </div>
            <div class="card">
            <div class="card-body text-center">
                <h1 class="card-text text-center text-primary">$ {{ number_format(round($user->primary_job->total_donations),2) }}</h1>
                <p class="card-text text-secondary">Dollars donated to date</p>
            </div>
            </div>
        </div>


        @if ($allow_edit)
        <div class="pb-3">
            <button type="button" class="btn btn-primary" onclick="location.href='/volunteering/profile/{{ $profile->id }}/edit';">
                    Edit Your Information
            </button>
        </div>
        @endif

        <div class="card" style="border-radius:0px;">
            <div class="card-body p-2">

                <h4 class="text-primary">Your Volunteer Details</h4>

                <div class="row pt-2">
                    <div class="col-12">
                        <div class="font-weight-bold">Campaign Year</div>
                        <div>
                            {{ $profile->campaign_year }}
                        </div>
                    </div>
                </div>                    

                <div class="row pt-2">
                    <div class="col-12">
                        <div class="font-weight-bold">Organization</div>
                        <div>
                            {{ $profile->business_unit->name }} ({{ $profile->business_unit->code }})
                        </div>
                    </div>
                </div>                    
        
                @if (!($profile->is_renew_profile))
                <div class="row pt-2">
                    <div class="col-12">
                        <div class="font-weight-bold">Number of years you have been volunteering with PECSF</div>
                        <div>
                            {{ $profile->no_of_years }}
                        </div>
                    </div>
                </div>
                @endif
        
                <div class="row pt-2">
                    <div class="col-12">
                        <div class="font-weight-bold">Your preferred Volunteer Role</div>
                        <div>
                            {{ $profile->preferred_role_name }}
                        </div>
                    </div>
                </div>   

                <div class="row pt-4 pt-2">
                    <div class="col-12">
                        <a target="_blank" href="https://www2.gov.bc.ca/gov/content/careers-myhr/about-the-bc-public-service/corporate-social-responsibility/pecsf/volunteer" class="text-primary text-bold mt-4">Learn more about available volunteer roles with PECSF</a>
                    </div>
                </div>

                <hr >
                <h4 class="text-primary">Recognition Items</h4>
        
                <div class="pt-2">

                    <div class="row pt-2">
                        <div class="col-12">
                            <div class="font-weight-bold">
                                {{ ($profile->address_type == 'G') ? 'Use my Global Address Listing' : 'Use the following address' }}
                                </div> 
                            <div>
                                {{  $profile->full_address }}
                            </div>
                        </div>    
                    </div>                 
            
                    <div class="row pt-2">
                        <div class="col-12">
                            <div class="font-weight-bold">Opt-out from receiving recognition items</div>
                            <div>
                                {{ $profile->opt_out_recongnition == 'Y' ? 'Yes' : 'No' }}
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
        </div>

    </div>
    <div class="col">
    </div>
</div>

@endsection


@push('css')
<style>
    .card a {
        text-decoration: underline !important;
    }

</style>
@endpush

@push('js')
<script>
</script>
@endpush